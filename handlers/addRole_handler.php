<?php
include("../Connection/database.php"); // Include database connection
session_start(); // Start session

try {
    if ($_SERVER["REQUEST_METHOD"] == 'POST') {

        function generateRID($conn, $prefix = 'RD') {
            // Query to get the latest RD-ID
            $query = "SELECT RID FROM roles WHERE RID LIKE '$prefix-%' ORDER BY CAST(SUBSTRING(RID, 4) AS UNSIGNED) DESC LIMIT 1";
            $result = mysqli_query($conn, $query);
        
            if ($row = mysqli_fetch_assoc($result)) {
                // Extract the numeric part and increment
                $lastNumber = intval(substr($row['RID'], 3)) + 1;
            } else {
                // If no existing RD-ID, start from 1
                $lastNumber = 1;
            }
        
            // Format with leading zeros (e.g., RD-001)
            return $prefix . '-' . str_pad($lastNumber, 3, '0', STR_PAD_LEFT);
        }
        
    
    
        $RID = generateRID($conn);
        $position = $_POST['position'] ?? '';
        $description = $_POST['description'] ?? '';

    


        // Check if position exists
        function position_exist($position) {
            global $conn;
            $stmnt = $conn->prepare("SELECT RID FROM roles WHERE rolename = ?");
            $stmnt->bind_param('s', $position);
            $stmnt->execute();
            $result = $stmnt->get_result();
            return $result->num_rows > 0;
        }

        // Check if RID exists
        function RID_exist($email) {
            global $conn;
            $stmnt = $conn->prepare("SELECT RID FROM roles WHERE RID = ?");
            $stmnt->bind_param('s', $RID);
            $stmnt->execute();
            $result = $stmnt->get_result();
            return $result->num_rows > 0;
        }

        // Validate username and email existence
        if (position_exist($position)) {
            $_SESSION['errors'] = 'Username already exists';
            header('Location: ../Pages/Roles.php');
            exit();
        }

        if (RID_exist($RID)) {
            $_SESSION['errors'] = 'RID already exists';
            header('Location: ../Pages/Roles.php');
            exit();
        }

        $stmnt2 = $conn->prepare("INSERT INTO roles (RID, rolename, description) VALUES (?, ?, ?)");
        $stmnt2->bind_param("sss", $RID, $position, $description);

        if (!$stmnt2->execute()) {
            $_SESSION['errors'] = "Error inserting user data.";
            header('Location: ../Pages/Roles.php');
            exit;
        }
        else{

    
        // Success message
        $_SESSION['success'] = "You're now registered!";
        header('Location: ../Pages/Roles.php');
        exit;
    }
    }
} catch (Exception $e) {
    $_SESSION['errors'] = "Error: " . $e->getMessage();
    header('Location: ../Pages/Roles.php');
    exit;
}
?>
