<?php
include('../Connection/database.php');
session_start();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    
    
    $rid = $_POST['RID'] ?? null;

    $position = $_POST['position']; 
        $description = $_POST['description'];
        

        unset($_SESSION['errors']);
        unset($_SESSION['success']); // Clear previous messages

        // Check if position exists
        function position_exist($position) {
        global $conn;
        $stmnt = $conn->prepare("SELECT RID FROM roles WHERE rolename = ?");
        $stmnt->bind_param('s', $position);
        $stmnt->execute();
        $result = $stmnt->get_result();
        return $result->num_rows > 0;
    }

    // Check if RID exists
    function RID_exist($RID) {
        global $conn;
        $stmnt = $conn->prepare("SELECT RID FROM roles WHERE RID = ?");
        $stmnt->bind_param('s', $RID);
        $stmnt->execute();
        $result = $stmnt->get_result();
        return $result->num_rows > 0;
    }

    // Validate username and email existence
    if (position_exist($position)) {
        $_SESSION['errors'] = 'Username already exists';
        header('Location: ../Pages/Roles.php');
        exit();
    }

    if (RID_exist($RID)) {
        $_SESSION['errors'] = 'RID already exists';
        header('Location: ../Pages/Roles.php');
        exit();
    }

        
    



        // Update role details
        $queryrole = "UPDATE roles
                    SET  rolename=?, description=?
                    WHERE RID=?";
        $stmtrole = $conn->prepare($queryrole);
        $stmtrole->bind_param("sss",$position, $description, $rid);
        
        if (!$stmtrole->execute()) {
            $_SESSION['errors'] = "Error updating role  details.";
            header('Location: ../Pages/Roles.php');
            exit();
        }
        $stmtrole->close();


        $_SESSION['success'] = 'role  details updated successfully!';
        header('Location: ../Pages/Roles.php');
        exit();
    
    
    } else {
        header('Location: ../Pages/Roles.php');
        exit();
    }
    ?>
