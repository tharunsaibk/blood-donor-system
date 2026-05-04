<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Benefits — ' . cfg('app','name');
include __DIR__ . '/includes/header.php';
?>

<div class="section-title">
  <h2>Benefits of donating blood</h2>
  <p>Good for the recipient. Good for you.</p>
</div>

<div class="grid">
  <div class="card">
    <div class="icon"><i class="fa-solid fa-heart-circle-check"></i></div>
    <h3>Healthier heart</h3>
    <p>Regular donation helps maintain healthy iron levels and is associated with better cardiovascular health.</p>
  </div>
  <div class="card">
    <div class="icon"><i class="fa-solid fa-stethoscope"></i></div>
    <h3>Free mini check-up</h3>
    <p>Every donation includes a quick health screening — pulse, blood pressure, temperature and hemoglobin.</p>
  </div>
  <div class="card">
    <div class="icon"><i class="fa-solid fa-droplet"></i></div>
    <h3>New blood cells</h3>
    <p>Your body replenishes the donated blood within a few weeks, encouraging healthy cell production.</p>
  </div>
  <div class="card">
    <div class="icon"><i class="fa-solid fa-hand-holding-heart"></i></div>
    <h3>Sense of purpose</h3>
    <p>Few small acts have a clearer impact: each unit of blood you give can save up to three lives.</p>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
