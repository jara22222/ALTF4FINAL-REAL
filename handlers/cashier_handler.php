<?php
include '../Connection/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   // Ensure this contains the correct DB connection
   session_start();
   
   // ✅ Get cashier details
   $cashierName = $_POST['cashier'] ?? 'Unknown';
   
   // ✅ Get order details
   $paymentMethod = $_POST['paymentMethod'] ?? 'Cash';
   $subtotal = $_POST['subtotal'] ?? 0;
   $cashGiven = $_POST['cashGiven'] ?? 0;
   $changeAmount = $_POST['changeAmount'] ?? 0;
   $gcashReference = $_POST['gcashReference'] ?? 'N/A';

   // Get product details as comma-separated strings 
   $productNamesStr = $_POST['products'] ?? '';
   $quantitiesStr = $_POST['totalQty'] ?? '';
   $sizesStr = $_POST['size'] ?? 'Regular';
   $addonsStr = $_POST['addons'] ?? 'N/A';
   
   // Split into arrays
   $productNames = explode(',', $productNamesStr);
   $quantities = explode(',', $quantitiesStr);
   $sizes = explode(',', $sizesStr);
   $addons = explode(',', $addonsStr);
   
   // Trim whitespace from each element
   $productNames = array_map('trim', $productNames);
   $quantities = array_map('trim', $quantities);
   $sizes = array_map('trim', $sizes);
   $addons = array_map('trim', $addons);
   
   // Verify we have products
   if (empty($productNames[0])) {
       $_SESSION['error'] = "No products selected.";
       header("Location: ../pages/Cashierdashboard.php");
       exit();
   }
    $fullname =$_SESSION['fullname'];
   // ✅ Step 2: Insert into `orders`
   $stmt = $conn->prepare("INSERT INTO orders (CashierName, TotalAmount) VALUES (?, ?)");
   $stmt->bind_param("sd", $fullname, $subtotal);
   $stmt->execute();
   $orderID = $stmt->insert_id;
   $stmt->close();
   
   // Process each product
   for ($i = 0; $i < count($productNames); $i++) {
       $currentProduct = $productNames[$i];
       $currentQuantity = $quantities[$i] ?? 1;     // Default to 1 if not specified
       $currentSize = $sizes[$i] ?? 'Regular';      // Default to Regular if not specified
       $currentAddon = $addons[$i] ?? 'None';       // Default to None if not specified
       
       // Get PID from products table - using a local variable
       $currentPID = "";  // Local PID variable for this iteration
       $stmt = $conn->prepare("SELECT PID FROM products WHERE product_name = ?");
       $stmt->bind_param("s", $currentProduct);
       $stmt->execute();
       $stmt->bind_result($currentPID);
       $stmt->fetch();
       $stmt->close();
       
       if (empty($currentPID)) {
           // Log error but continue with other products
           continue;
       }
       
       // Insert ordered item - using the local PID variable
       $stmt = $conn->prepare("INSERT INTO ordered_items (OrderID, PID, size, qty) VALUES (?, ?, ?, ?)");
       $stmt->bind_param("issi", $orderID, $currentPID, $currentSize, $currentQuantity);
       $stmt->execute();
       $orderedItemID = $stmt->insert_id;
       $stmt->close();
       
       // Process add-on if not 'None'
       if ($currentAddon !== 'None') {
           $currentADID = ""; // Local ADID variable for this iteration
           $stmt = $conn->prepare("SELECT ADID FROM adds_on WHERE add_name = ?");
           $stmt->bind_param("s", $currentAddon);
           $stmt->execute();
           $stmt->bind_result($currentADID);
           $stmt->fetch();
           $stmt->close();
           
           if (!empty($currentADID)) {
               // Insert add-on into `order_addons`
               $stmt = $conn->prepare("INSERT INTO order_addons (OID, ADID) VALUES (?, ?)");
               $stmt->bind_param("is", $orderedItemID, $currentADID);
               $stmt->execute();
               $stmt->close();
           }
       }
   }

   // ✅ Step 5: Insert into `payments`
   $stmt = $conn->prepare("INSERT INTO payments (OrderID, AmountPaid, PaymentMethod) VALUES (?, ?, ?)");
   $stmt->bind_param("ids", $orderID, $cashGiven, $paymentMethod);
   $stmt->execute();
   $stmt->close();

   // ✅ Redirect with success message
   $_SESSION['success'] = "Order #$orderID has been placed successfully!";
   header("Location: ../pages/Cashierdashboard.php");
   exit();
} else {
   $_SESSION['error'] = "Invalid request.";
    header("Location: ../pages/Cashierdashboard.php");
   exit();
}
?>