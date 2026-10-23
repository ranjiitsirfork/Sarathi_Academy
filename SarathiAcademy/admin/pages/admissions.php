<?php $pageTitle='Admissions'; $activePage='admissions';
require_once dirname(__DIR__).'/includes/admin_header.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $action=$_POST['action']??''; $id=(int)($_POST['id']??0);
    if($action==='approve'){
        $adm=dbFetch('SELECT a.*,c.code FROM admissions a JOIN batches b ON b.id=a.batch_id JOIN courses c ON c.id=b.course_id WHERE a.id=?',[$id]);
        if($adm){ $roll=$adm['code'].date('Y').str_pad($id,4,'0',STR_PAD_LEFT); dbExecute('UPDATE admissions SET status="approved",roll_no=?,approved_by=?,approved_at=NOW() WHERE id=?',[$roll,$adminUser['id'],$id]); setFlash('success','Approved. Roll No: '.$roll); }
    } elseif($action==='reject'){
        dbExecute('UPDATE admissions SET status="rejected",notes=?,approved_by=?,approved_at=NOW() WHERE id=?',[trim($_POST['notes']??''),$adminUser['id'],$id]); setFlash('success','Rejected.');
    }
    redirect(ADMIN_URL.'/pages/admissions.php');
}
$statusF=$_GET['status']??''; $batchF=(int)($_GET['batch']??0); $search=trim($_GET['search']??'');
$where=['1=1']; $params=[];
if($statusF){$where[]='a.status=?';$params[]=$statusF;}
if($batchF){$where[]='a.batch_id=?';$params[]=$batchF;}
if($search){$where[]='(u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)';$params[]="%$search%";$params[]="%$search%";$params[]="%$search%";}
$admissions=dbFetchAll('SELECT a.*,u.name AS student_name,u.phone,b.batch_name,c.name AS course_name FROM admissions a JOIN users u ON u.id=a.student_id JOIN batches b ON b.id=a.batch_id JOIN courses c ON c.id=b.course_id WHERE '.implode(' AND ',$where).' ORDER BY a.created_at DESC',$params);
$batches=dbFetchAll('SELECT b.id,b.batch_name,c.name AS course_name FROM batches b JOIN courses c ON c.id=b.course_id WHERE b.is_active=1 ORDER BY c.name,b.batch_name');
$viewAdm=null;
if(isset($_GET['view'])){
    $viewAdm=dbFetch('SELECT a.*,u.name AS student_name,u.email,u.phone,b.batch_name,b.timing,c.name AS course_name,ap.name AS approved_by_name FROM admissions a JOIN users u ON u.id=a.student_id JOIN batches b ON b.id=a.batch_id JOIN courses c ON c.id=b.course_id LEFT JOIN users ap ON ap.id=a.approved_by WHERE a.id=?',[(int)$_GET['view']]);
    if($viewAdm){ $viewAdm['fees_records']=dbFetchAll('SELECT f.*,u.name AS collected_by_name FROM fees f LEFT JOIN users u ON u.id=f.collected_by WHERE f.admission_id=? ORDER BY f.paid_date DESC',[$viewAdm['id']]); $viewAdm['total_paid']=array_sum(array_column($viewAdm['fees_records'],'amount')); }
}
?>
<?php if($viewAdm): ?>
<div class="card mb-3"><div class="card-header"><h3>Admission Detail – <?=e($viewAdm['student_name'])?></h3><a href="?" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a></div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
<div><table style="width:100%;font-size:14px;">
<?php foreach([['Roll No',$viewAdm['roll_no']?:'—'],['Student',$viewAdm['student_name']],['Email',$viewAdm['email']],['Phone',$viewAdm['phone']],['Course',$viewAdm['course_name']],['Batch',$viewAdm['batch_name']],['Parent',$viewAdm['parent_name']],['Parent Phone',$viewAdm['parent_phone']],['DOB',$viewAdm['dob']?date('d M Y',strtotime($viewAdm['dob'])):'—'],['Prev School',$viewAdm['prev_school']],['Status',ucfirst($viewAdm['status'])]] as [$l,$v]): ?>
<tr><td class="fw-600" style="padding:7px 0;width:40%;"><?=$l?></td><td><?=e($v)?></td></tr>
<?php endforeach; ?></table></div>
<div><h4 class="mb-2">Fees: <strong class="text-success">₹<?=number_format($viewAdm['total_paid'],0)?> paid</strong></h4>
<div class="table-wrap"><table><thead><tr><th>Date</th><th>Amount</th><th>Mode</th><th>Receipt</th></tr></thead><tbody>
<?php foreach($viewAdm['fees_records'] as $f): ?><tr><td><?=date('d M Y',strtotime($f['paid_date']))?></td><td>₹<?=number_format($f['amount'],0)?></td><td><?=ucfirst($f['mode'])?></td><td><?=e($f['receipt_no'])?></td></tr><?php endforeach; ?>
<?php if(!$viewAdm['fees_records']): ?><tr><td colspan="4" class="text-center text-gray">No fees yet.</td></tr><?php endif; ?>
</tbody></table></div>
<?php if($viewAdm['status']==='pending'): ?>
<div class="mt-3 d-flex gap-10">
<form method="POST"><input type="hidden" name="action" value="approve"><input type="hidden" name="id" value="<?=$viewAdm['id']?>"><button class="btn btn-success" data-confirm="Approve?"><i class="fas fa-check"></i> Approve</button></form>
<form method="POST" style="display:flex;gap:6px;"><input type="hidden" name="action" value="reject"><input type="hidden" name="id" value="<?=$viewAdm['id']?>"><input type="text" name="notes" class="form-control" placeholder="Reason (optional)" style="width:200px;"><button class="btn btn-danger" data-confirm="Reject?"><i class="fas fa-times"></i> Reject</button></form>
</div><?php endif; ?></div></div></div>
<?php else: ?>
<div class="filter-bar"><form method="GET" style="display:contents;">
  <div class="form-group"><label>Status</label><select name="status" class="form-control"><option value="">All</option><option value="pending" <?=$statusF==='pending'?'selected':''?>>Pending</option><option value="approved" <?=$statusF==='approved'?'selected':''?>>Approved</option><option value="rejected" <?=$statusF==='rejected'?'selected':''?>>Rejected</option></select></div>
  <div class="form-group"><label>Batch</label><select name="batch" class="form-control"><option value="">All</option><?php foreach($batches as $b): ?><option value="<?=$b['id']?>" <?=$batchF===$b['id']?'selected':''?>><?=e($b['course_name'])?> – <?=e($b['batch_name'])?></option><?php endforeach; ?></select></div>
  <div class="form-group"><label>Search</label><input type="text" name="search" class="form-control" placeholder="Name/Email/Phone" value="<?=e($search)?>"></div>
  <div class="form-group" style="align-self:flex-end;"><button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button> <a href="?" class="btn btn-secondary">Reset</a></div>
