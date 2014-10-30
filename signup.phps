<?php
include 'Class.php';
	 $n=$_POST['nama'];
	 $u=$_POST['uname'];
	 $p=$_POST['pword'];
	 $lo=$_POST['loc'];
	 $desc=$_POST['desc'];

$newUser=new Cuser;
$newUser->startConn();
if(strlen($p)>0 && strlen($p)<8)
{if(strlen($u)<8) echo "Username dan password anda minimal 8 karakter<br><a href=\"signup.html\"><input type=\"button\" value=\"Kembali\"></a>";}
else if($n!=''&&$u!=''&&$p!='')
{
if(!$newUser->compareUsername($u)){
$ex=$newUser->addUser($n,$u,$p,$lo,$desc);
$target_path = "photo/";

$target_path = $target_path 
.$u.".".strtolower(substr($_FILES['uploadfile']['name'],-3)); 
move_uploaded_file($_FILES['uploadfile']['tmp_name'], $target_path);
header("location:index.html");
}
else
{echo "ID sudah ada<br><a href=\"signup.html\"><input type=\"button\" value=\"Kembali\"></a>";}
}
else
{echo "Username dan password juga nama anda tak boleh kosong<br><a href=\"signup.html\"><input type=\"button\" value=\"Kembali\"></a>";}
?>
