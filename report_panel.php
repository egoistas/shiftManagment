<?php
session_start();

if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['commander', 'admin'])) {
    header("Location: login.php"); 
    exit();
}
ob_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Υπηρεσίες 165 - Καλώς ήρθατε</title>
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
            animation: fadeInDown 1s ease-in-out;
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

        .dashboard {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .card {
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .card h3 {
            margin-top: 0;
        }

        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 0.1em 0;
        }

        @keyframes fadeInDown {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<header>
    <h1>Υπηρεσίες 165 ΜΠΕΠ(RM-70)</h1>
</header>

<nav>
    <a onclick="navigateToHome()">Home</a>
    <!-- <a onclick="navigateToReportPanel()">Report Panel</a> -->
    <a onclick="navigateToLogout()">Logout</a>
</nav>

<div class="container">
    <h2>Καλώς ήρθατε</h2>
    <div class="dashboard">
        <div class="card">
            <h3>Σύνοψη Αναφορών</h3>
            <p>Δείτε τις τελευταίες αναφορές και στατιστικά στοιχεία.</p>
            <button onclick="navigateToReportPanel()">Προβολή Αναφορών</button>
        </div>
        <div class="card">
            <h3>Διαχείριση Χρηστών</h3>
            <p>Διαχειριστείτε τους χρήστες του συστήματος.</p>
            <button onclick="navigateToUserManagement()">Διαχείριση Χρηστών</button>
        </div>
        <div class="card">
            <h3>Ρυθμίσεις Συστήματος</h3>
            <p>Ενημερώστε τις ρυθμίσεις του συστήματος.</p>
            <button onclick="navigateToSettings()">Ρυθμίσεις</button>
        </div>
        <div class="card">
            <h3>Επικοινωνία</h3>
            <p>Επικοινωνήστε με την υποστήριξη.</p>
            <button onclick="navigateToSupport()">Επικοινωνία</button>
        </div>
    </div>
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

    function navigateToUserManagement() {
        window.location.href = 'user_management.php';
    }

    function navigateToSettings() {
        window.location.href = 'settings.php';
    }

    function navigateToSupport() {
        window.location.href = 'support.php';
    }
</script>

<footer>
    <p>&copy; Υπηρεσίες 165 ΜΠΕΠ(RM-70)</p>
</footer>
</body>
</html>
