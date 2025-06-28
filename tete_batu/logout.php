<?php
require_once 'includes/config.php';

session_destroy();
setcookie('remember_user', '', time() - 3600, '/');
header("Location: index.php");
exit;