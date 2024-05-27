<?php 
session_start();
include "../database/db_conn.php";

if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {
    $user_id = $_SESSION['id'];
    $user_name = $_SESSION['user_name'];

    function validate($data){
       $data = trim($data);
       $data = stripslashes($data);
       $data = htmlspecialchars($data);
       return $data;
    }

    // Fetch upcoming events from the database
    $events_sql = "SELECT * FROM events WHERE date >= CURDATE() ORDER BY date ASC";
    $events_result = mysqli_query($conn, $events_sql);
    $events = [];
    if ($events_result && mysqli_num_rows($events_result) > 0) {
        while ($row = mysqli_fetch_assoc($events_result)) {
            $events[] = $row;
        }
    }

    // Fetch recent events from the database
    $recent_events_sql = "SELECT * FROM events WHERE date < CURDATE() ORDER BY date DESC";
    $recent_events_result = mysqli_query($conn, $recent_events_sql);
    $recent_events = [];
    if ($recent_events_result && mysqli_num_rows($recent_events_result) > 0) {
        while ($row = mysqli_fetch_assoc($recent_events_result)) {
            $recent_events[] = $row;
        }
    }

    // Fetch events the user has already registered for
    $registered_events_sql = "SELECT event_title, registration_date FROM registrations WHERE user_id = $user_id";
    $registered_events_result = mysqli_query($conn, $registered_events_sql);
    $registered_events = [];
    if ($registered_events_result && mysqli_num_rows($registered_events_result) > 0) {
        while ($row = mysqli_fetch_assoc($registered_events_result)) {
            $registered_events[] = $row;
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>HOME</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
</head>
<body>
    <div class="header-container">
        <div class="userlogo"><img src="../assets/img/user.png" alt="User Logo"></div>
        <div class="homeheader"><?php echo $_SESSION['user_name']; ?></div>
    </div>
    <a href="../login/logout.php" class="logoutbtn">Logout</a>

    <h1 class="hometitle">CITE Events Registration</h1>

    <div class="main-display" id="main-display">
        <!-- This area will be updated with the registration form via JavaScript -->
    </div>

    <div class="recent-events">
        <h2>Recent Events</h2>
        <ul>
            <?php if (!empty($recent_events)): ?>
                <?php foreach ($recent_events as $event): ?>
                    <li>
                        <?php echo htmlspecialchars($event['event_title']); ?> - <?php echo htmlspecialchars($event['date']); ?>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No recent events.</li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="upcoming-events">
        <h2>Upcoming Events</h2>
        <ul>
            <?php foreach ($events as $event): ?>
                <?php 
                    $is_registered = in_array($event['event_title'], array_column($registered_events, 'event_title'));
                    $deadline_passed = (strtotime($event['deadline']) < time());
                ?>
                <?php if (!$is_registered && !$deadline_passed): ?>
                    <li>
                        <?php echo htmlspecialchars($event['event_title']); ?> - <?php echo htmlspecialchars($event['deadline']); ?>
                        <button onclick="showRegistrationForm('<?php echo $event['event_title']; ?>')">Register</button>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php if (empty($events) || (count($events) === count($registered_events))): ?>
                <li>No upcoming events.</li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="registered-events">
        <h2>Registered Events</h2>
        <ul>
            <?php if (!empty($registered_events)): ?>
                <?php foreach ($registered_events as $event): ?>
                    <li>
                        <?php echo htmlspecialchars($event['event_title']); ?>
                        <a href="download_registration.php?event_title=<?php echo urlencode($event['event_title']); ?>" class="downloadbtn">Download</a>
                        <span class="registration-date"><?php echo htmlspecialchars($event['registration_date']); ?></span>
                       
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No registered events.</li>
            <?php endif; ?>
        </ul>
    </div>

    <script>
        function showRegistrationForm(eventTitle) {
            const mainDisplay = document.getElementById('main-display');
            mainDisplay.innerHTML = `
                <h2>Register for ${eventTitle}</h2>
                <form method="post" action="register_event.php">
                    <input type="hidden" name="event_title" value="${eventTitle}">
                    <label for="user_id">User ID:</label>
                    <input type="text" id="user_id" name="user_id" value="<?php echo $user_id; ?>" readonly required>
                    <label for="user_name">Full Name:</label>
                    <input type="text" id="user_name" name="user_name" required>
                    <label for="section">Section:</label>
                    <input type="text" id="section" name="section" required>
                    <label for="department">Department:</label>
                    <select id="department" name="department" class="styled-select" required>
                        <option value="Electrical Department">Electrical Department</option>
                        <option value="Electronics Department">Electronics Department</option>
                        <option value="Mechanical Department">Mechanical Department</option>
                        <option value="Computer Department">Computer Department</option>
                    </select>
                    <button type="submit">Submit</button>
                </form>
            `;
        }
    </script>

</body>
</html>

<?php 
} else {
    header("Location: index.php");
    exit();
}
?>

