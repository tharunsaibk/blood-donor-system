<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Contact — ' . cfg('app','name');
include __DIR__ . '/includes/header.php';
?>

<div class="section-title">
  <h2>Contact us</h2>
  <p>Have a question, suggestion or partnership idea? Reach out.</p>
</div>

<div class="grid">
  <div class="card">
    <div class="icon"><i class="fa-solid fa-envelope"></i></div>
    <h3>Email</h3>
    <p class="muted">support@bdms.local</p>
  </div>
  <div class="card">
    <div class="icon"><i class="fa-solid fa-phone"></i></div>
    <h3>Phone</h3>
    <p class="muted">+91 00000 00000</p>
  </div>
  <div class="card">
    <div class="icon"><i class="fa-solid fa-location-dot"></i></div>
    <h3>Address</h3>
    <p class="muted">Your campus / city, India</p>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
