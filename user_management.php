<?php
session_start();

if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['commander', 'admin'])) {
    header("Location: login.php");
    exit();
}

require_once 'mysqli_connect.php'; // Προσαρμόστε αυτό το αρχείο με τη σύνδεση στη βάση δεδομένων σας

// Συνάρτηση για να ενημερώσουμε τον πίνακα `post`
function updatePostTable($dbc, $nameid, $postid, $setNa = false) {
    if ($setNa) {
        $query = "update serves_on 
                  set postid = 5 
                  where nameid = ?;
          ";
        $stmt = $dbc->prepare($query);
        $stmt->bind_param("i", $nameid);
        $stmt->execute();
    } else {
        $query = "INSERT INTO serves_on (postid, nameid) VALUES (?, ?)
                  ON DUPLICATE KEY UPDATE postid=VALUES(postid)";
        $stmt = $dbc->prepare($query);
        $stmt->bind_param("ii", $postid, $nameid);
        $stmt->execute();
    }
}

// Προσθήκη χρήστη
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $nameid = $_POST['nameid'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $AFM = $_POST['AFM'];
    $rankid = $_POST['rankid'];
    $postid = $_POST['postid'];

    $query = "INSERT INTO names (nameid, name, surname, AFM, rankid) VALUES (?, ?, ?, ?, ?)";
    $stmt = $dbc->prepare($query);
    $stmt->bind_param("isssi", $nameid, $name, $surname, $AFM, $rankid);
    $stmt->execute();

    updatePostTable($dbc, $nameid, $postid);
}

// Επεξεργασία χρήστη
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_user'])) {
    $nameid = $_POST['nameid'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $AFM = $_POST['AFM'];
    $rankid = $_POST['rankid'];
    $postid = $_POST['postid'];

    $query = "UPDATE names SET name=?, surname=?, AFM=?, rankid=? WHERE nameid=?";
    $stmt = $dbc->prepare($query);
    $stmt->bind_param("sssii", $name, $surname, $AFM, $rankid, $nameid);
    $stmt->execute();

    updatePostTable($dbc, $nameid, $postid);
}

// Διαγραφή χρήστη
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $nameid = $_POST['nameid'];
    updatePostTable($dbc, $nameid, null, true);  // Ενημέρωση για να θέσουμε το na = 1
}

// Ανάκτηση χρηστών
$query = "SELECT n.nameid, n.name, n.surname, n.AFM, r.rankName,
                 s.postid
          FROM names n
          JOIN ranki r ON n.rankid = r.rankid
          JOIN serves_on s ON n.nameid = s.nameid
          WHERE s.postid != 5
          ORDER BY r.rankid";
$result = $dbc->query($query);
// Left join sto query
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Διαχείριση Χρηστών - Υπηρεσίες 165 ΜΠΕΠ(RM-70)</title>
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

        .form-container {
            margin-bottom: 20px;
        }

        .form-container label {
            display: block;
            margin-bottom: 5px;
        }

        .form-container input, .form-container select {
            margin-bottom: 10px;
            padding: 8px;
            width: 30%;
        }

        .form-container button {
            padding: 8px 16px;
            margin-top: 10px;
        }

        .small-button {
            padding: 5px 10px;
            font-size: 12px;
        }

        .search-container {
            margin-bottom: 20px;
        }

        .search-container input {
            padding: 8px;
            width: 30%;
        }
    </style>
</head>
<body>

<header>
    <h1>Διαχείριση Χρηστών - Υπηρεσίες 165 ΜΠΕΠ(RM-70)</h1>
</header>

<nav>
    <a onclick="navigateToHome()">Home</a>
    <a onclick="navigateToNotAvailable()">Not Available Personel</a>
    <a onclick="navigateToSwap()">Duty Swap</a>
    <a onclick="navigateToLogout()">Logout</a>
</nav>

