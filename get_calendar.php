<?php
// get_calendar.php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "ipiresies";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$sql = "
    SELECT i.dateid,r.rankName, n.name, n.surname, 'eas' AS post, i.postid 
    FROM names n 
    JOIN ipiresia i ON i.nameid = n.nameid 
    JOIN post p ON p.postid = i.postid
    JOIN ranki r on r.rankid = i.rankid
    WHERE p.eas != 0
    UNION ALL
    SELECT i.dateid,r.rankName, n.name, n.surname, 'aydm' AS post, i.postid 
    FROM names n 
    JOIN ipiresia i ON i.nameid = n.nameid 
    JOIN post p ON p.postid = i.postid 
    JOIN ranki r on r.rankid = i.rankid
    WHERE p.aydm != 0
    UNION ALL
    SELECT i.dateid,r.rankName, n.name, n.surname, 'baydm' AS post, i.postid 
    FROM names n 
    JOIN ipiresia i ON i.nameid = n.nameid 
    JOIN post p ON p.postid = i.postid 
    JOIN ranki r on r.rankid = i.rankid
    WHERE p.baydm != 0
    UNION ALL
    SELECT i.dateid, r.rankName, n.name, n.surname, 'apil' AS post, i.postid 
    FROM names n 
    JOIN ipiresia i ON i.nameid = n.nameid 
    JOIN post p ON p.postid = i.postid 
    JOIN ranki r on r.rankid = i.rankid
    WHERE p.apil != 0
";

$result = $conn->query($sql);
$data = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

$conn->close();

echo json_encode($data);
?>
