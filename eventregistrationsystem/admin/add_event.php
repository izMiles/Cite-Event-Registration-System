<?php
session_start();
include "../database/db_conn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION['id']) && isset($_SESSION['user_name']) && $_SESSION['user_type'] === 'admin') {
        if(isset($_POST['event_title'], $_POST['event_date'], $_POST['event_deadline'])) {
            $event_title = $_POST['event_title'];
            $event_date = $_POST['event_date'];
            $event_deadline = $_POST['event_deadline'];

           

            // Insert event into the database
            $insert_sql = "INSERT INTO events (event_title, date, deadline) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insert_sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sss", $event_title, $event_date, $event_deadline);
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    header("Location: admin.php"); // Redirect to admin panel after adding event
                    exit();
                } else {
                    // Handle execution error
                    echo "Error: " . mysqli_stmt_error($stmt);
                }
            } else {
                // Handle prepare error
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            // Handle missing POST data
            echo "Error: Missing POST data";
        }
    } else {
        // Redirect to login page if not logged in as admin
        header("Location: index.php");
        exit();
    }
} else {
    // Redirect to add event form if accessed directly
    header("Location: admin_panel.php");
    exit();
}
?>
