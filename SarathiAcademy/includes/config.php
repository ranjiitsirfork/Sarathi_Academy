<?php
// ============================================================
//  SarathiAcademy – Database Configuration
//  File: includes/config.php
// ============================================================

// ---- Database credentials (edit these if needed) ----
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'sarathi_academy';

define('SITE_NAME',   'SarathiAcademy');
define('SITE_URL',    'http://localhost/SarathiAcademy');
define('ADMIN_URL',   SITE_URL . '/admin');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('UPLOAD_URL',  SITE_URL . '/uploads/');

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die('<div style="font-family:Arial,sans-serif;padding:30px;background:#fff3f3;border:2px solid #f88;margin:40px auto;max-width:600px;border-radius:10px;">
        <h2 style="color:#c00">&#10060; Database Connection Failed</h2>
        <p><strong>Error:</strong> ' . $conn->connect_error . '</p>
        <p>Please check:</p>
        <ul>
          <li>XAMPP MySQL service is running (green in XAMPP Control Panel)</li>
          <li>You have imported <strong>naturespure_db.sql</strong> in phpMyAdmin</li>
          <li>DB_NAME in config.php is <strong>naturespure_db</strong></li>
        </ul>
    </div>');
}
$conn->set_charset('utf8mb4');

// ---- mysqli connection (singleton) ----
function getDB() {
    global $db_host, $db_user, $db_pass, $db_name;
    static $conn = null;
    if ($conn === null) {
        $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
        if (!$conn) {
            die('<div style="font-family:sans-serif;padding:40px;color:#c0392b;">
                 <h2>Database Connection Error</h2>
                 <p>' . mysqli_connect_error() . '</p>
                 <p>Please ensure MySQL is running in XAMPP and the database
                 <strong>sarathi_academy</strong> has been imported.</p>
                 </div>');
        }
        mysqli_set_charset($conn, 'utf8mb4');
    }
    return $conn;
}
