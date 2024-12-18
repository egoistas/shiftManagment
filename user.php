<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php"); 
    exit();
}
ob_start();
if ($_SESSION['role'] == 'admin') {
    header("Location: controller.php");
    exit();
} elseif ($_SESSION['role'] == 'commander') {
    header("Location: welcome.php");
    exit();
} elseif ($_SESSION['role'] == 'eas'){
    header("Location: eas.php");
    exit();
}elseif ($_SESSION['role'] == 'aydm'){
    header("Location: aydm.php");
    exit();
}elseif ($_SESSION['role'] == 'baydm'){
    header("Location: baydm.php");
    exit();
}elseif ($_SESSION['role'] == 'apil'){
    header("Location: apil.php");
    exit();
}
?>

