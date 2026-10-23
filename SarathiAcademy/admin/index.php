<?php
$pageTitle='Dashboard'; $activePage='dashboard';
require_once __DIR__.'/includes/admin_header.php';
$totalStudents = dbCount("SELECT COUNT(*) FROM users WHERE role='student'");
$totalStaff    = dbCount("SELECT COUNT(*) FROM users WHERE role IN('admin','staff')");
$totalBatches  = dbCount("SELECT COUNT(*) FROM batches WHERE is_active=1");
$totalCourses  = dbCount("SELECT COUNT(*) FROM courses WHERE is_active=1");
$pendingAdm    = dbCount("SELECT COUNT(*) FROM admissions WHERE status='pending'");
$totalFees     = dbFetch("SELECT COALESCE(SUM(amount),0) AS t FROM fees")['t'];
$enquiriesNew  = dbCount("SELECT COUNT(*) FROM enquiries WHERE status='new'");
$todayAtt      = dbCount("SELECT COUNT(*) FROM attendance WHERE attend_date=CURDATE()");
$recentAdm     = dbFetchAll("SELECT a.*,u.name AS student_name,b.batch_name,c.name AS course_name FROM admissions a JOIN users u ON u.id=a.student_id JOIN batches b ON b.id=a.batch_id JOIN courses c ON c.id=b.course_id ORDER BY a.created_at DESC LIMIT 8");
$recentFees    = dbFetchAll("SELECT f.*,u.name AS student_name FROM fees f JOIN admissions a ON a.id=f.admission_id JOIN users u ON u.id=a.student_id ORDER BY f.created_at DESC LIMIT 6");
?>
<div class="stats-row">
  <div class="stat-card">        <div class="stat-icon sky">  <i class="fas fa-user-graduate"></i></div><div class="stat-info"><div class="num"><?=$totalStudents?></div><div class="lbl">Total Students</div></div></div>
  <div class="stat-card gold">   <div class="stat-icon gold"> <i class="fas fa-users-cog"></i></div>    <div class="stat-info"><div class="num"><?=$totalStaff?></div>   <div class="lbl">Staff & Faculty</div></div></div>
  <div class="stat-card green">  <div class="stat-icon green"><i class="fas fa-layer-group"></i></div>  <div class="stat-info"><div class="num"><?=$totalBatches?></div> <div class="lbl">Active Batches</div></div></div>
  <div class="stat-card">        <div class="stat-icon sky">  <i class="fas fa-book"></i></div>          <div class="stat-info"><div class="num"><?=$totalCourses?></div> <div class="lbl">Courses</div></div></div>
  <div class="stat-card red">    <div class="stat-icon red">  <i class="fas fa-file-signature"></i></div><div class="stat-info"><div class="num"><?=$pendingAdm?></div>  <div class="lbl">Pending Admissions</div></div></div>
  <div class="stat-card green">  <div class="stat-icon green"><i class="fas fa-rupee-sign"></i></div>   <div class="stat-info"><div class="num">₹<?=number_format($totalFees,0)?></div><div class="lbl">Fees Collected</div></div></div>
  <div class="stat-card gold">   <div class="stat-icon gold"> <i class="fas fa-question-circle"></i></div><div class="stat-info"><div class="num"><?=$enquiriesNew?></div><div class="lbl">New Enquiries</div></div></div>
  <div class="stat-card">        <div class="stat-icon sky">  <i class="fas fa-calendar-check"></i></div><div class="stat-info"><div class="num"><?=$todayAtt?></div>    <div class="lbl">Today's Attendance</div></div></div>
</div>
<div class="card mb-3">
  <div class="card-header"><h3>Quick Actions</h3></div>
  <div style="display:flex;gap:10px;flex-wrap:wrap;">
    <a href="<?=ADMIN_URL?>/pages/admissions.php"       class="btn btn-primary"><i class="fas fa-file-signature"></i> Admissions</a>
    <a href="<?=ADMIN_URL?>/pages/attendance.php"       class="btn btn-success"><i class="fas fa-calendar-check"></i> Attendance</a>
    <a href="<?=ADMIN_URL?>/pages/fees.php"             class="btn btn-gold">   <i class="fas fa-rupee-sign"></i> Fees</a>
    <a href="<?=ADMIN_URL?>/pages/reports.php"          class="btn btn-secondary"><i class="fas fa-file-alt"></i> Reports</a>
    <?php if(isAdmin()): ?>
    <a href="<?=ADMIN_URL?>/pages/batches.php"          class="btn btn-outline"><i class="fas fa-layer-group"></i> Batches</a>
    <a href="<?=ADMIN_URL?>/pages/staff.php"            class="btn btn-outline"><i class="fas fa-users-cog"></i> Staff</a>
    <?php endif; ?>
  </div>
</div>
<div style="display:grid;grid-template-columns:1.5fr 1fr;gap:20px;">
<div class="card">
  <div class="card-header"><h3>Recent Admissions</h3><a href="<?=ADMIN_URL?>/pages/admissions.php" class="btn btn-sm btn-outline">View All</a></div>
  <div class="table-wrap"><table>
    <thead><tr><th>Student</th><th>Course / Batch</th><th>Status</th><th>Date</th></tr></thead>
    <tbody>
    <?php foreach($recentAdm as $a): ?>
    <tr><td><?=e($a['student_name'])?></td><td><?=e($a['course_name'])?><br><span class="small text-gray"><?=e($a['batch_name'])?></span></td>
    <td><span class="badge badge-<?=$a['status']?>"><?=ucfirst($a['status'])?></span></td>
    <td class="small"><?=date('d M Y',strtotime($a['created_at']))?></td></tr>
    <?php endforeach; ?>
    <?php if(!$recentAdm): ?><tr><td colspan="4" class="text-center text-gray">No admissions yet.</td></tr><?php endif; ?>
    </tbody>
  </table></div>
</div>
<div class="card">
  <div class="card-header"><h3>Recent Fees</h3><a href="<?=ADMIN_URL?>/pages/fees.php" class="btn btn-sm btn-outline">View All</a></div>
  <div class="table-wrap"><table>
    <thead><tr><th>Student</th><th>Amount</th><th>Date</th></tr></thead>
    <tbody>
    <?php foreach($recentFees as $f): ?>
    <tr><td><?=e($f['student_name'])?></td><td>₹<?=number_format($f['amount'],0)?></td><td class="small"><?=date('d M Y',strtotime($f['paid_date']))?></td></tr>
    <?php endforeach; ?>
    <?php if(!$recentFees): ?><tr><td colspan="3" class="text-center text-gray">No fees yet.</td></tr><?php endif; ?>
    </tbody>
  </table></div>
</div>
</div>
<?php require_once __DIR__.'/includes/admin_footer.php'; ?>
