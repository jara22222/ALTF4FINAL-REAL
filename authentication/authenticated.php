<?php
session_start();
if (isset($_SESSION['access_token'])) {
    $token = $_SESSION['access_token'];
} elseif (isset($_COOKIE['access_token'])) {
    $token = $_COOKIE['access_token'];
} elseif (basename($_SERVER['PHP_SELF']) !== "login.php") { 
    header('Location: ../pages/login.php');
    exit;
}

?>