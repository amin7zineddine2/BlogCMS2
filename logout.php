<?php
require_once 'includes/auth.php';

session_destroy();
$_SESSION = array();

header('Location: login.php');
exit();
?>