<?php
session_start();
include '../Connection/database.php'; // Ensure database connection is included

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $PID = $_POST['PID'];
    $product_name = $_POST['product_name'];
    $CID = $_POST['CID'];
    $price = $_POST['price'];
    $SID = $_POST['SID'];
    $date = $_POST['date'];
    $product_description = $_POST['product_description'];

    // Validate price format (ensure it's a valid number)
    if (!is_numeric($price) || $price < 0) {
        $_SESSION['error'] = "Invalid price format.";
        header("Location: ../Pages/Product.php");
        exit();
    }

    // Update product details
    $sql = "UPDATE products 
            SET product_name = ?, CID = ?, price = ?, SID = ?, date = ?, product_description = ? 
            WHERE PID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsiss", $product_name, $CID, $price, $SID, $date, $product_description, $PID);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Product updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating product.";
    }

    $stmt->close();
    $conn->close();

    header("Location: ../Pages/Product.php");
    exit();
}
?>
