<?php
include("../Connection/database.php"); // Database connection
session_start(); // Start session

try {
    if ($_SERVER["REQUEST_METHOD"] == 'POST') {
        // Function to Generate Employee ID (ED-001)
        function generateEmployeeID($conn) {
            $prefix = "ED";
            $query = "SELECT EID FROM employees WHERE EID LIKE '$prefix-%' ORDER BY CAST(SUBSTRING(EID, 4) AS UNSIGNED) DESC LIMIT 1";
            $result = mysqli_query($conn, $query);

            if ($row = mysqli_fetch_assoc($result)) {
                $lastNumber = intval(substr($row['EID'], 3)) + 1;
            } else {
                $lastNumber = 1;
            }

            return $prefix . '-' . str_pad($lastNumber, 3, '0', STR_PAD_LEFT);
        }

        // Function to Generate User ID (UD-001)
        function generateUserID($conn) {
            $prefix = "UD";
            $query = "SELECT UID FROM users WHERE UID LIKE '$prefix-%' ORDER BY CAST(SUBSTRING(UID, 4) AS UNSIGNED) DESC LIMIT 1";
            $result = mysqli_query($conn, $query);

            if ($row = mysqli_fetch_assoc($result)) {
                $lastNumber = intval(substr($row['UID'], 3)) + 1;
            } else {
                $lastNumber = 1;
            }

            return $prefix . '-' . str_pad($lastNumber, 3, '0', STR_PAD_LEFT);
        }

        // Function to Generate Address ID (AD-001)
        function generateAddressID($conn) {
            $prefix = "AD";
            $query = "SELECT AID FROM address WHERE AID LIKE '$prefix-%' ORDER BY CAST(SUBSTRING(AID, 4) AS UNSIGNED) DESC LIMIT 1";
            $result = mysqli_query($conn, $query);

            if ($row = mysqli_fetch_assoc($result)) {
                $lastNumber = intval(substr($row['AID'], 3)) + 1;
            } else {
                $lastNumber = 1;
            }

            return $prefix . '-' . str_pad($lastNumber, 3, '0', STR_PAD_LEFT);
        }

        // Generate Unique IDs for new entries
        $EID = generateEmployeeID($conn);
        $UID = generateUserID($conn);
        $AID = generateAddressID($conn);

        // Assign Values from POST
        $username = $_POST['username'] ?? ''; // ✅ Fix: Ensure username is set
        $fn = $_POST['fn'] ?? '';
        $ln = $_POST['ln'] ?? '';
        $mid = $_POST['mid'] ?? '';
        $bday = $_POST['birthday'] ?? null;
        $age = $_POST['age'] ?? null;
        $gender = $_POST['gender'] ?? '';
        $email = $_POST['email'] ?? '';
        $phonenumber = $_POST['phone_num'] ?? '';
        $street = $_POST['street'] ?? '';
        $city = $_POST['city'] ?? '';
        $province = $_POST['province'] ?? '';
        $zipcode = $_POST['zipcode'] ?? '';
        $RID = $_POST['role'] ?? '';

        // Default Password (hashed)
        $default_password = password_hash("123", PASSWORD_DEFAULT);

        // Validation for Required Fields
        if (empty($RID) || empty($gender) || empty($EID) || empty($UID) || empty($username) || empty($email) || empty($phonenumber)) {
            $_SESSION['errors'] = "Missing required fields.";
            header('Location: ../Pages/Employee.php');
            exit;
        }

        if (strlen($phonenumber) !== 11 || !ctype_digit($phonenumber)) {
            $_SESSION['errors'] = "Phone number must be exactly 11 digits.";
            header('Location: ../Pages/Employee.php');
            exit;
        }

        // Function to Check If Record Exists
        function record_exists($conn, $table, $column, $value) {
            $stmt = $conn->prepare("SELECT 1 FROM `$table` WHERE `$column` = ?");
            $stmt->bind_param("s", $value);
            $stmt->execute();
            $exists = $stmt->get_result()->num_rows > 0;
            $stmt->close();
            return $exists;
        }

        // Check for duplicate data
        if (record_exists($conn, 'users', 'username', $username)) {
            $_SESSION['errors'] = "Username already exists.";
            header('Location: ../Pages/Employee.php');
            exit;
        }

        if (record_exists($conn, 'employees', 'email', $email)) {
            $_SESSION['errors'] = "Email already exists.";
            header('Location: ../Pages/Employee.php');
            exit;
        }

        if (record_exists($conn, 'employees', 'phone_num', $phonenumber)) {
            $_SESSION['errors'] = "Phone number already exists.";
            header('Location: ../Pages/Employee.php');
            exit;
        }

        // Start Transaction
        $conn->begin_transaction();

        // Insert into users
        $stmnt2 = $conn->prepare("INSERT INTO users (UID, username, password) VALUES (?, ?, ?)");
        $stmnt2->bind_param("sss", $UID, $username, $default_password);

        if (!$stmnt2->execute()) {
            throw new Exception("Error inserting user data.");
        }

        // Insert into employees
        $stmt = $conn->prepare("INSERT INTO employees (EID, RID, UID, fn, ln, mid, age, gender, bday, email, phone_num) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssissss", $EID, $RID, $UID, $fn, $ln, $mid, $age, $gender, $bday, $email, $phonenumber);

        if (!$stmt->execute()) {
            throw new Exception("Error inserting employee data: " . $stmt->error);
        }

        // Insert into address
        $stmnt4 = $conn->prepare("INSERT INTO address (AID, EID, street, city, province, zipcode) VALUES (?, ?, ?, ?, ?, ?)");
        $stmnt4->bind_param("sssssi", $AID, $EID, $street, $city, $province, $zipcode);

        if (!$stmnt4->execute()) {
            throw new Exception("Error inserting address data.");
        }

        // Commit Transaction
        $conn->commit();

        // Success Message
        $_SESSION['success'] = "Employee successfully added!";
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