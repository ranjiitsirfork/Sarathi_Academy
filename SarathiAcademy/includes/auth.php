<?php
// ============================================================
//  SarathiAcademy – Auth & Session Helpers (mysqli)
// ============================================================
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/config.php';

// ── mysqli helper functions ───────────────────────────────────────────────────
function dbFetch($sql, $params = []) {
    $db   = getDB();
    $stmt = mysqli_prepare($db, $sql);
    if ($params) {
        $types = str_repeat('s', count($params));
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($res);
}

function dbFetchAll($sql, $params = []) {
    $db   = getDB();
    $stmt = mysqli_prepare($db, $sql);
    if ($params) {
        $types = str_repeat('s', count($params));
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $res  = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($res)) $rows[] = $row;
    return $rows;
}

function dbExecute($sql, $params = []) {
    $db   = getDB();
    $stmt = mysqli_prepare($db, $sql);
    if ($params) {
        $types = str_repeat('s', count($params));
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    return mysqli_affected_rows($db);
}

function dbInsert($sql, $params = []) {
    $db   = getDB();
    $stmt = mysqli_prepare($db, $sql);
    if ($params) {
        $types = str_repeat('s', count($params));
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    return mysqli_insert_id($db);
}

function dbCount($sql, $params = []) {
    $db   = getDB();
    $stmt = mysqli_prepare($db, $sql);
    if ($params) {
        $types = str_repeat('s', count($params));
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_row($res);
    return (int)($row[0] ?? 0);
}

// ── Auth functions ────────────────────────────────────────────────────────────
function loginUser($email, $password) {
    $user = dbFetch('SELECT * FROM users WHERE email=? AND is_active=1 LIMIT 1', [$email]);
    if ($user && password_verify($password, $user['password'])) {
        unset($user['password']);
        $_SESSION['user'] = $user;
        return $user;
    }
    return false;
}

function logoutUser() {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time()-42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

function currentUser() { return $_SESSION['user'] ?? null; }
function isAdmin()     { return (currentUser()['role'] ?? '') === 'admin'; }
function isStaff()     { return in_array(currentUser()['role'] ?? '', ['admin','staff']); }
function isStudent()   { return (currentUser()['role'] ?? '') === 'student'; }
function isLoggedIn()  { return currentUser() !== null; }

function requireAdmin()   { if (!isAdmin())   { header('Location:'.SITE_URL.'/login.php?err=access'); exit; } }
function requireStaff()   { if (!isStaff())   { header('Location:'.SITE_URL.'/login.php?err=access'); exit; } }
function requireLogin()   { if (!isLoggedIn()){ header('Location:'.SITE_URL.'/login.php?err=login');  exit; } }

function setFlash($type, $msg) { $_SESSION['flash'] = ['type'=>$type,'msg'=>$msg]; }
function getFlash() {
    if (isset($_SESSION['flash'])) { $f=$_SESSION['flash']; unset($_SESSION['flash']); return $f; }
    return null;
}

function e($v)        { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function redirect($u) { header('Location:'.$u); exit; }
