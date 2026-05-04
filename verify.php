<?php
require_once __DIR__ . '/includes/bootstrap.php';

if (empty($_SESSION['pending_user_id'])) {
    flash('error', 'Please register first.');
    redirect('register.php');
}
$userId = (int) $_SESSION['pending_user_id'];

if (is_post()) {
    csrf_check();
    $code = preg_replace('/\D/', '', post('code'));

    if (strlen($code) !== 6) {
        flash('error', 'Enter the 6-digit code.');
    } else {
        $stmt = db()->prepare(
            'SELECT id FROM otps
             WHERE user_id = ? AND code = ? AND consumed_at IS NULL AND expires_at > NOW()
             ORDER BY id DESC LIMIT 1'
        );
        $stmt->execute([$userId, $code]);
        $otp = $stmt->fetch();

        if (!$otp) {
            flash('error', 'Invalid or expired code.');
        } else {
            db()->prepare('UPDATE otps SET consumed_at = NOW() WHERE id = ?')->execute([$otp['id']]);
            db()->prepare('UPDATE users SET is_verified = 1 WHERE id = ?')->execute([$userId]);
            unset($_SESSION['pending_user_id']);
            flash('success', 'Email verified! You can sign in now.');
            redirect('login.php');
        }
    }
}

$pageTitle = 'Verify — ' . cfg('app','name');
include __DIR__ . '/includes/header.php';
?>

<div class="form-card">
  <div class="card">
    <h2 style="text-align:center;"><i class="fa-solid fa-shield-halved"></i> Verify your email</h2>
    <p class="muted" style="text-align:center;">Enter the 6-digit code we just sent.</p>

    <form method="post">
      <?= csrf_field() ?>
      <div class="form-row">
        <label>Verification code</label>
        <input type="text" name="code" maxlength="6" inputmode="numeric" pattern="\d{6}" required autofocus>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Verify</button>
    </form>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
