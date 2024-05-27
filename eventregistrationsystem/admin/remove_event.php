<?php
session_start();
include "../database/db_conn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user is an admin
    if (isset($_SESSION['id']) && isset($_SESSION['user_name']) && $_SESSION['user_type'] === 'admin') {
        // Check if event_title is set in the POST data
        if (isset($_POST['event_title'])) {
            $event_title = $_POST['event_title'];

            // Prepare and execute SQL statement to delete the event
            $delete_sql = "DELETE FROM events WHERE event_title = ?";
            $stmt = mysqli_prepare($conn, $delete_sql);
            mysqli_stmt_bind_param($stmt, "s", $event_title);

            if (mysqli_stmt_execute($stmt)) {
                // Event deleted successfully
                echo "Event removed successfully.";
            } else {
                // Error occurred while deleting event
                echo "Error: Unable to remove event.";
            }

            mysqli_stmt_close($stmt);
        } else {
            // event_title not set in the POST data
            echo "Error: Event title not provided.";
        }
    } else {
        // User is not an admin
        echo "Error: You do not have permission to perform this action.";
    }
} else {
    // Only handle POST requests
    echo "Error: Invalid request method.";
}
?>
