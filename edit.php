<?php
session_start();
$me=$_SESSION['us'];
$p=$_SESSION['pw'];
include 'Class.php';
$baru=new Cuser;
$baru->startConn();
$row=$baru->getDetail($me);
$name=$row['name'];
$loc=$row['location'];
$desc=$row['description'];
?>
Editing for <?php echo $me?><BR>
<form action="change.php" method="post">
<br>Your desired name:<input type="text" name="nama" value=<?php echo $name ?>>
<br>password : <input type="password" name="pword" value=<?php echo $p ?>>
<br>location : <input type="text" name="loc" value=<?php echo $loc ?>>
<br>Short description : <textarea name="desc"><?php echo $desc ?></textarea>
<br><input type="submit" value="edit"></form>
<a href="home.php"><input type="button" value="Cancel"></a>
