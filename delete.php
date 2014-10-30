<?php
session_start();
session_destroy();
$user=$_REQUEST['user'];
include 'Class.php';
$baru=new Cuser;
$baru->startConn();
$ex=$baru->deleteUser($user);
if($ex)
echo "delete success";
else
echo "delete gagal";
echo "<BR><a href=\"index.html\"><input type=\"button\" value=\"Kembali Cuy\"></a>";
?>
