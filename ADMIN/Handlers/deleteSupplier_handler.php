<?php
include('../Connection/database.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['SID']) || empty($_POST['SID'])) {
        $_SESSION['errors'] = 'Invalid Supplier ID.';
        header('Location: ../Pages/Supplier.php');
        exit();
    }

    $sid = $_POST['SID'];

    // Fetch SID first to confirm existence
    $query = "SELECT SID FROM suppliers WHERE SID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $sid);
    $stmt->execute();
    $stmt->bind_result($sid);
    $stmt->fetch();
    $stmt->close();

    if (!$sid) {
        $_SESSION['errors'] = "Supplier not found.";
        header('Location: ../Pages/Supplier.php');
        exit();
    }

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Delete from suppliers table
        $query = "DELETE FROM suppliers WHERE SID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $sid);
        $stmt->execute();
        $stmt->close();

        // Delete from suppliers table
        $query = "DELETE FROM address WHERE SID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $sid);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $conn->commit();
        $_SESSION['success'] = "Supplier deleted successfully!";
        header('Location: ../Pages/Supplier.php');
        exit();

    } catch (Exception $e) {
        // Rollback if deletion fails
        $conn->rollback();
        $_SESSION['errors'] = 'Error deleting supplier: ' . $e->getMessage();
        header('Location: ../Pages/Supplier.php');
        exit();
    }
} else {
    header('Location: ../Pages/Supplier.php');
    exit();
}
?>
