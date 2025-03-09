<?php
session_start();
include '../Connection/database.php'; // Ensure database connection is included

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $PID = $_POST['PID'];
    $newStock = $_POST['stock'];
    $addedBy = $_SESSION['fullname'] ?? 'Unknown'; // Assuming you store cashier's username in session

    if ($newStock < 0) {
        $_SESSION['error'] = "Stock quantity cannot be negative.";
        header("Location: ../pages/manageproducts.php");
        exit();
    }

    // Update stock in products table
    $sql = "UPDATE products SET product_qty = product_qty + ? WHERE PID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $newStock, $PID);



    if ($stmt->execute()) {
        // Log stock-in history
        $logSql = "INSERT INTO stock_in (PID, QuantityAdded, AddedBy) VALUES (?, ?, ?)";
        $logStmt = $conn->prepare($logSql);
        $logStmt->bind_param("sis", $PID, $newStock, $addedBy);
        $logStmt->execute();
        $logStmt->close();

        $_SESSION['success'] = "Stock updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating stock.";
    }

    $stmt->close();
    $conn->close();
    header("Location: ../pages/manageproducts.php");
    exit();
}
?>
