<?php
session_start();

if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['commander', 'admin'])) {
    header("Location: login.php"); 
    exit();
}

require_once 'mysqli_connect.php';
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Υπηρεσίες 165 - Αλλαγή Υπηρεσιών</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>Υπηρεσίες 165 ΜΠΕΠ(RM-70)</h1>
</header>

<nav>
    <a onclick="navigateToHome()">Home</a>
    <a onclick="navigateToReportPanel()">Report Panel</a>
    <a onclick="navigateToLogout()">Logout</a>
</nav>

<div class="container">
    <h2>Αλλαγή Υπηρεσιών</h2>
    <div class="controls">
        <label for="year-select">Έτος:</label>
        <select id="year-select">
            <!-- Οι επιλογές του έτους θα φορτωθούν με JavaScript -->
        </select>
        
        <label for="month-select">Μήνας:</label>
        <select id="month-select">
            <option value="0">Ιανουάριος</option>
            <option value="1">Φεβρουάριος</option>
            <option value="2">Μάρτιος</option>
            <option value="3">Απρίλιος</option>
            <option value="4">Μάιος</option>
            <option value="5">Ιούνιος</option>
            <option value="6">Ιούλιος</option>
            <option value="7">Αύγουστος</option>
            <option value="8">Σεπτέμβριος</option>
            <option value="9">Οκτώβριος</option>
            <option value="10">Νοέμβριος</option>
            <option value="11">Δεκέμβριος</option>
        </select>

        <label for="post-select">Πόστο:</label>
        <select id="post-select" name="postid">
            <option value="">Επιλέξτε πόστο</option>
            <option value="1">EAS</option>
            <option value="2">AYDM</option>
            <option value="3">BAYDM</option>
            <option value="4">APIL</option>
        </select>
        
        <button onclick="loadPersonnel()">Φόρτωση Υπηρεσιών</button>
    </div>

    <form action="update_service.php" method="POST">
        <div class="controls">
            <label for="personnel-a">Άτομο Α:</label>
            <select id="personnel-a" name="personnel-a">
                <option value="">Επιλέξτε άτομο</option>
            </select>

            <label for="dates-a">Ημερομηνίες Α:</label>
            <select id="dates-a" name="dates-a">
                <option value="">Επιλέξτε ημερομηνία</option>
            </select>
        </div>

        <div class="controls">
            <label for="personnel-b">Άτομο Β:</label>
            <select id="personnel-b" name="personnel-b">
                <option value="">Επιλέξτε άτομο</option>
            </select>

            <label for="dates-b">Ημερομηνίες Β:</label>
            <select id="dates-b" name="dates-b">
                <option value="">Επιλέξτε ημερομηνία</option>
            </select>
        </div>

        <button type="submit">Αλλαγή Υπηρεσίας</button>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.0/anime.min.js"></script>
<script src="main.js"></script>

<footer>
    <p>&copy; Υπηρεσίες 165 ΜΠΕΠ(RM-70)</p>
</footer>
</body>
</html>
