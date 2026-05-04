<?php
require_once __DIR__ . '/includes/bootstrap.php';

$old = ['full_name'=>'','age'=>'','blood_group'=>'','phone'=>'','email'=>'','pincode'=>''];
$errors = [];

if (is_post()) {
    csrf_check();

    $payload = [
        'full_name'        => post('full_name'),
        'age'              => post('age'),
        'blood_group'      => strtoupper(post('blood_group')),
        'phone'            => post('phone'),
        'email'            => strtolower(post('email')),
        'pincode'          => post('pincode'),
        'password'         => $_POST['password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? '',
    ];
    $old = array_intersect_key($payload, $old);

    $errors = validate_donor_payload($payload);

    if (!$errors) {
        $exists = db()->prepare('SELECT id FROM users WHERE email = ?');
        $exists->execute([$payload['email']]);
        if ($exists->fetch()) {
            $errors[] = 'An account with this email already exists.';
        }
    }

    if (!$errors) {
        try {
            db()->beginTransaction();

            $insertUser = db()->prepare(
                'INSERT INTO users (full_name, email, phone, password_hash, role, is_verified)
                 VALUES (?, ?, ?, ?, "DONOR", 0)'
            );
            $insertUser->execute([
                $payload['full_name'],
                $payload['email'],
                $payload['phone'],
                password_hash($payload['password'], PASSWORD_BCRYPT),
            ]);
            $userId = (int) db()->lastInsertId();

            $insertProfile = db()->prepare(
                'INSERT INTO donor_profiles (user_id, age, blood_group, pincode)
                 VALUES (?, ?, ?, ?)'
            );
            $insertProfile->execute([
                $userId,
                (int)$payload['age'],
                $payload['blood_group'],
                $payload['pincode'],
            ]);

            $code      = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiresAt = (new DateTime('+' . (int)cfg('otp','length_minutes') . ' minutes'))
                            ->format('Y-m-d H:i:s');

            db()->prepare(
                'INSERT INTO otps (user_id, code, expires_at) VALUES (?, ?, ?)'
            )->execute([$userId, $code, $expiresAt]);

            db()->commit();

            $sent = send_mail(
                $payload['email'],
                'Your BDMS verification code',
                "Hi " . $payload['full_name'] . ",\n\nYour verification code is: " . $code .
                "\nIt expires in " . cfg('otp','length_minutes') . " minutes.\n\n— BDMS"
            );

            $_SESSION['pending_user_id'] = $userId;

            if (cfg('app','debug')) {
                flash('info', 'Dev mode: your OTP is ' . $code);
            } elseif (!$sent) {
                flash('info', 'We could not send the email — please contact support.');
            } else {
                flash('success', 'Verification code sent to ' . $payload['email']);
            }
            redirect('verify.php');
        } catch (Throwable $e) {
            db()->rollBack();
            $errors[] = 'Something went wrong. Please try again.';
        }
    }
}

$pageTitle = 'Register — ' . cfg('app','name');
include __DIR__ . '/includes/header.php';
?>

<div class="form-card">
  <div class="card">
    <h2 style="text-align:center;"><i class="fa-solid fa-user-plus"></i> Become a Donor</h2>
    <p class="muted" style="text-align:center;">Takes less than a minute.</p>

    <?php foreach ($errors as $err): ?>
      <div class="alert alert-error"><?= e($err) ?></div>
    <?php endforeach; ?>

    <form method="post" data-validate novalidate>
      <?= csrf_field() ?>

      <div class="form-row">
        <label>Full name</label>
        <input type="text" name="full_name" value="<?= e($old['full_name']) ?>" required>
        <span class="error"></span>
      </div>

      <div class="form-row">
        <label>Age</label>
        <input type="number" name="age" min="18" max="65" value="<?= e($old['age']) ?>" required>
        <span class="error"></span>
      </div>

      <div class="form-row">
        <label>Blood group</label>
        <select name="blood_group" required>
          <option value="">Select</option>
          <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
            <option value="<?= e($bg) ?>" <?= $old['blood_group'] === $bg ? 'selected' : '' ?>><?= e($bg) ?></option>
          <?php endforeach; ?>
        </select>
        <span class="error"></span>
      </div>

      <div class="form-row">
        <label>Phone</label>
        <input type="tel" name="phone" placeholder="10-digit mobile number" value="<?= e($old['phone']) ?>" required>
        <span class="error"></span>
      </div>

      <div class="form-row">
        <label>Email</label>
        <input type="email" name="email" value="<?= e($old['email']) ?>" required>
        <span class="error"></span>
      </div>

      <div class="form-row">
        <label>Pincode</label>
        <input type="text" name="pincode" maxlength="6" value="<?= e($old['pincode']) ?>" required>
        <span class="error"></span>
      </div>

      <div class="form-row">
        <label>Password</label>
        <input type="password" name="password" placeholder="Min 8, 1 uppercase, 1 symbol" required>
        <span class="error"></span>
      </div>

      <div class="form-row">
        <label>Confirm password</label>
        <input type="password" name="confirm_password" required>
        <span class="error"></span>
      </div>

      <button type="submit" class="btn btn-primary btn-block">Create account</button>

      <p class="form-help">Already registered? <a href="<?= e(url('login.php')) ?>">Sign in</a></p>
    </form>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
