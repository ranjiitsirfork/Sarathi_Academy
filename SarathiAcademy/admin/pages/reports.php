<?php $pageTitle='Reports'; $activePage='reports';
require_once dirname(__DIR__).'/includes/admin_header.php';
$reportType=$_GET['type']??'fees'; $dateFrom=$_GET['from']??date('Y-m-01'); $dateTo=$_GET['to']??date('Y-m-d'); $export=$_GET['export']??''; $batchId=(int)($_GET['batch']??0);
$batches=dbFetchAll('SELECT b.id,b.batch_name,c.name AS course_name FROM batches b JOIN courses c ON c.id=b.course_id ORDER BY c.name');
$feesData=[]; $admData=[]; $totalFees=0;
if($reportType==='fees'){
    $where=['f.paid_date BETWEEN ? AND ?']; $params=[$dateFrom,$dateTo];
    if($batchId){$where[]='a.batch_id=?';$params[]=$batchId;}
    $feesData=dbFetchAll('SELECT f.paid_date,u.name AS student_name,a.roll_no,c.name AS course_name,b.batch_name,f.amount,f.mode,f.receipt_no,cb.name AS collected_by FROM fees f JOIN admissions a ON a.id=f.admission_id JOIN users u ON u.id=a.student_id JOIN batches b ON b.id=a.batch_id JOIN courses c ON c.id=b.course_id LEFT JOIN users cb ON cb.id=f.collected_by WHERE '.implode(' AND ',$where).' ORDER BY f.paid_date DESC',$params);
    $totalFees=array_sum(array_column($feesData,'amount'));
    if($export==='csv'&&$feesData){header('Content-Type: text/csv');header('Content-Disposition: attachment; filename="fees_'.date('Ymd').'.csv"');$fp=fopen('php://output','w');fputcsv($fp,['Date','Student','Roll','Course','Batch','Amount','Mode','Receipt','By']);foreach($feesData as $r) fputcsv($fp,[$r['paid_date'],$r['student_name'],$r['roll_no'],$r['course_name'],$r['batch_name'],$r['amount'],$r['mode'],$r['receipt_no'],$r['collected_by']]);fclose($fp);exit;}
}
if($reportType==='admissions'){
    $where=['DATE(a.created_at) BETWEEN ? AND ?']; $params=[$dateFrom,$dateTo];
    if($batchId){$where[]='a.batch_id=?';$params[]=$batchId;}
    $admData=dbFetchAll('SELECT DATE(a.created_at) AS applied,u.name AS student,u.phone,c.name AS course,b.batch_name,a.status,a.roll_no FROM admissions a JOIN users u ON u.id=a.student_id JOIN batches b ON b.id=a.batch_id JOIN courses c ON c.id=b.course_id WHERE '.implode(' AND ',$where).' ORDER BY a.created_at DESC',$params);
    if($export==='csv'&&$admData){header('Content-Type: text/csv');header('Content-Disposition: attachment; filename="admissions_'.date('Ymd').'.csv"');$fp=fopen('php://output','w');fputcsv($fp,['Date','Student','Phone','Course','Batch','Status','Roll']);foreach($admData as $r) fputcsv($fp,[$r['applied'],$r['student'],$r['phone'],$r['course'],$r['batch_name'],$r['status'],$r['roll_no']]);fclose($fp);exit;}
}
?>
<div class="filter-bar"><form method="GET" style="display:contents;">
  <div class="form-group"><label>Report Type</label><select name="type" class="form-control"><option value="fees" <?=$reportType==='fees'?'selected':''?>>Fees Collection</option><option value="admissions" <?=$reportType==='admissions'?'selected':''?>>Admissions</option></select></div>
  <div class="form-group"><label>From</label><input type="date" name="from" class="form-control" value="<?=e($dateFrom)?>"></div>
  <div class="form-group"><label>To</label><input type="date" name="to" class="form-control" value="<?=e($dateTo)?>"></div>
  <div class="form-group"><label>Batch</label><select name="batch" class="form-control"><option value="">All</option><?php foreach($batches as $b): ?><option value="<?=$b['id']?>" <?=$batchId===$b['id']?'selected':''?>><?=e($b['course_name'])?> – <?=e($b['batch_name'])?></option><?php endforeach; ?></select></div>
  <div class="form-group" style="align-self:flex-end;"><button type="submit" class="btn btn-primary"><i class="fas fa-chart-bar"></i> Generate</button></div>
