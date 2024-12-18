<?php
session_start();

if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['commander', 'admin'])) {
    header("Location: login.php");
    exit();
}

require_once 'mysqli_connect.php'; // Προσαρμόστε αυτό το αρχείο με τη σύνδεση στη βάση δεδομένων σας

// Ελέγξτε αν έχει σταλεί το nameid μέσω GET
if (!isset($_GET['nameid'])) {
    echo "No user specified.";
    exit();
}

$nameid = intval($_GET['nameid']);

// Ερώτημα για να πάρουμε το όνομα και το επώνυμο
$query_name = "SELECT name, surname FROM names WHERE nameid = ?";
$stmt_name = $dbc->prepare($query_name);
$stmt_name->bind_param("i", $nameid);
$stmt_name->execute();
$result_name = $stmt_name->get_result();
$row_name = $result_name->fetch_assoc();
$name = $row_name['name'];
$surname = $row_name['surname'];

// Ερώτημα για να πάρουμε τον συνολικό αριθμό υπηρεσιών
$query_total = "SELECT COUNT(*) AS total_services FROM ipiresia WHERE nameid = ?";
$stmt_total = $dbc->prepare($query_total);
$stmt_total->bind_param("i", $nameid);
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$row_total = $result_total->fetch_assoc();
$total_services = $row_total['total_services'];

// Ερώτημα για να πάρουμε τον συνολικό αριθμό υπηρεσιών τα Σαββατοκύριακα
$query_sk = "SELECT COUNT(*) AS total_services_sk FROM ipiresia 
             JOIN `date` ON ipiresia.dateid = `date`.dateid
             WHERE nameid = ? AND sk = 1";
$stmt_sk = $dbc->prepare($query_sk);
$stmt_sk->bind_param("i", $nameid);
$stmt_sk->execute();
$result_sk = $stmt_sk->get_result();
$row_sk = $result_sk->fetch_assoc();
$total_services_sk = $row_sk['total_services_sk'];

// Ερώτημα για να πάρουμε τον συνολικό αριθμό υπηρεσιών τις αργίες
$query_argeia = "SELECT COUNT(*) AS total_services_argeia FROM ipiresia 
                 JOIN `date` ON ipiresia.dateid = `date`.dateid
                 WHERE nameid = ? AND argeia = 1";
$stmt_argeia = $dbc->prepare($query_argeia);
$stmt_argeia->bind_param("i", $nameid);
$stmt_argeia->execute();
$result_argeia = $stmt_argeia->get_result();
$row_argeia = $result_argeia->fetch_assoc();
$total_services_argeia = $row_argeia['total_services_argeia'];

// Ερώτημα για να πάρουμε όλα τα dateid του συγκεκριμένου χρήστη
$query_dateid = "SELECT dateid FROM ipiresia WHERE nameid = ?";
$stmt_dateid = $dbc->prepare($query_dateid);
$stmt_dateid->bind_param("i", $nameid);
$stmt_dateid->execute();
$result_dateid = $stmt_dateid->get_result();
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Στατιστικά Χρήστη</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            padding: 20px;
            max-width: 900px;
            margin: auto;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h2 {
            color: #4CAF50;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        .date-select-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .date-select {
            display: flex;
            align-items: center;
            margin: 0 10px;
        }

        .date-select label {
            margin-right: 10px;
        }

        .submit-btn {
            margin-left: 10px;
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .submit-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Στατιστικά Χρήστη</h2>
    <form action="" method="GET">
        <div class="date-select-container">
            <div class="date-select">
                <label for="start_date">Από:</label>
                <input type="date" id="start_date" name="start_date">
            </div>
            <div class="date-select">
                <label for="end_date">Έως:</label>
                <input type="date" id="end_date" name="end_date">
            </div>
            <input type="submit" value="Υποβολή" class="submit-btn">
        </div>
    </form>

    <table>
        <tr>
            <th>ID Χρήστη</th>
            <th>Όνομα</th>
            <th>Επώνυμο</th>
            <th>Συνολικός Αριθμός Υπηρεσιών</th>
            <th>Υπηρεσίες Σαββατοκύριακα</th>
            <th>Υπηρεσίες Αργίες</th>
        </tr>
        <tr>
            <td><?php echo htmlspecialchars($nameid); ?></td>
            <td><?php echo htmlspecialchars($name); ?></td>
            <td><?php echo htmlspecialchars($surname); ?></td>
            <td><?php echo htmlspecialchars($total_services); ?></td>
            <td><?php echo htmlspecialchars($total_services_sk); ?></td>
            <td><?php echo htmlspecialchars($total_services_argeia); ?></td>
        </tr>
    </table>

    <h2>Λίστα ημερομηνιών (dateid) για τον χρήστη</h2>
    <table>
        <tr>
            <th>dateid</th>
        </tr>
        <?php while ($row_dateid = $result_dateid->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row_dateid['dateid']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
