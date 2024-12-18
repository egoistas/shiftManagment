<?php
session_start();

if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['commander', 'admin'])) {
    header("Location: login.php");
    exit();
}

require_once 'mysqli_connect.php'; 

// Ερώτημα για να πάρουμε όλα τα ονόματα από τον πίνακα names με το rankName
$query_names = "SELECT n.nameid, n.rankid, n.surname, r.rankName, n.name FROM names n JOIN ranki r ON n.rankid = r.rankid ";
$result_names = $dbc->query($query_names);

$results = [];

// Έλεγχος αν έχει γίνει υποβολή της φόρμας
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['nameid']) && isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $nameid = $_GET['nameid'];
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];

    // Ερώτημα για να πάρουμε τα δεδομένα από τη βάση
    $query_data = "SELECT n.nameid, n.surname, n.name, r.rankName, 
                        COUNT(*) AS totalServices,
                        SUM(CASE WHEN DAYOFWEEK(d.dateid) IN (1, 7) THEN 1 ELSE 0 END) AS sk,
                        SUM(CASE WHEN d.dateid BETWEEN ? AND ? THEN 1 ELSE 0 END) AS argeia
                   FROM names n
                   JOIN ranki r ON n.rankid = r.rankid
                   JOIN ipiresia d ON n.nameid = d.nameid
                   WHERE n.nameid = ?
                   GROUP BY n.nameid";
    
    // Προετοιμασία και εκτέλεση του ερωτήματος
    $stmt = $dbc->prepare($query_data);
    $stmt->bind_param("ssi", $start_date, $end_date, $nameid);
    $stmt->execute();
    $result_data = $stmt->get_result();

    // Αποθήκευση αποτελεσμάτων για εμφάνιση στον πίνακα
    $results = $result_data->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Ερώτημα για να πάρουμε όλα τα dateid του επιλεγμένου χρήστη
$query_dates = "SELECT dateid FROM ipiresia WHERE nameid = ?";
$stmt_dates = $dbc->prepare($query_dates);
$stmt_dates->bind_param("i", $nameid);

// Εκτέλεση του ερωτήματος για τα dateid
$dateids = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['nameid'])) {
    $nameid = $_GET['nameid'];
    $stmt_dates->execute();
    $result_dates = $stmt_dates->get_result();
    $dateids = $result_dates->fetch_all(MYSQLI_ASSOC);
}
$stmt_dates->close();

?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Επιλογή Χρήστη και Ημερομηνιών</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h2 {
            color: #333;
        }

        .form-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 50%;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .user-select-container, .date-select-container {
            width: 100%;
            margin-bottom: 20px;
        }

        .user-select, .date-select {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .user-select label, .date-select label {
            margin-right: 10px;
            width: 100px;
        }

        .search-bar {
            width: 100%;
            margin-bottom: 10px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .dropdown {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            max-height: 200px;
            overflow-y: auto;
        }

        table {
            width: 80%;
            border-collapse: collapse;
            margin-top: 20px;
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.form-container form');
            form.addEventListener('submit', async function(event) {
                event.preventDefault(); // Ακυρώνει την προκαθορισμένη συμπεριφορά υποβολής φόρμας

                const formData = new FormData(form); // Δημιουργία αντικειμένου FormData από τη φόρμα

                try {
                    const response = await fetch(form.action + '?' + new URLSearchParams(formData), {
                        method: form.method,
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    });
                    
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    
                    const responseData = await response.text(); // Λήψη των δεδομένων από την απάντηση
                    const resultsTable = document.getElementById('resultsTable');
                    resultsTable.innerHTML = responseData; // Αντικατάσταση του περιεχομένου του πίνακα με τα αποτελέσματα

                } catch (error) {
                    console.error('There has been a problem with your fetch operation:', error);
                }
            });
        });
    </script>
</head>
<body>

<div class="container">
    <h2>Επιλογή Χρήστη και Ημερομηνιών</h2>
    <form method="GET" action="" class="form-container">
        <div class="user-select-container">
            <div class="user-select">
                <label for="searchInput">Αναζήτηση:</label>
                <input type="text" id="searchInput" onkeyup="filterDropdown()" class="search-bar" placeholder="Αναζήτηση ονόματος...">
            </div>
        </div>
        <div class="user-select-container">
            <div class="user-select">
                <label for="userSelect">Επιλέξτε Χρήστη:</label>
                <select id="userSelect" name="nameid" class="dropdown">
                    <option value="">-- Επιλέξτε Χρήστη --</option>
                    <?php while ($row = $result_names->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($row['nameid']); ?>">
                            <?php echo htmlspecialchars($row['rankName'] . " " . $row['surname'] . " " . $row['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        <div class="date-select-container">
            <div class="date-select">
                <label for="start_date">Από:</label>
                <input type="date" id="start_date" name="start_date" required>
            </div>
            <div class="date-select">
                <label for="end_date">Έως:</label>
                <input type="date" id="end_date" name="end_date" required>
            </div>
        </div>
        <input type="submit" value="Υποβολή" class="submit-btn">
    </form>

    <!-- Πίνακας για εμφάνιση αποτελεσμάτων -->
    <table id="resultsTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Επώνυμο</th>
                <th>Όνομα</th>
                <th>Βαθμός</th>
                <th>Συνολικός Αριθμός Υπηρεσιών</th>
                <th>Συνολικός Αριθμός Υπηρεσιών τα ΣΚ</th>
                <th>Συνολικός Αριθμός Υπηρεσιών τις Αργείες</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($results as $row) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['nameid']) . "</td>";
                echo "<td>" . htmlspecialchars($row['surname']) . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['rankName']) . "</td>";
                echo "<td>" . htmlspecialchars($row['totalServices']) . "</td>";
                echo "<td>" . htmlspecialchars($row['sk']) . "</td>";
                echo "<td>" . htmlspecialchars($row['argeia']) . "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Πίνακας για εμφάνιση dateid -->
    <?php if (!empty($dateids)): ?>
        <h3>Αριθμός Date ID του Επιλεγμένου Χρήστη</h3>
        <table>
            <thead>
                <tr>
                    <th>Date ID</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dateids as $dateid): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($dateid['dateid']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
