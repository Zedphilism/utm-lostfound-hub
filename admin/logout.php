<?php
// File: admin/logout.php
// Start session, clear & destroy
session_start();
session_unset();
session_destroy();
// Redirect to login
header('Location: login.php');
exit;