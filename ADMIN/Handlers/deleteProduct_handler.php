<?php
session_start();
include '../Connection/database.php'; // Ensure database connection is included

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $PID = $_POST['PID'];

    // Check if the product exists
    $checkProduct = $conn->prepare("SELECT * FROM products WHERE PID = ?");
    $checkProduct->bind_param("s", $PID);
    $checkProduct->execute();
    $result = $checkProduct->get_result();

    if ($result->num_rows == 0) {
        $_SESSION['error'] = "Product not found!";
        header("Location: ../Pages/Product.php");
        exit();
    }
    $checkProduct->close();

    // Delete the product
    $deleteQuery = $conn->prepare("DELETE FROM products WHERE PID = ?");
    $deleteQuery->bind_param("s", $PID);

    if ($deleteQuery->execute()) {
        $_SESSION['success'] = "Product deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting product.";
    }

    $deleteQuery->close();
    $conn->close();

    header("Location: ../Pages/Product.php");
    exit();
}
?>
