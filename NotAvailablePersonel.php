<?php
session_start();

if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['commander', 'admin'])) {
    header("Location: login.php");
    exit();
}

require_once 'mysqli_connect.php'; // Αντικαταστήστε με το αρχείο σύνδεσης στη βάση δεδομένων σας

// Ανάκτηση χρηστών χωρίς υπηρεσία (postid = 5)
$query = "SELECT n.nameid, n.name, n.surname, n.AFM, r.rankName
          FROM names n
          JOIN ranki r ON n.rankid = r.rankid
          JOIN serves_on s ON n.nameid = s.nameid
          WHERE s.postid = 5
          ORDER BY r.rankid";
$result = $dbc->query($query);
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Στελέχη Χωρίς Υπηρεσία - Υπηρεσίες 165 ΜΠΕΠ(RM-70)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 1em 0;
        }

        nav {
            background-color: #444;
            overflow: hidden;
        }

        nav a {
            float: left;
            display: block;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
            cursor: pointer;
        }

        nav a:hover {
            background-color: #ddd;
            color: black;
        }

        .container {
            padding: 20px;
        }

        h2 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<header>
    <h1>Στελέχη Χωρίς Υπηρεσία - Υπηρεσίες 165 ΜΠΕΠ(RM-70)</h1>
</header>

<nav>
    <a onclick="navigateToHome()">Home</a>
    <a onclick="navigateToReportPanel()">Report Panel</a>
    <a onclick="navigateToLogout()">Logout</a>
</nav>

<div class="container">
    <h2>Στελέχη Χωρίς Υπηρεσία</h2>

    <table id="userTable">
        <tr>
            <th>ID</th>
            <th>Όνομα</th>
            <th>Επώνυμο</th>
            <th>ΑΦΜ</th>
            <th>Βαθμός</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['nameid']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['surname']; ?></td>
                <td><?php echo $row['AFM']; ?></td>
                <td><?php echo $row['rankName']; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<script>
    function navigateToLogout() {
        window.location.href = 'logout.php';
    }

    function navigateToHome() {
        window.location.href = 'welcome.php';
    }

    function navigateToReportPanel() {
        window.location.href = 'graphs.php';
    }
</script>

<footer>
    <p>&copy; Υπηρεσίες 165 ΜΠΕΠ(RM-70)</p>
</footer>
</body>
</html>
