<?php
session_start();
include('../includes/config.php');

// Check if user is logged in, else redirect to login


// Fetch session data
$admin_username = $_SESSION['login'];
// Initialize variables
$event_id = $event_name = $event_type = $min_participants = $max_participants = "";

// **FETCH DATA FOR EDITING**
if (isset($_GET['edit_id'])) {
    $event_id = $_GET['edit_id'];
    $sql = "SELECT * FROM events WHERE id = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $event_id, PDO::PARAM_INT);
    $query->execute();
    $eventData = $query->fetch(PDO::FETCH_ASSOC);

    if ($eventData) {
        $event_name = $eventData['event_name'];
        $event_type = $eventData['event_type'];
        $min_participants = $eventData['min_participants'];
        $max_participants = $eventData['max_participants'];
    }
}

// **INSERT OR UPDATE EVENT**
if (isset($_POST['save_event'])) {
    $event_id = $_POST['event_id'];
    $event_name = $_POST['event_name'];
    $event_type = $_POST['event_type'];
    $min_participants = $_POST['min_participants'];
    $max_participants = $_POST['max_participants'];

    if (!empty($event_id)) {
        $sql = "UPDATE events SET event_name = :event_name, event_type = :event_type, min_participants = :min_participants, max_participants = :max_participants WHERE id = :id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $event_id, PDO::PARAM_INT);
    } else {
        $sql = "INSERT INTO events (event_name, event_type, min_participants, max_participants) VALUES (:event_name, :event_type, :min_participants, :max_participants)";
        $query = $dbh->prepare($sql);
    }

    $query->bindParam(':event_name', $event_name, PDO::PARAM_STR);
    $query->bindParam(':event_type', $event_type, PDO::PARAM_STR);
    $query->bindParam(':min_participants', $min_participants, PDO::PARAM_INT);
    $query->bindParam(':max_participants', $max_participants, PDO::PARAM_INT);

    if ($query->execute()) {
        echo "<script> window.location.href='addevent.php';</script>";
    } else {
        echo "";
    }
}

// **DELETE EVENT**
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $sql = "DELETE FROM events WHERE id = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $id, PDO::PARAM_INT);

    if ($query->execute()) {
        echo "<script>window.location.href='addevent.php';</script>";
    } else {
        echo "<script>alert('Failed to delete event!');</script>";
    }
    
}

