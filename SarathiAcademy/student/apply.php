<?php
require_once dirname(__DIR__).'/includes/auth.php';
requireLogin();
if(isAdmin()||isStaff()) redirect(ADMIN_URL.'/');
$uid=currentUser()['id']; $user=currentUser(); $errors=[];
$existing=dbFetch('SELECT id,status FROM admissions WHERE student_id=? AND status IN("pending","approved") LIMIT 1',[$uid]);
if($_SERVER['REQUEST_METHOD']==='POST'&&!$existing){
    $batch_id=(int)($_POST['batch_id']??0); $parent_name=trim($_POST['parent_name']??''); $parent_phone=trim($_POST['parent_phone']??'');
    $address=trim($_POST['address']??''); $dob=$_POST['dob']??''; $prev_school=trim($_POST['prev_school']??''); $prev_pct=(float)($_POST['prev_percentage']??0);
    if(!$batch_id)     $errors[]='Please select a batch.';
    if(!$parent_name)  $errors[]='Parent/Guardian name is required.';
    if(!$parent_phone) $errors[]='Parent phone is required.';
    if(!$dob)          $errors[]='Date of birth is required.';
    if(!$errors){ dbInsert('INSERT INTO admissions(student_id,batch_id,parent_name,parent_phone,address,dob,prev_school,prev_percentage) VALUES(?,?,?,?,?,?,?,?)',[$uid,$batch_id,$parent_name,$parent_phone,$address,$dob,$prev_school,$prev_pct]); setFlash('success','Application submitted! Our team will review it shortly.'); redirect(SITE_URL.'/student/dashboard.php'); }
}
$batches=dbFetchAll('SELECT b.id,b.batch_name,b.timing,b.start_date,b.capacity,c.name AS course_name,c.fees,(SELECT COUNT(*) FROM admissions a WHERE a.batch_id=b.id AND a.status="approved") AS enrolled FROM batches b JOIN courses c ON c.id=b.course_id WHERE b.is_active=1 ORDER BY c.name,b.batch_name');
?><!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Apply for Admission | SarathiAcademy</title>
<link rel="icon" type="image/png" href="<?=SITE_URL?>/assets/images/logo.png">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="<?=SITE_URL?>/assets/css/style.css">
<style>body{background:#f0f7ff;}</style></head><body>
<nav style="background:#fff;padding:12px 20px;box-shadow:0 2px 10px rgba(56,182,255,.1);display:flex;align-items:center;justify-content:space-between;">
  <a href="<?=SITE_URL?>/" style="display:flex;align-items:center;gap:10px;"><img src="<?=SITE_URL?>/assets/images/logo.png" alt="Logo" style="height:44px;"><span style="font-family:'Playfair Display',serif;font-weight:700;color:var(--navy);font-size:1.1rem;">SarathiAcademy</span></a>
  <div style="display:flex;gap:12px;"><a href="<?=SITE_URL?>/student/dashboard.php" style="font-size:13px;color:var(--navy);"><i class="fas fa-arrow-left"></i> Dashboard</a><a href="<?=SITE_URL?>/logout.php" style="color:var(--danger);font-size:13px;"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
</nav>
<div style="max-width:780px;margin:30px auto;padding:0 20px;">
<?php if($existing): ?>
<div class="card text-center" style="padding:50px;">
  <i class="fas fa-<?=$existing['status']==='approved'?'check-circle':'clock'?>" style="font-size:3rem;color:var(--<?=$existing['status']==='approved'?'success':'warning'?>);margin-bottom:16px;display:block;"></i>
  <h3 style="color:var(--navy);">You already have a <?=ucfirst($existing['status'])?> Admission</h3>
  <p class="text-gray mt-2">You cannot submit another application while one is active.</p>
  <a href="<?=SITE_URL?>/student/dashboard.php" class="btn btn-primary mt-3" style="border-radius:20px;">Go to Dashboard</a>
</div>
<?php else: ?>
<div class="card">
  <div style="text-align:center;margin-bottom:28px;"><img src="<?=SITE_URL?>/assets/images/logo.png" alt="Logo" style="height:60px;margin:auto;"><h2 style="color:var(--navy);margin-top:14px;">Admission Application</h2><p class="text-gray" style="font-size:14px;">Fill the form below. Our team will review and approve your application.</p></div>
  <?php if($errors): ?><div class="flash flash-error"><?=implode('<br>',array_map('e',$errors))?></div><?php endif; ?>
  <form method="POST">
    <div style="background:var(--sky-light);border-radius:8px;padding:14px;margin-bottom:20px;font-size:14px;"><strong>Applicant:</strong> <?=e($user['name'])?> | <?=e($user['email'])?> | <?=e($user['phone']?:'Phone not set')?></div>
    <div class="form-group"><label>Select Batch *</label>
      <select name="batch_id" class="form-control" required><option value="">-- Select Course & Batch --</option>
      <?php foreach($batches as $b): $seats=$b['capacity']-$b['enrolled']; ?>
      <option value="<?=$b['id']?>" <?=($_POST['batch_id']??'')==$b['id']?'selected':''?> <?=$seats<=0?'disabled':''?>><?=e($b['course_name'])?> – <?=e($b['batch_name'])?><?=$b['timing']?' | '.$b['timing']:''?> | ₹<?=number_format($b['fees'],0)?> <?=$seats<=0?' [FULL]':' ['.$seats.' seats]'?></option>
      <?php endforeach; ?>
      </select>
    </div>
    <h4 style="color:var(--navy);margin:20px 0 14px;font-size:1rem;"><i class="fas fa-user-friends text-sky"></i> Parent / Guardian Details</h4>
    <div class="form-row">
      <div class="form-group"><label>Parent/Guardian Name *</label><input type="text" name="parent_name" class="form-control" value="<?=e($_POST['parent_name']??'')?>" required></div>
      <div class="form-group"><label>Parent Phone *</label><input type="text" name="parent_phone" class="form-control" value="<?=e($_POST['parent_phone']??'')?>" required></div>
    </div>
    <h4 style="color:var(--navy);margin:20px 0 14px;font-size:1rem;"><i class="fas fa-info-circle text-sky"></i> Personal Details</h4>
    <div class="form-row">
      <div class="form-group"><label>Date of Birth *</label><input type="date" name="dob" class="form-control" value="<?=e($_POST['dob']??'')?>" required></div>
      <div class="form-group"><label>Previous School/College</label><input type="text" name="prev_school" class="form-control" value="<?=e($_POST['prev_school']??'')?>"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Previous Year %</label><input type="number" name="prev_percentage" class="form-control" step="0.01" min="0" max="100" value="<?=e($_POST['prev_percentage']??'')?>"></div>
      <div class="form-group"><label>Residential Address</label><textarea name="address" class="form-control" rows="2"><?=e($_POST['address']??'')?></textarea></div>
    </div>
    <div style="margin-top:22px;display:flex;gap:12px;flex-wrap:wrap;">
      <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Submit Application</button>
      <a href="<?=SITE_URL?>/student/dashboard.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
<?php endif; ?>
</div>
<script src="<?=SITE_URL?>/assets/js/main.js"></script>
</body></html>
