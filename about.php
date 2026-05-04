<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'About — ' . cfg('app','name');
include __DIR__ . '/includes/header.php';
?>

<div class="section-title">
  <h2>About BDMS</h2>
  <p>A small platform with a big purpose.</p>
</div>

<div class="grid">
  <div class="card">
    <h3>Our mission</h3>
    <p>We make it easy for hospitals, families and individuals to find verified blood donors in their area, and easy for donors to step up when they're needed.</p>
  </div>
  <div class="card">
    <h3>How it works</h3>
    <p>Donors register and verify their email with a one-time code. Anyone in need can search by blood group or pincode and send a request that notifies matching donors.</p>
  </div>
  <div class="card">
    <h3>Built with</h3>
    <p>Plain PHP, MySQL and a tiny bit of vanilla JavaScript. Designed to be readable, hackable and easy to run with PHP's built-in development server.</p>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