</form></div>
<?php if($reportType==='fees'): ?>
<div class="card"><div class="card-header"><h3>Fees Collection Report</h3><div class="d-flex gap-10"><span class="fw-600 text-success">Total: ₹<?=number_format($totalFees,0)?></span><a href="?type=fees&from=<?=e($dateFrom)?>&to=<?=e($dateTo)?>&batch=<?=$batchId?>&export=csv" class="btn btn-success btn-sm"><i class="fas fa-file-csv"></i> CSV</a><button onclick="window.print()" class="btn btn-secondary btn-sm"><i class="fas fa-print"></i> Print</button></div></div>
<div class="table-wrap"><table><thead><tr><th>#</th><th>Date</th><th>Student</th><th>Roll No</th><th>Course/Batch</th><th>Amount</th><th>Mode</th><th>Receipt</th><th>By</th></tr></thead>
<tbody><?php foreach($feesData as $i=>$r): ?>
<tr><td><?=$i+1?></td><td><?=date('d M Y',strtotime($r['paid_date']))?></td><td class="fw-600"><?=e($r['student_name'])?></td><td><?=e($r['roll_no']?:'—')?></td><td><?=e($r['course_name'])?><br><span class="small text-gray"><?=e($r['batch_name'])?></span></td><td class="fw-600 text-success">₹<?=number_format($r['amount'],0)?></td><td><?=ucfirst($r['mode'])?></td><td><?=e($r['receipt_no']?:'—')?></td><td><?=e($r['collected_by']?:'—')?></td></tr>
<?php endforeach; if(!$feesData): ?><tr><td colspan="9" class="text-center text-gray" style="padding:28px;">No records found.</td></tr><?php endif; ?>
<?php if($feesData): ?><tfoot><tr style="background:#f0f7ff;font-weight:700;"><td colspan="5" style="padding:12px 16px;">Total</td><td style="padding:12px 16px;color:var(--success);">₹<?=number_format($totalFees,0)?></td><td colspan="3"></td></tr></tfoot><?php endif; ?>
</tbody></table></div></div>
<?php elseif($reportType==='admissions'): ?>
<div class="card"><div class="card-header"><h3>Admissions Report (<?=count($admData)?>)</h3><div class="d-flex gap-10"><a href="?type=admissions&from=<?=e($dateFrom)?>&to=<?=e($dateTo)?>&batch=<?=$batchId?>&export=csv" class="btn btn-success btn-sm"><i class="fas fa-file-csv"></i> CSV</a><button onclick="window.print()" class="btn btn-secondary btn-sm"><i class="fas fa-print"></i> Print</button></div></div>
<div class="table-wrap"><table><thead><tr><th>#</th><th>Applied</th><th>Student</th><th>Phone</th><th>Course/Batch</th><th>Status</th><th>Roll No</th></tr></thead>
<tbody><?php foreach($admData as $i=>$r): ?>
<tr><td><?=$i+1?></td><td><?=date('d M Y',strtotime($r['applied']))?></td><td class="fw-600"><?=e($r['student'])?></td><td><?=e($r['phone'])?></td><td><?=e($r['course'])?><br><span class="small text-gray"><?=e($r['batch_name'])?></span></td><td><span class="badge badge-<?=$r['status']?>"><?=ucfirst($r['status'])?></span></td><td><?=e($r['roll_no']?:'—')?></td></tr>
<?php endforeach; if(!$admData): ?><tr><td colspan="7" class="text-center text-gray" style="padding:28px;">No records.</td></tr><?php endif; ?>
</tbody></table></div></div>
<?php endif; ?>
<?php require_once dirname(__DIR__).'/includes/admin_footer.php'; ?>
