<?php
include('../Connection/database.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  
    $adid = $_POST['ADID'] ?? null;
    $add_name = $_POST['add_name'] ?? '';
    $price = $_POST['price'] ?? '';
    $weight = $_POST['weight'] ?? '';
    $cid = $_POST['CID'] ?? '';

    unset($_SESSION['errors']);
    unset($_SESSION['success']); // Clear previous messages

    if (empty($adid) || empty($add_name) || empty($price) || empty($weight) || empty($cid)) {
        $_SESSION['errors'] = 'All fields are required.';
        header('Location: ../Pages/Add_ons.php');
        exit();
    }

    // Check if add-on name already exists for another ADID
    function add_on_exists($add_name, $adid) {
        global $conn;
        $stmt = $conn->prepare("SELECT ADID FROM adds_on WHERE add_name = ? AND ADID != ?");
        $stmt->bind_param('ss', $add_name, $adid);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    if (add_on_exists($add_name, $adid)) {
        $_SESSION['errors'] = 'Add-on name already exists.';
        header('Location: ../Pages/Add_ons.php');
        exit();
    }

    // Update add-on details
    $query = "UPDATE adds_on SET add_name=?, price=?, weight=?, CID=? WHERE ADID=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sddss", $add_name, $price, $weight, $cid, $adid);
    
    if (!$stmt->execute()) {
        $_SESSION['errors'] = "Error updating add-on details.";
        header('Location: ../Pages/Add_ons.php');
        exit();
    }

    $stmt->close();
    $_SESSION['success'] = 'Add-on details updated successfully!';
    header('Location: ../Pages/Add_ons.php');
    exit();

} else {
    header('Location: ../Pages/Add_ons.php');
    exit();
}
?>
