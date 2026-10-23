<?php
// admin/includes/admin_header.php
require_once dirname(__DIR__, 2) . '/includes/auth.php';
requireStaff();
$adminUser = currentUser();
$flash = getFlash();
$activePage = $activePage ?? '';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= isset($pageTitle) ? e($pageTitle) . ' | ' : '' ?>SarathiAcademy Admin</title>
<link rel="icon" type="image/png" href="<?= SITE_URL ?>/assets/images/logo.png">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/admin.css">
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <img src="<?= SITE_URL ?>/assets/images/logo.png" alt="Logo">
    <span>SarathiAcademy</span>
  </div>
  <div class="sidebar-user">
    <strong><?= e($adminUser['name']) ?></strong>
    <span class="role-badge"><?= e($adminUser['role']) ?></span>
  </div>
  <nav>
    <div class="sidebar-section">Overview</div>
    <a href="<?= ADMIN_URL ?>/" class="<?= $activePage==='dashboard'?'active':'' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>

    <?php if (isAdmin()): ?>
    <div class="sidebar-section">Admin Only</div>
    <a href="<?= ADMIN_URL ?>/pages/staff.php" class="<?= $activePage==='staff'?'active':'' ?>"><i class="fas fa-users-cog"></i> Manage Staff</a>
    <a href="<?= ADMIN_URL ?>/pages/courses.php" class="<?= $activePage==='courses'?'active':'' ?>"><i class="fas fa-book"></i> Courses</a>
    <a href="<?= ADMIN_URL ?>/pages/batches.php" class="<?= $activePage==='batches'?'active':'' ?>"><i class="fas fa-layer-group"></i> Batches</a>
    <a href="<?= ADMIN_URL ?>/pages/assignments.php" class="<?= $activePage==='assignments'?'active':'' ?>"><i class="fas fa-chalkboard-teacher"></i> Faculty Assign</a>
    <a href="<?= ADMIN_URL ?>/pages/gallery_admin.php" class="<?= $activePage==='gallery'?'active':'' ?>"><i class="fas fa-images"></i> Gallery</a>
    <a href="<?= ADMIN_URL ?>/pages/enquiries.php" class="<?= $activePage==='enquiries'?'active':'' ?>"><i class="fas fa-question-circle"></i> Enquiries</a>
    <?php endif; ?>

    <div class="sidebar-section">Admissions & Fees</div>
    <a href="<?= ADMIN_URL ?>/pages/admissions.php" class="<?= $activePage==='admissions'?'active':'' ?>"><i class="fas fa-file-signature"></i> Admissions</a>
    <a href="<?= ADMIN_URL ?>/pages/fees.php" class="<?= $activePage==='fees'?'active':'' ?>"><i class="fas fa-rupee-sign"></i> Fees</a>

    <div class="sidebar-section">Attendance</div>
    <a href="<?= ADMIN_URL ?>/pages/attendance.php" class="<?= $activePage==='attendance'?'active':'' ?>"><i class="fas fa-calendar-check"></i> Mark Attendance</a>
    <a href="<?= ADMIN_URL ?>/pages/attendance_report.php" class="<?= $activePage==='att_report'?'active':'' ?>"><i class="fas fa-chart-bar"></i> Attendance Report</a>

    <div class="sidebar-section">Reports</div>
    <a href="<?= ADMIN_URL ?>/pages/reports.php" class="<?= $activePage==='reports'?'active':'' ?>"><i class="fas fa-file-alt"></i> All Reports</a>
  </nav>
  <div class="sidebar-footer">
    <a href="<?= SITE_URL ?>/" target="_blank"><i class="fas fa-external-link-alt"></i> View Website</a>
    <a href="<?= SITE_URL ?>/logout.php" style="margin-top:8px;color:#e88;"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>
</aside>

<!-- TOP BAR -->
<div class="admin-topbar">
  <div style="display:flex;align-items:center;gap:12px;">
    <button id="sidebarToggle" style="background:none;border:none;font-size:1.3rem;color:var(--navy);cursor:pointer;display:none;" id="sidebarToggle"><i class="fas fa-bars"></i></button>
    <span class="page-title"><?= $pageTitle ?? 'Dashboard' ?></span>
  </div>
  <div class="topbar-right">
    <span style="font-size:13px;color:var(--gray);"><i class="fas fa-user-circle"></i> <?= e($adminUser['name']) ?></span>
    <a href="<?= SITE_URL ?>/logout.php" style="color:var(--danger);"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>
</div>

<!-- MAIN CONTENT -->
<div class="admin-main">
  <div class="admin-content">
  <?php if ($flash): ?>
  <div class="flash flash-<?= e($flash['type']) ?>">
    <?= e($flash['msg']) ?>
    <button onclick="this.parentElement.remove()" class="flash-close">&times;</button>
  </div>
  <?php endif; ?>
