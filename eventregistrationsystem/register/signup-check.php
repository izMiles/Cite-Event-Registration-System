<?php 
session_start(); 
include "../database/db_conn.php";

if (isset($_POST['uname']) && isset($_POST['password'])
    && isset($_POST['name']) && isset($_POST['re_password'])
    && isset($_POST['department']) && isset($_POST['section'])) {

    function validate($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $uname = validate($_POST['uname']);
    $pass = validate($_POST['password']);
    $re_pass = validate($_POST['re_password']);
    $name = validate($_POST['name']);
    $department = validate($_POST['department']);
    $section = validate($_POST['section']);

    $user_data = 'uname='. $uname. '&name='. $name . '&department=' . $department . '&section=' . $section;

    if (empty($uname)) {
        header("Location: signup.php?error=User Name is required&$user_data");
        exit();
    }else if(empty($pass)){
        header("Location: signup.php?error=Password is required&$user_data");
        exit();
    }
    else if(empty($re_pass)){
        header("Location: signup.php?error=Re Password is required&$user_data");
        exit();
    }
    else if(empty($name)){
        header("Location: signup.php?error=Name is required&$user_data");
        exit();
    }
    else if($pass !== $re_pass){
        header("Location: signup.php?error=The confirmation password does not match&$user_data");
        exit();
    }
    else if(empty($department)){
        header("Location: signup.php?error=Department is required&$user_data");
        exit();
    }
    else if(empty($section)){
        header("Location: signup.php?error=Section is required&$user_data");
        exit();
    }
    else{
        // hashing the password
        $pass = md5($pass);

        $sql = "SELECT * FROM users WHERE user_name='$uname' ";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            header("Location: signup.php?error=The username is taken try another&$user_data");
            exit();
        }else {
            $sql2 = "INSERT INTO users(user_name, password, name, department, section) VALUES('$uname', '$pass', '$name', '$department', '$section')";
            $result2 = mysqli_query($conn, $sql2);
            if ($result2) {
                header("Location: signup.php?success=Your account has been created successfully");
                exit();
            }else {
                header("Location: signup.php?error=unknown error occurred&$user_data");
                exit();
            }
        }
    }
}else{
    header("Location: signup.php");
    exit();
}
