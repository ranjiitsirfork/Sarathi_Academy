<?php $pageTitle='Faculty Assignment'; $activePage='assignments';
require_once dirname(__DIR__).'/includes/admin_header.php'; requireAdmin();
if($_SERVER['REQUEST_METHOD']==='POST'){
    $batch_id=(int)$_POST['batch_id']; $assigns=$_POST['assigns']??[];
    foreach($assigns as $subj_id=>$fac_id){
        $subj_id=(int)$subj_id; $fac_id=(int)$fac_id;
        if($fac_id) dbExecute('INSERT INTO faculty_assignments(batch_id,subject_id,faculty_id) VALUES(?,?,?) ON DUPLICATE KEY UPDATE faculty_id=?',[$batch_id,$subj_id,$fac_id,$fac_id]);
        else dbExecute('DELETE FROM faculty_assignments WHERE batch_id=? AND subject_id=?',[$batch_id,$subj_id]);
    }
    setFlash('success','Assignments saved.'); redirect(ADMIN_URL.'/pages/assignments.php?batch='.$batch_id);
}
$batches=dbFetchAll('SELECT b.id,b.batch_name,c.name AS course_name,c.id AS course_id FROM batches b JOIN courses c ON c.id=b.course_id WHERE b.is_active=1 ORDER BY c.name,b.batch_name');
$faculty=dbFetchAll("SELECT id,name,designation FROM users WHERE role IN('admin','staff') AND is_active=1 ORDER BY name");
$selBatch=(int)($_GET['batch']??0); $subjects=[]; $assignments=[]; $batchInfo=null;
if($selBatch){
    foreach($batches as $b){if($b['id']===$selBatch){$batchInfo=$b;break;}}
    if($batchInfo){
        $subjects=dbFetchAll('SELECT * FROM subjects WHERE course_id=?',[$batchInfo['course_id']]);
        $rows=dbFetchAll('SELECT subject_id,faculty_id FROM faculty_assignments WHERE batch_id=?',[$selBatch]);
        foreach($rows as $r) $assignments[$r['subject_id']]=$r['faculty_id'];
    }
}
?>
<div class="card mb-3"><div class="card-header"><h3>Select Batch</h3></div>
<form method="GET"><div class="d-flex gap-10 align-center flex-wrap">
  <select name="batch" class="form-control" style="max-width:360px;"><option value="">-- Select Batch --</option>
  <?php foreach($batches as $b): ?><option value="<?=$b['id']?>" <?=$selBatch===$b['id']?'selected':''?>><?=e($b['course_name'])?> – <?=e($b['batch_name'])?></option><?php endforeach; ?>
  </select><button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Load</button>
</div></form></div>
<?php if($selBatch&&$subjects): ?>
<div class="card"><div class="card-header"><h3>Assign Faculty – <?=e($batchInfo['course_name'])?> / <?=e($batchInfo['batch_name'])?></h3></div>
<form method="POST"><input type="hidden" name="batch_id" value="<?=$selBatch?>">
<div class="table-wrap"><table><thead><tr><th>Subject</th><th>Assigned Faculty</th></tr></thead><tbody>
<?php foreach($subjects as $s): ?>
<tr><td class="fw-600"><?=e($s['name'])?></td><td>
  <select name="assigns[<?=$s['id']?>]" class="form-control" style="max-width:300px;"><option value="">-- Unassigned --</option>
  <?php foreach($faculty as $f): ?><option value="<?=$f['id']?>" <?=($assignments[$s['id']]??'')==$f['id']?'selected':''?>><?=e($f['name'])?><?=$f['designation']?' ('.e($f['designation']).')':''?></option><?php endforeach; ?>
  </select></td></tr>
<?php endforeach; ?>
</tbody></table></div>
<div class="mt-3"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Assignments</button></div>
</form></div>
<?php endif; ?>
<div class="card mt-3"><div class="card-header"><h3>Current Assignments</h3></div>
<div class="table-wrap"><table><thead><tr><th>Batch</th><th>Course</th><th>Subject</th><th>Faculty</th></tr></thead><tbody>
<?php $all=dbFetchAll('SELECT b.batch_name,c.name AS course,s.name AS subject,u.name AS faculty,u.designation FROM faculty_assignments fa JOIN batches b ON b.id=fa.batch_id JOIN courses c ON c.id=b.course_id JOIN subjects s ON s.id=fa.subject_id JOIN users u ON u.id=fa.faculty_id ORDER BY c.name,b.batch_name,s.name');
foreach($all as $row): ?>
<tr><td><?=e($row['batch_name'])?></td><td><?=e($row['course'])?></td><td><?=e($row['subject'])?></td><td><?=e($row['faculty'])?> <span class="small text-gray"><?=$row['designation']?'('.e($row['designation']).')':''?></span></td></tr>
<?php endforeach; if(!$all): ?><tr><td colspan="4" class="text-center text-gray">No assignments yet.</td></tr><?php endif; ?>
</tbody></table></div></div>
<?php require_once dirname(__DIR__).'/includes/admin_footer.php'; ?>
