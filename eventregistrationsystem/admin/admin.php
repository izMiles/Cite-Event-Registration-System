<?php
session_start();
include "../database/db_conn.php";

// Check if the user is logged in and is an admin
if (isset($_SESSION['id']) && isset($_SESSION['user_name']) && $_SESSION['user_type'] === 'admin') {
    $user_name = $_SESSION['user_name'];

    // Fetch all events from the database
    $events_sql = "SELECT * FROM events ORDER BY date ASC";
    $events_result = mysqli_query($conn, $events_sql);
    $events = [];
    if ($events_result && mysqli_num_rows($events_result) > 0) {
        while ($row = mysqli_fetch_assoc($events_result)) {
            $events[] = $row;
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Events</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
</head>
<body>
    <div class="header-container">
        <div class="userlogo"><img src="../assets/img/user.png" alt="Admin Logo"></div>
        <div class="homeheader"><?php echo $_SESSION['user_name']; ?></div>
    </div>
    <a href="../login/logout.php" class="logoutbtn">Logout</a>

    <h1 class="hometitle">Admin Panel - Events</h1>

    <div class="event-list main-display">
        <table>
            <tr>
                <th class="table-title">Event List</th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <th>Title</th>
                <th>Date</th>
                <th>Deadline</th>
                <th>Action</th>
            </tr>
            <!-- Loop through the events and display them in the table -->
            <?php foreach ($events as $event): ?>
            <tr>
                <td><a href="?event=<?php echo urlencode($event['event_title']); ?>"><?php echo htmlspecialchars($event['event_title']); ?></a></td>
                <td><?php echo htmlspecialchars($event['date']); ?></td>
                <td><?php echo htmlspecialchars($event['deadline']); ?></td>
                <td>
                    <button class="remove-event-btn" onclick="removeEvent('<?php echo htmlspecialchars($event['event_title']); ?>')">Remove</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <br>
        <button onclick="openAddEventModal()" class="add-event-btn">Add Event</button>
    </div>

    <div class="user-list">
        <?php
        // Check if a specific event is selected
        if (isset($_GET['event'])) {
            $selected_event = $_GET['event'];
            $registrations_sql = "SELECT * FROM registrations WHERE event_title = ?";
            $stmt = mysqli_prepare($conn, $registrations_sql);
            mysqli_stmt_bind_param($stmt, "s", $selected_event);
            mysqli_stmt_execute($stmt);
            $registrations_result = mysqli_stmt_get_result($stmt);
        ?>
            <h2>Registered Users for <?php echo htmlspecialchars($selected_event); ?></h2>
            <br>
            <input type="text" id="searchInput" onkeyup="searchUsers()" placeholder="Search by name or department...">
            <br>
            <ul id="usersList" class="horizontal-scroll">
                <!-- Loop through the registered users and display them -->
                <?php while ($row = mysqli_fetch_assoc($registrations_result)): ?>
                <li>
                    <?php echo htmlspecialchars($row['user_name']); ?> - 
                    <?php echo htmlspecialchars($row['section']); ?> - 
                    <?php echo htmlspecialchars($row['department']); ?> - 
                    <?php echo htmlspecialchars($row['registration_date']); ?>
                    <button class="remove-btn" onclick="removeUser('<?php echo $selected_event; ?>', '<?php echo $row['user_id']; ?>')">Remove</button>
                </li>
                <?php endwhile; ?>
            </ul>
        <?php
            mysqli_stmt_close($stmt);
        }
        ?>
    </div>

    <!-- Users List by Department -->
    <div class="department-users">
        <h2>Students List by Department</h2>
        <br>
        <select id="departmentSelect" onchange="fetchUsersByDepartment()">
            <option value="">Select Department</option>
            <option value="Electrical Department">Electrical Department</option>
            <option value="Computer Department">Computer Department</option>
            <option value="Mechanical Department">Mechanical Department</option>
            <option value="Electronics Department">Electronics Department</option>
        </select>
        <ul id="userList">
            <!-- Users will be dynamically loaded here -->
        </ul>
    </div>

    <!-- Add Event Modal -->
    <div id="addEventModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddEventModal()">&times;</span>
            <h2>Add Event</h2>
            <form action="add_event.php" method="POST">
                <label for="event_title">Event Title</label>
                <input type="text" name="event_title" placeholder="Event Title" required><br><br>
                <label for="event_date">Event Date</label>
                <input type="date" name="event_date" required><br><br>
                <label for="event_deadline">Event Deadline</label>
                <input type="date" name="event_deadline" required><br><br>
                <button type="submit">Add</button>
            </form>
        </div>
    </div>

    <script>
        // Open the modal to add a new event
        function openAddEventModal() {
            var modal = document.getElementById("addEventModal");
            modal.style.display = "block";
        }

        // Close the modal to add a new event
        function closeAddEventModal() {
            var modal = document.getElementById("addEventModal");
            modal.style.display = "none";
        }

        // Remove an event
        function removeEvent(eventTitle) {
            if (confirm("Are you sure you want to remove this event?")) {
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            location.reload();
                        } else {
                            alert('Error: Unable to remove event.');
                        }
                    }
                };
                xhr.open("POST", "remove_event.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.send("event_title=" + encodeURIComponent(eventTitle));
            }
        }

        // Remove a user from an event
        function removeUser(eventTitle, userId) {
            if (confirm("Are you sure you want to remove this user from the event?")) {
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            location.reload();
                        } else {
                            alert('Error: Unable to remove user from the event.');
                        }
                    }
                };
                xhr.open("POST", "remove_user.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.send("event_title=" + encodeURIComponent(eventTitle) + "&user_id=" + encodeURIComponent(userId));
            }
        }

        // Fetch users by department
        function fetchUsersByDepartment() {
            var department = document.getElementById('departmentSelect').value;
            if (department !== "") {
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            document.getElementById('userList').innerHTML = xhr.responseText;
                        } else {
                            alert('Error: Unable to fetch users.');
                        }
                    }
                };
                xhr.open("GET", "fetch_users.php?department=" + encodeURIComponent(department), true);
                xhr.send();
            } else {
                document.getElementById('userList').innerHTML = "";
            }
        }

        // Remove a user from a department
        function removeUserFromDepartment(userId) {
            if (confirm("Are you sure you want to remove this user?")) {
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            fetchUsersByDepartment(); // Refresh the user list
                        } else {
                            alert('Error: Unable to remove user.');
                        }
                    }
                };
                xhr.open("POST", "remove_user_department.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.send("user_id=" + encodeURIComponent(userId));
            }
        }

        // Search users within the selected event's registrations
        function searchUsers() {
            var input, filter, ul, li, i, txtValue;
            input = document.getElementById('searchInput');
            filter = input.value.toUpperCase();
            ul = document.getElementById("usersList");
            li = ul.getElementsByTagName('li');

            // Loop through all list items and hide those that don't match the search query
            for (i = 0; i < li.length; i++) {
                txtValue = li[i].textContent || li[i].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    li[i].style.display = "";
                } else {
                    li[i].style.display = "none";
                }
            }
        }
    </script>
</body>
</html>

<?php 
} else {
    // Redirect to login page if the user is not an admin
    header("Location: index.php");
    exit();
}
?>
