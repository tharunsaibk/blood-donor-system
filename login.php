<?php
require_once __DIR__ . '/includes/bootstrap.php';

if (is_logged_in()) {
    redirect(is_admin() ? 'admin/index.php' : 'donor/index.php');
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
            'SELECT id, full_name, email, password_hash, role, is_verified, is_active
             FROM users WHERE email = ? LIMIT 1'
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($pwd, $user['password_hash'])) {
            flash('error', 'Invalid email or password.');
        } elseif (!$user['is_active']) {
            flash('error', 'Account disabled. Contact an admin.');
        } elseif ($user['role'] === 'DONOR' && !$user['is_verified']) {
            $_SESSION['pending_user_id'] = (int)$user['id'];
            flash('info', 'Please verify your email first.');
            redirect('verify.php');
        } else {
            auth_login($user);
            flash('success', 'Welcome back, ' . $user['full_name'] . '!');
            redirect($user['role'] === 'ADMIN' ? 'admin/index.php' : 'donor/index.php');
        }
    }
}

$pageTitle = 'Login — ' . cfg('app','name');
include __DIR__ . '/includes/header.php';
?>

<div class="form-card">
  <div class="card">
    <h2 style="text-align:center;"><i class="fa-solid fa-right-to-bracket"></i> Sign in</h2>

    <form method="post">
      <?= csrf_field() ?>
      <div class="form-row">
        <label>Email</label>
        <input type="email" name="email" value="<?= e($email) ?>" required autofocus>
      </div>
      <div class="form-row">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Sign in</button>
      <p class="form-help">
        New here? <a href="<?= e(url('register.php')) ?>">Create an account</a><br>
        <a href="<?= e(url('admin/login.php')) ?>">Admin login</a>
      </p>
    </form>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
