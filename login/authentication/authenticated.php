<?php
session_start();


if($_SESSION['access_token']){
    $token = $_SESSION['access_token'];
}
else if($_COOKIE['access_token']){
    $token = $_COOKIE['access_token'];
}
else{
    header('Location: ../login.php');
}
?>