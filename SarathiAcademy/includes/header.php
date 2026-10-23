<?php
// includes/header.php  –  Public site header
require_once __DIR__ . '/auth.php';
$user = currentUser();
$flash = getFlash();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? e($pageTitle) . ' | ' : '' ?>SarathiAcademy</title>
<link rel="icon" type="image/png" href="<?= SITE_URL ?>/assets/images/logo.png">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>

<!-- TOP BAR -->
<div class="topbar">
  <div class="container topbar-inner">
    <span><i class="fas fa-phone-alt"></i> 9139959698 / 9172135355</span>
    <span><i class="fas fa-envelope"></i> admin@SarathiAcademy.com</span>
    <div class="topbar-social">
      <a href="https://instagram.com/SarathiAcademy" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
      <a href="https://wa.me/919139959698" target="_blank" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
      <a href="https://facebook.com/SarathiAcademy" target="_blank" title="Facebook"><i class="fab fa-facebook-f"></i></a>
      <a href="https://linkedin.com/company/SarathiAcademy" target="_blank" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
    </div>
  </div>
</div>

<!-- NAVBAR -->
<nav class="navbar">
  <div class="container navbar-inner">
    <a class="brand" href="<?= SITE_URL ?>/">
      <img src="<?= SITE_URL ?>/assets/images/logo.png" alt="SarathiAcademy Logo">
      <span>SarathiAcademy</span>
    </a>
    <button class="nav-toggle" id="navToggle" aria-label="Menu"><i class="fas fa-bars"></i></button>
    <ul class="nav-links" id="navLinks">
      <li><a href="<?= SITE_URL ?>/"          class="<?= $currentPage==='index'?'active':'' ?>">Home</a></li>
      <li><a href="<?= SITE_URL ?>/about.php" class="<?= $currentPage==='about'?'active':'' ?>">About</a></li>
      <li><a href="<?= SITE_URL ?>/courses.php" class="<?= $currentPage==='courses'?'active':'' ?>">Courses</a></li>
      <li><a href="<?= SITE_URL ?>/gallery.php" class="<?= $currentPage==='gallery'?'active':'' ?>">Gallery</a></li>
      <li><a href="<?= SITE_URL ?>/enquiry.php" class="<?= $currentPage==='enquiry'?'active':'' ?>">Enquiry</a></li>
      <li><a href="<?= SITE_URL ?>/contact.php" class="<?= $currentPage==='contact'?'active':'' ?>">Contact</a></li>
      <?php if ($user): ?>
        <?php if ($user['role']==='admin' || $user['role']==='staff'): ?>
          <li><a href="<?= ADMIN_URL ?>/" class="btn-nav">Dashboard</a></li>
        <?php else: ?>
          <li><a href="<?= SITE_URL ?>/student/dashboard.php" class="btn-nav">My Dashboard</a></li>
        <?php endif; ?>
        <li><a href="<?= SITE_URL ?>/logout.php" class="btn-nav btn-outline">Logout</a></li>
      <?php else: ?>
        <li><a href="<?= SITE_URL ?>/register.php" class="btn-nav">Register</a></li>
        <li><a href="<?= SITE_URL ?>/login.php"    class="btn-nav btn-outline">Login</a></li>
      <?php endif; ?>
    </ul>
  </div>
</nav>

<?php if ($flash): ?>
<div class="flash flash-<?= e($flash['type']) ?>">
  <?= e($flash['msg']) ?>
  <button onclick="this.parentElement.remove()" class="flash-close">&times;</button>
</div>
<?php endif; ?>
