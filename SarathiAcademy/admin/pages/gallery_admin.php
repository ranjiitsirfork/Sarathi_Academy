<?php $pageTitle='Gallery Management'; $activePage='gallery';
require_once dirname(__DIR__).'/includes/admin_header.php'; requireAdmin();
if($_SERVER['REQUEST_METHOD']==='POST'){
    $action=$_POST['action']??'';
    if($action==='upload'){
        $title=trim($_POST['title']??''); $category=trim($_POST['category']??'General');
        if(isset($_FILES['image'])&&$_FILES['image']['error']===0){
            $ext=strtolower(pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION));
            if(!in_array($ext,['jpg','jpeg','png','webp','gif'])){ setFlash('error','Only JPG, PNG, WEBP, GIF allowed.'); }
            else{
                $filename='gallery_'.time().'_'.mt_rand(1000,9999).'.'.$ext;
                $dir=UPLOAD_PATH.'gallery/';
                if(!is_dir($dir)) mkdir($dir,0755,true);
                if(move_uploaded_file($_FILES['image']['tmp_name'],$dir.$filename)){
                    dbInsert('INSERT INTO gallery(title,image_path,category,uploaded_by) VALUES(?,?,?,?)',[$title,'gallery/'.$filename,$category,$adminUser['id']]);
                    setFlash('success','Image uploaded.');
                } else setFlash('error','Upload failed. Check folder permissions.');
            }
        } else setFlash('error','Please select an image.');
    } elseif($action==='delete'){
        $id=(int)$_POST['id'];
        $row=dbFetch('SELECT image_path FROM gallery WHERE id=?',[$id]);
        if($row){ $file=UPLOAD_PATH.$row['image_path']; if(file_exists($file)) unlink($file); dbExecute('DELETE FROM gallery WHERE id=?',[$id]); setFlash('success','Image deleted.'); }
    }
    redirect(ADMIN_URL.'/pages/gallery_admin.php');
}
$images=dbFetchAll('SELECT g.*,u.name AS uploaded_by_name FROM gallery g LEFT JOIN users u ON u.id=g.uploaded_by ORDER BY g.created_at DESC');
?>
<div class="card mb-3"><div class="card-header"><h3>Upload Image</h3></div>
<form method="POST" enctype="multipart/form-data"><input type="hidden" name="action" value="upload">
<div class="form-row">
  <div class="form-group"><label>Image File *</label><input type="file" name="image" class="form-control" accept="image/*" required></div>
  <div class="form-group"><label>Title</label><input type="text" name="title" class="form-control" placeholder="e.g. Annual Day 2024"></div>
  <div class="form-group"><label>Category</label><select name="category" class="form-control"><option>General</option><option>Events</option><option>Results</option><option>Campus</option><option>Faculty</option><option>Students</option></select></div>
</div>
<button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Upload Image</button>
</form></div>
<div class="card"><div class="card-header"><h3>Gallery Images (<?=count($images)?>)</h3></div>
<?php if($images): ?>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;margin-top:4px;">
<?php foreach($images as $img): ?>
<div style="border:1px solid #dce8f4;border-radius:8px;overflow:hidden;">
  <div style="aspect-ratio:4/3;background:#e8f0f8;overflow:hidden;"><img src="<?=SITE_URL.'/uploads/'.e($img['image_path'])?>" alt="<?=e($img['title'])?>" style="width:100%;height:100%;object-fit:cover;"></div>
  <div style="padding:10px 12px;">
    <p class="fw-600 small"><?=e($img['title']?:'Untitled')?></p>
    <p class="small text-gray"><?=e($img['category'])?> · <?=date('d M Y',strtotime($img['created_at']))?></p>
    <form method="POST" style="margin-top:8px;"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$img['id']?>"><button class="btn btn-sm btn-danger" data-confirm="Delete this image?"><i class="fas fa-trash"></i> Delete</button></form>
  </div>
</div>
<?php endforeach; ?>
</div>
<?php else: ?><p class="text-center text-gray" style="padding:30px;">No images yet.</p><?php endif; ?>
</div>
<?php require_once dirname(__DIR__).'/includes/admin_footer.php'; ?>
