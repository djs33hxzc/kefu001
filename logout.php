<?php
require_once __DIR__ . '/includes/Auth.php';
Auth::startSession();
session_unset();
session_destroy();
header('Location: login.php');
exit;
