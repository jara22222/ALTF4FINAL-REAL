<?php
include("../Connection/database.php");
session_start();

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Sanitize and validate input
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Username and password are required.';
            header('Location: ../login.php');
            exit;
        }

        // Verify user credentials
        $user = verify_user($username, $password);

        if ($user) {
            // Fetch user role from the database
            $role = get_user_role($user);

            // Store user ID and role in the session
            $_SESSION['UID'] = $user;
            $_SESSION['role'] = $role;

            // Generate and store a secure access token
            $token = bin2hex(random_bytes(32));
            $_SESSION['access_token'] = $token;

            // Redirect based on user role
            if ($role === 'Admin') {
                header("Location: ../../ADMIN/Pages/Dashboard.php");
            } else {
                header("Location: ../../CASHIERENTERFACE/dashboardCashier/Cashierdashboard.php");
            }
            exit;
        } else {
            $_SESSION['error'] = 'Invalid username or password.';
            header('Location: ../login.php');
            exit;
        }
    }
} catch (Exception $e) {
    // Log the error and display a generic message
    error_log("Login error: " . $e->getMessage());
    $_SESSION['error'] = 'An error occurred. Please try again later.';
    header('Location: ../login.php');
    exit;
}

/**
 * Verify user credentials.
 *
 * @param string $username The username.
 * @param string $password The password.
 * @return string|false The user ID if credentials are valid, otherwise false.
 */
function verify_user($username, $password)
{
    global $conn;

    $stmt = $conn->prepare("SELECT UID, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            return $row['UID'];
        }
    }
    return false;
}

/**
 * Get the role of a user.
 *
 * @param string $userID The user ID.
 * @return string The user's role.
 */
function get_user_role($userID)
{
    global $conn;

    $stmt = $conn->prepare("SELECT r.rolename FROM roles r JOIN employees e ON r.RID = e.RID WHERE e.UID = ?");
    $stmt->bind_param("s", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row['rolename'];
    }
    return 'Cashier'; // Default role if not found
}
?>