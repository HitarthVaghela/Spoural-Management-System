<?php
session_start();
include('../includes/config.php');

// Ensure ULSC is Logged In
if (!isset($_SESSION['ulsc_id'])) {
    header("Location: ulsc_login.php");
    exit;
}

$ulsc_id = $_SESSION['ulsc_id']; // Fetch ULSC ID from session

// Fetch ULSC Member's Department ID
$sql = "SELECT u.dept_id, d.dept_name, u.ulsc_name 
        FROM ulsc u 
        JOIN departments d ON u.dept_id = d.id 
        WHERE u.ulsc_id = :ulsc_id";
$query = $dbh->prepare($sql);
$query->bindParam(':ulsc_id', $ulsc_id, PDO::PARAM_INT);
$query->execute();
$ulsc = $query->fetch(PDO::FETCH_ASSOC);

$dept_id = $ulsc['dept_id'] ?? 0;
$ulsc_name = htmlspecialchars($ulsc['ulsc_name']);
$dept_name = htmlspecialchars($ulsc['dept_name']);

// Count Total Managed Events
$sql = "SELECT COUNT(DISTINCT event_id) AS total_events FROM participants WHERE dept_id = :dept_id";
$query = $dbh->prepare($sql);
$query->bindParam(':dept_id', $dept_id, PDO::PARAM_INT);
$query->execute();
$total_events = $query->fetch(PDO::FETCH_ASSOC)['total_events'] ?? 0;

// Count Total Participants
$sql = "SELECT COUNT(*) AS total_participants FROM participants WHERE dept_id = :dept_id";
$query = $dbh->prepare($sql);
$query->bindParam(':dept_id', $dept_id, PDO::PARAM_INT);
$query->execute();
$total_participants = $query->fetch(PDO::FETCH_ASSOC)['total_participants'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ULSC Member Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f8f9fa;
        }
        header {
            background: #007BFF;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .sidenav {
            width: 0;
            position: fixed;
            left: 0;
            background: #111;
            height: 100%;
            padding-top: 60px;
            transition: 0.5s;
        }
        .sidenav a {
            color: white;
            display: block;
            padding: 10px;
            text-decoration: none;
        }
        .stats-container {
            display: flex;
            justify-content: space-around;
            padding: 10px;
        }
        .stat-box {
            background: #f4f4f4;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            width: 150px;
        }
        .notifications ul {
            list-style: none;
            padding: 10px;
            background: #fff;
        }
        .notifications ul li {
            padding: 8px 0;
        }
        .dropdown {
            position: relative;
            display: inline-block;
        }
        .dropdown-trigger {
            background-color: #ffffff;
            border: none;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .dropdown-menu {
            display: none;
            position: absolute;
            background-color: white;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            min-width: 150px;
            right: 0;
            z-index: 10;
            border-radius: 4px;
            overflow: hidden;
        }
        .dropdown:hover .dropdown-menu {
            display: block;
        }
        .dropdown-item {
            padding: 10px;
            display: block;
            text-decoration: none;
            color: black;
            font-size: 14px;
        }
        .dropdown-item:hover {
            background-color: #f0f0f0;
        }
        footer {
            background: #007BFF;
            color: white;
            text-align: center;
            padding: 10px;
            position: relative;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>

<body>
<header>
        <span style="font-size:30px;cursor:pointer" onclick="openNav()">&#9776;</span>
        <div id="mySidenav" class="sidenav">
            <span class="closebtn" onclick="closeNav()">&times;</span>
            <a href="addparticipants.php">Add Participants</a>
            <a href="viewevents.php">View Events</a>
        </div>

        <div class="logo">
        <a href="ulsc_dashboard.php">
        <img src="../assets/images/charusat.png" alt="Logo">
        </a>
        </div>
        
        <h1>ULSC - Dashboard</h1>

        <div class="logo">
            <img src="../assets/images/ulsc.png" alt="ULSC Logo">
        </div>

        <div class="dropdown">
            <button class="dropdown-trigger">
                <?php echo "$ulsc_name - $dept_name"; ?> <i class="fas fa-caret-down"></i>
            </button>
            <div class="dropdown-menu">
                <a href="#" class="dropdown-item"><i class="fas fa-user"></i> My Profile</a>
                <hr>
                <a href="ulsc_logout.php" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Sign-Out</a>
            </div>
        </div>
    </header>

<main>
    <!-- Quick Stats Section -->
    <section class="stats-container">
        <div class="stat-box"><h3><?php echo $total_events; ?></h3><p>Events Managed</p></div>
        <div class="stat-box"><h3><?php echo $total_participants; ?></h3><p>Total Participants</p></div>
    </section>

    <!-- Notifications -->
    <section class="notifications">
        <h3>Notifications</h3>
        <ul>
            <li>🔔 <?php echo $total_participants; ?> students have registered for events.</li>
            <li>🔔 <?php echo $total_events; ?> events are currently being managed.</li>
        </ul>
    </section>
</main>

<footer>
    <p>&copy; 2025 ULSC Member Dashboard. All Rights Reserved.</p>
</footer>

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
        if (!sidebar.contains(event.target) && !sidebarButton.contains(event.target)) {
            closeNav();
        }
    });
</script>
</body>
</html>
