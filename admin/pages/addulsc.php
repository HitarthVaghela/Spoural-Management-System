<?php
session_start();
include('../includes/config.php');



// Fetch session data
$admin_username = $_SESSION['login'];
// Function to generate a random password
function generateRandomPassword($length = 10) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    return substr(str_shuffle($chars), 0, $length);
}

// INSERT ULSC
if (isset($_POST['add_ulsc'])) {
    $ulsc_id = $_POST['ulsc_id'];
    $ulsc_name = $_POST['ulsc_name'];
    $random_password = generateRandomPassword(); // Generate a random password
   

    $sql = "INSERT INTO ulsc (ulsc_id, ulsc_name, password) VALUES (:ulsc_id, :ulsc_name, :password)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':ulsc_id', $ulsc_id, PDO::PARAM_STR);
    $query->bindParam(':ulsc_name', $ulsc_name, PDO::PARAM_STR);
    $query->bindParam(':password', $random_password, PDO::PARAM_STR);

    if ($query->execute()) {
        echo "<script> window.location.href='addulsc.php';</script>";
    } else {
        echo "";
    }
}

// FETCH DATA FOR EDIT
$editData = null;
if (isset($_GET['edit_id'])) {
    $id = $_GET['edit_id'];
    $sql = "SELECT * FROM ulsc WHERE id=:id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->execute();
    $editData = $query->fetch(PDO::FETCH_ASSOC);
}

// UPDATE ULSC
if (isset($_POST['update_ulsc'])) {
    $id = $_POST['id'];
    $ulsc_id = $_POST['ulsc_id'];
    $ulsc_name = $_POST['ulsc_name'];

    $sql = "UPDATE ulsc SET ulsc_id=:ulsc_id, ulsc_name=:ulsc_name WHERE id=:id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->bindParam(':ulsc_id', $ulsc_id, PDO::PARAM_STR);
    $query->bindParam(':ulsc_name', $ulsc_name, PDO::PARAM_STR);

    if ($query->execute()) {
        echo "<script>window.location.href='addulsc.php';</script>";
    } else {
        echo "<script>alert('Error Updating ULSC');</script>";
    }
}

// DELETE ULSC
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    $sql = "DELETE FROM ulsc WHERE id=:id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $id, PDO::PARAM_INT);

    if ($query->execute()) {
        // Reset Auto Increment
        $dbh->exec("ALTER TABLE ulsc AUTO_INCREMENT = 1");
        echo "<script>window.location.href='addulsc.php';</script>";
    } else {
        echo "<script>alert('Error Deleting ULSC');</script>";
    }
}
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
        label{
            font-size: 20px;
        }
        input{
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
                <h2><?= isset($editData) ? 'Edit ULSC' : 'New ULSC' ?></h2>
                <form action="" method="POST">
                    <input type="hidden" name="id" value="<?= $editData['id'] ?? '' ?>">
                    <label>ULSC ID:</label><input type="text" name="ulsc_id" value="<?= $editData['ulsc_id'] ?? '' ?>"
                        required>
                    <label>ULSC Name:</label><input type="text" name="ulsc_name"
                        value="<?= $editData['ulsc_name'] ?? '' ?>" required>

                    <?php if (isset($editData)): ?>
                        <button type="submit" name="update_ulsc">Submit</button>
                    
                    <?php else: ?>
                        <button type="submit" name="add_ulsc">Submit</button>
                    <?php endif; ?>
                </form>
            </section>

            <section>
                <h2>View ULSC Details</h2>
                <table border="2px" class="table table-bordered table-striped small-table">
                    <thead>
                        <tr>
                            <th>Sr.no</th>
                            <th>ULSC ID</th>
                            <th>ULSC NAME</th>
                            <th>Edit</th>
                            <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM ulsc";
                        $query = $dbh->prepare($sql);
                        $query->execute();
                        $results = $query->fetchAll(PDO::FETCH_ASSOC);
                        $sr = 1;
                        foreach ($results as $row) { ?>
                            <tr>
                                <td><?= $sr ?></td>
                                <td><?= htmlspecialchars($row['ulsc_id']) ?></td>
                                <td><?= htmlspecialchars($row['ulsc_name']) ?></td>
                             
                                <td>
                                    <a href="addulsc.php?edit_id=<?= $row['id'] ?>">
                                        <img src="../assets/images/edit.jpg" alt="Edit" width="20" height="20">
                                    </a>
                                </td>
                                <td>
                                    <a href="addulsc.php?delete_id=<?= $row['id'] ?>"
                                        onclick="return confirm('Are you sure?')">
                                        <img src="../assets/images/delete.jpg" alt="Edit" width="20" height="20">
                                    </a>
                                </td>
                            </tr>
                            <?php
                            $sr++;
                        } ?>
                    </tbody>

                </table>
            </section>
            <a href="download.php" class="btn btn-primary" style="margin-top: 10px;">Download PDF</a>

        </main>
    </div>

    <script>
        function openNav() {
            document.getElementById("mySidenav").style.width = "250px";
        }
        function closeNav() {
            document.getElementById("mySidenav").style.width = "0";
            document.body.removeEventListener('click', closeNavOnOutsideClick);
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