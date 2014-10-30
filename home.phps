<?php
session_start();
$u=$_SESSION['us'];
$p=$_SESSION['pw'];
include 'Class.php';

$baru=new Cuser;
$baru->startConn();
if(!$baru->CekUserPass($u,$p)){
echo "anda salah memasukkan username dan password";
echo "<BR><a href=\"index.html\"><input type=\"button\" value=\"Kembali Cuy\"></a>";
}
else{
$baru2=new Cpost;
$baru2->startConn();
session_start();
$row=$baru->getDetail($u);
$name=$row['name'];
$loc=$row['location'];
$desc=$row['description'];
$_SESSION['namamu']=$name;
$_SESSION['lokasi']=$loc;
$_SESSION['deskrip']=$desc;
echo"<meta http-equiv=\"Refresh\" 
content=\"60;URL=home.php\">";

echo"<style type=\"text/css\">";
echo"
body{
background-image:url('pattern.jpg');
}
#container {
padding-left:10px;
color:#170447; 
background: -moz-linear-gradient(-45deg, #a9fff9,#2426ff );
background: -webkit-gradient(linear, left top, right bottom,from(#a9fff9), to(#2426ff));width:auto;
display:box; 
box-align:stretch; 
-moz-border-radius: 25px;
border-radius: 25px 25px 25px 25px;
border-style:solid;
border-color:#5521e8;
style:dotted;
box-orient:vertical;
margin-bottom:5px;
padding:10px;
box-flex:2;
}";

echo "#friendbox {
box-align:stretch; 
-moz-border-radius-bottomleft: 25px;
border-bottom-left-radius: 25px;
-moz-border-radius-topright: 25px;
border-top-right-radius: 25px;
text-align:center;
color:#170447; 
background: -moz-linear-gradient(top, #55aaee, #003366);
background: -webkit-gradient(linear, left top, left bottom, from(#55aaee), to(#003366));width:auto;
height:auto;
padding:10px; 
style:dotted;
margin:5px;
float:left;
}";

echo ".header{
border : 5px dashed #33CCFF;
text-align:center;
font-family:bleeding cowboys,acens,gigi,calibri;
padding-bottom:20px;
font-size:300%;
padding-top:30px;
width:960px;
height:50px;
margin-left:auto;
margin-right:auto;
float:left;
clear:right;
clear:bottom;
background-color:#b0c4de
}
 
.badan{
width:100%;
margin-left:auto;
margin-right:auto;
}
 
.footer{
border : 5px dashed #33CCFF;
text-align:center;
padding-bottom:20px;
padding-top:30px;
width:98%;
margin-top:20px;
height:50px;
margin-left:auto;
margin-right:auto;
float:left;
background-color:#b0c4de
}
 
.left{
display:box;
box-align:stretch;
-moz-border-radius: 50px;
border-radius: 50px;
box-flex:1;
width:50%;
border : 3px dashed #33CCFF;
padding:20px;
margin-top:20px;
margin-left:0px;
float:left;
background-color:#58657f;
position:relative;
}
 
.right{
-moz-border-radius: 50px;
border-radius: 50px;
border : 3px dashed #33CCFF;
padding-bottom:20px;
padding-right:20px;
padding-top:20px;
margin-top:20px;
padding-left:20px;
width:40%;
height:auto;
margin-left:20px;
background-color:#58657f;
float:left;
}
.right .right{
width:35%;
height:50%;
overflow:scroll;
float:left;
}

";


 
echo "<body>";
echo"#isi{color:#aad7ff;font-size:150%;}";
echo"</style>";
echo"<body>";
echo"<div class=\"badan\">";
echo"<div class=\"left\">";
echo"<form action=\"posting.php\" method=\"get\">"."Your Mind : 
<textarea 
name=\"post\" style=\"width:60%;\"></textarea>
.<input type=\"submit\" value=\"POST\"></form>";
echo"<a href=\"logout.php\"><input type=\"button\" value=\"Logout\"></a>";
$query=$baru2->getAllPost($u);
while($row=$query->fetch(PDO::FETCH_ASSOC)){
$id=$row['id'];
$photoname=$row['username'];
echo"<div 
id=\"container\">";
if(substr($row['post'],0,2)=="RT")
echo"di retweet ";
$nama=$row['name'];
echo"Oleh <a href=\"view.php?user=$nama\">".$row['name']."</a> pada ".$row['tanggal'];
echo"<br>"."<div id=\"isi\">";
$filename='photo/'.$photoname.".jpg";
if (file_exists($filename))
echo "<img src=\"photo/$photoname.jpg\" width=\"80\" height=\"100\">";
else
echo "<img src=\"photo/photonormal.jpg\" width=\"80\" height=\"100\">";
echo $row['post']."<br>";
if($row['username']!=$u)
echo"<form action=\"retweet.php?postid=$id\" method=\"post\">"."<input type=\"text\" name=\"rep\">"."<input 
type=\"submit\" value=\"reply\">"."</form>";
echo "</div>"."</div>";
}
echo"</div>";
echo"<div class=\"right\">";
$photoname=$u;
echo"<div id=\"container\"><div id=\"isi\">";
$filename='photo/'.$photoname.".jpg";
if (file_exists($filename))
echo "<img src=\"photo/$photoname.jpg\" width=\"80\" height=\"100\">";
else
echo "<img src=\"photo/photonormal.jpg\" width=\"80\" height=\"100\">";
echo "You are $name
<br>Your Location $loc
<br>short description: $desc<br><a href=\"edit.php?user=$u\"><input type=\"button\" value=\"Edit\"></a>
<a href=\"delete.php?user=$u\"><input type=\"button\" value=\"Delete My Account\"></a></div></div>";
echo"<div class=\"right\">";
echo"<br>Following<br>";
$query=$baru->getFollowing($u);
while($row=$query->fetch(PDO::FETCH_ASSOC)){
$photoname=$row['username'];
$nama=$row['name'];
echo"<div 
id=\"friendbox\">";
$filename='photo/'.$photoname.".jpg";
if (file_exists($filename))
echo "<img src=\"photo/$photoname.jpg\" width=\"40\" height=\"50\">";
else
echo "<img src=\"photo/photonormal.jpg\" width=\"40\" height=\"50\">";
echo "<br><a href=\"view.php?user=$nama\">$nama</a><br>".
"<a href=\"unfollow.php?user=$photoname\"><input type=\"button\" value=\"Unfollow\"></a></div>";
}
echo"</div>";
echo"<div class=\"right\">";
echo"<br>Followers<br>";
$query=$baru->getFollower($u);
while($row=$query->fetch(PDO::FETCH_ASSOC)){
$photoname=$row['username'];
$nama=$row['name'];
echo"<div 
id=\"friendbox\">";
$filename='photo/'.$photoname.".jpg";
if (file_exists($filename))
echo "<img src=\"photo/$photoname.jpg\" width=\"40\" height=\"50\">";
else
echo "<img src=\"photo/photonormal.jpg\" width=\"40\" height=\"50\">";
echo "<br><a href=\"view.php?user=$nama\">$nama</a><br>".
"</div>";
}
echo"</div>";

echo"</div>";

echo"<div class=\"right\">";
echo"<br>Follow Reccomendation<br>";
$query=$baru->getUnfollowing($u);
while($row=$query->fetch(PDO::FETCH_ASSOC)){
$photoname=$row['username'];
$nama=$row['name'];
echo"<div 
id=\"friendbox\">";
$filename='photo/'.$photoname.".jpg";
if (file_exists($filename))
echo "<img src=\"photo/$photoname.jpg\" width=\"80\" height=\"100\">";
else
echo "<img src=\"photo/photonormal.jpg\" width=\"80\" height=\"100\">";
echo "<br><a href=\"view.php?user=$nama\">$nama</a><br>".
"<a href=\"follownew.php?user=$photoname\"><input type=\"button\" value=\"follow\"></div></a>";}

echo"</div>";
}
?>
