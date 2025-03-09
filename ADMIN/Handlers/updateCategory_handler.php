<?php
include('../Connection/database.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  
  $cid = $_POST['CID'] ?? null;
  $name = $_POST['category_name']; 
  $description = $_POST['description'];
  
  unset($_SESSION['errors']);
  unset($_SESSION['success']); // Clear previous messages

  // Check if category name exists
  function category_exist($name) {
      global $conn;
      $stmnt = $conn->prepare("SELECT CID FROM categories WHERE category_name = ?");
      $stmnt->bind_param('s', $name);
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
  if (category_exist($name)) {
      $_SESSION['errors'] = 'Category name already exists';
      header('Location: ../Pages/Category.php');
      exit();
  }

  if (CID_exist($CID)) {
      $_SESSION['errors'] = 'CID already exists';
      header('Location: ../Pages/Category.php');
      exit();
  }

  // Update category details
  $queryCategory = "UPDATE categories SET category_name=?, description=? WHERE CID=?";
  $stmtCategory = $conn->prepare($queryCategory);
  $stmtCategory->bind_param("sss", $name, $description, $cid);
  
  if (!$stmtCategory->execute()) {
      $_SESSION['errors'] = "Error updating category details.";
      header('Location: ../Pages/Category.php');
      exit();
  }
  $stmtCategory->close();

  $_SESSION['success'] = 'Category details updated successfully!';
  header('Location: ../Pages/Category.php');
  exit();
  
} else {
  header('Location: ../Pages/Category.php');
  exit();
}
?>
