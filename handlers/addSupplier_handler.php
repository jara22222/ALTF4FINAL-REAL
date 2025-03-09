<?php
include("../Connection/database.php"); // Include database connection
session_start(); // Start session

try {
    if ($_SERVER["REQUEST_METHOD"] == 'POST') {

        // Auto-generate Supplier ID
        function generateSD($conn, $prefix = 'SD') {
            // Query to get the latest SD-ID
            $query = "SELECT SID FROM suppliers WHERE SID LIKE '$prefix-%' ORDER BY CAST(SUBSTRING(SID, 4) AS UNSIGNED) DESC LIMIT 1";
            $result = mysqli_query($conn, $query);
        
            if ($row = mysqli_fetch_assoc($result)) {
                // Extract numeric part and increment
                $lastNumber = intval(substr($row['SID'], 3)) + 1;
            } else {
                // If no existing SD-ID, start from 1
                $lastNumber = 1;
            }
        
            // Format with leading zeros (e.g., SD-001)
            return $prefix . '-' . str_pad($lastNumber, 3, '0', STR_PAD_LEFT);
        }
        
        function generateAD($conn, $prefix = 'AD') {
            // Query to get the latest AD-ID
            $query = "SELECT AID FROM address WHERE AID LIKE '$prefix-%' ORDER BY CAST(SUBSTRING(AID, 4) AS UNSIGNED) DESC LIMIT 1";
            $result = mysqli_query($conn, $query);
        
            if ($row = mysqli_fetch_assoc($result)) {
                // Extract numeric part and increment
                $lastNumber = intval(substr($row['AID'], 3)) + 1;
            } else {
                // If no existing AD-ID, start from 1
                $lastNumber = 1;
            }
        
            // Format with leading zeros (e.g., AD-001)
            return $prefix . '-' . str_pad($lastNumber, 3, '0', STR_PAD_LEFT);
        }
        

        $SID = generateSD($conn);
        $AID = generateAD($conn);
        $company_name = $_POST['company_name'] ?? '';
        $contact_number = $_POST['contact_number'] ?? '';
        $email = $_POST['email'] ?? '';
        $license_number = $_POST['license_number'] ?? '';
        $street = $_POST['street'] ?? '';
        $city = $_POST['city'] ?? '';
        $province = $_POST['province'] ?? '';
        $zipcode = $_POST['zipcode'] ?? '';

        // Check if company name already exists
        function company_exist($company_name) {
            global $conn;
            $stmnt = $conn->prepare("SELECT SID FROM suppliers WHERE company_name = ?");
            $stmnt->bind_param('s', $company_name);
            $stmnt->execute();
            $result = $stmnt->get_result();
            return $result->num_rows > 0;
        }

        // Check if Supplier ID exists
        function SID_exist($SID) {
            global $conn;
            $stmnt = $conn->prepare("SELECT SID FROM suppliers WHERE SID = ?");
            $stmnt->bind_param('s', $SID);
            $stmnt->execute();
            $result = $stmnt->get_result();
            return $result->num_rows > 0;
        }

        // Validate existence
        if (company_exist($company_name)) {
            $_SESSION['errors'] = 'Company name already exists';
            header('Location: ../Pages/Supplier.php');
            exit();
        }

        if (SID_exist($SID)) {
            $_SESSION['errors'] = 'Supplier ID already exists';
            header('Location: ../Pages/Supplier.php');
            exit();
        }

        // Insert into database
        $stmnt2 = $conn->prepare("INSERT INTO suppliers (SID, company_name, contact_number, email, license_number) VALUES (?, ?, ?, ?, ?)");
        $stmnt2->bind_param("sssss", $SID, $company_name, $contact_number, $email, $license_number);

         // Insert into address
            $stmnt4 = $conn->prepare("INSERT INTO address (AID, SID, street, city, province, zipcode) VALUES (?, ?, ?, ?, ?, ?)");
            $stmnt4->bind_param("sssssi", $AID, $SID, $street, $city, $province, $zipcode);


            if (!$stmnt2->execute()) {
                $_SESSION['errors'] = "Supplier insert failed: " . $stmnt2->error;
                header('Location: ../Pages/Supplier.php');
                exit;
            }
        
        if (!$stmnt4->execute()) {
            $_SESSION['errors'] = "Address insert failed: " . $stmnt4->error;
            header('Location: ../Pages/Supplier.php');
            exit;
        }

            // Success message
            $_SESSION['success'] = "Supplier added successfully!";
            header('Location: ../Pages/Supplier.php');
            exit;
        
    }
} catch (Exception $e) {
    $_SESSION['errors'] = "Error: " . $e->getMessage();
    header('Location: ../Pages/Supplier.php');
    exit;
}
?>
