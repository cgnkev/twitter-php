<?php
	// Include the CRC Object class that needs to
	// extended by all classes. This is the super
	// class.
	include_once('crc_constants.mod.php');
	include_once('crc_object.cls.php');
	include_once('crc_mysql.cls.php');

	//******************************************
	// Name: crc_object
	//******************************************
	//
	// Desc: The Admin Object
	// Developer: FreeSMS team
	// Email: cristeab@gmail.com
	// Date: September 22th, 2010
	// Version: 1.0.0
	//
	// Copyright
	// =========
	// This code is copyright, use in part or
	// whole is prohibited without a written
	// concent to the developer.
	//******************************************

	class crc_admin extends crc_object {

		var $m_sql;
		var $m_data;
		var $m_courseid;
		var $m_coursename;
		var $m_coursedesc;
		var $m_startdate;
		var $m_enddate;
		var $m_daytime;
		var $m_status;		
		var $m_roomid;
		var $m_roomname;
		var $m_roomdesc;
		var $m_active;
		var $m_venueid;
		var $m_coursefee;
		var $m_profileid;
		var $m_scheduleid;
		var $m_evaluation;
		
		var $m_firstname;
		var $m_lastname;
		var $m_gender;
		var $m_email;
		var $m_phone;
		
		var $m_courselist;
		var $m_teacherlist;
		var $m_studentlist;
		
		function crc_admin($debug) {
			//******************************************
			// Initialization by constructor
			//******************************************
			$this->classname = 'crc_admin';
			$this->classdescription = 'Handle course administration.';
			$this->classversion = '1.0.0';
			$this->classdate = 'September 22th, 2010';
			$this->classdevelopername = 'Bogdan Cristea';
			$this->classdeveloperemail = 'cristeab@gmail.com';
			$this->_DEBUG = $debug;
			
			$this->m_data['cname'] = '';
			$this->m_data['cdesc'] = '';
			$this->m_data['daytime'] = '';
			$this->m_data['roomname'] = '';
			$this->m_data['syear'] = '';
			$this->m_data['smonth'] = '';
			$this->m_data['sday'] = '';
			$this->m_data['eyear'] = '';
			$this->m_data['emonth'] = '';
			$this->m_data['eday'] = '';
			
			$this->m_data['fname'] = '';
			$this->m_data['lname'] = '';
			$this->m_data['gender'] = 'male';
			$this->m_data['email'] = '';
			$this->m_data['lcode'] = '0040';
			$this->m_data['lprefix'] = '0000';
			$this->m_data['lpostfix'] = '000000';

			if ($this->_DEBUG) {
				echo "DEBUG {crc_admin::constructor}: The class \"crc_admin\" was successfuly created. <br>";
				echo "DEBUG {crc_admin::constructor}: Running in debug mode. <br>";
			}

		}

		function fn_setcourse($post) {
			//******************************************
			// Update the course information
			//******************************************

			if ($this->_DEBUG) {
				echo "DEBUG {crc_admin::fn_setcourse}: Setting course information <br>";
			}

			$db = new crc_mysql($this->_DEBUG);
			$db->fn_connect();
			$result = true;
			if ($db->m_mysqlhandle != false) {

				if(isset($post['cname'])) {
					$this->m_coursename = $post['cname'];	
				} else {
					$this->m_coursename = "";
				}
				if(isset($post['cdesc'])) {
					$this->m_coursedesc = $post['cdesc'];
				} else {
					$this->m_coursedesc = "";
				}
				if(isset($post['syear']) && isset($post['smonth']) && isset($post['sday'])) {
					$this->m_startdate = $post['syear'] . '-' . $post['smonth'] . '-' . $post['sday'];
					$this->m_data['syear'] = $post['syear'];
					$this->m_data['smonth'] = $post['smonth'];
					$this->m_data['sday'] = $post['sday'];
				} else {
					$this->m_startdate = "";
					$this->m_data['syear'] = "";
					$this->m_data['smonth'] = "";
					$this->m_data['sday'] = "";
				}
				if(isset($post['eyear']) && isset($post['emonth']) && isset($post['eday'])) {
					$this->m_enddate = $post['eyear'] . '-' . $post['emonth'] . '-' . $post['eday'];
					$this->m_data['eyear'] = $post['eyear'];
					$this->m_data['emonth'] = $post['emonth'];
					$this->m_data['eday'] = $post['eday'];
				} else {
					$this->m_enddate = "";
					$this->m_data['eyear'] = "";
					$this->m_data['emonth'] = "";
					$this->m_data['eday'] = "";
				}
				if(isset($post['daytime'])) {
					$this->m_daytime = $post['daytime'];
				} else {
					$this->m_daytime = "";
				}
				$this->m_status = 'In progress';
				if(isset($post['roomname'])) {
					$this->m_roomname = $post['roomname'];
				} else {
					$this->m_roomname = "";
				}
				$this->m_roomdesc = 'N/A';
				$this->m_active = '0';//fixed when adding a course
				$this->m_venueid = '1';//unused
				$this->m_coursefee = '0';//unused
				$this->m_evaluation = '10';//unused
				
				//this data should be restored if something goes wrong
				$this->m_data['cname'] = $this->m_coursename;
				$this->m_data['cdesc'] = $this->m_coursedesc;
				$this->m_data['daytime'] = $this->m_daytime;
				$this->m_data['roomname'] = $this->m_roomname;
				
				if( ($this->m_coursename == "") || 
				    ($this->m_data['syear'] == "") ||
				    ($this->m_data['smonth'] == "") ||
				    ($this->m_data['sday'] == "") ||
				    ($this->m_data['eyear'] == "") ||
				    ($this->m_data['emonth'] == "") ||
				    ($this->m_data['eday'] == "") ||
				    ($this->m_daytime == "") ||
				    ($this->m_roomname == "") ) {
					return false;
				}

				//check if at least one teacher has been selected
				$teacherselected = false;
				$this->fn_getteacherlist($db);
				for($i = 0; $i < count($this->m_teacherlist); $i++) {
					if(isset($post['teacher' . $i])) {
						$teacherselected = true;
						break;
					}
				}
				if ($teacherselected == false) {
					if ($this->_DEBUG) {
						echo 'ERROR {crc_admin::fn_setcourse}: No teacher has been selected. <br>';
					}					
					$this->lasterrmsg = "No teacher has been selected";
					return false;
				}
				
				//check if the room already exists in database
				$resource = null;
				$this->fn_getroomid($db, $this->m_roomname);
				if($this->m_roomid == 0) { //the room doesn't exist in database
					$this->m_sql = 'insert into ' . MYSQL_ROOMS_TBL . '(' .
												'room_name, room_desc) ' .
												'values("' . $this->m_roomname .
												'","' . $this->m_roomdesc . '")';	

					$resource = $db->fn_runsql(MYSQL_DB, $this->m_sql);
					if (mysql_affected_rows() <= 0) {
						if ($this->_DEBUG) {
							echo 'ERROR {crc_admin::fn_setcourse}: Could not insert room information. <br>';
						}
						$db->fn_freesql($resource);
						$db->fn_disconnect();
						$this->lasterrmsg = "Could not insert room information";						
						return false;
					}

					//initialize m_roomid
					$this->fn_getroomid($db, $this->m_roomname);
					if($this->m_roomid == 0) {
						if ($this->_DEBUG) {
							echo 'ERROR {crc_admin::fn_setcourse}: Could not get room id. <br>';
						}
						$db->fn_freesql($resource);
						$db->fn_disconnect();
						$this->lasterrmsg = "Could not get room id";						
						return false;
					}
				}
				
				//set course information
				$this->fn_getcourseid($db, $this->m_coursename);
				if ($this->m_courseid == 0) { //the course doesn't exist in database
				$dbh=$db->fn_connect();
				$query=$dbh->query("select max(course_id) from crc_courses");
				$resource=$query->fetch(PDO::FETCH_ASSOC);
				if($resource==null){
				$this->m_courseid=1;}
				else{
				$this->m_courseid=$resource['course_id']+1;}
					$this->m_sql = 'insert into ' . MYSQL_COURSES_TBL . '(' .
												'course_id, course_name, course_desc, ' .
												'course_active, course_fee) ' .
												'values("' . $this->m_courseid. '","' . $this->m_coursename . '","' . $this->m_coursedesc .
												 '","' . $this->m_active . '","' . $this->m_coursefee . '")';
				
				echo $this->m_sql;
				$dbh->exec($this->m_sql);	
				} else {
					$db->fn_freesql($resource);
					$db->fn_disconnect();
					$this->lasterrmsg = "Course \"" . $this->m_coursename . "\" already exists in database.<br>Use \"Edit Course\" menu if you want to modify this course.";						
					return false;
				}			
				//$resource = $db->fn_runsql(MYSQL_DB, $this->m_sql);			
				if (mysql_errno() != 0) {
					if ($this->_DEBUG) {
						echo 'ERROR {crc_admin::fn_setcourse}: Could not update/insert course information. <br>';
					}
					$db->fn_freesql($resource);
					$db->fn_disconnect();
					$this->lasterrmsg = "Could not update/insert course information";					
					return false;
				}
				
				//initialize m_courseid
				$this->fn_getcourseid($db, $this->m_coursename);
				if($this->m_courseid == 0) {
					if ($this->_DEBUG) {
						echo 'ERROR {crc_admin::fn_setcourse}: Could not get course id. <br>';
					}
					$db->fn_freesql($resource);
					$db->fn_disconnect();
					$this->lasterrmsg = "Could not get course id";					
					return false;
				}
				
				//set schedule for this course
				$this->m_sql = 'insert into ' . MYSQL_SCHEDULE_TBL . '(' .
												'schedule_course_id, schedule_start_date, ' .
												'schedule_end_date, schedule_day_time, ' .
												'schedule_status, schedule_room_id, ' .
												'schedule_active, schedule_venue_id) ' .
												'values("' . $this->m_courseid . '","' . $this->m_startdate .
												'","' . $this->m_enddate . '","' . $this->m_daytime .
												'","' . $this->m_status . '","' . $this->m_roomid .
												 '","' . $this->m_active . '","' . $this->m_venueid . '")';
				$dbh->exec($this->m_sql);
				
				
				//initialize m_scheduleid
				$this->fn_getscheduleid($db, $this->m_courseid);
				if($this->m_scheduleid == 0) {
					if ($this->_DEBUG) {
						echo 'ERROR {crc_admin::fn_setcourse}: Could not get schedule id. <br>';
					}
					$db->fn_freesql($resource);
					$db->fn_disconnect();
					$this->lasterrmsg = "Could not get schedule id";					
					return false;
				}				

				//set teacher schedule				
				$this->m_profileid = 0;
				for($i = 0; $i < count($this->m_teacherlist); $i++) {
					if(isset($post['teacher' . $i]) && (strtolower($post['teacher' . $i]) == "on")) {
						$this->m_profileid = $this->m_teacherlist[$i]['profileid'];
						$this->m_sql = 'insert into ' . MYSQL_TEACHER_SCHEDULE_TBL . '(' .
												'teacher_schedule_profile_id, ' .
												'teacher_schedule_schedule_id, ' .
												'teacher_schedule_evaluation) ' .
												'values("' . $this->m_profileid . 
												'","' . $this->m_scheduleid .
												 '","' . $this->m_evaluation . '")';
						$dbh->exec($this->m_sql);
						
					}
				}

				//check if at least one teacher has been selected
				if ($this->m_profileid == 0) {
					if ($this->_DEBUG) {
						echo 'ERROR {crc_admin::fn_setcourse}: No teacher has been selected. <br>';
					}
					$result = false;
					$this->lasterrmsg = "No teacher has been selected";
				}
				$db->fn_freesql($resource);
				$db->fn_disconnect();
			} else {
				$db->fn_disconnect();
				$result = false;
				$this->lasterrmsg = "Cannot connect to MySQL database";
			}
			return $result;
		}

		function fn_getcourseid($db, $course) {
			//******************************************
			// Get course ID
			//******************************************

			if ($this->_DEBUG) {
				echo "DEBUG {crc_admin::fn_getcourseid}: Retreiving the course id for " . $course . " <br>";
			}

			$this->m_courseid = 0;
			if ($db->m_mysqlhandle != false) {

				$this->m_sql = "select course_id from " . MYSQL_COURSES_TBL .
												" where (course_name = '$course')";

				$query=$db->m_mysqlhandle->query($this->m_sql);
				$resource=$query->fetch(PDO::FETCH_ASSOC);
				if ($resource) {
					$this->m_courseid = $resource['course_id'];
				} else {
					if ($this->_DEBUG) {
						echo 'ERROR {crc_admin::fn_getcourseid}: The sql command returned nothing. <br>';
					}
				}				
			} 
			return $this->m_courseid;
		}

		function fn_getroomid($db, $room) {
			//******************************************
			// Get room ID
			//******************************************

			if ($this->_DEBUG) {
				echo "DEBUG {crc_admin::fn_getroomid}: Retreiving the room id for " . $room . " <br>";
			}

			$this->m_roomid = 0;
			if ($db->m_mysqlhandle != false) {

				$this->m_sql = 'select room_id from ' . MYSQL_ROOMS_TBL .
												' where (room_name = "' . $room . '")';
				$query=$db->m_mysqlhandle->query($this->m_sql);
				$resource=$query->fetch(PDO::FETCH_ASSOC);
				//$resource = $db->fn_runsql(MYSQL_DB, $this->m_sql);
				if ($resource) {
					$this->m_roomid = $resource['room_id'];
				} else {
					if ($this->_DEBUG) {
						echo 'ERROR {crc_admin::fn_getroomid}: The sql command returned nothing. <br>';
					}
				}
			}
			return $this->m_roomid;
		}
		
		function fn_getscheduleid($db, $courseid) {
			//******************************************
			// Get schedule ID
			//******************************************

			if ($this->_DEBUG) {
				echo "DEBUG {crc_admin::fn_getscheduleid}: Retreiving the schedule id for " . $courseid . " <br>";
			}

			if ($db->m_mysqlhandle != 0) {

				$this->m_sql = 'select schedule_id from ' . MYSQL_SCHEDULE_TBL .
							    ' where (schedule_course_id = "' . $courseid . '")';
				$query=$db->m_mysqlhandle->query($this->m_sql);
				$resource=$query->fetch(PDO::FETCH_ASSOC);
				//$resource = $db->fn_runsql(MYSQL_DB, $this->m_sql);
				if ($resource) {
					$this->m_scheduleid = $resource['schedule_id'];
				} else {
					$this->m_scheduleid = 0;
					if ($this->_DEBUG) {
						echo 'ERROR {crc_admin::fn_getscheduleid}: The sql command returned nothing. <br>';						
					}
				}
				return $this->m_scheduleid;
			} else {
				return 0;
			}
		}
		
		function fn_getcourselist($db) {
			
			//******************************************
			// Get the active course list
			//******************************************
			
			if ($this->_DEBUG) {
				echo "DEBUG {crc_admin::fn_getcourselist}: Getting course list <br>";
			}

			$closedb = false;
			if($db == null) {
				$db = new crc_mysql($this->_DEBUG);
				$this->m_mysqlhandle=$db->fn_connect();
				$closedb = true;
			}
			if ($db->m_mysqlhandle != 0) {
				$this->m_sql = 'select * ' .
								'from ' . MYSQL_COURSES_TBL . 
								' where (course_active = "0") ' .
								'order by course_name asc';				
				$query=$db->m_mysqlhandle->query($this->m_sql);
				$resource=$query->fetch(PDO::FETCH_ASSOC);											
				if ($resource > 0) {					
					$index = 0;
					$this->m_courselist = '';
					if(strlen($resource['course_desc'])>0) {
							$this->m_courselist[$index]['cnamedesc'] = $resource['course_name'] . ', ' . $resource['course_desc'];
						} else {
							$this->m_courselist[$index]['cnamedesc'] = $resource['course_name'];
						}
						$this->m_courselist[$index]['courseid'] = $row['course_id'];					
						$index++;					
					while ($row = $query->fetch(PDO::FETCH_ASSOC)) {						
						if(strlen($row['course_desc'])>0) {
							$this->m_courselist[$index]['cnamedesc'] = $row['course_name'] . ', ' . $row['course_desc'];
						} else {
							$this->m_courselist[$index]['cnamedesc'] = $row['course_name'];
						}
						$this->m_courselist[$index]['courseid'] = $row['course_id'];					
						$index++;
					}
				} else {
					$this->m_courselist = null;
					if ($this->_DEBUG) {
						echo 'ERROR {crc_admin::fn_getcourselist}: The sql command returned nothing. <br>';
					}
				}
				if ($closedb == true) {
					$db->fn_freesql($resource);
					$db->fn_disconnect();
				}
				return $this->m_courselist;
			} else {
				if ($closedb == true) {
					$db->fn_disconnect();
				}
				return null;
			}
		}

		function fn_getteacherlist($db) {
			
			//******************************************
			// Get the teacher list
			//******************************************
			
			if ($this->_DEBUG) {
				echo "DEBUG {crc_admin::fn_getteacherlist}: Getting course list <br>";
			}

			$closedb = false;
			if($db == null) {
				$db = new crc_mysql($this->_DEBUG);
				$db->fn_connect();
				$closedb = true;
			}
			$this->m_teacherlist = null;
			if ($db->m_mysqlhandle != false) {
				$this->m_sql = 'select profile_id, profile_firstname, profile_lastname ' .
								'from ' . MYSQL_PROFILES_TBL . ' as p' . 
								' where (profile_active = "0") and ' .
								'(profile_role_id between 1 and 2) ' .
								'order by p.profile_lastname';
				$query=$db->m_mysqlhandle->query($this->m_sql);
				$resource=$query->fetch(PDO::FETCH_ASSOC);
				//$resource = $db->fn_runsql(MYSQL_DB, $this->m_sql);											
				if ($resource) {					
					$index = 0;
					$this->m_teacherlist = '';	
						$this->m_teacherlist[$index]['lastfirstname'] = $resource['profile_lastname'] . ', ' . $resource['profile_firstname'];//lastname, firstname
						$this->m_teacherlist[$index]['profileid'] = $resource['profile_id'];
						$index++;				
					while ($row = $query->fetch(PDO::FETCH_ASSOC)) {						
						$this->m_teacherlist[$index]['lastfirstname'] = $row['profile_lastname'] . ', ' . $row['profile_firstname'];//lastname, firstname
						$this->m_teacherlist[$index]['profileid'] = $row['profile_id'];
						$index++;
					}
				} else {
					if ($this->_DEBUG) {
						echo 'ERROR {crc_admin::fn_getteacherlist}: The sql command returned nothing.<br>';
					}
				}
				if ($closedb == true) {
					$db->fn_freesql($resource);
					$db->fn_disconnect();
				}
			} else {
				if ($closedb == true) {
					$db->fn_disconnect();
				}
			}
			return $this->m_teacherlist;
		}		

		function fn_getstudentlist($db) {
			
			//******************************************
			// Get registered student list
			//******************************************
			
			if ($this->_DEBUG) {
				echo "DEBUG {crc_admin::fn_getstudentlist}: Getting registered student list<br>";
			}

			$closedb = false;
			if($db == null) {
				$db = new crc_mysql($this->_DEBUG);
				$db->fn_connect();
				$closedb = true;
			}
			if ($db->m_mysqlhandle != false) {
				$this->m_sql = 'select profile_uid, profile_firstname, profile_lastname ' .
								'from ' . MYSQL_PROFILES_TBL . ' as p' . 
								' where (profile_active = "0") and ' .
								'(profile_role_id = "3") ' .
								'order by p.profile_lastname';
				$query=$db->m_mysqlhandle->query($this->m_sql);
				$resource=$query->fetch(PDO::FETCH_ASSOC);										
				if ($resource) {					
					$index = 0;
					$this->m_studentlist = '';	
					$this->m_studentlist[$index]['lastfirstname'] = $resource['profile_lastname'] . ', ' . $resource['profile_firstname'];//lastname, firstname
						$this->m_studentlist[$index]['profileuid'] = $resource['profile_uid'];
						$index++;				
					while ($row = mysql_fetch_array($resource)) {						
						$this->m_studentlist[$index]['lastfirstname'] = $row['profile_lastname'] . ', ' . $row['profile_firstname'];//lastname, firstname
						$this->m_studentlist[$index]['profileuid'] = $row['profile_uid'];
						$index++;
					}
				} else {
					$this->m_studentlist = null;
					if ($this->_DEBUG) {
						echo 'ERROR {crc_admin::fn_getstudentlist}: The sql command returned nothing.<br>';
					}
				}
				if ($closedb == true) {
					$db->fn_freesql($resource);
					$db->fn_disconnect();
				}
				return $this->m_studentlist;
			} else {
				if ($closedb == true) {
					$db->fn_disconnect();
				}
				return null;
			}
		}		
		function fn_getmaxprofileid() {
			//******************************************
			// Get profile ID
			//******************************************

			if ($this->_DEBUG) {
				echo "DEBUG {crc_admin::fn_getprofileid}: Retreiving the schedule id for " . $firstname . " " . $lastname . "<br>";
			}

			if ($db->m_mysqlhandle != false) {

				$this->m_sql = 'select max(profile_id) from crc_profiles';

				$query=$db->m_mysqlhandle->query($this->m_sql);
				$resource=$query->fetch(PDO::FETCH_ASSOC);										
				if ($resource) {			
					$this->m_profileid = $resource['profile_id'];
					return $resource['profile_id'];
				} 
			else {
				return 0;
			}
		}

		function fn_getprofileid($db, $firstname, $lastname) {
			//******************************************
			// Get profile ID
			//******************************************

			if ($this->_DEBUG) {
				echo "DEBUG {crc_admin::fn_getprofileid}: Retreiving the schedule id for " . $firstname . " " . $lastname . "<br>";
			}

			if ($db->m_mysqlhandle != false) {

				$this->m_sql = 'select profile_id from ' . MYSQL_PROFILES_TBL .
							    ' where (profile_firstname = "' . $firstname . 
							    '" and profile_lastname = "' . $lastname . '")';

				$query=$db->m_mysqlhandle->query($this->m_sql);
				$resource=$query->fetch(PDO::FETCH_ASSOC);										
				if ($resource) {			
					$this->m_profileid = $resource['profile_id'];
				} else {
					$this->m_profileid = 0;
					if ($this->_DEBUG) {
						echo 'ERROR {crc_admin::fn_getprofileid}: The sql command returned nothing. <br>';
					}
				}
				return $this->m_profileid;
			} else {
				return 0;
			}
		}
		
		function fn_setstudent($post) {

			if ($this->_DEBUG) {
				echo "DEBUG {crc_admin::fn_setstudent}: Setting student information <br>";
			}
				
			$db = new crc_mysql($this->_DEBUG);
			$db->fn_connect();
			$result = true;
			if ($db->m_mysqlhandle != false) {
				if(isset($post['fname'])) {
					$this->m_firstname = $post['fname'];
				} else {
					$this->m_firstname = "";
				}
				if(isset($post['lname'])) {
					$this->m_lastname = $post['lname'];
				} else {
					$this->m_lastname = "";
				}
				if(isset($post['gender']))
				{
					$this->m_gender = strtoupper($post['gender'][0]);
				} else {
					$this->m_gender = "";
				}
				if(isset($post['email'])) {
					$this->m_email = $post['email'];
				} else {
					$this->m_email = "";
				}
				if(isset($post['lcode']) && isset($post['lprefix']) && isset($post['lpostfix'])) {
					$this->m_phone = $post['lcode'] . $post['lprefix'] . $post['lpostfix'];
					$this->m_data['lcode'] = $post['lcode'];
					$this->m_data['lprefix'] = $post['lprefix'];
					$this->m_data['lpostfix'] = $post['lpostfix'];
				} else {
					$this->m_phone = "";
					$this->m_data['lcode'] = "";
					$this->m_data['lprefix'] = "";
					$this->m_data['lpostfix'] = "";
				}

				//this data should be restored if something goes wrong
				$this->m_data['fname'] = $this->m_firstname;
				$this->m_data['lname'] = $this->m_lastname;
				$this->m_data['gender'] = $this->m_gender;
				$this->m_data['email'] = $this->m_email;

				if( ($this->m_firstname == "") || ($this->m_lastname == "") ) {
					if ($this->_DEBUG) {
						echo 'ERROR {crc_admin::fn_setstudent}: First or last name is empty.<br>';
					}
					$db->fn_disconnect();
					$this->lasterrmsg = "First or last name is empty";
					return false;
				}
				
				//check if at least one course has been selected
				$this->fn_getcourselist($db);
				$courseselected = false;
				for($i = 0; $i < count($this->m_courselist); $i++) {
					if(isset($post['course' . $i]) && (strtolower($post['course' . $i]) == "on")) {
						$courseselected = true;
						break;
					}
				}
				if($courseselected == false) {
					if ($this->_DEBUG) {
						echo 'ERROR {crc_admin::fn_setstudent}: No course has been selected.<br>';
					}
					$db->fn_disconnect();
					$this->lasterrmsg = "No course has been selected";
					return false;
				}
					
				//check for user name
				$this->m_sql = 'select * ' .
							'from ' . MYSQL_PROFILES_TBL . 
							' where (profile_firstname = "' . $this->m_firstname .
							'" and profile_lastname = "' . $this->m_lastname . '")';
				$query=$db->m_mysqlhandle->query($this->m_sql);
				$resource=$query->fetch(PDO::FETCH_ASSOC);	
				if ($resource==null) {					
					//insert student
					$profileid=$this->fn_getmaxprofileid()+1;
					$this->m_sql = 'insert into ' . MYSQL_PROFILES_TBL . '(' .
									'profile_id, profile_uid, profile_pwd, profile_firstname, profile_lastname, ' .
									'profile_email, profile_gender, profile_phone_land, ' .
									'profile_role_id, profile_rdn) ' .
									'values("' . $profileid . '","' . $this->m_firstname . '","' . $this->m_lastname . '","' . $this->m_firstname .
									'","' . $this->m_lastname .
									'","' . $this->m_email .
									'","' . $this->m_gender .
									'","' . $this->m_phone . 
									'","3","ou=don mills,ou=toronto,ou=ontario,ou=canada,o=crc world")';
					$this->m_mysqlhandle->exec($this->m_sql);

					//initialize m_profileid
					$this->fn_getprofileid($db, $this->m_firstname, $this->m_lastname);
					if ($this->m_profileid == 0) {
						if ($this->_DEBUG) {
							echo 'ERROR {crc_admin::fn_setstudent}: Could not get profile id. <br>';
						}
						$db->fn_freesql($resource);
						$db->fn_disconnect();
						$this->lasterrmsg = "Could not get profile id";
						return false;
					}

					//initialize m_scheduleid using selected course(s)
					if ($this->fn_setstudentschedule($db, $post, $this->m_profileid) == false) {
						if ($this->_DEBUG) {
							echo 'ERROR {crc_admin::fn_setstudent}: Cannot set student schedule. <br>';
						}
						$db->fn_freesql($resource);
						$db->fn_disconnect();
						$this->lasterrmsg = "Cannot set student schedule";
						return false;
					}
				} else {
					if ($this->_DEBUG) {
						echo 'ERROR {crc_admin::fn_setstudent}: User ' . $this->m_firstname . ' ' . $this->m_lastname . ' already exists in database.<br>';
					}
					$this->lasterrmsg = "User " . $this->m_firstname . " " . $this->m_lastname . " already exists in database.<br>Use \"Edit Student\" menu if you want to modify this user.";
					$result = false;
				}
				
				$db->fn_freesql($resource);
				$db->fn_disconnect();
			} else {
				$db->fn_disconnect();
			}
			return $result;
		}
		
		function fn_setstudentschedule($db, $post, $profileid) {
			
			//***************************************************
			// Helper function for inserting the student schedule
			//***************************************************
			
			if ($this->_DEBUG) {
				echo "DEBUG {crc_admin::fn_setstudentschedule}: Setting student schedule <br>";
			}
			
			$closedb = false;
			if($db == null) {
				$db = new crc_mysql($this->_DEBUG);
				$db->fn_connect();
				$closedb = true;
			}
			if ($db->m_mysqlhandle == false) {
				$db->fn_disconnect();
				return false;
			}
			
			$this->fn_getcourselist($db);
			$this->m_scheduleid = 0;
            $resource = 0;
			for($i = 0; $i < count($this->m_courselist); $i++) {
				if(isset($post['course' . $i]) && (strtolower($post['course' . $i]) == "on")) {
					$this->fn_getscheduleid($db, $this->m_courselist[$i]['courseid']);
					if ($this->m_scheduleid == 0) {
						if ($this->_DEBUG) {
							echo 'ERROR {crc_admin::fn_setstudentschedule}: Could not get schedule id. <br>';
						}
						if (is_resource($resource)) {
							$db->fn_freesql($resource);
						}
						$db->fn_disconnect();
						return false;
					}
					//check if the student has been already assigned to this course
					$this->m_sql = 'select * ' .
							'from ' . MYSQL_STUDENT_SCHEDULE_TBL . 
							' where (student_schedule_profile_id = "' . $profileid .
							'" and student_schedule_schedule_id = "' . $this->m_scheduleid . '")';
					$query=$this->m_mysqlhandle->query($this->m_sql);
					$resource = $query->fetch(PDO::FETCH_ASSOC);
					if ($resource==null) {
						//set student schedule
						$this->m_sql = 'insert into ' . MYSQL_STUDENT_SCHEDULE_TBL . '(' .
									'student_schedule_profile_id, ' .
									'student_schedule_schedule_id, ' .
									'student_schedule_questions) ' .
									'values("' . $profileid .
									'","' . $this->m_scheduleid .
									'","1")';
						$this->m_mysqlhandle->exec($this->m_sql);
					}
				}
			}
			
			if ($closedb == true) {
				if (is_resource($resource)) {
					$db->fn_freesql($resource);
				}
				$db->fn_disconnect();
			}
			
			return true;
		}
	
?>
