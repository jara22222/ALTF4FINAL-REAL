<?php
include('../Connection/database.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['EID']) || empty($_POST['EID'])) {
        $_SESSION['errors'] = 'Invalid employee ID.';
        header('Location: ../Pages/Employee.php');
        exit();
    }

    $eid = $_POST['EID'];

    // Fetch UID first
    $query = "SELECT UID FROM employees WHERE EID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $eid);
    $stmt->execute();
    $stmt->bind_result($uid);
    $stmt->fetch();
    $stmt->close();

    if (!$uid) {
        $_SESSION['errors'] = "Employee not found.";
        header('Location: ../Pages/Employee.php');
        exit();
    }

    // Fetch AID from the address table (if applicable)
    $query = "SELECT AID FROM address WHERE EID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $eid);
    $stmt->execute();
    $stmt->bind_result($aid);
    $stmt->fetch();
    $stmt->close();

    // Start a transaction
    $conn->begin_transaction();

    try {
        // 1️⃣ Delete from employees table first
        $query = "DELETE FROM employees WHERE EID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $eid);
        $stmt->execute();
        $stmt->close();

        // 2️⃣ Delete from users table
        $query = "DELETE FROM users WHERE UID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $uid);
        $stmt->execute();
        $stmt->close();

        // 3️⃣ Delete from address table (only if AID exists)
        
            $query = "DELETE FROM address WHERE EID = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $eid);
            $stmt->execute();
            $stmt->close();
        

        // ✅ Commit transaction
        $conn->commit();

        header('Location: ../Pages/Employee.php');
        exit();
    } catch (Exception $e) {
        // ❌ Rollback if any deletion fails
        $conn->rollback();
        $_SESSION['errors'] = 'Error deleting employee: ' . $e->getMessage();
        header('Location: ../Pages/Employee.php');
        exit();
    }
} else {
    header('Location: ../Pages/Employee.php');
    exit();
}
?>
