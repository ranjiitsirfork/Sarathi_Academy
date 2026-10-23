<?php $pageTitle='Manage Staff'; $activePage='staff';
require_once dirname(__DIR__).'/includes/admin_header.php'; requireAdmin();
if($_SERVER['REQUEST_METHOD']==='POST'){
    $action=$_POST['action']??'';
    if(in_array($action,['add','edit'])){
        $id=(int)($_POST['id']??0); $name=trim($_POST['name']??''); $email=trim($_POST['email']??'');
        $phone=trim($_POST['phone']??''); $role=$_POST['role']??'staff'; $desig=trim($_POST['designation']??''); $pass=$_POST['password']??'';
        if(!$name||!$email){setFlash('error','Name and email required.');redirect(ADMIN_URL.'/pages/staff.php');}
        if($action==='add'){
            if(!$pass){setFlash('error','Password required.');redirect(ADMIN_URL.'/pages/staff.php');}
            $exists=dbFetch('SELECT id FROM users WHERE email=?',[$email]);
            if($exists){setFlash('error','Email already exists.');}
            else{dbInsert('INSERT INTO users(name,email,phone,password,role,designation) VALUES(?,?,?,?,?,?)',[$name,$email,$phone,password_hash($pass,PASSWORD_BCRYPT),$role,$desig]);setFlash('success','Staff added.');}
        } else {
            if($pass) dbExecute('UPDATE users SET name=?,email=?,phone=?,role=?,designation=?,password=? WHERE id=?',[$name,$email,$phone,$role,$desig,password_hash($pass,PASSWORD_BCRYPT),$id]);
            else       dbExecute('UPDATE users SET name=?,email=?,phone=?,role=?,designation=? WHERE id=?',[$name,$email,$phone,$role,$desig,$id]);
            setFlash('success','Staff updated.');
        }
    } elseif($_POST['action']==='toggle'){
        dbExecute('UPDATE users SET is_active=1-is_active WHERE id=? AND role!="admin"',[(int)$_POST['id']]);setFlash('success','Status updated.');
    }
    redirect(ADMIN_URL.'/pages/staff.php');
}
$staffList=dbFetchAll("SELECT * FROM users WHERE role IN('admin','staff') ORDER BY role,name");
$edit=null; if(isset($_GET['edit'])){foreach($staffList as $s){if($s['id']==(int)$_GET['edit']){$edit=$s;break;}}}
?>
<div class="card mb-3">
  <div class="card-header"><h3><?=$edit?'Edit Staff':'Add New Staff'?></h3><?php if($edit): ?><a href="?" class="btn btn-secondary btn-sm">Cancel</a><?php endif; ?></div>
  <form method="POST">
    <input type="hidden" name="action" value="<?=$edit?'edit':'add'?>">
    <?php if($edit): ?><input type="hidden" name="id" value="<?=$edit['id']?>"><?php endif; ?>
    <div class="form-row-3">
      <div class="form-group"><label>Full Name *</label><input type="text" name="name" class="form-control" value="<?=e($edit['name']??'')?>" required></div>
      <div class="form-group"><label>Email *</label><input type="email" name="email" class="form-control" value="<?=e($edit['email']??'')?>" required></div>
      <div class="form-group"><label>Phone</label><input type="text" name="phone" class="form-control" value="<?=e($edit['phone']??'')?>"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Role</label><select name="role" class="form-control"><option value="staff" <?=($edit['role']??'staff')==='staff'?'selected':''?>>Staff/Faculty</option><option value="admin" <?=($edit['role']??'')==='admin'?'selected':''?>>Admin</option></select></div>
      <div class="form-group"><label>Designation</label><input type="text" name="designation" class="form-control" placeholder="e.g. Faculty-Physics" value="<?=e($edit['designation']??'')?>"></div>
    </div>
    <div class="form-group"><label>Password <?=$edit?'(leave blank to keep)':' *'?></label><input type="password" name="password" class="form-control" <?=$edit?'':'required'?>></div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?=$edit?'Update':'Add Staff'?></button>
  </form>
</div>
<div class="card"><div class="card-header"><h3>Staff List</h3></div>
<div class="table-wrap"><table>
<thead><tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Designation</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($staffList as $i=>$s): ?>
<tr><td><?=$i+1?></td><td class="fw-600"><?=e($s['name'])?></td><td><?=e($s['email'])?></td><td><?=e($s['phone'])?></td>
<td><span class="badge badge-sky"><?=ucfirst($s['role'])?></span></td><td><?=e($s['designation'])?></td>
<td><span class="badge badge-<?=$s['is_active']?'active':'inactive'?>"><?=$s['is_active']?'Active':'Inactive'?></span></td>
<td><div class="actions">
  <a href="?edit=<?=$s['id']?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
  <?php if(!($s['role']==='admin'&&$s['id']===$adminUser['id'])): ?>
  <form method="POST" style="display:inline;"><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?=$s['id']?>">
  <button class="btn btn-sm <?=$s['is_active']?'btn-danger':'btn-success'?>" data-confirm="Toggle status?"><i class="fas fa-<?=$s['is_active']?'ban':'check'?>"></i></button></form>
  <?php endif; ?>
</div></td></tr>
<?php endforeach; ?>
</tbody></table></div></div>
<?php require_once dirname(__DIR__).'/includes/admin_footer.php'; ?>
