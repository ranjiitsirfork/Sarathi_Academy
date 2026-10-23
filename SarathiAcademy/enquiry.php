<?php $pageTitle='Enquiry'; require_once 'includes/header.php';
$courses=dbFetchAll('SELECT code,name FROM courses WHERE is_active=1');
$selectedCourse=e($_GET['course']??''); $success=''; $errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
    $name=trim($_POST['name']??''); $email=trim($_POST['email']??''); $phone=trim($_POST['phone']??''); $course=trim($_POST['course']??''); $message=trim($_POST['message']??'');
    if(!$name) $errors[]='Name is required.'; if(!$phone) $errors[]='Phone is required.';
    if(!$errors){ dbInsert('INSERT INTO enquiries(name,email,phone,course,message) VALUES(?,?,?,?,?)',[$name,$email,$phone,$course,$message]); $success='Thank you! Your enquiry has been submitted. We will contact you shortly.'; }
}
?>
<div class="page-banner"><div class="container"><h1>Enquiry</h1><p>Have questions? We'd love to hear from you!</p></div></div>
<section class="section"><div class="container"><div class="enquiry-wrap">
<div><div class="card"><h3 class="mb-3" style="color:var(--navy);">Send an Enquiry</h3>
<?php if($success): ?><div class="flash flash-success"><?=e($success)?></div><?php endif; ?>
<?php if($errors):  ?><div class="flash flash-error"><?=implode('<br>',array_map('e',$errors))?></div><?php endif; ?>
<form method="POST">
  <div class="form-row">
    <div class="form-group"><label>Full Name *</label><input type="text" name="name" class="form-control" required value="<?=e($_POST['name']??'')?>"></div>
    <div class="form-group"><label>Phone *</label><input type="text" name="phone" class="form-control" required value="<?=e($_POST['phone']??'')?>"></div>
  </div>
  <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" value="<?=e($_POST['email']??'')?>"></div>
  <div class="form-group"><label>Course Interested In</label>
    <select name="course" class="form-control"><option value="">-- Select Course --</option>
    <?php foreach($courses as $c): ?><option value="<?=e($c['code'])?>" <?=($selectedCourse===$c['code']||($_POST['course']??'')===$c['code'])?'selected':''?>><?=e($c['name'])?></option><?php endforeach; ?>
    </select></div>
  <div class="form-group"><label>Message</label><textarea name="message" class="form-control" rows="4"><?=e($_POST['message']??'')?></textarea></div>
  <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Submit Enquiry</button>
</form></div></div>
<div><div class="card" style="background:linear-gradient(135deg,var(--navy-dark),var(--navy));color:#fff;">
  <h3 style="color:var(--sky);margin-bottom:22px;">Reach Us Directly</h3>
  <div class="contact-detail"><i class="fas fa-map-marker-alt" style="color:var(--sky);"></i><div><h5 style="color:var(--sky-mid);font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Address</h5><p>Vidyanagar, Karad, Maharashtra – 415124</p></div></div>
  <div class="contact-detail"><i class="fas fa-phone-alt" style="color:var(--sky);"></i><div><h5 style="color:var(--sky-mid);font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Phone</h5><p>9139959698 / 9172135355</p></div></div>
  <div class="contact-detail"><i class="fas fa-envelope" style="color:var(--sky);"></i><div><h5 style="color:var(--sky-mid);font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Email</h5><p>admin@SarathiAcademy.com</p></div></div>
</div></div>
</div></div></section>
<?php require_once 'includes/footer.php'; ?>
