<?php
require 'mysqli_connect.php'; // το αρχείο που περιέχει τη σύνδεση με τη βάση δεδομένων

$startDate = $_GET['start_date'];
$endDate = $_GET['end_date'];
$post = $_GET['post'];

// Καθημερινές υπηρεσίες
$query = "
    SELECT n.name, n.surname, r.rankName, COUNT(i.ipiresiaid) as count
    FROM names n
    JOIN ranki r ON n.rankid = r.rankid
    JOIN ipiresia i ON i.nameid = n.nameid
    JOIN date d ON i.dateid = d.dateid
    JOIN post p ON i.postid = p.postid
    WHERE d.dateid BETWEEN ? AND ? AND d.argeia = 0 AND p.$post = 1
    GROUP BY n.name, n.surname, r.rankName, r.rankid
    ORDER BY r.rankid ASC
";

$stmt = $dbc->prepare($query);
$stmt->bind_param('ss', $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();
$dailyRecords = [];
while ($row = $result->fetch_assoc()) {
    $dailyRecords[] = $row;
}

// Αργίες
$query = "
    SELECT n.name, n.surname, r.rankName, 
           COALESCE(SUM(CASE WHEN d.argeia = 1 THEN 1 ELSE 0 END), 0) as holidayCount
    FROM names n
    JOIN ranki r ON n.rankid = r.rankid
    JOIN ipiresia i ON i.nameid = n.nameid
    JOIN date d ON i.dateid = d.dateid
    JOIN post p ON i.postid = p.postid
    WHERE d.dateid BETWEEN ? AND ? AND p.$post = 1
    GROUP BY n.name, n.surname, r.rankName, r.rankid
    ORDER BY r.rankid ASC;
";

$stmt = $dbc->prepare($query);
$stmt->bind_param('ss', $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();
$holidayRecords = [];
while ($row = $result->fetch_assoc()) {
    $holidayRecords[] = $row;
}

// Σαββατοκύριακα
$query = "
    SELECT n.name, n.surname, r.rankName, 
           COALESCE(SUM(CASE WHEN d.sk = 1 THEN 1 ELSE 0 END), 0) as weekendCount
    FROM names n
    JOIN ranki r ON n.rankid = r.rankid
    JOIN ipiresia i ON i.nameid = n.nameid
    JOIN date d ON i.dateid = d.dateid
    JOIN post p ON i.postid = p.postid
    WHERE d.dateid BETWEEN ? AND ? AND p.$post = 1
    GROUP BY n.name, n.surname, r.rankName, r.rankid
    ORDER BY r.rankid ASC;
";

$stmt = $dbc->prepare($query);
$stmt->bind_param('ss', $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();
$weekendRecords = [];
while ($row = $result->fetch_assoc()) {
    $weekendRecords[] = $row;
}

// Συνδυασμός δεδομένων για αργίες και σαββατοκύριακα
$combinedHolidayWeekendRecords = [];
foreach ($holidayRecords as $holiday) {
    $key = $holiday['name'] . ' ' . $holiday['surname'] . ' ' . $holiday['rankName'];
    $combinedHolidayWeekendRecords[$key] = [
        'name' => $holiday['name'],
        'surname' => $holiday['surname'],
        'rankName' => $holiday['rankName'],
        'holidayCount' => $holiday['holidayCount'],
        'weekendCount' => 0
    ];
}

foreach ($weekendRecords as $weekend) {
    $key = $weekend['name'] . ' ' . $weekend['surname'] . ' ' . $weekend['rankName'];
    if (isset($combinedHolidayWeekendRecords[$key])) {
        $combinedHolidayWeekendRecords[$key]['weekendCount'] = $weekend['weekendCount'];
    } else {
        $combinedHolidayWeekendRecords[$key] = [
            'name' => $weekend['name'],
            'surname' => $weekend['surname'],
            'rankName' => $weekend['rankName'],
            'holidayCount' => 0,
            'weekendCount' => $weekend['weekendCount']
        ];
    }
}

echo json_encode([
    'dailyRecords' => $dailyRecords,
    'holidayRecords' => array_values($combinedHolidayWeekendRecords)
]);
?>
