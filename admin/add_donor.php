<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

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
        'confirm_password' => $_POST['password'] ?? '',
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
                 VALUES (?, ?, ?, ?, "DONOR", 1)'
            );
            $insertUser->execute([
                $payload['full_name'],
                $payload['email'],
                $payload['phone'],
                password_hash($payload['password'], PASSWORD_BCRYPT),
            ]);
            $userId = (int) db()->lastInsertId();

            db()->prepare(
                'INSERT INTO donor_profiles (user_id, age, blood_group, pincode)
                 VALUES (?, ?, ?, ?)'
            )->execute([$userId, (int)$payload['age'], $payload['blood_group'], $payload['pincode']]);

            db()->commit();
            flash('success', 'Donor added.');
            redirect('admin/donors.php');
        } catch (Throwable $e) {
            db()->rollBack();
            $errors[] = 'Could not add donor. Please try again.';
        }
    }
}

$pageTitle = 'Add Donor — Admin';
include __DIR__ . '/../includes/header.php';
?>

<div class="form-card">
  <div class="card">
    <h2 style="text-align:center;"><i class="fa-solid fa-user-plus"></i> Add donor</h2>
    <p class="muted" style="text-align:center;">Donor will be added as already verified.</p>

    <?php foreach ($errors as $err): ?>
      <div class="alert alert-error"><?= e($err) ?></div>
    <?php endforeach; ?>

    <form method="post">
      <?= csrf_field() ?>
      <div class="form-row"><label>Full name</label><input type="text" name="full_name" value="<?= e($old['full_name']) ?>" required></div>
      <div class="form-row"><label>Age</label><input type="number" name="age" min="18" max="65" value="<?= e($old['age']) ?>" required></div>
      <div class="form-row">
        <label>Blood group</label>
        <select name="blood_group" required>
          <option value="">Select</option>
          <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
            <option value="<?= e($bg) ?>" <?= $old['blood_group'] === $bg ? 'selected' : '' ?>><?= e($bg) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-row"><label>Phone</label><input type="tel" name="phone" value="<?= e($old['phone']) ?>" required></div>
      <div class="form-row"><label>Email</label><input type="email" name="email" value="<?= e($old['email']) ?>" required></div>
      <div class="form-row"><label>Pincode</label><input type="text" name="pincode" maxlength="6" value="<?= e($old['pincode']) ?>" required></div>
      <div class="form-row"><label>Initial password</label><input type="password" name="password" placeholder="Min 8, 1 uppercase, 1 symbol" required></div>

      <button type="submit" class="btn btn-primary btn-block">Add donor</button>

      <p class="form-help"><a href="<?= e(url('admin/donors.php')) ?>">Back to donor list</a></p>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
