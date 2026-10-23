<?php $pageTitle='Manage Courses'; $activePage='courses';
require_once dirname(__DIR__).'/includes/admin_header.php'; requireAdmin();
if($_SERVER['REQUEST_METHOD']==='POST'){
    $action=$_POST['action']??'';
    if(in_array($action,['add','edit'])){
        $id=(int)($_POST['id']??0); $name=trim($_POST['name']??''); $code=strtoupper(trim($_POST['code']??''));
        $desc=trim($_POST['description']??''); $dur=trim($_POST['duration']??''); $fees=(float)($_POST['fees']??0);
        $coord=(int)($_POST['coordinator_id']??0)?:null;
        if(!$name||!$code){setFlash('error','Name and code required.');redirect(ADMIN_URL.'/pages/courses.php');}
        if($action==='add'){ $exists=dbFetch('SELECT id FROM courses WHERE code=?',[$code]); if($exists){setFlash('error','Code exists.');}else{dbInsert('INSERT INTO courses(name,code,description,duration,fees,coordinator_id) VALUES(?,?,?,?,?,?)',[$name,$code,$desc,$dur,$fees,$coord]);setFlash('success','Course added.');}}
        else{dbExecute('UPDATE courses SET name=?,code=?,description=?,duration=?,fees=?,coordinator_id=? WHERE id=?',[$name,$code,$desc,$dur,$fees,$coord,$id]);setFlash('success','Course updated.');}
    } elseif($_POST['action']==='toggle'){dbExecute('UPDATE courses SET is_active=1-is_active WHERE id=?',[(int)$_POST['id']]);setFlash('success','Status changed.');}
    redirect(ADMIN_URL.'/pages/courses.php');
}
$courses=dbFetchAll('SELECT c.*,u.name AS coord_name FROM courses c LEFT JOIN users u ON u.id=c.coordinator_id ORDER BY c.id');
$faculty=dbFetchAll("SELECT id,name,designation FROM users WHERE role IN('admin','staff') AND is_active=1 ORDER BY name");
$edit=null; if(isset($_GET['edit'])){foreach($courses as $c){if($c['id']==(int)$_GET['edit']){$edit=$c;break;}}}
?>
<div class="card mb-3">
  <div class="card-header"><h3><?=$edit?'Edit Course':'Add Course'?></h3><?php if($edit): ?><a href="?" class="btn btn-secondary btn-sm">Cancel</a><?php endif; ?></div>
  <form method="POST"><input type="hidden" name="action" value="<?=$edit?'edit':'add'?>"><?php if($edit): ?><input type="hidden" name="id" value="<?=$edit['id']?>"><?php endif; ?>
    <div class="form-row">
      <div class="form-group"><label>Course Name *</label><input type="text" name="name" class="form-control" value="<?=e($edit['name']??'')?>" required></div>
      <div class="form-group"><label>Code * (e.g. JEE)</label><input type="text" name="code" class="form-control" value="<?=e($edit['code']??'')?>" required></div>
    </div>
    <div class="form-group"><label>Description</label><textarea name="description" class="form-control" rows="2"><?=e($edit['description']??'')?></textarea></div>
    <div class="form-row">
      <div class="form-group"><label>Duration</label><input type="text" name="duration" class="form-control" placeholder="e.g. 1 Year" value="<?=e($edit['duration']??'')?>"></div>
      <div class="form-group"><label>Annual Fees (₹)</label><input type="number" name="fees" class="form-control" step="0.01" value="<?=e($edit['fees']??'')?>"></div>
      <div class="form-group"><label>Coordinator</label><select name="coordinator_id" class="form-control"><option value="">-- None --</option><?php foreach($faculty as $f): ?><option value="<?=$f['id']?>" <?=($edit['coordinator_id']??'')==$f['id']?'selected':''?>><?=e($f['name'])?> (<?=e($f['designation'])?>)</option><?php endforeach; ?></select></div>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?=$edit?'Update':'Add Course'?></button>
  </form>
</div>
<div class="card"><div class="card-header"><h3>All Courses</h3></div>
<div class="table-wrap"><table><thead><tr><th>#</th><th>Name</th><th>Code</th><th>Duration</th><th>Fees</th><th>Coordinator</th><th>Status</th><th>Actions</th></tr></thead>
<tbody><?php foreach($courses as $i=>$c): ?>
<tr><td><?=$i+1?></td><td class="fw-600"><?=e($c['name'])?></td><td><span class="badge badge-sky"><?=e($c['code'])?></span></td><td><?=e($c['duration'])?></td><td>₹<?=number_format($c['fees'],0)?></td><td><?=e($c['coord_name']??'—')?></td>
<td><span class="badge badge-<?=$c['is_active']?'active':'inactive'?>"><?=$c['is_active']?'Active':'Inactive'?></span></td>
<td><div class="actions"><a href="?edit=<?=$c['id']?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
<form method="POST" style="display:inline;"><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?=$c['id']?>"><button class="btn btn-sm <?=$c['is_active']?'btn-danger':'btn-success'?>" data-confirm="Toggle?"><i class="fas fa-<?=$c['is_active']?'eye-slash':'eye'?>"></i></button></form></div></td></tr>
<?php endforeach; ?></tbody></table></div></div>
<?php require_once dirname(__DIR__).'/includes/admin_footer.php'; ?>
