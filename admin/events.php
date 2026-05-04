<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$old = ['title' => '', 'message' => ''];
$errors = [];

if (is_post()) {
    csrf_check();

    if (post('action') === 'delete') {
        $id = (int) post('id');
        if ($id > 0) {
            db()->prepare('DELETE FROM events WHERE id = ?')->execute([$id]);
            flash('success', 'Event deleted.');
        }
        redirect('admin/events.php');
    }

    $title   = post('title');
    $message = post('message');
    $old = ['title' => $title, 'message' => $message];

    if ($title === '' || strlen($title) < 3) {
        $errors[] = 'Title is required (min 3 characters).';
    }
    if ($message === '') {
        $errors[] = 'Message is required.';
    }

    if (!$errors) {
        db()->prepare(
            'INSERT INTO events (title, message, created_by) VALUES (?, ?, ?)'
        )->execute([$title, $message, current_user()['id']]);

        $rows = db()->query("SELECT email, full_name FROM users WHERE role='DONOR' AND is_verified=1 AND is_active=1")->fetchAll();
        $sent = 0;
        foreach ($rows as $r) {
            if (send_mail($r['email'], '[BDMS] ' . $title, "Hi " . $r['full_name'] . ",\n\n" . $message . "\n\n— BDMS")) {
                $sent++;
            }
        }
        flash('success', 'Event posted. Notified ' . $sent . ' / ' . count($rows) . ' donor(s).');
        redirect('admin/events.php');
    }
}

$events = db()->query('SELECT id, title, message, created_at FROM events ORDER BY created_at DESC')->fetchAll();

$pageTitle = 'Events — Admin';
include __DIR__ . '/../includes/header.php';
?>

<div class="section-title">
  <h2>Events / announcements</h2>
  <p>Post a message — all verified donors get notified by email.</p>
</div>

<div style="margin-bottom:16px;">
  <a class="btn btn-ghost btn-sm" href="<?= e(url('admin/index.php')) ?>"><i class="fa-solid fa-arrow-left"></i> Back to dashboard</a>
</div>

<div class="form-card">
  <div class="card">
    <h3>Post new event</h3>

    <?php foreach ($errors as $err): ?>
      <div class="alert alert-error"><?= e($err) ?></div>
    <?php endforeach; ?>

    <form method="post">
      <?= csrf_field() ?>
      <div class="form-row">
        <label>Title</label>
        <input type="text" name="title" value="<?= e($old['title']) ?>" required>
      </div>
      <div class="form-row">
        <label>Message</label>
        <textarea name="message" rows="4" required><?= e($old['message']) ?></textarea>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Post &amp; notify</button>
    </form>
  </div>
</div>

<section class="section">
  <h3>Past events</h3>
  <?php if (!$events): ?>
    <p class="muted">No events yet.</p>
  <?php else: ?>
    <div class="grid">
      <?php foreach ($events as $ev): ?>
        <div class="card">
          <h3><?= e($ev['title']) ?></h3>
          <p><?= nl2br(e($ev['message'])) ?></p>
          <p class="muted"><?= e($ev['created_at']) ?></p>
          <form method="post" onsubmit="return confirm('Delete this event?');" style="margin:0;">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$ev['id'] ?>">
            <button class="btn btn-danger btn-sm" type="submit"><i class="fa-solid fa-trash"></i> Delete</button>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
