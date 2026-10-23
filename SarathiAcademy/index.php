<?php
$pageTitle = 'Home';
require_once 'includes/header.php';
$courses      = dbFetchAll('SELECT * FROM courses WHERE is_active=1');
$testimonials = dbFetchAll('SELECT * FROM testimonials WHERE is_active=1 LIMIT 3');
$stats = [
  ['num'=>'1200+','lbl'=>'Students Enrolled'],
  ['num'=>'15+',  'lbl'=>'Expert Faculty'],
  ['num'=>'98%',  'lbl'=>'Success Rate'],
  ['num'=>'10+',  'lbl'=>'Years of Excellence'],
];
$icons = ['11TH'=>'fa-book-open','12TH'=>'fa-graduation-cap','JEE'=>'fa-flask','NEET'=>'fa-heartbeat','CET'=>'fa-calculator'];
?>
<section class="hero">
  <div class="container hero-inner">
    <div class="hero-text">
      <h1>Shape Your Future with <span>SarathiAcademy</span></h1>
      <p>Expert coaching for 11th, 12th, JEE, NEET & MHT-CET. Join thousands of successful students who trusted us with their academic journey.</p>
      <div class="hero-btns">
        <a href="<?= SITE_URL ?>/register.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Enroll Now</a>
        <a href="<?= SITE_URL ?>/courses.php"  class="btn btn-secondary"><i class="fas fa-book"></i> View Courses</a>
      </div>
      <div class="hero-stats">
        <?php foreach ($stats as $s): ?>
        <div class="stat-item">
          <div class="stat-num"><?= e($s['num']) ?></div>
          <div class="stat-lbl"><?= e($s['lbl']) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="hero-image">
      <img src="<?= SITE_URL ?>/assets/images/logo.png" alt="SarathiAcademy">
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-header">
      <h2>Our Courses</h2>
      <div class="underline"></div>
      <p>Comprehensive coaching programmes designed to ensure your success in academics and competitive exams.</p>
    </div>
    <div class="courses-grid">
      <?php foreach ($courses as $c): $icon = $icons[$c['code']] ?? 'fa-book'; ?>
      <div class="course-card">
        <div class="course-icon"><i class="fas <?= $icon ?>"></i></div>
        <h3><?= e($c['name']) ?></h3>
        <p><?= e(mb_strimwidth($c['description'],0,90,'…')) ?></p>
        <div class="course-meta">
          <?php if ($c['duration']): ?><span class="badge"><i class="fas fa-clock"></i> <?= e($c['duration']) ?></span><?php endif; ?>
          <?php if ($c['fees']): ?><span class="badge badge-gold">₹<?= number_format($c['fees'],0) ?></span><?php endif; ?>
        </div>
        <a href="<?= SITE_URL ?>/courses.php" class="btn btn-primary" style="font-size:13px;padding:8px 18px;border-radius:20px;">Learn More</a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section-alt">
  <div class="container">
    <div class="section-header">
      <h2>Why Choose SarathiAcademy?</h2>
      <div class="underline"></div>
    </div>
    <div class="features-grid">
      <?php
      $features = [
        ['icon'=>'fa-chalkboard-teacher','title'=>'Expert Faculty',    'desc'=>'Highly qualified teachers with years of experience in competitive exam coaching.'],
        ['icon'=>'fa-users',             'title'=>'Small Batches',     'desc'=>'Limited batch size ensures personal attention for every student.'],
        ['icon'=>'fa-clipboard-list',    'title'=>'Regular Tests',     'desc'=>'Weekly tests and mock exams to track progress and build exam readiness.'],
        ['icon'=>'fa-book-reader',       'title'=>'Study Material',    'desc'=>'Comprehensive notes, practice papers and digital resources provided.'],
        ['icon'=>'fa-chart-line',        'title'=>'Result Tracking',   'desc'=>'Detailed performance analytics to identify strengths and improvement areas.'],
        ['icon'=>'fa-headset',           'title'=>'Doubt Sessions',    'desc'=>'Dedicated doubt-clearing sessions with faculty after every lecture.'],
      ];
      foreach ($features as $f): ?>
      <div class="feature-card">
        <div class="feature-icon"><i class="fas <?= $f['icon'] ?>"></i></div>
        <h4><?= $f['title'] ?></h4>
        <p><?= $f['desc'] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php if ($testimonials): ?>
<section class="section">
  <div class="container">
    <div class="section-header"><h2>Student Success Stories</h2><div class="underline"></div></div>
    <div class="testimonials-grid">
      <?php foreach ($testimonials as $t): ?>
      <div class="testi-card">
        <p><?= e($t['content']) ?></p>
        <div class="testi-name"><?= e($t['student_name']) ?></div>
        <div class="testi-score"><?= e($t['score']) ?> &bull; <?= e($t['course']) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="cta-section">
  <div class="container">
    <h2>Ready to Begin Your Journey?</h2>
    <p>Register today and take the first step towards your dream career in science & engineering.</p>
    <a href="<?= SITE_URL ?>/register.php" class="btn" style="background:#fff;color:var(--navy);font-weight:700;">
      <i class="fas fa-user-plus"></i> Register Now — It's Free!
    </a>
  </div>
</section>
<?php require_once 'includes/footer.php'; ?>
