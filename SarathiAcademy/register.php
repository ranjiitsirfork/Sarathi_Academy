<?php
$pageTitle = 'Register';
require_once 'includes/auth.php';
if (isLoggedIn()) redirect(SITE_URL.'/student/dashboard.php');
$errors=[];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name=$_POST['name']??''; $email=$_POST['email']??''; $phone=$_POST['phone']??'';
    $password=$_POST['password']??''; $confirm=$_POST['confirm']??'';
    if (!trim($name))                              $errors[]='Full name is required.';
    if (!filter_var(trim($email),FILTER_VALIDATE_EMAIL)) $errors[]='Valid email is required.';
    if (strlen($password)<6)                       $errors[]='Password must be at least 6 characters.';
    if ($password!==$confirm)                      $errors[]='Passwords do not match.';
    if (!$errors) {
        $existing = dbFetch('SELECT id FROM users WHERE email=?',[trim($email)]);
        if ($existing) { $errors[]='This email is already registered. Please login.'; }
        else {
            $hash=password_hash($password,PASSWORD_BCRYPT);
            dbInsert('INSERT INTO users(name,email,phone,password,role) VALUES(?,?,?,?,?)',[trim($name),trim($email),trim($phone),$hash,'student']);
            loginUser(trim($email),$password);
            redirect(SITE_URL.'/student/dashboard.php');
        }
    }
}
?><!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Register | SarathiAcademy</title>
<link rel="icon" type="image/png" href="<?= SITE_URL ?>/assets/images/logo.png">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head><body>
<div class="auth-wrap">
  <div class="auth-box" style="max-width:500px;">
    <div class="logo-wrap"><a href="<?= SITE_URL ?>"><img src="<?= SITE_URL ?>/assets/images/logo.png" alt="Logo"></a></div>
    <h2>Create Account</h2>
    <p class="subtitle">Join SarathiAcademy – Learn · Grow · Achieve</p>
    <?php if ($errors): ?><div class="flash flash-error"><?= implode('<br>',array_map('e',$errors)) ?></div><?php endif; ?>
    <form method="POST">
      <div class="form-group"><label>Full Name *</label><input type="text" name="name" class="form-control" value="<?= e($_POST['name']??'') ?>" required></div>
      <div class="form-group"><label>Email Address *</label><input type="email" name="email" class="form-control" value="<?= e($_POST['email']??'') ?>" required></div>
      <div class="form-group"><label>Phone Number</label><input type="text" name="phone" class="form-control" value="<?= e($_POST['phone']??'') ?>"></div>
      <div class="form-row">
        <div class="form-group"><label>Password *</label><input type="password" name="password" class="form-control" required></div>
        <div class="form-group"><label>Confirm Password *</label><input type="password" name="confirm" class="form-control" required></div>
      </div>
      <button type="submit" class="btn btn-primary w-100 mt-2" style="justify-content:center;padding:12px;"><i class="fas fa-user-plus"></i> Create Account</button>
    </form>
    <hr class="divider">
    <p class="text-center small">Already have an account? <a href="<?= SITE_URL ?>/login.php" class="text-sky fw-600">Login here</a></p>
    <p class="text-center small mt-1"><a href="<?= SITE_URL ?>/" class="text-gray"><i class="fas fa-arrow-left"></i> Back to Home</a></p>
  </div>
</div>
</body></html>
