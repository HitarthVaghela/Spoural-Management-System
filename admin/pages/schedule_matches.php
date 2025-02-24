<?php


// Check if user is logged in, else redirect to login

session_start();
// Fetch session data
$admin_username = $_SESSION['login'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPORUAL Event Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card {
            text-align: center;
            padding: 20px;
        }

        .btn-green {
            background-color: #28a745;
            color: white;
        }

        .btn-green:hover {
            background-color: #218838;
        }

        .table th,
        .table td {
            text-align: center;
        }

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
            font-size: 14px;
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
            <img src="../assets/images/charusat.png" alt="Logo 1">
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
    <!-- Schedule Matches Section -->
    <div class="container mt-5 main-content">
        <h4 class="mb-4">Schedule Matches</h4>
        <form class="mb-4">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Match Type</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="matchType" value="sports" checked>
                        <label class="form-check-label">Sports</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sport Name</label>
                    <input type="text" class="form-control" placeholder="Enter Sport Name">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Team 1 Name</label>
                    <input type="text" class="form-control" placeholder="Enter Team 1">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Team 2 Name</label>
                    <input type="text" class="form-control" placeholder="Enter Team 2">
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">Match Date</label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Match Time</label>
                    <input type="time" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Location</label>
                    <input type="text" class="form-control" placeholder="Enter Location">
                </div>
                <center>
                    <div class="col-md-2">
                        <br><br><br>
                        <button class="btn btn-green w-100">Add Match</button>
                    </div>
                </center>
            </div>
        </form>
    </div>
    <!--SIDEBAR JAVASCRIPT-->
    <script>

        function openNav() {
            document.getElementById("mySidenav").style.width = "250px";
            document.querySelector(".main-content").style.marginLeft = "250px";
        }
        function closeNav() {
            document.getElementById("mySidenav").style.width = "0";
            document.querySelector(".main-content").style.marginLeft = "0";
        }


    </script>
</body>

</html>