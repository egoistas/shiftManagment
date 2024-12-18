<?php
require_once 'mysqli_connect.php';

$month = $_GET['month'];
$year = $_GET['year'];

$sql = "SELECT d.dateid, n.name, n.surname, r.rankName, s.postid
        FROM ipiresia i
        JOIN date d ON i.dateid = d.dateid
        JOIN names n ON i.nameid = n.nameid
        JOIN ranki r ON n.rankid = r.rankid
        JOIN post s ON i.postid = s.postid
        WHERE MONTH(d.dateid) = ? AND YEAR(d.dateid) = ?";

$stmt = $dbc->prepare($sql);
$stmt->bind_param("ii", $month, $year);
$stmt->execute();
$result = $stmt->get_result();
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);

$stmt->close();
$dbc->close();
?>
