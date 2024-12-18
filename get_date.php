<?php
require_once 'mysqli_connect.php';

// Εξασφάλιση ότι οι παράμετροι είναι καθορισμένες και μη κενές
if (!isset($_GET['person']) || !isset($_GET['post']) || !isset($_GET['month']) || !isset($_GET['year'])) {
    echo json_encode(['error' => 'Invalid input']);
    exit();
}

$person = intval($_GET['person']);
$post = intval($_GET['post']);
$month = intval($_GET['month']);
$year = intval($_GET['year']);

if ($person <= 0 || $post <= 0 || $month < 0 || $month > 11 || $year < 2000) {
    echo json_encode(['error' => 'Invalid input']);
    exit();
}

// Προετοιμασία και εκτέλεση του SQL query για την ανάκτηση ημερομηνιών
$query = "
    SELECT DATE_FORMAT(service_date, '%Y-%m-%d') AS date, CONCAT(name, ' ', surname) AS person
    FROM personnel
    WHERE id = ? AND postid = ? AND MONTH(service_date) = ? AND YEAR(service_date) = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("iiii", $person, $post, $month + 1, $year); // Εδώ προσθέτουμε 1 στο μήνα γιατί το MySQL χρησιμοποιεί 1-based μήνες
$stmt->execute();
$result = $stmt->get_result();

$dates = [];
while ($row = $result->fetch_assoc()) {
    $dates[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($dates);
//1926
?>
