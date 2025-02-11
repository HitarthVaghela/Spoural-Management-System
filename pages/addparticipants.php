<?php
session_start();
include('../includes/config.php');

// **Ensure ULSC is Logged In**
if (!isset($_SESSION['ulsc_id'])) {
    header("Location: ulsc_login.php");
    exit;
}

// **Fetch ULSC Member's Department ID**
$ulsc_id = $_SESSION['ulsc_id'];
$sql = "SELECT dept_id FROM ulsc WHERE ulsc_id = :ulsc_id";
$query = $dbh->prepare($sql);
$query->bindParam(':ulsc_id', $ulsc_id, PDO::PARAM_STR);
$query->execute();
$ulsc = $query->fetch(PDO::FETCH_ASSOC);

if (!$ulsc) {
    echo "<script>alert('ULSC member not found'); window.location.href='ulsc_dashboard.php';</script>";
    exit;
}

$dept_id = $ulsc['dept_id']; // Auto-assign dept_id

// **Insert Participant**
if (isset($_POST['add_participant'])) {
    $student_id = $_POST['student_id'];
    $event_id = $_POST['event_id'];

    $sql = "INSERT INTO participants (student_id, dept_id, event_id) VALUES (:student_id, :dept_id, :event_id)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':student_id', $student_id, PDO::PARAM_STR);
    $query->bindParam(':dept_id', $dept_id, PDO::PARAM_INT);
    $query->bindParam(':event_id', $event_id, PDO::PARAM_INT);

    if ($query->execute()) {
        $_SESSION['success'] = "Participant Added Successfully!";
    } else {
        $_SESSION['error'] = "Failed to Add Participant";
    }
    header("Location: addparticipants.php");
    exit;
}

// **Delete Participant**
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    $sql = "DELETE FROM participants WHERE id = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $id, PDO::PARAM_INT);

    if ($query->execute()) {
        $_SESSION['success'] = "Participant Deleted Successfully!";
    } else {
        $_SESSION['error'] = "Failed to Delete Participant";
    }
    header("Location: addparticipants.php");
    exit;
}

// **Fetch Events for Dropdown**
$sql = "SELECT id, event_name FROM events";
$query = $dbh->prepare($sql);
$query->execute();
$events = $query->fetchAll(PDO::FETCH_ASSOC);

// **Fetch Participants**
$sql = "SELECT participants.id, participants.student_id, events.event_name 
        FROM participants 
        INNER JOIN events ON participants.event_id = events.id
        WHERE participants.dept_id = :dept_id";
$query = $dbh->prepare($sql);
$query->bindParam(':dept_id', $dept_id, PDO::PARAM_INT);
$query->execute();
$participants = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ULSC - Add Participants</title>
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
        .search-box {
            width: 50%;
            margin: 20px auto;
            display: flex;
            align-items: center;
            background: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .search-box input {
            width: 100%;
            padding: 8px;
            border: none;
            outline: none;
            font-size: 16px;
        }
        .search-box i {
            margin-right: 10px;
            color: #007BFF;
        }
        .confirm-overlay, .message-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .confirm-box, .message-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .confirm-box button, .message-box button {
            margin: 10px;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .confirm-box button:first-child {
            background: #d9534f;
            color: white;
        }

        .confirm-box button:last-child, .message-box button {
            background: #5bc0de;
            color: white;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #ffffff;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: center;
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
        <h1>ULSC - Add Participants</h1>

        <div class="logo">
            <img src="../assets/images/ulsc.png" alt="ULSC Logo">
        </div>

        <div class="dropdown">
            <button class="dropdown-trigger">
                <?php echo htmlspecialchars($_SESSION['ulsc_name']); ?> <i class="fas fa-caret-down"></i>
            </button>
            <div class="dropdown-menu">
                <a href="#" class="dropdown-item"><i class="fas fa-user"></i> My Profile</a>
                <hr>
                <a href="ulsc_logout.php" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Sign-Out</a>
            </div>
        </div>
    </header>

    <main>
        <!-- Add Participant Form (As it was) -->
        <form method="post" action="addparticipants.php">
            <label>Student ID:</label>
            <input type="text" name="student_id" required>

            <label>Event:</label>
            <select name="event_id" required>
                <option value="">Select Event</option>
                <?php foreach ($events as $event) { ?>
                    <option value="<?php echo $event['id']; ?>"><?php echo $event['event_name']; ?></option>
                <?php } ?>
            </select>

            <button type="submit" name="add_participant">Add Participant</button>
        </form>

        <!-- View Participants Table -->
        
            <!-- Search Box -->
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search by Student ID or Event Name">
            </div>
            <!-- Participants Table -->
            <section>
                <h2>Participants List</h2>
                <table id="participantsTable">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>Student ID</th>
                            <th>Event</th>
                            <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sr_no = 1; foreach ($participants as $participant) { ?>
                            <tr>
                                <td><?php echo $sr_no++; ?></td>
                                <td><?php echo $participant['student_id']; ?></td>
                                <td><?php echo $participant['event_name']; ?></td>
                                <td><a href="#" onclick="confirmDelete(<?php echo $participant['id']; ?>)">Delete</a></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </section>
    </main>

    <script>
    function openNav() {
        document.getElementById("mySidenav").style.width = "250px";
    }

    function closeNav() {
        document.getElementById("mySidenav").style.width = "0";
    }
    </script>

    <script>
        document.getElementById("searchInput").addEventListener("keyup", function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll("#participantsTable tbody tr");
            rows.forEach(row => {
                let studentID = row.cells[1].textContent.toLowerCase();
                let eventName = row.cells[2].textContent.toLowerCase();
                row.style.display = studentID.includes(filter) || eventName.includes(filter) ? "" : "none";
            });
        });
    </script>

    <script>
        function confirmDelete(id) {
            let confirmationBox = document.createElement("div");
            confirmationBox.innerHTML = `
                <div class="confirm-box">
                    <p>Are you sure you want to delete this participant?</p>
                    <button onclick="window.location.href='addparticipants.php?delete_id=${id}'">Yes</button>
                    <button onclick="closeConfirmBox()">No</button>
                </div>
            `;
            confirmationBox.classList.add("confirm-overlay");
            document.body.appendChild(confirmationBox);
        }
        
        function closeConfirmBox() {
            document.querySelector(".confirm-overlay").remove();
        }
    </script>


    <footer>
        <p>&copy; 2025 ULSC Dashboard. All Rights Reserved.</p>
    </footer>
</body>
</html>