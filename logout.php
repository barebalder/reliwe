<?php
/**
 * LOGOUT.PHP - USER LOGOUT
 * 
 * Destroys session and redirects to home page.
 */

require_once 'config/functions.php';
ensure_session();
session_destroy();
header('Location: index.php');
exit();
?>