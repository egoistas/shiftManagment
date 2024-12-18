<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "ipiresies";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$postid = $_GET['postid'];

$sql = "SELECT rankName, n.surname, n.name, n.nameid, r.num_of_duty
        FROM names n 
        JOIN serves_on s ON n.nameid = s.nameid 
        JOIN ranki r on r.rankid = n.rankid 
        WHERE s.postid = ?
        ORDER BY r.rankid ;";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $postid);
$stmt->execute();
$result = $stmt->get_result();

$names = array();
while($row = $result->fetch_assoc()) {
    $names[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($names);
?>
