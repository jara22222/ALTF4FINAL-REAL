<?php
include("../Connection/database.php");
session_start();



try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $username = $_POST['username'];
        $password = $_POST['password'];

        var_dump($username);
        var_dump($password);


        $user = verify_user($username, $password);

        if ($user) {
            $_SESSION['UID'] = $user;
            $token = bin2hex(random_bytes(32));
            $_SESSION['access_token'] = $token;
            header("Location: ../pages/Cashierdashboard.php");
            exit;
        } else {
            $_SESSION['error'] = 'Invalid username or password';
            header('Location: ../login.php');
            exit;
        }

        if ($user == "Admin") {
            $_SESSION['UID'] = $user;
            $token = bin2hex(random_bytes(32));
            $_SESSION['access_token'] = $token;
            header("Location: ../../ADMIN/Pages/Dashboard.php");
            exit;
        } else {
            $_SESSION['error'] = 'Invalid username or password';
            header('Location: ../login.php');
            exit;
        }

        

    }
} catch (Exception $e) {
    echo "" . $e->getMessage() . "";
}


function verify_user($username, $password)
{
    global $conn;

    $stmt = $conn->prepare("Select UID,password from users where username = ?");
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
?>