<?php
session_start();
include('../includes/config.php');

// **Fetch ULSC Member's Department**
$ulsc_id = $_SESSION['ulsc_id'];
$sql = "SELECT u.dept_id, d.dept_name, u.ulsc_name 
        FROM ulsc u 
        JOIN departments d ON u.dept_id = d.id 
        WHERE u.ulsc_id = :ulsc_id";

$query = $dbh->prepare($sql);
$query->bindParam(':ulsc_id', $ulsc_id, PDO::PARAM_STR);
$query->execute();
$ulsc = $query->fetch(PDO::FETCH_ASSOC);

if (!$ulsc) {
    echo "<script>alert('ULSC member not found'); window.location.href='ulsc_dashboard.php';</script>";
    exit;
}

// Store ULSC name and department name
$ulsc_name = htmlspecialchars($ulsc['ulsc_name']);
$dept_name = htmlspecialchars($ulsc['dept_name']);

// **Fetch Events**
$sql = "SELECT id, event_name, event_type FROM events";
$query = $dbh->prepare($sql);
$query->execute();
$events = $query->fetchAll(PDO::FETCH_ASSOC);





$query = "SELECT e.id, e.event_name, e.event_type, 
                 (SELECT COUNT(*) FROM participants p 
                  WHERE p.event_id = e.id AND p.dept_id = {$ulsc['dept_id']}) AS participant_count
          FROM events e";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ULSC - View Events</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .sidenav {
            width: 250px;
            position: fixed;
            left: -250px;
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
        .sidenav.open {
            left: 0;
        }
        .event-header { 
            font-size: 20px; 
            font-weight: bold; 
            margin-top: 20px; 
            cursor: pointer; 
        }
        .no-participants { background-color: #ffcccc; } /* Highlight events with no participants */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; display: none; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color:rgb(36, 25, 25); }
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
        
        <h1>ULSC - View Events</h1>

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

    <h2>Event List</h2>

    <?php while ($row = $result->fetch_assoc()): ?>
    <div class="event-header <?= $row['participant_count'] == 0 ? 'no-participants' : '' ?>" 
         <?= $row['participant_count'] > 0 ? 'onclick="toggleTable(' . $row['id'] . ')"' : '' ?>>
        <?= htmlspecialchars($row['event_name']) ?> (<?= htmlspecialchars($row['event_type']) ?>) 
        <?= $row['participant_count'] > 0 ? '[+]' : '' ?>
    </div>
    
    <?php if ($row['participant_count'] > 0): ?>
    <table id="participants-table-<?= $row['id'] ?>">
        <thead>
            <tr>
                <th>Student ID</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $event_id = $row['id'];
            $participantQuery = "SELECT student_id FROM participants 
                     WHERE event_id = $event_id AND dept_id = {$ulsc['dept_id']}";

            $participantResult = $conn->query($participantQuery);

            while ($participant = $participantResult->fetch_assoc()) {
                echo "<tr><td>" . htmlspecialchars($participant['student_id']) . "</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <?php endif; ?>
<?php endwhile; ?>



    <script>
    function openNav() {
        document.getElementById("mySidenav").classList.add("open");
    }
    function closeNav() {
        document.getElementById("mySidenav").classList.remove("open");
    }
    document.addEventListener("click", function(event) {
        var sidebar = document.getElementById("mySidenav");
        var sidebarButton = document.querySelector("span[onclick='openNav()']");

        // Check if the click is outside the sidebar and not on the open button
        if (!sidebar.contains(event.target) && !sidebarButton.contains(event.target)) {
            closeNav();
        }
    });
    function toggleTable(eventId) {
    let table = $("#participants-table-" + eventId);
    let header = $(".event-header[onclick='toggleTable(" + eventId + ")']");

    if (table.is(":visible")) {
        table.slideUp();
        header.html(header.html().replace("[-]", "[+]"));
    } else {
        table.slideDown();
        header.html(header.html().replace("[+]", "[-]"));
    }
}

    </script>

    <footer>
        <p>&copy; 2025 ULSC Dashboard. All Rights Reserved.</p>
    </footer>
</body>
</html>
