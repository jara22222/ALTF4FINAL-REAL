<?php
include("../Connection/database.php"); // Include database connection
session_start(); // Start session

try {
    if ($_SERVER["REQUEST_METHOD"] == 'POST') {

        function generateADID($conn, $prefix = 'AD') {
            // Query to get the latest AD-ID
            $query = "SELECT ADID FROM adds_on WHERE ADID LIKE '$prefix-%' ORDER BY CAST(SUBSTRING(ADID, 4) AS UNSIGNED) DESC LIMIT 1";
            $result = mysqli_query($conn, $query);
        
            if ($row = mysqli_fetch_assoc($result)) {
                // Extract the numeric part and increment
                $lastNumber = intval(substr($row['ADID'], 3)) + 1;
            } else {
                // If no existing AD-ID, start from 1
                $lastNumber = 1;
            }
        
            // Format with leading zeros (e.g., AD-001)
            return $prefix . '-' . str_pad($lastNumber, 3, '0', STR_PAD_LEFT);
        }
        
        $ADID = generateADID($conn);
        $add_name = $_POST['add_name'] ?? '';
        $price = $_POST['price'] ?? '';
        $weight = $_POST['weight'] ?? '';

        $CID = $_POST['CID'];
        // Check if add-on name exists
        function addon_exist($add_name) {
            global $conn;
            $stmnt = $conn->prepare("SELECT ADID FROM adds_on WHERE add_name = ?");
            $stmnt->bind_param('s', $add_name);
            $stmnt->execute();
            $result = $stmnt->get_result();
            return $result->num_rows > 0;
        }

        // Check if ADID exists
        function ADID_exist($ADID) {
            global $conn;
            $stmnt = $conn->prepare("SELECT ADID FROM adds_on WHERE ADID = ?");
            $stmnt->bind_param('s', $ADID);
            $stmnt->execute();
            $result = $stmnt->get_result();
            return $result->num_rows > 0;
        }

        // Validate add-on existence
        if (addon_exist($add_name)) {
            $_SESSION['errors'] = 'Add-On already exists';
            header('Location: ../Pages/Add_ons.php');
            exit();
        }

        if (ADID_exist($ADID)) {
            $_SESSION['errors'] = 'ADID already exists';
            header('Location: ../Pages/Add_ons.php');
            exit();
        }

        $stmnt2 = $conn->prepare("INSERT INTO adds_on (ADID, add_name, price, weight, CID) VALUES (?, ?, ?, ?, ?)");
        $stmnt2->bind_param("ssdds", $ADID, $add_name, $price, $weight, $CID);

        if (!$stmnt2->execute()) {
            $_SESSION['errors'] = "Error inserting add-on data.";
            header('Location: ../Pages/Add_ons.php');
            exit;
        } else {
            // Success message
            $_SESSION['success'] = "Add-On successfully added!";
            header('Location: ../Pages/Add_ons.php');
            exit;
        }
    }
} catch (Exception $e) {
    $_SESSION['errors'] = "Error: " . $e->getMessage();
    header('Location: ../Pages/Add_ons.php');
    exit;
}
?>
