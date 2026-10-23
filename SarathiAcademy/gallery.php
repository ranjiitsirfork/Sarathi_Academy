<?php $pageTitle='Gallery'; require_once 'includes/header.php';
$images=dbFetchAll('SELECT * FROM gallery ORDER BY created_at DESC'); ?>
<div class="page-banner"><div class="container"><h1>Gallery</h1><p>Moments from SarathiAcademy's journey of excellence</p></div></div>
<section class="section"><div class="container">
<?php if($images): ?>
<div class="gallery-grid">
<?php foreach($images as $img): ?>
<div class="gallery-item"><img src="<?=SITE_URL.'/uploads/'.e($img['image_path'])?>" alt="<?=e($img['title'])?>" loading="lazy"><div class="gallery-overlay"><i class="fas fa-search-plus"></i></div></div>
<?php endforeach; ?>
</div>
<?php else: ?><div class="text-center" style="padding:60px 0;color:var(--gray);"><i class="fas fa-images" style="font-size:4rem;opacity:.3;display:block;margin-bottom:16px;"></i><p>Gallery images will appear here soon!</p></div><?php endif; ?>
</div></section>
<?php require_once 'includes/footer.php'; ?>
