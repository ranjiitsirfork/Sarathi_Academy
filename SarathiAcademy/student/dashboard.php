<?php
require_once dirname(__DIR__).'/includes/auth.php';
requireLogin();
if(isAdmin()||isStaff()) redirect(ADMIN_URL.'/');
$user=currentUser(); $uid=$user['id'];
$admissions=dbFetchAll('SELECT a.*,b.batch_name,b.timing,c.name AS course_name,(SELECT COALESCE(SUM(f.amount),0) FROM fees f WHERE f.admission_id=a.id) AS paid FROM admissions a JOIN batches b ON b.id=a.batch_id JOIN courses c ON c.id=b.course_id WHERE a.student_id=? ORDER BY a.created_at DESC',[$uid]);
$activeAdm=null; foreach($admissions as $a){if($a['status']==='approved'){$activeAdm=$a;break;}}
$attSummary=null; $recentAtt=[];
if($activeAdm){
    $total  =dbCount('SELECT COUNT(DISTINCT attend_date) FROM attendance WHERE batch_id=? AND student_id=?',[$activeAdm['batch_id'],$uid]);
    $present=dbCount('SELECT COUNT(*) FROM attendance WHERE batch_id=? AND student_id=? AND status="present"',[$activeAdm['batch_id'],$uid]);
    $attSummary=['total'=>$total,'present'=>$present,'pct'=>$total>0?round(($present/$total)*100,1):0];
    $recentAtt=dbFetchAll('SELECT a.attend_date,a.status,s.name AS subject FROM attendance a LEFT JOIN subjects s ON s.id=a.subject_id WHERE a.batch_id=? AND a.student_id=? ORDER BY a.attend_date DESC LIMIT 10',[$activeAdm['batch_id'],$uid]);
}
$flash=getFlash();
?><!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>My Dashboard | SarathiAcademy</title>
<link rel="icon" type="image/png" href="<?=SITE_URL?>/assets/images/logo.png">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="<?=SITE_URL?>/assets/css/style.css">
<style>
body{background:#f0f7ff;}
.dash-wrap{max-width:1100px;margin:0 auto;padding:30px 20px;}
.dash-header{background:linear-gradient(135deg,var(--navy-dark),var(--navy));color:#fff;border-radius:14px;padding:28px 30px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;}
.dash-header h2{font-size:1.5rem;}.dash-header p{opacity:.8;font-size:14px;}
.dash-grid{display:grid;grid-template-columns:1fr 1fr;gap:22px;}
.dash-card{background:#fff;border-radius:12px;box-shadow:0 4px 16px rgba(56,182,255,.1);padding:22px;}
.dash-card h4{color:var(--navy);margin-bottom:14px;display:flex;align-items:center;gap:8px;}
.adm-status-box{border-radius:8px;padding:16px;margin-bottom:14px;}
.adm-status-box.pending{background:#fef3c7;border-left:4px solid var(--warning);}
.adm-status-box.approved{background:#c6f6d5;border-left:4px solid var(--success);}
.adm-status-box.rejected{background:#fed7d7;border-left:4px solid var(--danger);}
.att-bar{height:10px;background:#e2e8f0;border-radius:5px;margin:8px 0;overflow:hidden;}
.att-fill{height:100%;border-radius:5px;}
@media(max-width:700px){.dash-grid{grid-template-columns:1fr;}}
</style></head><body>
<nav style="background:#fff;padding:12px 20px;box-shadow:0 2px 10px rgba(56,182,255,.1);display:flex;align-items:center;justify-content:space-between;">
  <a href="<?=SITE_URL?>/" style="display:flex;align-items:center;gap:10px;"><img src="<?=SITE_URL?>/assets/images/logo.png" alt="Logo" style="height:44px;"><span style="font-family:'Playfair Display',serif;font-weight:700;color:var(--navy);font-size:1.1rem;">SarathiAcademy</span></a>
  <div style="display:flex;gap:12px;align-items:center;">
    <a href="<?=SITE_URL?>/student/apply.php" class="btn btn-primary" style="font-size:13px;padding:8px 16px;border-radius:20px;"><i class="fas fa-plus"></i> Apply</a>
    <a href="<?=SITE_URL?>/logout.php" style="color:var(--danger);font-size:13px;"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>
</nav>
<?php if($flash): ?><div class="flash flash-<?=e($flash['type'])?>" style="margin:16px auto;max-width:1100px;"><?=e($flash['msg'])?><button onclick="this.parentElement.remove()" class="flash-close">&times;</button></div><?php endif; ?>
<div class="dash-wrap">
  <div class="dash-header">
    <div><h2>Welcome, <?=e($user['name'])?>! 👋</h2><p><i class="fas fa-envelope"></i> <?=e($user['email'])?> &nbsp;|&nbsp; <i class="fas fa-phone"></i> <?=e($user['phone']?:'Not set')?></p></div>
    <a href="<?=SITE_URL?>/student/apply.php" class="btn" style="background:#fff;color:var(--navy);font-weight:700;padding:10px 22px;border-radius:20px;"><i class="fas fa-file-signature"></i> Apply for Admission</a>
  </div>
  <div class="dash-grid">
    <div class="dash-card">
      <h4><i class="fas fa-file-signature text-sky"></i> My Admissions</h4>
      <?php if($admissions): foreach($admissions as $a): ?>
      <div class="adm-status-box <?=$a['status']?>">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;">
          <div><strong><?=e($a['course_name'])?></strong><br><span class="small"><?=e($a['batch_name'])?></span><br><span class="small text-gray">Timing: <?=e($a['timing']?:'—')?></span><?php if($a['roll_no']): ?><br><span class="small fw-600">Roll No: <?=e($a['roll_no'])?></span><?php endif; ?></div>
          <span class="badge badge-<?=$a['status']?>" style="margin-top:4px;"><?=ucfirst($a['status'])?></span>
        </div>
        <?php if($a['status']==='approved'): ?><div style="margin-top:10px;padding-top:10px;border-top:1px solid rgba(0,0,0,.08);"><span class="small fw-600">Fees Paid: </span><span class="small text-success fw-600">₹<?=number_format($a['paid'],0)?></span></div><?php endif; ?>
        <?php if($a['status']==='rejected'&&$a['notes']): ?><p class="small mt-1" style="color:#9b2c2c;"><i class="fas fa-info-circle"></i> <?=e($a['notes'])?></p><?php endif; ?>
      </div>
      <?php endforeach; else: ?>
      <div style="text-align:center;padding:30px 0;color:var(--gray);"><i class="fas fa-file-signature" style="font-size:2.5rem;opacity:.3;display:block;margin-bottom:12px;"></i><p>No admissions yet.</p><a href="<?=SITE_URL?>/student/apply.php" class="btn btn-primary mt-2" style="font-size:13px;padding:8px 18px;border-radius:20px;">Apply Now</a></div>
      <?php endif; ?>
    </div>
    <div class="dash-card">
      <h4><i class="fas fa-calendar-check text-sky"></i> My Attendance</h4>
      <?php if($attSummary): ?>
      <div style="margin-bottom:18px;">
        <div style="display:flex;justify-content:space-between;align-items:center;"><span class="small fw-600">Overall Attendance</span><span class="fw-600" style="font-size:1.2rem;color:<?=$attSummary['pct']>=75?'var(--success)':($attSummary['pct']>=50?'var(--warning)':'var(--danger)')?>;"><?=$attSummary['pct']?>%</span></div>
        <div class="att-bar"><div class="att-fill" style="width:<?=$attSummary['pct']?>%;background:<?=$attSummary['pct']>=75?'var(--success)':($attSummary['pct']>=50?'var(--warning)':'var(--danger)')?>;"></div></div>
        <div style="display:flex;gap:16px;margin-top:8px;"><span class="small"><span style="color:var(--success);">●</span> Present: <?=$attSummary['present']?></span><span class="small"><span style="color:var(--danger);">●</span> Absent: <?=$attSummary['total']-$attSummary['present']?></span><span class="small">Total: <?=$attSummary['total']?> classes</span></div>
      </div>
      <?php if($recentAtt): ?>
      <h5 class="small fw-600 mb-1" style="color:var(--navy);">Recent Records</h5>
      <div style="max-height:200px;overflow-y:auto;">
      <?php foreach($recentAtt as $r): ?>
      <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #f0f0f0;font-size:13px;"><span><?=date('d M',strtotime($r['attend_date']))?><?=$r['subject']?' – '.$r['subject']:''?></span><span class="badge badge-<?=$r['status']?>"><?=ucfirst($r['status'])?></span></div>
      <?php endforeach; ?></div><?php endif; ?>
      <?php else: ?><div style="text-align:center;padding:30px 0;color:var(--gray);"><i class="fas fa-calendar" style="font-size:2.5rem;opacity:.3;display:block;margin-bottom:12px;"></i><p>Attendance will appear after admission approval.</p></div><?php endif; ?>
    </div>
  </div>
  <div style="margin-top:22px;display:flex;gap:14px;flex-wrap:wrap;">
    <a href="<?=SITE_URL?>/courses.php" class="btn btn-primary" style="font-size:13px;"><i class="fas fa-book"></i> View Courses</a>
    <a href="<?=SITE_URL?>/enquiry.php" class="btn btn-secondary" style="font-size:13px;"><i class="fas fa-question-circle"></i> Enquiry</a>
    <a href="<?=SITE_URL?>/" class="btn btn-secondary" style="font-size:13px;"><i class="fas fa-home"></i> Home</a>
  </div>
</div>
<script src="<?=SITE_URL?>/assets/js/main.js"></script>
</body></html>
