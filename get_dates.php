<?php
require_once 'mysqli_connect.php';

$personnel = $_GET['person'];
$post = $_GET['post'];
$month = $_GET['month'] + 1;
$year = $_GET['year'];

if (isset($personnel, $post, $month, $year)) {
    $sql = "SELECT d.dateid AS date, r.rankName, n.name, n.surname
            FROM ipiresia i
            JOIN date d ON i.dateid = d.dateid
            JOIN names n ON i.nameid = n.nameid
            JOIN ranki r ON n.rankid = r.rankid
            WHERE i.nameid = ?
            AND i.postid = ?
            AND MONTH(i.dateid) = ? AND YEAR(i.dateid) = ?;";

    $stmt = $dbc->prepare($sql);
    $stmt->bind_param("iiii", $personnel, $post, $month, $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);

    $stmt->close();
} else {
    echo json_encode(['error' => 'Missing parameters']);
}

$dbc->close();
?>
