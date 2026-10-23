<?php $pageTitle='About Us'; require_once 'includes/header.php'; ?>
<div class="page-banner"><div class="container"><h1>About SarathiAcademy</h1><p>Guiding students since 2014 from Karad, Maharashtra</p></div></div>
<section class="section"><div class="container"><div class="about-grid">
  <div class="about-img"><img src="<?=SITE_URL?>/assets/images/logo.png" alt="SarathiAcademy" style="border-radius:16px;box-shadow:0 8px 32px rgba(56,182,255,.18);padding:30px;background:#e8f6ff;"></div>
  <div class="about-text">
    <h2>Our Story</h2>
    <p>SarathiAcademy was founded with a single vision — to be the guiding charioteer (Sarathi) for every student's academic journey, just as Lord Krishna guided Arjuna in the Mahabharata.</p>
    <p>Located in the heart of Karad's Vidyanagar, we have been shaping the futures of thousands of students across 11th, 12th, JEE, NEET, and MHT-CET streams.</p>
    <p>Our approach combines rigorous academics with personal mentoring, ensuring each student grows in knowledge, confidence, and character.</p>
    <div class="about-features">
      <div class="about-feature"><i class="fas fa-check-circle"></i><div><strong>Expert Faculty</strong><p class="small text-gray">Our teachers are IIT, NIT, and university toppers with proven track records.</p></div></div>
      <div class="about-feature"><i class="fas fa-check-circle"></i><div><strong>Proven Results</strong><p class="small text-gray">Over 98% of our students clear their target exams every year.</p></div></div>
      <div class="about-feature"><i class="fas fa-check-circle"></i><div><strong>Holistic Development</strong><p class="small text-gray">Beyond exams — we nurture curiosity, discipline, and leadership.</p></div></div>
    </div>
  </div>
</div></div></section>
<section class="section section-alt"><div class="container">
  <div class="section-header"><h2>Our Values</h2><div class="underline"></div></div>
  <div class="features-grid">
    <?php foreach([['fa-star','Excellence','We never settle for mediocrity — in teaching or in results.'],['fa-heart','Dedication','Every teacher goes the extra mile for every student.'],['fa-handshake','Integrity','Transparent fees, honest guidance, and genuine care.'],['fa-lightbulb','Innovation','Modern teaching techniques blended with time-tested methods.']] as $v): ?>
    <div class="feature-card"><div class="feature-icon"><i class="fas <?=$v[0]?>"></i></div><h4><?=$v[1]?></h4><p><?=$v[2]?></p></div>
    <?php endforeach; ?>
  </div>
</div></section>
<?php require_once 'includes/footer.php'; ?>
