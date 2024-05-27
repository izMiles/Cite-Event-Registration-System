<?php
session_start();
include "../database/db_conn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['event_title']) && isset($_POST['user_id']) && isset($_POST['user_name']) && isset($_POST['section']) && isset($_POST['department'])) {
        $event_title = $_POST['event_title'];
        $user_id = $_POST['user_id'];
        $user_name = $_POST['user_name'];
        $section = $_POST['section'];
        $department = $_POST['department'];
        $registration_date = date('Y-m-d'); // Capture the current date

        // Prepare the SQL statement to insert the registration
        $insert_sql = "INSERT INTO registrations (event_title, user_id, user_name, section, department, registration_date) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_sql);
        mysqli_stmt_bind_param($stmt, "sissss", $event_title, $user_id, $user_name, $section, $department, $registration_date);

        if (mysqli_stmt_execute($stmt)) {
            // Registration successful, redirect to the home page or display a success message
            header("Location: home.php");
            exit();
        } else {
            // Handle execution error
            echo "Error: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    } else {
        // Handle missing POST data
        echo "Error: Missing POST data";
    }
} else {
    // Handle invalid request method
    echo "Error: Invalid request method";
}
?>
