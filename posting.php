<?php
include 'Class.php';
session_start();
$n=$_SESSION['namamu'];
$u=$_SESSION['us'];
$p=$_SESSION['pw'];
$post=$_GET['post'];
if($post!='')
{$baru=new Cpost;
$baru->startConn();
$baru->posting($u,$n,$post);
header("location:home.php");}
else
{echo "Blank Post will only waste my memory.. At least insert a word";}

?>
