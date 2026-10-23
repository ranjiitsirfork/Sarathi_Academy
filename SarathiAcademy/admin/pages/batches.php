<?php $pageTitle='Manage Batches'; $activePage='batches';
require_once dirname(__DIR__).'/includes/admin_header.php'; requireAdmin();
if($_SERVER['REQUEST_METHOD']==='POST'){
    $action=$_POST['action']??'';
    if(in_array($action,['add','edit'])){
        $id=(int)($_POST['id']??0); $course=(int)$_POST['course_id']; $bname=trim($_POST['batch_name']??'');
        $start=$_POST['start_date']??null; $end=$_POST['end_date']??null; $timing=trim($_POST['timing']??''); $cap=(int)($_POST['capacity']??40);
        if(!$bname||!$course){setFlash('error','Batch name and course required.');redirect(ADMIN_URL.'/pages/batches.php');}
        if($action==='add') dbInsert('INSERT INTO batches(course_id,batch_name,start_date,end_date,timing,capacity) VALUES(?,?,?,?,?,?)',[$course,$bname,$start,$end,$timing,$cap]);
        else dbExecute('UPDATE batches SET course_id=?,batch_name=?,start_date=?,end_date=?,timing=?,capacity=? WHERE id=?',[$course,$bname,$start,$end,$timing,$cap,$id]);
        setFlash('success','Batch saved.');
    } elseif($_POST['action']==='toggle'){ dbExecute('UPDATE batches SET is_active=1-is_active WHERE id=?',[(int)$_POST['id']]); setFlash('success','Status changed.'); }
    redirect(ADMIN_URL.'/pages/batches.php');
}
$courses=dbFetchAll('SELECT id,name FROM courses WHERE is_active=1');
$batches=dbFetchAll('SELECT b.*,c.name AS course_name,(SELECT COUNT(*) FROM admissions a WHERE a.batch_id=b.id AND a.status="approved") AS enrolled FROM batches b JOIN courses c ON c.id=b.course_id ORDER BY b.id DESC');
$edit=null; if(isset($_GET['edit'])){foreach($batches as $b){if($b['id']==(int)$_GET['edit']){$edit=$b;break;}}}
?>
<div class="card mb-3">
  <div class="card-header"><h3><?=$edit?'Edit Batch':'Add Batch'?></h3><?php if($edit): ?><a href="?" class="btn btn-secondary btn-sm">Cancel</a><?php endif; ?></div>
  <form method="POST"><input type="hidden" name="action" value="<?=$edit?'edit':'add'?>"><?php if($edit): ?><input type="hidden" name="id" value="<?=$edit['id']?>"><?php endif; ?>
    <div class="form-row">
      <div class="form-group"><label>Course *</label><select name="course_id" class="form-control" required><?php foreach($courses as $c): ?><option value="<?=$c['id']?>" <?=($edit['course_id']??'')==$c['id']?'selected':''?>><?=e($c['name'])?></option><?php endforeach; ?></select></div>
      <div class="form-group"><label>Batch Name *</label><input type="text" name="batch_name" class="form-control" value="<?=e($edit['batch_name']??'')?>" placeholder="e.g. 11th Batch A – 2025-26" required></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Start Date</label><input type="date" name="start_date" class="form-control" value="<?=e($edit['start_date']??'')?>"></div>
      <div class="form-group"><label>End Date</label><input type="date" name="end_date" class="form-control" value="<?=e($edit['end_date']??'')?>"></div>
      <div class="form-group"><label>Timing</label><input type="text" name="timing" class="form-control" placeholder="07:00 AM – 09:00 AM" value="<?=e($edit['timing']??'')?>"></div>
      <div class="form-group"><label>Capacity</label><input type="number" name="capacity" class="form-control" value="<?=e($edit['capacity']??40)?>"></div>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?=$edit?'Update':'Add Batch'?></button>
  </form>
</div>
<div class="card"><div class="card-header"><h3>All Batches</h3></div>
<div class="table-wrap"><table><thead><tr><th>#</th><th>Batch Name</th><th>Course</th><th>Start</th><th>End</th><th>Timing</th><th>Enrolled</th><th>Status</th><th>Actions</th></tr></thead>
<tbody><?php foreach($batches as $i=>$b): ?>
<tr><td><?=$i+1?></td><td class="fw-600"><?=e($b['batch_name'])?></td><td><?=e($b['course_name'])?></td>
<td><?=$b['start_date']?date('d M Y',strtotime($b['start_date'])):'—'?></td>
<td><?=$b['end_date']?date('d M Y',strtotime($b['end_date'])):'—'?></td>
<td><?=e($b['timing'])?></td><td><?=$b['enrolled']?>/<?=$b['capacity']?></td>
<td><span class="badge badge-<?=$b['is_active']?'active':'inactive'?>"><?=$b['is_active']?'Active':'Inactive'?></span></td>
<td><div class="actions"><a href="?edit=<?=$b['id']?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
<form method="POST" style="display:inline;"><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?=$b['id']?>"><button class="btn btn-sm <?=$b['is_active']?'btn-danger':'btn-success'?>" data-confirm="Toggle?"><i class="fas fa-<?=$b['is_active']?'eye-slash':'eye'?>"></i></button></form></div></td></tr>
<?php endforeach; ?></tbody></table></div></div>
<?php require_once dirname(__DIR__).'/includes/admin_footer.php'; ?>
