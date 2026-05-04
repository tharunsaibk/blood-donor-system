<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

if (is_post()) {
    csrf_check();
    $id = (int) post('id');
    if ($id > 0) {
        $stmt = db()->prepare('SELECT id, role FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $target = $stmt->fetch();
        if (!$target) {
            flash('error', 'Donor not found.');
        } elseif ($target['role'] === 'ADMIN') {
            flash('error', 'You cannot delete an admin from here.');
        } else {
            db()->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);
            flash('success', 'Donor deleted.');
        }
    }
    redirect('admin/donors.php');
}

$rows = db()->query(
    "SELECT u.id, u.full_name, u.email, u.phone, u.is_verified,
            dp.age, dp.blood_group, dp.pincode
     FROM users u
     LEFT JOIN donor_profiles dp ON dp.user_id = u.id
     WHERE u.role = 'DONOR'
     ORDER BY u.created_at DESC"
)->fetchAll();

$pageTitle = 'Donors — Admin';
include __DIR__ . '/../includes/header.php';
?>

<div class="section-title">
  <h2>Donors</h2>
  <p>All registered donors. Unverified donors are marked.</p>
</div>

<div style="margin-bottom:16px;">
  <a class="btn btn-ghost btn-sm" href="<?= e(url('admin/index.php')) ?>"><i class="fa-solid fa-arrow-left"></i> Back to dashboard</a>
  <a class="btn btn-primary btn-sm" href="<?= e(url('admin/add_donor.php')) ?>"><i class="fa-solid fa-plus"></i> Add donor</a>
</div>

<?php if (!$rows): ?>
  <div class="alert alert-info">No donors registered yet.</div>
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
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= e($r['full_name']) ?></td>
            <td><?= $r['blood_group'] ? '<span class="bg-pill">'.e($r['blood_group']).'</span>' : '<span class="muted">—</span>' ?></td>
            <td><?= e((string)($r['age'] ?? '—')) ?></td>
            <td><?= e($r['phone']) ?></td>
            <td><?= e($r['email']) ?></td>
            <td><?= e($r['pincode'] ?? '—') ?></td>
            <td>
              <?php if ($r['is_verified']): ?>
                <span class="muted"><i class="fa-solid fa-check"></i> Verified</span>
              <?php else: ?>
                <span class="muted"><i class="fa-solid fa-clock"></i> Pending</span>
              <?php endif; ?>
            </td>
            <td>
              <form method="post" onsubmit="return confirm('Delete this donor?');" style="margin:0;">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button class="btn btn-danger btn-sm" type="submit"><i class="fa-solid fa-trash"></i></button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
