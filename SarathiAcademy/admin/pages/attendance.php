<?php $pageTitle='Mark Attendance'; $activePage='attendance';
require_once dirname(__DIR__).'/includes/admin_header.php';
if($_SERVER['REQUEST_METHOD']==='POST'&&isset($_POST['batch_id'],$_POST['attend_date'])){
    $batch_id=(int)$_POST['batch_id']; $attend_date=$_POST['attend_date']; $subject_id=(int)($_POST['subject_id']??0)?:null; $attendance=$_POST['attendance']??[];
    foreach($attendance as $sid=>$status){ $sid=(int)$sid; $status=in_array($status,['present','absent','late'])?$status:'present';
    dbExecute('INSERT INTO attendance(batch_id,student_id,subject_id,attend_date,status,marked_by) VALUES(?,?,?,?,?,?) ON DUPLICATE KEY UPDATE status=?,marked_by=?',[$batch_id,$sid,$subject_id,$attend_date,$status,$adminUser['id'],$status,$adminUser['id']]); }
    setFlash('success','Attendance saved for '.count($attendance).' students.'); redirect(ADMIN_URL.'/pages/attendance.php?batch='.$batch_id.'&date='.$attend_date.'&subject='.($subject_id??''));
}
$batches=dbFetchAll('SELECT b.id,b.batch_name,c.name AS course_name,b.course_id FROM batches b JOIN courses c ON c.id=b.course_id WHERE b.is_active=1 ORDER BY c.name,b.batch_name');
$selBatch=(int)($_GET['batch']??0); $selDate=$_GET['date']??date('Y-m-d'); $selSubject=(int)($_GET['subject']??0)?:null;
$students=[]; $subjects=[]; $existingAtt=[]; $batchInfo=null;
if($selBatch){
    foreach($batches as $b){if($b['id']===$selBatch){$batchInfo=$b;break;}}
    if($batchInfo){
        $students=dbFetchAll('SELECT u.id,u.name,u.phone,a.roll_no FROM admissions a JOIN users u ON u.id=a.student_id WHERE a.batch_id=? AND a.status="approved" ORDER BY u.name',[$selBatch]);
        $subjects=dbFetchAll('SELECT * FROM subjects WHERE course_id=?',[$batchInfo['course_id']]);
        $rows=dbFetchAll('SELECT student_id,status FROM attendance WHERE batch_id=? AND attend_date=? AND (subject_id=? OR (subject_id IS NULL AND ? IS NULL))',[$selBatch,$selDate,$selSubject,$selSubject]);
        foreach($rows as $r) $existingAtt[$r['student_id']]=$r['status'];
    }
}
?>
<div class="card mb-3"><div class="card-header"><h3>Select Batch & Date</h3></div>
<form method="GET"><div class="form-row">
  <div class="form-group"><label>Batch *</label><select name="batch" class="form-control" required><option value="">-- Select --</option><?php foreach($batches as $b): ?><option value="<?=$b['id']?>" <?=$selBatch===$b['id']?'selected':''?>><?=e($b['course_name'])?> – <?=e($b['batch_name'])?></option><?php endforeach; ?></select></div>
  <div class="form-group"><label>Date *</label><input type="date" name="date" class="form-control" value="<?=e($selDate)?>" required></div>
  <div class="form-group"><label>Subject (Optional)</label><select name="subject" class="form-control"><option value="">-- General --</option><?php foreach($subjects as $s): ?><option value="<?=$s['id']?>" <?=$selSubject===$s['id']?'selected':''?>><?=e($s['name'])?></option><?php endforeach; ?></select></div>
  <div class="form-group" style="align-self:flex-end;"><button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Load</button></div>
</div></form></div>
<?php if($selBatch&&$students): ?>
<div class="card">
  <div class="card-header"><h3>Attendance – <?=e($batchInfo['course_name'])?> / <?=e($batchInfo['batch_name'])?></h3><span class="small text-gray"><?=date('d M Y',strtotime($selDate))?> | <?=count($students)?> Students</span></div>
  <form method="POST"><input type="hidden" name="batch_id" value="<?=$selBatch?>"><input type="hidden" name="attend_date" value="<?=e($selDate)?>"><input type="hidden" name="subject_id" value="<?=$selSubject??''?>">
  <div class="d-flex gap-10 mb-3" style="padding:0 4px;">
    <button type="button" class="btn btn-success btn-sm" onclick="markAll('present')"><i class="fas fa-check-circle"></i> All Present</button>
    <button type="button" class="btn btn-danger btn-sm"  onclick="markAll('absent')"><i class="fas fa-times-circle"></i> All Absent</button>
  </div>
  <div class="att-grid">
  <?php foreach($students as $s): $cur=$existingAtt[$s['id']]??'present'; ?>
  <div class="att-student"><label><?=e($s['name'])?><?=$s['roll_no']?' <span class="small text-gray">['.e($s['roll_no']).']</span>':''?></label>
  <div class="att-radio">
    <label><input type="radio" name="attendance[<?=$s['id']?>]" value="present" class="att-r" <?=$cur==='present'?'checked':''?>> <span style="color:var(--success);">P</span></label>
    <label><input type="radio" name="attendance[<?=$s['id']?>]" value="absent"  class="att-r" <?=$cur==='absent'?'checked':''?>> <span style="color:var(--danger);">A</span></label>
    <label><input type="radio" name="attendance[<?=$s['id']?>]" value="late"    class="att-r" <?=$cur==='late'?'checked':''?>> <span style="color:var(--warning);">L</span></label>
  </div></div>
  <?php endforeach; ?>
  </div>
  <div class="mt-3"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Attendance</button></div>
  </form>
</div>
<?php elseif($selBatch): ?><div class="card"><p class="text-center text-gray" style="padding:30px;">No approved students in this batch.</p></div><?php endif; ?>
<script>function markAll(s){document.querySelectorAll('.att-r[value="'+s+'"]').forEach(r=>r.checked=true);}</script>
<?php require_once dirname(__DIR__).'/includes/admin_footer.php'; ?>
