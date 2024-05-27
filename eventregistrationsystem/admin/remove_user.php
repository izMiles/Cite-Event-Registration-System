<?php
session_start();
include "../database/db_conn.php";

if (isset($_SESSION['id']) && isset($_SESSION['user_name']) && $_SESSION['user_type'] === 'admin') {
    // Check if event title and user ID are provided
    if (isset($_POST['event_title']) && isset($_POST['user_id'])) {
        $event_title = $_POST['event_title'];
        $user_id = $_POST['user_id'];

        // Delete the user registration for the event from the database
        $delete_sql = "DELETE FROM registrations WHERE event_title = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $delete_sql);
        mysqli_stmt_bind_param($stmt, "ss", $event_title, $user_id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            echo "User removed successfully from the event.";
            exit();
        } else {
            echo "Error: Unable to remove user from the event.";
            exit();
        }
    } else {
        echo "Error: Event title and user ID are required.";
        exit();
    }
} else {
    echo "Error: Unauthorized access.";
    exit();
}
?>
