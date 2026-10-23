<?php $pageTitle='Attendance Report'; $activePage='att_report';
require_once dirname(__DIR__).'/includes/admin_header.php';
$batches=dbFetchAll('SELECT b.id,b.batch_name,c.name AS course_name FROM batches b JOIN courses c ON c.id=b.course_id WHERE b.is_active=1 ORDER BY c.name,b.batch_name');
$selBatch=(int)($_GET['batch']??0); $dateFrom=$_GET['from']??date('Y-m-01'); $dateTo=$_GET['to']??date('Y-m-d'); $export=$_GET['export']??'';
$report=[]; $batchInfo=null;
if($selBatch){
    foreach($batches as $b){if($b['id']===$selBatch){$batchInfo=$b;break;}}
    $students=dbFetchAll('SELECT u.id,u.name,a.roll_no FROM admissions a JOIN users u ON u.id=a.student_id WHERE a.batch_id=? AND a.status="approved" ORDER BY u.name',[$selBatch]);
    foreach($students as $s){
        $total  =dbCount('SELECT COUNT(DISTINCT attend_date) FROM attendance WHERE batch_id=? AND student_id=? AND attend_date BETWEEN ? AND ?',[$selBatch,$s['id'],$dateFrom,$dateTo]);
        $present=dbCount('SELECT COUNT(*) FROM attendance WHERE batch_id=? AND student_id=? AND status="present" AND attend_date BETWEEN ? AND ?',[$selBatch,$s['id'],$dateFrom,$dateTo]);
        $absent =dbCount('SELECT COUNT(*) FROM attendance WHERE batch_id=? AND student_id=? AND status="absent"  AND attend_date BETWEEN ? AND ?',[$selBatch,$s['id'],$dateFrom,$dateTo]);
        $late   =dbCount('SELECT COUNT(*) FROM attendance WHERE batch_id=? AND student_id=? AND status="late"    AND attend_date BETWEEN ? AND ?',[$selBatch,$s['id'],$dateFrom,$dateTo]);
        $pct=$total>0?round(($present/$total)*100,1):0;
        $report[]=['name'=>$s['name'],'roll'=>$s['roll_no'],'total'=>$total,'present'=>$present,'absent'=>$absent,'late'=>$late,'pct'=>$pct];
    }
    if($export==='csv'&&$report){
        header('Content-Type: text/csv'); header('Content-Disposition: attachment; filename="attendance_'.date('Ymd').'.csv"');
        $fp=fopen('php://output','w'); fputcsv($fp,['Roll No','Student','Total','Present','Absent','Late','%']);
        foreach($report as $r) fputcsv($fp,[$r['roll'],$r['name'],$r['total'],$r['present'],$r['absent'],$r['late'],$r['pct'].'%']);
        fclose($fp); exit;
    }
}
?>
<div class="filter-bar"><form method="GET" style="display:contents;">
  <div class="form-group"><label>Batch *</label><select name="batch" class="form-control"><option value="">-- Select --</option><?php foreach($batches as $b): ?><option value="<?=$b['id']?>" <?=$selBatch===$b['id']?'selected':''?>><?=e($b['course_name'])?> – <?=e($b['batch_name'])?></option><?php endforeach; ?></select></div>
  <div class="form-group"><label>From</label><input type="date" name="from" class="form-control" value="<?=e($dateFrom)?>"></div>
  <div class="form-group"><label>To</label><input type="date" name="to" class="form-control" value="<?=e($dateTo)?>"></div>
  <div class="form-group" style="align-self:flex-end;"><button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Generate</button></div>
</form></div>
<?php if($selBatch&&$report): ?>
<div class="card"><div class="card-header"><h3>Report – <?=e($batchInfo['course_name'])?> / <?=e($batchInfo['batch_name'])?></h3>
<div class="d-flex gap-10"><a href="?batch=<?=$selBatch?>&from=<?=e($dateFrom)?>&to=<?=e($dateTo)?>&export=csv" class="btn btn-success btn-sm"><i class="fas fa-file-csv"></i> Export CSV</a><button onclick="window.print()" class="btn btn-secondary btn-sm"><i class="fas fa-print"></i> Print</button></div></div>
<p class="small text-gray mb-3">Period: <?=date('d M Y',strtotime($dateFrom))?> to <?=date('d M Y',strtotime($dateTo))?></p>
<div class="table-wrap"><table><thead><tr><th>#</th><th>Roll No</th><th>Student</th><th>Total</th><th>Present</th><th>Absent</th><th>Late</th><th>Attendance %</th></tr></thead>
<tbody><?php foreach($report as $i=>$r): ?>
<tr><td><?=$i+1?></td><td><?=e($r['roll']?:'—')?></td><td class="fw-600"><?=e($r['name'])?></td><td><?=$r['total']?></td>
<td><span class="badge badge-present"><?=$r['present']?></span></td><td><span class="badge badge-absent"><?=$r['absent']?></span></td><td><span class="badge badge-late"><?=$r['late']?></span></td>
<td><div style="display:flex;align-items:center;gap:8px;"><div style="width:80px;height:8px;background:#e2e8f0;border-radius:4px;overflow:hidden;"><div style="width:<?=$r['pct']?>%;height:100%;background:<?=$r['pct']>=75?'var(--success)':($r['pct']>=50?'var(--warning)':'var(--danger)')?>; border-radius:4px;"></div></div><span class="fw-600"><?=$r['pct']?>%</span></div></td></tr>
<?php endforeach; ?></tbody></table></div></div>
<?php elseif($selBatch): ?><div class="card"><p class="text-center text-gray" style="padding:30px;">No attendance records found.</p></div><?php endif; ?>
<?php require_once dirname(__DIR__).'/includes/admin_footer.php'; ?>
