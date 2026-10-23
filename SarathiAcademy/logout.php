<?php
require_once 'includes/auth.php';
logoutUser();
redirect(SITE_URL . '/login.php');
