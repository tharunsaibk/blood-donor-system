<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login();

$user = current_user();

$profile = db()->prepare(
    'SELECT age, blood_group, pincode, last_donation_date
     FROM donor_profiles WHERE user_id = ?'
);
$profile->execute([$user['id']]);
$profile = $profile->fetch();

$events = db()->query(
    'SELECT title, message, created_at
     FROM events ORDER BY created_at DESC LIMIT 5'
)->fetchAll();

$pageTitle = 'Dashboard — ' . cfg('app','name');
include __DIR__ . '/../includes/header.php';
?>

<div class="section-title">
  <h2>Hello, <?= e($user['full_name']) ?></h2>
  <p>Welcome to your donor dashboard.</p>
</div>

<div class="grid">
  <div class="card">
    <h3><i class="fa-solid fa-id-card"></i> Your profile</h3>
    <?php if ($profile): ?>
      <p>Blood group: <span class="bg-pill"><?= e($profile['blood_group']) ?></span></p>
      <p class="muted">Age: <?= e((string)$profile['age']) ?> &middot; Pincode: <?= e($profile['pincode']) ?></p>
      <?php if ($profile['last_donation_date']): ?>
        <p class="muted">Last donation: <?= e($profile['last_donation_date']) ?></p>
      <?php endif; ?>
    <?php else: ?>
      <p class="muted">No donor profile yet.</p>
    <?php endif; ?>
  </div>

  <div class="card">
    <h3><i class="fa-solid fa-magnifying-glass"></i> Search donors</h3>
    <p>Find verified donors by blood group or pincode.</p>
    <a class="btn btn-primary btn-sm" href="<?= e(url('donor/search.php')) ?>">Open search</a>
  </div>

  <div class="card">
    <h3><i class="fa-solid fa-hand-holding-medical"></i> Need blood?</h3>
    <p>Submit a request and matching donors will be notified by email.</p>
    <a class="btn btn-primary btn-sm" href="<?= e(url('request.php')) ?>">Make request</a>
  </div>
</div>

<section class="section">
  <h2><i class="fa-solid fa-bullhorn"></i> Latest announcements</h2>
  <?php if (!$events): ?>
    <p class="muted">No announcements yet.</p>
  <?php else: ?>
    <div class="grid">
      <?php foreach ($events as $ev): ?>
        <div class="card">
          <h3><?= e($ev['title']) ?></h3>
          <p><?= nl2br(e($ev['message'])) ?></p>
          <p class="muted"><?= e($ev['created_at']) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
