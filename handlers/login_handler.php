<?php
include("../Connection/database.php");
session_start();

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Verify user credentials
        $user = verify_user($username, $password);

        if ($user) {
            // Store user data in session
            $_SESSION['UID'] = $user['UID']; // Store user ID
            $_SESSION['role'] = $user['rolename']; // Store role
             $_SESSION['fullname'] = $user['fullname']; //Store fullname
            $_SESSION['access_token'] = bin2hex(random_bytes(32)); // Generate a secure access token

            // Redirect based on user role
            if ($user['rolename'] === "Admin") {
                header('Location: ../pages/Admindashboard.php');
            } else {
                header('Location: ../pages/Cashierdashboard.php');
            }
            exit;
        } else {
            // Invalid credentials
            $_SESSION['error'] = 'Invalid username or password';
            header('Location: ../login.php');
            exit;
        }
    }
} catch (Exception $e) {
    // Handle exceptions
    $_SESSION['error'] = 'An error occurred. Please try again later.';
    header('Location: ../login.php');
    exit;
}

// Function to verify user credentials and role
function verify_user($username, $password)
{
    global $conn;

    // Prepare SQL query to fetch user data
    $stmt = $conn->prepare(" SELECT u.UID,Concat(e.fn,' ',e.mid,' ',e.ln) fullname,
                             u.password, r.rolename 
                            FROM users u 
                            JOIN employees e ON e.UID = u.UID 
                            JOIN roles r ON r.RID = e.RID 
                            WHERE u.username = ?");
    if (!$stmt) {
        throw new Exception("Database query preparation failed: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Verify password
        if (password_verify($password, $row['password'])) {
            return $row; // Return user data (UID & role)
        }
    }
    return false; // Invalid credentials
}
?>