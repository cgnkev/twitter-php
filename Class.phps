<?php
class Cuser {
    // In OOP classes are usually named starting with a cap letter.
    var $db;

    function startConn() {
        $this->db=new PDO("sqlite:twitter.db");
    }


    function compareUsername($username) {
		$query=$this->db->query("select * from user where username='$username'");
		$row=$query->fetch(PDO::FETCH_ASSOC);
		if($row){return true;}
		else {return false;}
    }

	
	function addUser($name,$us,$pass,$l,$d) {
		$this->db->exec("insert into user values('$name','$us','$pass','$l','$d')");
	}

	function CekUserPass($us,$pw) {
		$query=$this->db->query("select * from user where username='$us' and pass='$pw'");
		$row=$query->fetch(PDO::FETCH_ASSOC);
		if($row){return true;}
		else {return false;}
	}


	function getFollowing($us) {
		$query=$this->db->query("SELECT * from user where username in(select mengikut FROM follow where pengikut='$us')");

		return $query;
	}

	function getFollower($us) {
		$query=$this->db->query("SELECT * from user where username in(select pengikut FROM follow where mengikut='$us')");

		return $query;
	}

	function getUnfollowing($us) {//dapetin follow recommendation
		$query=$this->db->query("SELECT * FROM user where username not like '$us' 
				and username not in(select mengikut from follow where pengikut='$us')");

		return $query;
	}

	function getDetail($us) {//detail;e username
		$query=$this->db->query("SELECT * FROM user where username='$us'");
		$row=$query->fetch(PDO::FETCH_ASSOC);

		return $row;
	}

	function deleteFollow($us,$us2) {
		$this->db->exec("delete from follow where pengikut='$us' and mengikut='$us2'");
	}

	function addFollow($us,$us2) {
		$this->db->exec("insert into follow values ('$us','$us2')");
	}

	function editUser($nama,$loc,$des,$pw,$us) {
		$ex=$this->db->exec("update user set name='$nama',location='$loc',description='$des',pass='$pw'
		where username='$us'");

		if($ex) {return true;}
		else {return false;}
	}

	function deleteUser($us) {
		$ex=$this->db->exec("delete from user where username='$us'");
		if($ex)
		{
		$ex2=$this->db->exec("delete from follow where pengikut='$us'");
		$ex2=$this->db->exec("delete from follow where mengikut='$us'");
		}

		if($ex) {return true;}
		else {return false;}	
	}

}

class Cpost{
	var $db;

    function startConn() {
        $this->db=new PDO("sqlite:twitter.db");
    }

	function posting($us,$name,$post) {
		$this->db->exec("insert into post values (null,'$us','$name','$post',datetime('now','+7 hour'))");
	}

	function reTweet($us,$name,$rep,$id) {
		$query=$this->db->query("select * from post where id=$id");
		$row=$query->fetch(PDO::FETCH_ASSOC);
		$post=$rep."<BR>RT @".$row['name']." : ".$row['post'];
		$this->db->exec("insert into post values(null,'$us','$name','$post',datetime('now','+7 hour'))");
	}

	function getAllPost($us) {//buat yang abis login nampilno semua + yg di follow
		$query=$this->db->query("SELECT * FROM post where username='$us' or username 
						in (select mengikut from follow where pengikut='$us') 
						ORDER BY tanggal desc");

		return $query;
	}

	function getMyPost($us) {//buat search profile
		$query=$this->db->query("SELECT * FROM post where username='$us' ORDER BY tanggal desc");

		return $query;
	}

	function changePostOwner($nama,$us) {
		$this->db->exec("update post set name='$nama' where username='$us'");
		echo "$us $nama";
	}
}
?>
