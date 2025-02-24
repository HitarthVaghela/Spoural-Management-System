<?php

session_start();
include('../includes/config.php');

// Check if user is logged in, else redirect to login

// Fetch session data
$admin_username = $_SESSION['login'];
function generateRandomPassword($length = 10)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    return substr(str_shuffle($chars), 0, $length);
}

$message = "";

// **FETCH ADMIN DATA FOR EDITING**
$editData = null;
if (isset($_GET['edit_id'])) {
    $admin_id = $_GET['edit_id'];
    $sql = "SELECT * FROM admins WHERE admin_id=:admin_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':admin_id', $admin_id, PDO::PARAM_STR);
    $query->execute();
    $editData = $query->fetch(PDO::FETCH_ASSOC);
}

// **INSERT ADMIN**
if (isset($_POST['add_admin'])) {
    $admin_name = $_POST['admin_name'];
    $admin_id = $_POST['admin_id'];
    $random_password = generateRandomPassword(); // Generate a random password

    $sql = "INSERT INTO admins (admin_id, admin_name, password) VALUES (:admin_id, :admin_name, :password)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':admin_id', $admin_id, PDO::PARAM_STR);
    $query->bindParam(':admin_name', $admin_name, PDO::PARAM_STR);
    $query->bindParam(':password', $random_password, PDO::PARAM_STR);

    if ($query->execute()) {
        echo "<script> window.location.href='addadmin.php';</script>";
    } else {
        echo "";
    }
}

// **UPDATE ADMIN**
if (isset($_POST['edit_admin'])) {
    $admin_id = $_POST['admin_id'];
    $admin_name = $_POST['admin_name'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $editData['password'];

    $sql = "UPDATE admins SET admin_name = :admin_name, password = :password WHERE admin_id = :admin_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':admin_id', $admin_id, PDO::PARAM_STR);
    $query->bindParam(':admin_name', $admin_name, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);

    if ($query->execute()) {
        echo "<script> window.location.href='addadmin.php';</script>";
    } else {
        echo "";
    }
}

// **DELETE ADMIN**
if (isset($_GET['delete_id'])) {
    $admin_id = $_GET['delete_id'];

    $sql = "DELETE FROM admins WHERE admin_id = :admin_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':admin_id', $admin_id, PDO::PARAM_STR);

    if ($query->execute()) {
        echo " <Script>window.location.href='addadmin.php';</script>";
    } else {
        echo "";
    }
}

// **FETCH ALL ADMINS**
$sql = "SELECT * FROM admins";
$query = $dbh->prepare($sql);
$query->execute();
$admins = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spoural Management System - Admin</title>
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
                <h2><?= isset($editData) ? 'Edit Admin' : 'New Admin' ?></h2>
                <form method="POST" action="addadmin.php">
                    <label>Admin ID:</label>
                    <input type="text" name="admin_id" value="<?= $editData['admin_id'] ?? '' ?>">
                    <label>Admin Name:</label>
                    <input type="text" name="admin_name" value="<?= $editData['admin_name'] ?? '' ?>" required>
                    <?php if (isset($editData)): ?>
                        <button type="submit" name="edit_admin">Submit</button>

                    <?php else: ?>
                        <button type="submit" name="add_admin">Submit</button>
                    <?php endif; ?>
                </form>
            </section>

            <section>
                <h2>View Admin Details</h2>
                <table border="2px" class="table table-bordered table-striped small-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Admin ID</th>
                            <th>Admin Name</th>
                            <th>Edit</th>
                            <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($admins as $admin) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($admin['id']); ?></td>
                                <td><?php echo htmlspecialchars($admin['admin_id']); ?></td>
                                <td><?php echo htmlspecialchars($admin['admin_name']); ?></td>
                                <td>
                                    <a href="addadmin.php?edit_id=<?php echo $admin['admin_id']; ?>">
                                        <img src="../assets/images/edit.jpg" alt="Edit" width="20" height="20">
                                    </a>
                                </td>
                                <td>
                                    <a href="addadmin.php?delete_id=<?php echo $admin['admin_id']; ?>"
                                        onclick="return confirm('Are you sure you want to delete this admin?');">
                                        <img src="../assets/images/delete.jpg" alt="Edit" width="20" height="20">
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
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
    </script>
</body>

</html>