<?php
include("../Connection/database.php"); // Database connection
session_start(); // Start session

try {
    if ($_SERVER["REQUEST_METHOD"] == 'POST') {
        // Assign Values from POST
        $EID = $_POST['EID'] ?? '';
        $fn = $_POST['fn'] ?? '';
        $mid = $_POST['mid'] ?? ''; // Middle Name added
        $ln = $_POST['ln'] ?? '';
        $bday = !empty($_POST['birthday']) ? date('Y-m-d', strtotime($_POST['birthday'])) : null;
        $gender = $_POST['gender'] ?? '';
        $email = $_POST['email'] ?? '';
        $phonenumber = $_POST['phone_num'] ?? '';

        // Address Fields
        $street = $_POST['street'] ?? '';
        $city = $_POST['city'] ?? '';
        $province = $_POST['province'] ?? '';
        $zipcode = $_POST['zipcode'] ?? '';

        // Role
        $role = $_POST['role'] ?? '';

        // Validate Required Fields
        if (empty($EID) || empty($fn) || empty($ln) || empty($bday) || empty($gender) || empty($email) || empty($phonenumber) ||
            empty($street) || empty($city) || empty($province) || empty($zipcode) || empty($role)) {
            $_SESSION['errors'] = "Missing required fields.";
            header('Location: ../Pages/Employee.php');
            exit;
        }

        // Validate Phone Number
        if (strlen($phonenumber) !== 11 || !ctype_digit($phonenumber)) {
            $_SESSION['errors'] = "Phone number must be exactly 11 digits.";
            header('Location: ../Pages/Employee.php');
            exit;
        }

        // Check for Existing Records
        function record_exists($conn, $table, $column, $value, $EID = null) {
            $query = "SELECT 1 FROM $table WHERE $column = ?";
            if ($EID !== null) {
                $query .= " AND EID != ?";
            }
            $stmt = $conn->prepare($query);
            if ($EID !== null) {
                $stmt->bind_param("ss", $value, $EID);
            } else {
                $stmt->bind_param("s", $value);
            }
            $stmt->execute();
            return $stmt->get_result()->num_rows > 0;
        }

        if (record_exists($conn, 'employees', 'email', $email, $EID)) {
            $_SESSION['errors'] = "Email already exists.";
            header('Location: ../Pages/Employee.php');
            exit;
        }

        if (record_exists($conn, 'employees', 'phone_num', $phonenumber, $EID)) {
            $_SESSION['errors'] = "Phone number already exists.";
            header('Location: ../Pages/Employee.php');
            exit;
        }

        // Start Transaction
        $conn->begin_transaction();

        // Update Employee Information
        $stmt = $conn->prepare("
            UPDATE employees 
            SET fn = ?, mid = ?, ln = ?, gender = ?, bday = ?, email = ?, phone_num = ?, RID = ?
            WHERE EID = ? LIMIT 1
        ");
        $stmt->bind_param("sssssssss", $fn, $mid, $ln, $gender, $bday, $email, $phonenumber, $role, $EID);

        if (!$stmt->execute()) {
            throw new Exception("Error updating employee data.");
        }

        // Retrieve AID (Address ID) using EID
        $stmt = $conn->prepare("SELECT AID FROM address WHERE EID = ? LIMIT 1");
        $stmt->bind_param("s", $EID);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $AID = $row['AID'] ?? null;

        if (!$AID) {
            throw new Exception("Error fetching address ID.");
        }

        // Update Address Information
        $stmt = $conn->prepare("
            UPDATE address 
            SET street = ?, city = ?, province = ?, zipcode = ? 
            WHERE AID = ? LIMIT 1
        ");
        $stmt->bind_param("sssss", $street, $city, $province, $zipcode, $AID);

        if (!$stmt->execute()) {
            throw new Exception("Error updating address data.");
        }

        // Commit Transaction
        $conn->commit();

        $_SESSION['success'] = "Employee details successfully updated!";
        header('Location: ../Pages/Employee.php');
        exit;
    }
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    $_SESSION['errors'] = "Error: " . $e->getMessage();
    header('Location: ../Pages/Employee.php');
    exit;
}
?>
