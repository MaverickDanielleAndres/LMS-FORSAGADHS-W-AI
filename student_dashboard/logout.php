<?php

error_reporting(E_ALL ^ E_WARNING);
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "student") {
    header("Location: ../index.php");
    exit();
}

session_start();
unset($_SESSION);
session_destroy();
session_write_close();
header('Location: ../login');
die;
?>