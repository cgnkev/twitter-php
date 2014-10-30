<?php
session_start();
$p=$_SESSION['pw'];
$name=$_SESSION['namamu'];
$me=$_SESSION['us'];
$user=$_REQUEST['user'];
include 'Class.php';
$baru=new Cuser;
$baru->startConn();
$baru->deleteFollow($me,$user);
header("location:home.php");
?>
