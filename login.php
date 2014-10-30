<?php
session_start();
$_SESSION['us']=$_POST['user'];
$_SESSION['pw']=$_POST['pword'];
header("location:home.php");
?>