$query = $dbh->prepare("SELECT * FROM events ORDER BY id DESC");
$query->execute();
$events = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spoural Management System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .small-table {
            max-width: 600px;
            /* Adjust width */
            margin: left;
            /* Center the table */
            font-size: 20px;
            /* Smaller text */
        }

        label {
            font-size: 20px;
        }

        input {
            font-size: 15px;
        }

       /* Button Styling */
       .custom-dropdown-toggle {
            background-color: #ffffff; /* Light gray */
            color: black;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }

        /* Ensure hover does not change color */
        .custom-dropdown-toggle:hover,
        .custom-dropdown-toggle:focus,
        .custom-dropdown-toggle:active {
            background-color: #ddd !important; /* Same as default */
            color: black !important;
            outline: none !important;
            box-shadow: none !important;
        }

        /* Align Dropdown */
        .dropdown-menu {
            min-width: 150px;
            right: 0;
            left: auto;
        }
    </style>
    <style>
        /* Sidebar Styling */
        .sidenav {
            height: 100%;
            width: 0;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #111;
            overflow-x: hidden;
            transition: 0.3s;
            padding-top: 60px;
            z-index: 1000;
        }

        .sidenav a {
            padding: 10px 20px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
            transition: 0.3s;
        }

        .sidenav a:hover {
            background-color: #575757;
        }

        .closebtn {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 30px;
            color: white;
            cursor: pointer;
        }

        /* Sidebar Button */
        .sidebar-btn {
            font-size: 30px;
            cursor: pointer;
            background: none;
            border: none;
            color: black;
            padding: 10px;
        }

        .sidebar-btn:focus {
            outline: none;
        }

        /* Dropdown */
        .dropdown-menu {
            min-width: 150px;
            right: 0;
            left: auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <span style="font-size:30px;cursor:pointer" onclick="openNav()">&#9776; </span>
            <!-- SIDE BAR -->
            <div id="mySidenav" class="sidenav">
                <span class="closebtn" onclick="closeNav()">&times;</span>
                <br><Br>
                <br><br>

                <a href="addevent.php">Add Event</a>
                <br>
                <a href="addulsc.php">Add ULSC</a>
                <br>
                <a href="addadmin.php">Add Admin</a>
                <br>
                <a href="schedule_matches.php">Add Schedule</a>
            </div>

            <div class="logo">
                <br><br>
                <a href="admindashboard.php">
                    <img src="../assets/images/charusat.png" alt="Logo 1">
                </a>

            </div>
            <h1>Spoural Event Management System</h1>

            <div class="logo">

                <img src="../assets/images/ulsc.png" alt="Logo 2">
            </div>


            <!-- DROPDOWN FOR ADMIN NAME -->
            <div class="dropdown">
                <button class="custom-dropdown-toggle" type="button">
                    <?php echo htmlspecialchars($admin_username); ?> <!-- Display admin name -->
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item" href="#">Profile</a></li>
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </header>
        <main>
            <section>
                <h2><?= !empty($event_id) ? 'Edit Event' : 'New Event' ?></h2>
                <form method="post" action="addevent.php">
                    <input type="hidden" name="event_id" value="<?= htmlspecialchars($event_id) ?>">

                    <label>Event Name:</label>
                    <input type="text" name="event_name" value="<?= htmlspecialchars($event_name) ?>" required>

                    <label>Event Type:</label>
                    <input type="radio" name="event_type" value="Sports" <?= ($event_type == 'Sports') ? 'checked' : '' ?>
                        required> Sports
                    <input type="radio" name="event_type" value="Cultural" <?= ($event_type == 'Cultural') ? 'checked' : '' ?> required> Cultural

                    <label>Min Participants:</label>
                    <input type="number" name="min_participants" value="<?= htmlspecialchars($min_participants) ?>" required>

                    <label>Max Participants:</label>
                    <input type="number" name="max_participants" value="<?= htmlspecialchars($max_participants) ?>" required>

                    <button type="submit" name="save_event"><?= !empty($event_id) ? 'Submit' : 'Submit' ?></button>

                </form>
            </section>

            <section>
                <h2>View Events</h2>
                <div>
                    <table border="2px" class="table table-bordered table-striped small-table">
                        <thead>
                            <tr>
                                <th>Event ID</th>
                                <th>Event Name</th>
                                <th>Event Type</th>
                                <th>Min Participants</th>
                                <th>Max Participants</th>
                                <th>Edit</th>
                                <th>Remove</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $event) { ?>
                                <tr>
                                    <td><?= $event['id'] ?></td>
                                    <td><?= htmlspecialchars($event['event_name']) ?></td>
                                    <td><?= htmlspecialchars($event['event_type']) ?></td>
                                    <td><?= htmlspecialchars($event['min_participants']) ?></td>
                                    <td><?= htmlspecialchars($event['max_participants']) ?></td>
                                    <td>
                                        <a href="addevent.php?edit_id=<?= $event['id'] ?>">
                                            <img src="../assets/images/edit.jpg" alt="Edit" width="20" height="20">
                                        </a>
                                    </td>

                                    <td><a href="addevent.php?delete_id=<?= $event['id'] ?>"
                                            onclick="return confirm('Are you sure?')">
                                            <img src="../assets/images/delete.jpg" alt="Edit" width="20" height="20">
                                        </a></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script>
        function openNav() {
            document.getElementById("mySidenav").style.width = "250px";
        }
        function closeNav() {
            document.getElementById("mySidenav").style.width = "0";
            
        }
        document.addEventListener("click", function(event) {
        var sidebar = document.getElementById("mySidenav");
        var sidebarButton = document.querySelector("span[onclick='openNav()']");

        // Check if the click is outside the sidebar and not on the open button
        if (!sidebar.contains(event.target) && !sidebarButton.contains(event.target)) {
            closeNav();
        }
    });

    </script>
</body>

</html>