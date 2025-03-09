
<?php
include('../Connection/database.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['RID']) || empty($_POST['RID'])) {
        $_SESSION['errors'] = 'Invalid employee ID.';
        header('Location: ../Pages/Roles.php');
        exit();
    }

    $rid = $_POST['RID'];

    // Fetch UID first
    $query = "SELECT RID FROM roles WHERE RID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $rid);
    $stmt->execute();
    $stmt->bind_result($rid);
    $stmt->fetch();
    $stmt->close();

    if (!$rid) {
        $_SESSION['errors'] = "roles not found.";
        header('Location: ../Pages/Roles.php');
        exit();
    }

    

    // Start a transaction
    $conn->begin_transaction();

    try {
        // 1️⃣ Delete from employees table first
        $query = "DELETE FROM roles WHERE RID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $rid);
        $stmt->execute();
        $stmt->close();


    

        // ✅ Commit transaction
        $conn->commit();
        $_SESSION['success'] = "Deleted role!";

        header('Location: ../Pages/Roles.php');
        exit();

    } catch (Exception $e) {
        // ❌ Rollback if any deletion fails
        $conn->rollback();
        $_SESSION['errors'] = 'Error deleting employee: ' . $e->getMessage();
        header('Location: ../Pages/Roles.php');
        exit();
    }
} else {
    header('Location: ../Pages/Roles.php');
    exit();
}
?>
