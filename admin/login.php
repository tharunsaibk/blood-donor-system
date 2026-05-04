<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if (is_admin()) {
    redirect('admin/index.php');
}

$email = '';

if (is_post()) {
    csrf_check();
    $email = strtolower(post('email'));
    $pwd   = $_POST['password'] ?? '';

    if ($email === '' || $pwd === '') {
        flash('error', 'Email and password are required.');
    } else {
        $stmt = db()->prepare(
            'SELECT id, full_name, email, password_hash, role, is_active
             FROM users WHERE email = ? AND role = "ADMIN" LIMIT 1'
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($pwd, $user['password_hash']) || !$user['is_active']) {
            flash('error', 'Invalid admin credentials.');
        } else {
            auth_login($user);
            flash('success', 'Welcome, ' . $user['full_name'] . '.');
            redirect('admin/index.php');
        }
    }
}

$pageTitle = 'Admin Login — ' . cfg('app','name');
include __DIR__ . '/../includes/header.php';
?>

<div class="form-card">
  <div class="card">
    <h2 style="text-align:center;"><i class="fa-solid fa-user-shield"></i> Admin sign in</h2>

    <form method="post">
      <?= csrf_field() ?>
      <div class="form-row">
        <label>Admin email</label>
        <input type="email" name="email" value="<?= e($email) ?>" required autofocus>
      </div>
      <div class="form-row">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Sign in</button>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
