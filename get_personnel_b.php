<?php
require_once 'mysqli_connect.php';

$post = $_GET['post'];
$month = $_GET['month'] + 1;
$year = $_GET['year'];
$exclude = $_GET['exclude'];

$sql = "SELECT distinct i.nameid, r.rankName, n.name, n.surname, i.rankid
        FROM ipiresia i
        JOIN date d ON i.dateid = d.dateid
        JOIN names n ON i.nameid = n.nameid
        JOIN ranki r ON i.rankid = r.rankid
        WHERE i.nameid <> ?
        and i.postid = ?
        AND MONTH(i.dateid) = ? AND YEAR(i.dateid) = ?
        order by i.rankid; ";

$stmt = $dbc->prepare($sql);
$stmt->bind_param("iiii", $exclude, $post, $month, $year );
$stmt->execute();
$result = $stmt->get_result();

$personnel = array();
while ($row = $result->fetch_assoc()) {
    $personnel[] = $row;
}

header('Content-Type: application/json');
echo json_encode($personnel);
?>
