<?php
session_start();
include "../database/db_conn.php";

if (isset($_SESSION['id']) && isset($_SESSION['user_name']) && $_SESSION['user_type'] === 'admin') {
    if (isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];

        $sql = "DELETE FROM users WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);

        if (mysqli_stmt_execute($stmt)) {
            echo "User deleted successfully.";
        } else {
            echo "Error: Could not delete user.";
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error: User ID not provided.";
    }
} else {
    header("Location: index.php");
    exit();
}
?>
