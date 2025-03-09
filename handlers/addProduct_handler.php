<?php
include("../Connection/database.php"); // Include database connection
session_start(); // Start session

try {
    if ($_SERVER["REQUEST_METHOD"] == 'POST') {

        function generatePID($conn, $prefix = 'PD') {
            // Query to get the latest PID
            $query = "SELECT PID FROM products WHERE PID LIKE '$prefix-%' ORDER BY CAST(SUBSTRING(PID, 5) AS UNSIGNED) DESC LIMIT 1";
            $result = mysqli_query($conn, $query);
        
            if ($row = mysqli_fetch_assoc($result)) {
                // Extract the numeric part and increment
                $lastNumber = intval(substr($row['PID'], 4)) + 1;
            } else {
                // If no existing PID, start from 1
                $lastNumber = 1;
            }
        
            // Format with leading zeros (e.g., PID-001)
            return $prefix . '-' . str_pad($lastNumber, 3, '0', STR_PAD_LEFT);
        }
        
        $PID = generatePID($conn);
        $product_name = $_POST['product_name'] ?? '';
        $category_id = $_POST['CID'] ?? '';
        $price = $_POST['price'] ?? '';
        $supplier_id = $_POST['SID'] ?? '';
        $date_added = $_POST['date'] ?? '';
        $product_description = $_POST['product_description'] ?? '';

        // Check if product name exists
        function product_exist($product_name) {
            global $conn;
            $stmnt = $conn->prepare("SELECT PID FROM products WHERE product_name = ?");
            $stmnt->bind_param('s', $product_name);
            $stmnt->execute();
            $result = $stmnt->get_result();
            return $result->num_rows > 0;
        }

        if (product_exist($product_name)) {
            $_SESSION['errors'] = 'Product name already exists';
            header('Location: ../Pages/product.php');
            exit();
        }

        // Handle image upload
if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
  $img_tmp = $_FILES['img']['tmp_name'];
  $img_type = $_FILES['img']['type'];

  // Read the binary image data
  $img_data = file_get_contents($img_tmp);

  // Validate allowed image types
  $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
  if (!in_array($img_type, $allowed_types)) {
      $_SESSION['errors'] = "Invalid image format. Allowed: JPG, PNG, GIF.";
      header('Location: ../Pages/product.php');
      exit();
  }

  // Prepare SQL to insert binary image data
  $stmnt2 = $conn->prepare("INSERT INTO products (PID, product_name, CID, price, SID, date, product_description, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmnt2->bind_param("sssssssb", $PID, $product_name, $category_id, $price, $supplier_id, $date_added, $product_description, $null); // Use NULL for BLOB initially

  // Send image data as BLOB
  $stmnt2->send_long_data(7, $img_data); // 7th parameter is 'image'

  if ($stmnt2->execute()) {
      $_SESSION['success'] = "Product added successfully!";
      header("Location: ../Pages/product.php");
      exit();
  } else {
      $_SESSION['errors'] = "Database error: " . $conn->error;
      header("Location: ../Pages/product.php");
      exit();
  }
} else {
  $_SESSION['errors'] = "No image uploaded.";
  header("Location: ../Pages/product.php");
  exit();
}

    }
} catch (Exception $e) {
    $_SESSION['errors'] = "Error: " . $e->getMessage();
    header('Location: ../Pages/product.php');
    exit();
}
?>
