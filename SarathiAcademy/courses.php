<?php
$pageTitle='Courses'; require_once 'includes/header.php';
$courses=dbFetchAll('SELECT c.*,u.name AS coordinator_name,(SELECT COUNT(*) FROM batches b WHERE b.course_id=c.id AND b.is_active=1) AS batch_count FROM courses c LEFT JOIN users u ON u.id=c.coordinator_id WHERE c.is_active=1');
$icons=['11TH'=>'fa-book-open','12TH'=>'fa-graduation-cap','JEE'=>'fa-flask','NEET'=>'fa-heartbeat','CET'=>'fa-calculator'];
?>
<div class="page-banner"><div class="container"><h1>Our Courses</h1><p>Expert coaching for all Science streams and competitive exams</p></div></div>
<section class="section"><div class="container">
<div class="courses-grid" style="grid-template-columns:repeat(auto-fill,minmax(280px,1fr));">
<?php foreach($courses as $c): $icon=$icons[$c['code']]??'fa-book'; ?>
<div class="course-card">
  <div class="course-icon"><i class="fas <?=$icon?>"></i></div>
  <h3><?=e($c['name'])?></h3><p><?=e($c['description'])?></p>
  <div class="course-meta">
    <?php if($c['duration']): ?><span class="badge"><i class="fas fa-clock"></i> <?=e($c['duration'])?></span><?php endif; ?>
    <?php if($c['fees']): ?><span class="badge badge-gold">₹<?=number_format($c['fees'],0)?></span><?php endif; ?>
    <?php if($c['batch_count']): ?><span class="badge"><i class="fas fa-users"></i> <?=$c['batch_count']?> Batch<?=$c['batch_count']>1?'es':''?></span><?php endif; ?>
  </div>
  <?php if($c['coordinator_name']): ?><p class="small text-gray mb-2"><i class="fas fa-user-tie"></i> Coordinator: <?=e($c['coordinator_name'])?></p><?php endif; ?>
  <a href="<?=SITE_URL?>/enquiry.php?course=<?=e($c['code'])?>" class="btn btn-primary" style="padding:9px 20px;font-size:13px;"><i class="fas fa-paper-plane"></i> Enquire Now</a>
</div>
<?php endforeach; ?>
</div></div></section>
<section class="cta-section"><div class="container"><h2>Ready to Enroll?</h2><p>Register now to apply for admission and secure your seat in upcoming batches.</p><a href="<?=SITE_URL?>/register.php" class="btn" style="background:#fff;color:var(--navy);font-weight:700;"><i class="fas fa-user-plus"></i> Register &amp; Apply</a></div></section>
<?php require_once 'includes/footer.php'; ?>
