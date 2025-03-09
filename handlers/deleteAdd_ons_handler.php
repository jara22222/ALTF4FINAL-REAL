<?php
include('../Connection/database.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['ADID']) || empty($_POST['ADID'])) {
        $_SESSION['errors'] = 'Invalid add-on ID.';
        header('Location: ../Pages/Add_ons.php');
        exit();
    }

    $adid = $_POST['ADID'];

    // Fetch ADID first
    $query = "SELECT ADID FROM adds_on WHERE ADID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $adid);
    $stmt->execute();
    $stmt->bind_result($adid);
    $stmt->fetch();
    $stmt->close();

    if (!$adid) {
        $_SESSION['errors'] = "Add-on not found.";
        header('Location: ../Pages/Add_ons.php');
        exit();
    }

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Delete from adds_on table
        $query = "DELETE FROM adds_on WHERE ADID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $adid);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $conn->commit();
        $_SESSION['success'] = "Deleted add-on successfully!";

        header('Location: ../Pages/Add_ons.php');
        exit();
    } catch (Exception $e) {
        // Rollback if any deletion fails
        $conn->rollback();
        $_SESSION['errors'] = 'Error deleting add-on: ' . $e->getMessage();
        header('Location: ../Pages/Add_ons.php');
        exit();
    }
} else {
    header('Location: ../Pages/Add_ons.php');
    exit();
}
?>