</form></div>
<div class="card"><div class="card-header"><h3>Admissions (<?=count($admissions)?>)</h3></div>
<div class="table-wrap"><table><thead><tr><th>#</th><th>Student</th><th>Course/Batch</th><th>Applied</th><th>Status</th><th>Roll No</th><th>Actions</th></tr></thead>
<tbody><?php foreach($admissions as $i=>$a): ?>
<tr><td><?=$i+1?></td><td><strong><?=e($a['student_name'])?></strong><br><span class="small text-gray"><?=e($a['phone'])?></span></td>
<td><?=e($a['course_name'])?><br><span class="small text-gray"><?=e($a['batch_name'])?></span></td>
<td class="small"><?=date('d M Y',strtotime($a['created_at']))?></td>
<td><span class="badge badge-<?=$a['status']?>"><?=ucfirst($a['status'])?></span></td>
<td><?=e($a['roll_no']?:'—')?></td>
<td><div class="actions"><a href="?view=<?=$a['id']?>" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
<?php if($a['status']==='pending'): ?>
<form method="POST" style="display:inline;"><input type="hidden" name="action" value="approve"><input type="hidden" name="id" value="<?=$a['id']?>"><button class="btn btn-sm btn-success" data-confirm="Approve?"><i class="fas fa-check"></i></button></form>
<form method="POST" style="display:inline;"><input type="hidden" name="action" value="reject"><input type="hidden" name="id" value="<?=$a['id']?>"><button class="btn btn-sm btn-danger" data-confirm="Reject?"><i class="fas fa-times"></i></button></form>
<?php endif; ?></div></td></tr>
<?php endforeach; if(!$admissions): ?><tr><td colspan="7" class="text-center text-gray" style="padding:28px;">No admissions found.</td></tr><?php endif; ?>
</tbody></table></div></div>
<?php endif; ?>
<?php require_once dirname(__DIR__).'/includes/admin_footer.php'; ?>
