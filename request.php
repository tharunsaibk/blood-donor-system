<?php
require_once __DIR__ . '/includes/bootstrap.php';

$old = ['name'=>'','phone'=>'','blood_group'=>'','pincode'=>'','notes'=>''];
$errors = [];

if (is_post()) {
    csrf_check();
    $payload = [
        'name'        => post('name'),
        'phone'       => post('phone'),
        'blood_group' => strtoupper(post('blood_group')),
        'pincode'     => post('pincode'),
        'notes'       => post('notes'),
    ];
    $old = $payload;

    if (strlen($payload['name']) < 2)                       $errors[] = 'Name is required.';
    if (!preg_match('/^[6-9]\d{9}$/', $payload['phone']))   $errors[] = 'Enter a 10-digit Indian mobile number.';
    if (!in_array($payload['blood_group'], ['A+','A-','B+','B-','AB+','AB-','O+','O-'], true))
        $errors[] = 'Pick a valid blood group.';
    if ($payload['pincode'] !== '' && !preg_match('/^\d{6}$/', $payload['pincode']))
        $errors[] = 'Pincode must be 6 digits (or leave it blank).';

    if (!$errors) {
        db()->prepare(
            'INSERT INTO blood_requests (requester_name, requester_phone, blood_group, pincode, notes)
             VALUES (?, ?, ?, ?, ?)'
        )->execute([
            $payload['name'], $payload['phone'], $payload['blood_group'],
            $payload['pincode'] !== '' ? $payload['pincode'] : null,
            $payload['notes'] !== '' ? $payload['notes'] : null,
        ]);

        $stmt = db()->prepare(
            'SELECT u.email, u.full_name
             FROM users u JOIN donor_profiles dp ON dp.user_id = u.id
             WHERE u.is_verified=1 AND u.is_active=1
               AND dp.blood_group = ?
               AND (? = "" OR dp.pincode = ?)'
        );
        $stmt->execute([$payload['blood_group'], $payload['pincode'], $payload['pincode']]);
        $matches = $stmt->fetchAll();

        $notified = 0;
        foreach ($matches as $m) {
            if (send_mail(
                $m['email'],
                'Blood request: ' . $payload['blood_group'] . ' needed',
                "Hi " . $m['full_name'] . ",\n\n" .
                $payload['name'] . " is looking for " . $payload['blood_group'] .
                " blood. Phone: " . $payload['phone'] .
                ($payload['pincode'] !== '' ? "\nPincode: " . $payload['pincode'] : '') .
                ($payload['notes'] !== '' ? "\nNote: " . $payload['notes'] : '') .
                "\n\nIf you can help, please reach out directly.\n\n— BDMS"
            )) {
                $notified++;
            }
        }

        flash('success', 'Request submitted. ' . count($matches) . ' matching donor(s) found' .
            ($notified ? ", $notified notified by email." : '.'));
        redirect('request.php');
    }
}

$pageTitle = 'Request Blood — ' . cfg('app','name');
include __DIR__ . '/includes/header.php';
?>

<div class="form-card">
  <div class="card">
    <h2 style="text-align:center;"><i class="fa-solid fa-hand-holding-medical"></i> Request blood</h2>
    <p class="muted" style="text-align:center;">We'll notify donors that match your blood group (and pincode, if provided).</p>

    <?php foreach ($errors as $err): ?>
      <div class="alert alert-error"><?= e($err) ?></div>
    <?php endforeach; ?>

    <form method="post">
      <?= csrf_field() ?>
      <div class="form-row">
        <label>Your name</label>
        <input type="text" name="name" value="<?= e($old['name']) ?>" required>
      </div>
      <div class="form-row">
        <label>Your phone</label>
        <input type="tel" name="phone" value="<?= e($old['phone']) ?>" required>
      </div>
      <div class="form-row">
        <label>Blood group needed</label>
        <select name="blood_group" required>
          <option value="">Select</option>
          <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
            <option value="<?= e($bg) ?>" <?= $old['blood_group'] === $bg ? 'selected' : '' ?>><?= e($bg) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-row">
        <label>Pincode <span class="muted">(optional)</span></label>
        <input type="text" name="pincode" maxlength="6" value="<?= e($old['pincode']) ?>">
      </div>
      <div class="form-row">
        <label>Notes <span class="muted">(optional)</span></label>
        <textarea name="notes" rows="3"><?= e($old['notes']) ?></textarea>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Send request</button>
    </form>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
