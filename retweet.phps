<?php
session_start();
$name=$_SESSION['namamu'];
$u=$_SESSION['us'];
$id=$_REQUEST['postid'];
$reply=$_REQUEST['rep'];
include 'Class.php';
$baru=new Cpost;
$baru->startConn();
$baru->retweet($u,$name,$reply,$id);
header("location:home.php");
?>
