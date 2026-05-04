<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login();

$query   = trim($_GET['q'] ?? '');
$results = [];

if ($query !== '') {
    $stmt = db()->prepare(
        'SELECT u.full_name, u.phone, u.email, dp.age, dp.blood_group, dp.pincode
         FROM users u
         JOIN donor_profiles dp ON dp.user_id = u.id
         WHERE u.is_verified = 1 AND u.is_active = 1
           AND (UPPER(dp.blood_group) = UPPER(?) OR dp.pincode = ?)
         ORDER BY u.full_name'
    );
    $stmt->execute([$query, $query]);
    $results = $stmt->fetchAll();
}

$pageTitle = 'Search Donors — ' . cfg('app','name');
include __DIR__ . '/../includes/header.php';
?>

<div class="section-title">
  <h2>Search donors</h2>
  <p>Search by blood group (e.g. <em>A+</em>) or pincode.</p>
</div>

<form method="get" class="form-card" style="margin-bottom: 28px;">
  <div class="card" style="display:flex; gap:8px;">
    <input type="text" name="q" value="<?= e($query) ?>" placeholder="Blood group or pincode" style="flex:1; padding:10px 12px; border:1px solid var(--border); border-radius:6px;">
    <button type="submit" class="btn btn-primary">Search</button>
  </div>
</form>

<?php if ($query !== ''): ?>
  <?php if (!$results): ?>
    <div class="alert alert-info">No matching donors found for "<?= e($query) ?>".</div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="data">
        <thead>
          <tr>
            <th>Name</th>
            <th>Blood</th>
            <th>Age</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Pincode</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($results as $r): ?>
            <tr>
              <td><?= e($r['full_name']) ?></td>
              <td><span class="bg-pill"><?= e($r['blood_group']) ?></span></td>
              <td><?= e((string)$r['age']) ?></td>
              <td><?= e($r['phone']) ?></td>
              <td><?= e($r['email']) ?></td>
              <td><?= e($r['pincode']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
