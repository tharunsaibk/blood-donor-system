<?php
require_once __DIR__ . '/includes/bootstrap.php';

$totalDonors = (int) db()->query(
    "SELECT COUNT(*) FROM users WHERE role='DONOR' AND is_verified=1 AND is_active=1"
)->fetchColumn();

$bloodGroups = db()->query(
    "SELECT dp.blood_group, COUNT(*) AS c
     FROM donor_profiles dp
     JOIN users u ON u.id = dp.user_id
     WHERE u.is_verified=1 AND u.is_active=1
     GROUP BY dp.blood_group
     ORDER BY dp.blood_group"
)->fetchAll();

$pageTitle = cfg('app','name');
include __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <h1>Donate Blood. Save Lives.</h1>
  <p>BDMS connects voluntary blood donors with people who need blood — quickly, safely, and locally.</p>
  <a class="btn btn-light" href="<?= e(url('register.php')) ?>"><i class="fa-solid fa-heart"></i> Become a Donor</a>
  <a class="btn btn-light" href="<?= e(url('request.php')) ?>"><i class="fa-solid fa-hand-holding-medical"></i> Request Blood</a>
</section>

<section class="section">
  <div class="stat-grid">
    <div class="stat">
      <div class="label">Verified donors</div>
      <div class="value"><?= e((string)$totalDonors) ?></div>
    </div>
    <?php foreach ($bloodGroups as $g): ?>
      <div class="stat">
        <div class="label">Blood group <?= e($g['blood_group']) ?></div>
        <div class="value"><?= e((string)$g['c']) ?></div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="section">
  <div class="section-title">
    <h2>Why donate?</h2>
    <p>One donation can save up to three lives.</p>
  </div>
  <div class="grid">
    <div class="card">
      <div class="icon"><i class="fa-solid fa-heart-pulse"></i></div>
      <h3>Save lives</h3>
      <p>Your single donation can help accident victims, surgery patients and people fighting cancer.</p>
    </div>
    <div class="card">
      <div class="icon"><i class="fa-solid fa-shield-heart"></i></div>
      <h3>Safe &amp; simple</h3>
      <p>The whole process — from registration to donation — takes less than an hour and is completely safe.</p>
    </div>
    <div class="card">
      <div class="icon"><i class="fa-solid fa-people-group"></i></div>
      <h3>Build community</h3>
      <p>Be part of a network of donors that responds to local emergencies whenever blood is needed.</p>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
