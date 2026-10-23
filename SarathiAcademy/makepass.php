<?php
	$hash1 = password_hash('Admin@1234', PASSWORD_DEFAULT);
	$hash2 = password_hash('Rahul@1234', PASSWORD_DEFAULT);
	$hash3 = password_hash('Priya@1234', PASSWORD_DEFAULT);

	// Directly update database
	require_once 'includes/config.php';

	$conn->query("UPDATE users SET password='$hash1' WHERE name='Super Admin'");
	$conn->query("UPDATE users SET password='$hash2' WHERE name='Rahul Patil'");
	$conn->query("UPDATE users SET password='$hash3' WHERE name='Priya Sharma'");

	echo "✅ Done! Hash1 updated: " . $hash1;
	echo "✅ Done! Hash1 updated: " . $hash2;
	echo "✅ Done! Hash1 updated: " . $hash3;
	echo "<br><br><a href='login.php'>Click here to Login</a>";
?>