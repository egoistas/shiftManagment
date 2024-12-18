<?php
include_once("mysqli_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['duty']) && is_array($_POST['duty'])) {
        $today = new DateTime();
        $today->modify('first day of next month');
        $nextMonth = $today->format('m');
        $nextYear = $today->format('Y');

        foreach ($_POST['duty'] as $nameid => $dates) {
            foreach ($dates as $date => $value) {
                if ($value === '1') {
                    $nameid = intval($nameid);
                    $dateid = date('Y-m-d', strtotime($date));

                    $q = "SELECT r.rankid, s.postid FROM ranki r 
                          JOIN names n ON r.rankid = n.rankid 
                          JOIN serves_on s ON s.nameid = n.nameid
                          WHERE n.nameid = ?;";
                    $stmt = mysqli_prepare($dbc, $q);

                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, 'i', $nameid);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $rankid, $postid);
                        mysqli_stmt_fetch($stmt);
                        mysqli_stmt_close($stmt);

                        $insert_query = "INSERT INTO ipiresia (nameid, dateid, rankid, postid) VALUES (?, ?, ?, ?)";
                        $insert_stmt = mysqli_prepare($dbc, $insert_query);

                        if ($insert_stmt) {
                            mysqli_stmt_bind_param($insert_stmt, 'isii', $nameid, $dateid, $rankid, $postid);
                            mysqli_stmt_execute($insert_stmt);
                            mysqli_stmt_close($insert_stmt);
                        } else {
                            die("Insert Statement Error: " . mysqli_error($dbc));
                        }

                    } else {
                        die("Select Statement Error: " . mysqli_error($dbc));
                    }
                }
            }
        }

        // Redirect to the same page with a success message
        header("Location: eas_schedule_success.php");
        exit();
    } else {
        echo "No schedule data received.";
    }
} else {
    echo "Invalid request method.";
}
?>
