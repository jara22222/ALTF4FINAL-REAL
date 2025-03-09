<?php
session_start();

if (isset($_SESSION["access_token"]) or isset($_COOKIE["access_token"])) {
   header("Location: ../index.php");
    exit;
}
?>