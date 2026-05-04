<?php
/**
 * One-time installer.
 * Creates the initial admin user, then asks you to delete this file.
 *
 * URL: http://<host>/<base_url>/install.php
 */

require_once __DIR__ . '/includes/bootstrap.php';

$existingAdmin = db()->query("SELECT id FROM users WHERE role='ADMIN' LIMIT 1")->fetch();

$errors = [];
$created = false;

if (is_post()) {
    csrf_check();

    $name  = post('full_name');
    $email = strtolower(post('email'));
    $phone = post('phone');
    $pwd   = $_POST['password'] ?? '';

    if (strlen($name) < 2)                                         $errors[] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))                $errors[] = 'Email looks invalid.';
    if (!preg_match('/^[6-9]\d{9}$/', $phone))                     $errors[] = 'Phone must be a 10-digit Indian mobile number.';
    if (strlen($pwd) < 8 || !preg_match('/[A-Z]/', $pwd) || !preg_match('/[^A-Za-z0-9]/', $pwd))
        $errors[] = 'Password must be at least 8 characters with one uppercase letter and one symbol.';

    if (!$errors) {
        $exists = db()->prepare('SELECT id FROM users WHERE email = ?');
        $exists->execute([$email]);
        if ($exists->fetch()) {
            $errors[] = 'A user with this email already exists.';
        }
    }

    if (!$errors) {
        db()->prepare(
            'INSERT INTO users (full_name, email, phone, password_hash, role, is_verified, is_active)
             VALUES (?, ?, ?, ?, "ADMIN", 1, 1)'
        )->execute([$name, $email, $phone, password_hash($pwd, PASSWORD_BCRYPT)]);
        $created = true;
    }
}

$pageTitle = 'Install — ' . cfg('app','name');
include __DIR__ . '/includes/header.php';
?>

<div class="form-card">
  <div class="card">
    <h2 style="text-align:center;"><i class="fa-solid fa-screwdriver-wrench"></i> One-time setup</h2>

    <?php if ($existingAdmin && !$created): ?>
      <div class="alert alert-info">
        An admin already exists. <strong>Delete <code>install.php</code> now</strong> and use
        <a href="<?= e(url('admin/login.php')) ?>">admin login</a>.
      </div>
    <?php elseif ($created): ?>
      <div class="alert alert-success">
        Admin created! For security, <strong>delete <code>install.php</code></strong> from your project folder.<br>
        Then go to <a href="<?= e(url('admin/login.php')) ?>">admin login</a>.
      </div>
    <?php else: ?>

      <p class="muted" style="text-align:center;">Create the first admin account.</p>

      <?php foreach ($errors as $err): ?>
        <div class="alert alert-error"><?= e($err) ?></div>
      <?php endforeach; ?>

      <form method="post">
        <?= csrf_field() ?>
        <div class="form-row"><label>Full name</label><input type="text" name="full_name" required></div>
        <div class="form-row"><label>Email</label><input type="email" name="email" required></div>
        <div class="form-row"><label>Phone</label><input type="tel" name="phone" required></div>
        <div class="form-row"><label>Password</label><input type="password" name="password" placeholder="Min 8, 1 uppercase, 1 symbol" required></div>
        <button type="submit" class="btn btn-primary btn-block">Create admin</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
