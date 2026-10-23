<?php $pageTitle='Fees Management'; $activePage='fees';
require_once dirname(__DIR__).'/includes/admin_header.php';
if($_SERVER['REQUEST_METHOD']==='POST'&&$_POST['action']==='add_fee'){
    $adm_id=(int)$_POST['admission_id']; $amount=(float)$_POST['amount']; $paid_date=$_POST['paid_date']; $mode=$_POST['mode']; $receipt=trim($_POST['receipt_no']??''); $remarks=trim($_POST['remarks']??'');
    if($adm_id&&$amount>0&&$paid_date){ dbInsert('INSERT INTO fees(admission_id,amount,paid_date,mode,receipt_no,remarks,collected_by) VALUES(?,?,?,?,?,?,?)',[$adm_id,$amount,$paid_date,$mode,$receipt,$remarks,$adminUser['id']]); setFlash('success','Fee of ₹'.number_format($amount,0).' recorded.'); }
    else setFlash('error','Fill all required fields.');
    redirect(ADMIN_URL.'/pages/fees.php');
}
$approvedAdm=dbFetchAll('SELECT a.id,u.name AS student_name,b.batch_name,c.name AS course_name,a.roll_no FROM admissions a JOIN users u ON u.id=a.student_id JOIN batches b ON b.id=a.batch_id JOIN courses c ON c.id=b.course_id WHERE a.status="approved" ORDER BY u.name');
$searchAdm=(int)($_GET['admission']??0); $monthFilter=$_GET['month']??'';
$where=['1=1']; $params=[];
if($searchAdm){$where[]='f.admission_id=?';$params[]=$searchAdm;}
if($monthFilter){$where[]='DATE_FORMAT(f.paid_date,"%Y-%m")=?';$params[]=$monthFilter;}
$fees=dbFetchAll('SELECT f.*,u.name AS student_name,b.batch_name,c.name AS course_name,a.roll_no,cb.name AS collector FROM fees f JOIN admissions a ON a.id=f.admission_id JOIN users u ON u.id=a.student_id JOIN batches b ON b.id=a.batch_id JOIN courses c ON c.id=b.course_id LEFT JOIN users cb ON cb.id=f.collected_by WHERE '.implode(' AND ',$where).' ORDER BY f.paid_date DESC',$params);
$totalShown=array_sum(array_column($fees,'amount'));
?>
<div class="card mb-3"><div class="card-header"><h3>Record Fee Payment</h3></div>
<form method="POST"><input type="hidden" name="action" value="add_fee">
<div class="form-row">
  <div class="form-group"><label>Student *</label><select name="admission_id" class="form-control" required><option value="">-- Select --</option><?php foreach($approvedAdm as $a): ?><option value="<?=$a['id']?>"><?=e($a['student_name'])?> | <?=e($a['course_name'])?> – <?=e($a['batch_name'])?><?=$a['roll_no']?' ['.$a['roll_no'].']':''?></option><?php endforeach; ?></select></div>
  <div class="form-group"><label>Amount (₹) *</label><input type="number" name="amount" class="form-control" step="0.01" min="1" required></div>
</div>
<div class="form-row">
  <div class="form-group"><label>Payment Date *</label><input type="date" name="paid_date" class="form-control" value="<?=date('Y-m-d')?>" required></div>
  <div class="form-group"><label>Mode</label><select name="mode" class="form-control"><option value="cash">Cash</option><option value="online">Online/UPI</option><option value="cheque">Cheque</option><option value="dd">Demand Draft</option></select></div>
  <div class="form-group"><label>Receipt No.</label><input type="text" name="receipt_no" class="form-control" placeholder="Optional"></div>
</div>
<div class="form-group"><label>Remarks</label><input type="text" name="remarks" class="form-control" placeholder="Optional"></div>
<button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Record Payment</button>
</form></div>
<div class="filter-bar"><form method="GET" style="display:contents;">
  <div class="form-group"><label>Student</label><select name="admission" class="form-control"><option value="">All</option><?php foreach($approvedAdm as $a): ?><option value="<?=$a['id']?>" <?=$searchAdm===$a['id']?'selected':''?>><?=e($a['student_name'])?></option><?php endforeach; ?></select></div>
  <div class="form-group"><label>Month</label><input type="month" name="month" class="form-control" value="<?=e($monthFilter)?>"></div>
  <div class="form-group" style="align-self:flex-end;"><button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button> <a href="?" class="btn btn-secondary">Reset</a></div>
</form></div>
<div class="card"><div class="card-header"><h3>Fee Records (<?=count($fees)?>)</h3><span class="fw-600 text-success">Total: ₹<?=number_format($totalShown,0)?></span></div>
<div class="table-wrap"><table><thead><tr><th>#</th><th>Student</th><th>Course/Batch</th><th>Roll No</th><th>Amount</th><th>Date</th><th>Mode</th><th>Receipt</th><th>By</th></tr></thead>
<tbody><?php foreach($fees as $i=>$f): ?>
<tr><td><?=$i+1?></td><td class="fw-600"><?=e($f['student_name'])?></td><td><?=e($f['course_name'])?><br><span class="small text-gray"><?=e($f['batch_name'])?></span></td>
<td><?=e($f['roll_no']?:'—')?></td><td class="fw-600 text-success">₹<?=number_format($f['amount'],0)?></td>
<td><?=date('d M Y',strtotime($f['paid_date']))?></td><td><span class="badge badge-sky"><?=ucfirst($f['mode'])?></span></td>
<td><?=e($f['receipt_no']?:'—')?></td><td><?=e($f['collector']?:'—')?></td></tr>
<?php endforeach; if(!$fees): ?><tr><td colspan="9" class="text-center text-gray" style="padding:28px;">No records found.</td></tr><?php endif; ?>
</tbody></table></div></div>
<?php require_once dirname(__DIR__).'/includes/admin_footer.php'; ?>
