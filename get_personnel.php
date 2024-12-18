<?php
require_once 'mysqli_connect.php';

// Εξασφάλιση ότι οι παράμετροι είναι καθορισμένες και μη κενές
if (!isset($_GET['post']) || !isset($_GET['month']) || !isset($_GET['year'])) {
    echo json_encode(['error' => 'Invalid input']);
    exit();
}

$post = intval($_GET['post']);
$month = intval($_GET['month']) + 1;
$year = intval($_GET['year']);


// Προετοιμασία και εκτέλεση του SQL query για την ανάκτηση στελεχών
$query = "SELECT distinct i.nameid ,r.rankName, n.name, n.surname, i.rankid
          FROM ipiresia i 
          JOIN names n ON n.nameid = i.nameid
          JOIN date d ON d.dateid = i.dateid
          JOIN post p ON p.postid = i.postid
          JOIN ranki r on r.rankid = i.rankid 
          WHERE i.postid = ? 
          AND MONTH(d.dateid) = ? 
          AND YEAR(d.dateid) = ?
          order by i.rankid;";

$stmt = $dbc->prepare($query);
if ($stmt === false) {
    echo json_encode(['error' => 'SQL prepare failed']);
    exit();
}

$stmt->bind_param("iii", $post, $month, $year); // Εδώ προσθέτουμε 1 στο μήνα γιατί το MySQL χρησιμοποιεί 1-based μήνες
$stmt->execute();
$result = $stmt->get_result();

$personnel = [];
while ($row = $result->fetch_assoc()) {
    $personnel[] = $row;
}

$stmt->close();
$dbc->close();

header('Content-Type: application/json; charset=UTF-8');
echo json_encode($personnel);
?>
