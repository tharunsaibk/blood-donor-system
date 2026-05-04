<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$donorCount  = (int) db()->query("SELECT COUNT(*) FROM users WHERE role='DONOR' AND is_verified=1")->fetchColumn();
$pendingCount= (int) db()->query("SELECT COUNT(*) FROM users WHERE role='DONOR' AND is_verified=0")->fetchColumn();
$eventCount  = (int) db()->query("SELECT COUNT(*) FROM events")->fetchColumn();
$openReqs    = (int) db()->query("SELECT COUNT(*) FROM blood_requests WHERE status='OPEN'")->fetchColumn();

$bgRows = db()->query(
    "SELECT dp.blood_group, COUNT(*) AS c
     FROM donor_profiles dp JOIN users u ON u.id = dp.user_id
     WHERE u.is_verified=1 GROUP BY dp.blood_group ORDER BY dp.blood_group"
)->fetchAll();

$pageTitle = 'Admin — ' . cfg('app','name');
include __DIR__ . '/../includes/header.php';
?>

<div class="section-title">
  <h2>Admin dashboard</h2>
  <p>Welcome back, <?= e(current_user()['full_name']) ?>.</p>
</div>

<div class="stat-grid">
  <div class="stat"><div class="label">Verified donors</div><div class="value"><?= $donorCount ?></div></div>
  <div class="stat"><div class="label">Pending verification</div><div class="value"><?= $pendingCount ?></div></div>
  <div class="stat"><div class="label">Events posted</div><div class="value"><?= $eventCount ?></div></div>
  <div class="stat"><div class="label">Open requests</div><div class="value"><?= $openReqs ?></div></div>
</div>

<div class="grid">
  <div class="card">
    <h3><i class="fa-solid fa-list"></i> Donor list</h3>
    <p>View, search and remove registered donors.</p>
    <a class="btn btn-primary btn-sm" href="<?= e(url('admin/donors.php')) ?>">Open</a>
  </div>
  <div class="card">
    <h3><i class="fa-solid fa-user-plus"></i> Add donor</h3>
    <p>Manually add a verified donor.</p>
    <a class="btn btn-primary btn-sm" href="<?= e(url('admin/add_donor.php')) ?>">Add</a>
  </div>
  <div class="card">
    <h3><i class="fa-solid fa-bullhorn"></i> Events</h3>
    <p>Post an announcement and notify all donors.</p>
    <a class="btn btn-primary btn-sm" href="<?= e(url('admin/events.php')) ?>">Manage</a>
  </div>
</div>

<section class="section">
  <h2>Donors by blood group</h2>
  <?php if (!$bgRows): ?>
    <p class="muted">No verified donors yet.</p>
  <?php else: ?>
    <div class="table-wrap">
      <table class="data">
        <thead><tr><th>Blood group</th><th>Donors</th></tr></thead>
        <tbody>
          <?php foreach ($bgRows as $r): ?>
            <tr>
              <td><span class="bg-pill"><?= e($r['blood_group']) ?></span></td>
              <td><?= e((string)$r['c']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
