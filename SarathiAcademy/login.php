<?php
$pageTitle = 'Login';
require_once 'includes/auth.php';
if (isLoggedIn()) {
    $role = currentUser()['role'];
    redirect(($role==='admin'||$role==='staff') ? ADMIN_URL.'/' : SITE_URL.'/student/dashboard.php');
}
$error = '';
if ($_GET['err']??''==='access') $error='Access denied. Please log in with appropriate credentials.';
if ($_GET['err']??''==='login')  $error='Please log in to continue.';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $email=$_POST['email']??''; $password=$_POST['password']??'';
    if (!$email||!$password) { $error='Please enter email and password.'; }
    else {
        $user = loginUser(trim($email), $password);
        if ($user) redirect(in_array($user['role'],['admin','staff']) ? ADMIN_URL.'/' : SITE_URL.'/student/dashboard.php');
        else $error='Invalid email or password. Please try again.';
    }
}
?><!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login | SarathiAcademy</title>
<link rel="icon" type="image/png" href="<?= SITE_URL ?>/assets/images/logo.png">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head><body>
<div class="auth-wrap">
  <div class="auth-box">
    <div class="logo-wrap"><a href="<?= SITE_URL ?>"><img src="<?= SITE_URL ?>/assets/images/logo.png" alt="Logo"></a></div>
    <h2>Welcome Back</h2>
    <p class="subtitle">Login to your SarathiAcademy account</p>
    <?php if ($error): ?><div class="flash flash-error"><?= e($error) ?></div><?php endif; ?>
    <form method="POST">
      <div class="form-group"><label>Email Address</label><input type="email" name="email" class="form-control" placeholder="your@email.com" value="<?= e($_POST['email']??'') ?>" required></div>
      <div class="form-group"><label>Password</label><input type="password" name="password" class="form-control" placeholder="••••••••" required></div>
      <button type="submit" class="btn btn-primary w-100 mt-2" style="justify-content:center;padding:12px;"><i class="fas fa-sign-in-alt"></i> Login</button>
    </form>
    <hr class="divider">
    <p class="text-center small">Don't have an account? <a href="<?= SITE_URL ?>/register.php" class="text-sky fw-600">Register here</a></p>
    <p class="text-center small mt-1"><a href="<?= SITE_URL ?>/" class="text-gray"><i class="fas fa-arrow-left"></i> Back to Home</a></p>
    <div class="mt-3 small text-gray text-center" style="background:#f0f7ff;padding:10px;border-radius:6px;">
      <strong>Demo Login</strong><br>Admin: admin@SarathiAcademy.com / Admin@1234<br>Staff: rahul@SarathiAcademy.com / Rahul@1234
    </div>
  </div>
</div>
</body></html>
