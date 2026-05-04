<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle ?? cfg('app','name')) ?></title>
  <link rel="icon" type="image/svg+xml" href="<?= e(asset('favicon.svg')) ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
</head>
<body>
<header class="site-header">
  <div class="container nav-wrap">
    <a class="brand" href="<?= e(url('/')) ?>">
      <i class="fa-solid fa-droplet"></i>
      <span>BDMS</span>
    </a>
    <nav class="nav-links">
      <a href="<?= e(url('/')) ?>">Home</a>
      <a href="<?= e(url('about.php')) ?>">About</a>
      <a href="<?= e(url('benefits.php')) ?>">Benefits</a>
      <a href="<?= e(url('request.php')) ?>">Request Blood</a>
      <a href="<?= e(url('contact.php')) ?>">Contact</a>
      <?php if (is_admin()): ?>
        <a href="<?= e(url('admin/index.php')) ?>">Admin</a>
        <a class="btn btn-ghost" href="<?= e(url('logout.php')) ?>">Logout</a>
      <?php elseif (is_logged_in()): ?>
        <a href="<?= e(url('donor/index.php')) ?>">Dashboard</a>
        <a class="btn btn-ghost" href="<?= e(url('logout.php')) ?>">Logout</a>
      <?php else: ?>
        <a href="<?= e(url('login.php')) ?>">Login</a>
        <a class="btn btn-primary" href="<?= e(url('register.php')) ?>">Become a Donor</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main class="container page">
<?= render_flashes() ?>
