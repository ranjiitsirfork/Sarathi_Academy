<?php $pageTitle='Enquiries'; $activePage='enquiries';
require_once dirname(__DIR__).'/includes/admin_header.php'; requireAdmin();
if($_SERVER['REQUEST_METHOD']==='POST'){dbExecute('UPDATE enquiries SET status=? WHERE id=?',[$_POST['status'],(int)$_POST['id']]);setFlash('success','Status updated.');redirect(ADMIN_URL.'/pages/enquiries.php');}
$statusF=$_GET['status']??'';
$enquiries=$statusF?dbFetchAll('SELECT * FROM enquiries WHERE status=? ORDER BY created_at DESC',[$statusF]):dbFetchAll('SELECT * FROM enquiries ORDER BY created_at DESC');
?>
<div class="filter-bar"><form method="GET" style="display:contents;">
  <div class="form-group"><label>Status</label><select name="status" class="form-control"><option value="">All</option><option value="new" <?=$statusF==='new'?'selected':''?>>New</option><option value="contacted" <?=$statusF==='contacted'?'selected':''?>>Contacted</option><option value="closed" <?=$statusF==='closed'?'selected':''?>>Closed</option></select></div>
  <div class="form-group" style="align-self:flex-end;"><button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button> <a href="?" class="btn btn-secondary">All</a></div>
</form></div>
<div class="card"><div class="card-header"><h3>Enquiries (<?=count($enquiries)?>)</h3></div>
<div class="table-wrap"><table><thead><tr><th>#</th><th>Name</th><th>Phone</th><th>Course</th><th>Message</th><th>Date</th><th>Status</th><th>Update</th></tr></thead>
<tbody><?php foreach($enquiries as $i=>$eq): ?>
<tr><td><?=$i+1?></td><td class="fw-600"><?=e($eq['name'])?></td><td><?=e($eq['phone'])?></td><td><?=e($eq['course']?:'—')?></td>
<td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?=e($eq['message'])?>"><?=e($eq['message'])?></td>
<td class="small"><?=date('d M Y',strtotime($eq['created_at']))?></td>
<td><span class="badge <?=$eq['status']==='new'?'badge-pending':($eq['status']==='contacted'?'badge-sky':'badge-inactive')?>"><?=ucfirst($eq['status'])?></span></td>
<td><form method="POST" style="display:flex;gap:6px;"><input type="hidden" name="id" value="<?=$eq['id']?>">
<select name="status" class="form-control" style="font-size:12px;padding:4px 8px;width:110px;"><option value="new" <?=$eq['status']==='new'?'selected':''?>>New</option><option value="contacted" <?=$eq['status']==='contacted'?'selected':''?>>Contacted</option><option value="closed" <?=$eq['status']==='closed'?'selected':''?>>Closed</option></select>
<button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-save"></i></button></form></td></tr>
<?php endforeach; if(!$enquiries): ?><tr><td colspan="8" class="text-center text-gray" style="padding:28px;">No enquiries.</td></tr><?php endif; ?>
</tbody></table></div></div>
<?php require_once dirname(__DIR__).'/includes/admin_footer.php'; ?>
