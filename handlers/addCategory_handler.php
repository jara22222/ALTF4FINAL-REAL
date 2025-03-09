<?php
include("../Connection/database.php"); // Include database connection
session_start(); // Start session

try {
    if ($_SERVER["REQUEST_METHOD"] == 'POST') {

        function generateCID($conn, $prefix = 'CD') {
            // Query to get the latest CD-ID
            $query = "SELECT CID FROM categories WHERE CID LIKE '$prefix-%' ORDER BY CAST(SUBSTRING(CID, 4) AS UNSIGNED) DESC LIMIT 1";
            $result = mysqli_query($conn, $query);
        
            if ($row = mysqli_fetch_assoc($result)) {
                // Extract the numeric part and increment
                $lastNumber = intval(substr($row['CID'], 3)) + 1;
            } else {
                // If no existing CD-ID, start from 1
                $lastNumber = 1;
            }
        
            // Format with leading zeros (e.g., CD-001)
            return $prefix . '-' . str_pad($lastNumber, 3, '0', STR_PAD_LEFT);
        }
        
        $CID = generateCID($conn);
        $category_name = $_POST['category_name'] ?? '';
        $description = $_POST['description'] ?? '';

        // Check if category name exists
        function category_exist($category_name) {
            global $conn;
            $stmnt = $conn->prepare("SELECT CID FROM categories WHERE category_name = ?");
            $stmnt->bind_param('s', $category_name);
            $stmnt->execute();
            $result = $stmnt->get_result();
            return $result->num_rows > 0;
        }

        // Check if CID exists
        function CID_exist($CID) {
            global $conn;
            $stmnt = $conn->prepare("SELECT CID FROM categories WHERE CID = ?");
            $stmnt->bind_param('s', $CID);
            $stmnt->execute();
            $result = $stmnt->get_result();
            return $result->num_rows > 0;
        }

        // Validate category name existence
        if (category_exist($category_name)) {
            $_SESSION['errors'] = 'Category already exists';
            header('Location: ../Pages/Category.php');
            exit();
        }

        if (CID_exist($CID)) {
            $_SESSION['errors'] = 'CID already exists';
            header('Location: ../Pages/Category.php');
            exit();
        }

        $stmnt2 = $conn->prepare("INSERT INTO categories (CID, category_name, description) VALUES (?, ?, ?)");
        $stmnt2->bind_param("sss", $CID, $category_name, $description);

        if (!$stmnt2->execute()) {
            $_SESSION['errors'] = "Error inserting category data.";
            header('Location: ../Pages/Category.php');
            exit;
        }
        else {
            // Success message
            $_SESSION['success'] = "Category successfully added!";
            header('Location: ../Pages/Category.php');
            exit;
        }
    }
} catch (Exception $e) {
    $_SESSION['errors'] = "Error: " . $e->getMessage();
    header('Location: ../Pages/Category.php');
    exit;
}
?>
