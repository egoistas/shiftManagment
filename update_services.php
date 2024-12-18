<?php
session_start();
require_once 'mysqli_connect.php';

$action = 'Αλλαγή Υπηρεσίας';

$nameOfFirst = intval($_POST['personnel-a']);
$nameOfSecond = intval($_POST['personnel-b']);
$dateOfFirst = $_POST['dates-a'];
$dateOfSecond = $_POST['dates-b'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    var_dump($_POST);
    print "$nameOfFirst, $nameOfSecond, $dateOfFirst";

    // Start a transaction
    $dbc->begin_transaction();

    try {
        // First query to fetch first individual's record
        $query = "SELECT * FROM ipiresia WHERE nameid = ? AND dateid = ?";
        $stmt = $dbc->prepare($query);
        $stmt->bind_param("is", $nameOfFirst, $dateOfFirst);
        $stmt->execute();
        $result = $stmt->get_result();
        $firstRecord = $result->fetch_assoc();
        $stmt->close();

        // Second query to fetch second individual's record
        $query2 = "SELECT * FROM ipiresia WHERE nameid = ? AND dateid = ?";
        $stmt2 = $dbc->prepare($query2);
        $stmt2->bind_param("is", $nameOfSecond, $dateOfSecond);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $secondRecord = $result2->fetch_assoc();
        $stmt2->close();

        // Update first individual's record to have the second individual's data
        $updateQuery1 = "UPDATE ipiresia SET rankid = ?, postid = ?, nameid = ? WHERE dateid = ? AND nameid = ?";
        $updateStmt1 = $dbc->prepare($updateQuery1);
        $updateStmt1->bind_param("iiisi", $secondRecord['rankid'], $secondRecord['postid'], $secondRecord['nameid'], $dateOfFirst, $nameOfFirst);
        $updateStmt1->execute();
        $updateStmt1->close();

        // Update second individual's record to have the first individual's data
        $updateQuery2 = "UPDATE ipiresia SET rankid = ?, postid = ?, nameid = ? WHERE dateid = ? AND nameid = ?";
        $updateStmt2 = $dbc->prepare($updateQuery2);
        $updateStmt2->bind_param("iiisi", $firstRecord['rankid'], $firstRecord['postid'], $firstRecord['nameid'], $dateOfSecond, $nameOfSecond);
        $updateStmt2->execute();
        $updateStmt2->close();

        // Commit the transaction
        $dbc->commit();

        // Insert audit log with detailed information
        $audit_sql = "INSERT INTO audits (action, first_nameid, first_dateid, second_nameid, second_dateid, user, action_date) VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $audit_stmt = $dbc->prepare($audit_sql);
        $audit_stmt->bind_param("sisiss", $action, $nameOfFirst, $dateOfFirst, $nameOfSecond, $dateOfSecond, $_SESSION['username']);
        $audit_stmt->execute();
        $audit_stmt->close();

        $dbc->close();

        header("Location: index.php");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        $dbc->rollback();
        echo "Failed to swap dates: " . $e->getMessage();
    }
}
?>
