<?php 
session_start(); 
include "../database/db_conn.php";

if (isset($_POST['uname']) && isset($_POST['password'])) {
    function validate($data) {
       $data = trim($data);
       $data = stripslashes($data);
       $data = htmlspecialchars($data);
       return $data;
    }

    $uname = validate($_POST['uname']);
    $pass = validate($_POST['password']);

    if (empty($uname)) {
        header("Location: ../index.php?error=User Name is required");
        exit();
    } else if(empty($pass)){
        header("Location: ../index.php?error=Password is required");
        exit();
    } else {
        // Hashing the password
        $pass = md5($pass);

        $sql = "SELECT * FROM users WHERE user_name='$uname' AND password='$pass'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            if ($row['user_name'] === $uname && $row['password'] === $pass) {
                // Set user session variables
                $_SESSION['user_name'] = $row['user_name'];
                $_SESSION['name'] = $row['name'];
                $_SESSION['id'] = $row['id'];
                $_SESSION['user_type'] = $row['user_type'];
                
                // Check if the user is an admin
                if ($row['user_type'] === 'admin') {
                    // Redirect to admin page if the user is an admin
                    header("Location: ../admin/admin.php");
                } else {
                    // Redirect to user's homepage if not an admin
                    header("Location: ../homepage/home.php");
                }
                exit();
            } else {
                header("Location: ../index.php?error=Incorrect User name or password");
                exit();
            }
        } else {
            header("Location: ../index.php?error=Incorrect User name or password");
            exit();
        }
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>
