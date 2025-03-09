<?php
include('../Connection/database.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['CID']) || empty($_POST['CID'])) {
        $_SESSION['errors'] = 'Invalid category ID.';
        header('Location: ../Pages/Category.php');
        exit();
    }

    $cid = $_POST['CID'];

    // Fetch CID first
    $query = "SELECT CID FROM categories WHERE CID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $cid);
    $stmt->execute();
    $stmt->bind_result($cid);
    $stmt->fetch();
    $stmt->close();

    if (!$cid) {
        $_SESSION['errors'] = "Category not found.";
        header('Location: ../Pages/Category.php');
        exit();
    }

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Delete from categories table
        $query = "DELETE FROM categories WHERE CID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $cid);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $conn->commit();
        $_SESSION['success'] = "Deleted category successfully!";

        header('Location: ../Pages/Category.php');
        exit();
    } catch (Exception $e) {
        // Rollback if any deletion fails
        $conn->rollback();
        $_SESSION['errors'] = 'Error deleting category: ' . $e->getMessage();
        header('Location: ../Pages/Category.php');
        exit();
    }
} else {
    header('Location: ../Pages/Category.php');
    exit();
}
?>