<div class="container">
    <h2>Διαχείριση Χρηστών</h2>

    <div class="form-container">
        <h3>Προσθήκη Χρήστη</h3>
        <form method="post" action="user_management.php">
            <label for="nameid">ID</label>
            <input type="number" id="nameid" name="nameid" required>
            <label for="name">Όνομα</label>
            <input type="text" id="name" name="name" required>
            <label for="surname">Επώνυμο</label>
            <input type="text" id="surname" name="surname" required>
            <label for="AFM">ΑΦΜ</label>
            <input type="text" id="AFM" name="AFM" required>
            <label for="rankid">Βαθμός</label>
            <select id="rankid" name="rankid" required>
                <?php
                $rankQuery = "SELECT rankid, rankName FROM ranki ORDER BY rankid";
                $rankResult = $dbc->query($rankQuery);
                while ($rankRow = $rankResult->fetch_assoc()) {
                    echo "<option value='" . $rankRow['rankid'] . "'>" . $rankRow['rankName'] . "</option>";
                }
                ?>
            </select>
            <label for="postid">Υπηρεσία</label>
            <select id="postid" name="postid" required>
                <option value="1">EAS</option>
                <option value="2">AYDM</option>
                <option value="3">BAYDM</option>
                <option value="4">APIL</option>
            </select>
            <button type="submit" name="add_user" class="small-button">Προσθήκη</button>
        </form>
    </div>

    <div>
        <h3>Λίστα Χρηστών</h3>
        <div class="search-container">
            <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Αναζήτηση για ονόματα..">
        </div>
        <table id="userTable">
            <tr>
                <th>ID</th>
                <th>Όνομα</th>
                <th>Επώνυμο</th>
                <th>ΑΦΜ</th>
                <th>Βαθμός</th>
                <th>Υπηρεσία</th>
                <th>Ενέργειες</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['nameid']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['surname']; ?></td>
                    <td><?php echo $row['AFM']; ?></td>
                    <td><?php echo $row['rankName']; ?></td>
                    <td><?php echo ($row['postid'] == 1) ? 'EAS' : (($row['postid'] == 2) ? 'AYDM' : (($row['postid'] == 3) ? 'BAYDM' : 'APIL')); ?></td>
                    <td>
                        <button class="small-button" onclick='editUser(<?php echo json_encode($row); ?>)'>Επεξεργασία</button>
                        <form method="post" action="user_management.php" style="display:inline;">
                            <input type="hidden" name="nameid" value="<?php echo $row['nameid']; ?>">
                            <button type="submit" name="delete_user" class="small-button">Διαγραφή</button>
                        </form>
                        <button class="small-button" onclick='navigateToStatistics(<?php echo $row["nameid"]; ?>)'>Στατιστικά</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div class="form-container" id="editFormContainer" style="display:none;">
        <h3><a name="edit-form">Επεξεργασία Χρήστη</a></h3>
        <form method="post" action="user_management.php">
            <input type="hidden" id="edit_nameid" name="nameid" required>
            <label for="edit_name">Όνομα</label>
            <input type="text" id="edit_name" name="name" required>
            <label for="edit_surname">Επώνυμο</label>
            <input type="text" id="edit_surname" name="surname" required>
            <label for="edit_AFM">ΑΦΜ</label>
            <input type="text" id="edit_AFM" name="AFM" required>
            <label for="edit_rankid">Βαθμός</label>
            <select id="edit_rankid" name="rankid" required>
                <?php
                $rankResult = $dbc->query($rankQuery);
                while ($rankRow = $rankResult->fetch_assoc()) {
                    echo "<option value='" . $rankRow['rankid'] . "'>" . $rankRow['rankName'] . "</option>";
                }
                ?>
            </select>
            <label for="edit_postid">Υπηρεσία</label>
            <select id="edit_postid" name="postid" required>
                <option value="1">EAS</option>
                <option value="2">AYDM</option>
                <option value="3">BAYDM</option>
                <option value="4">APIL</option>
            </select>
            <button type="submit" name="edit_user" class="small-button">Ενημέρωση</button>
        </form>
    </div>
</div>

<script>
    function navigateToLogout() {
        window.location.href = 'logout.php';
    }

    function navigateToHome() {
        window.location.href = 'welcome.php';
    }
    function navigateToSwap() {
        window.location.href = 'index.php';
    }

    function navigateToReportPanel() {
        window.location.href = 'graphs.php';
    }

    function navigateToStatistics(nameid) {
        window.location.href = 'fetch_statistics.php?nameid=' + nameid;
    }
    function navigateToNotAvailable() {
        window.location.href = 'NotAvailablePersonel.php';
    }
    function editUser(user) {
        document.getElementById('edit_nameid').value = user.nameid;
        document.getElementById('edit_name').value = user.name;
        document.getElementById('edit_surname').value = user.surname;
        document.getElementById('edit_AFM').value = user.AFM;
        document.getElementById('edit_rankid').value = user.rankid;
        document.getElementById('edit_postid').value = user.postid;
        document.getElementById('editFormContainer').style.display = 'block';
        window.location.hash = 'edit-form';
    }

    function filterTable() {
        const searchInput = document.getElementById('searchInput');
        const filter = searchInput.value.toUpperCase();
        const table = document.getElementById('userTable');
        const tr = table.getElementsByTagName('tr');
        for (let i = 1; i < tr.length; i++) {
            let td = tr[i].getElementsByTagName('td');
            let showRow = false;
            for (let j = 0; j < td.length; j++) {
                if (td[j]) {
                    if (td[j].innerText.toUpperCase().indexOf(filter) > -1) {
                        showRow = true;
                        break;
                    }
                }
            }
            tr[i].style.display = showRow ? '' : 'none';
        }
    }
</script>

<footer>
    <p>&copy; Υπηρεσίες 165 ΜΠΕΠ(RM-70)</p>
</footer>
</body>
</html>
