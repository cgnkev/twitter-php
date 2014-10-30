<?php
SESSION_START();
$n=$_POST['nama'];
$u=$_SESSION['us'];
$p=$_POST['pword'];
$lo=$_POST['loc'];
$desc=$_POST['desc'];
include 'Class.php';
$baru=new Cuser;
$baru->startConn();
$baru2=new Cpost;
$baru2->startConn();
if(strlen($p)>0 && strlen($p)<8)
{if(strlen($u)<8) echo "Username dan password anda minimal 8 karakter<br><a href=\"edit.php\"><input type=\"button\" value=\"Kembali\"></a>";}
else if($n!=''&&$u!=''&&$p!='')
{
$_SESSION['pw']=$p;
if($baru->editUser($n,$lo,$desc,$p,$u) )
{$baru2->changePostOwner($n,$u);
echo "update successful";
}
else
echo "gagal update";
echo "<a href=\"home.php?user=$me&pword=$p\"><input type=\"Button\" value=\"Home\"></a>";
}
else
{echo "Username dan password juga nama anda tak boleh kosong<br><a href=\"edit.php\"><input type=\"button\" value=\"Kembali\"></a>";}

?>
