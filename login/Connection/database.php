<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coffee_shop";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) { // display connection error if not successfully connected
    die("Connection Failed: " . $conn->connect_error);
}
?>
