<?php
include "../database/db_conn.php";

if (isset($_GET['department'])) {
    $department = $_GET['department'];

    $sql = "SELECT * FROM users WHERE department=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $department);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        echo '<li>' . htmlspecialchars($row['user_name']) . ' - ' . htmlspecialchars($row['section']) . ' - ' . htmlspecialchars($row['department']);
        echo ' <button class="remove-btn" onclick="removeUserFromDepartment(' . $row['id'] . ')">Remove</button></li>';
    }

    mysqli_stmt_close($stmt);
}
?>
