<?php
session_start();
include "../database/db_conn.php";

if (isset($_GET['event_title']) && isset($_SESSION['id'])) {
    $event_title = $_GET['event_title'];
    $user_id = $_SESSION['id'];

    // Fetch registration info from the database
    $registration_sql = "SELECT * FROM registrations WHERE user_id = ? AND event_title = ?";
    $stmt = mysqli_prepare($conn, $registration_sql);
    mysqli_stmt_bind_param($stmt, "is", $user_id, $event_title);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $filename = "registration_info.txt";
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo "Event Title: " . htmlspecialchars($row['event_title']) . "\n";
        echo "User ID: " . htmlspecialchars($row['user_id']) . "\n";
        echo "User Name: " . htmlspecialchars($row['user_name']) . "\n";
        echo "Section: " . htmlspecialchars($row['section']) . "\n";
        echo "Department: " . htmlspecialchars($row['department']) . "\n";
        echo "Registration Date: " . htmlspecialchars($row['registration_date']) . "\n";
    } else {
        echo "No registration found.";
    }
    mysqli_stmt_close($stmt);
    exit();
} else {
    echo "Invalid request.";
    exit();
}
?>
