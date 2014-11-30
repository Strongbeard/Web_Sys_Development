<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once(SITE_ROOT . '/PHP/User.php');
require_once(SITE_ROOT . '/PHP/Course.php');
require(SITE_ROOT . '/php/check_logged_in.php');


if (isset($_GET['ta_name']) && !empty($_GET['ta_name'])) {
	try {
		$dbconn = DB::getInstance();
		$ta_name = mysql_real_escape_string(stripslashes($_GET['ta_name']));
		$name_arr = explode(' ', $ta_name);

		$result = null;
		//more than one name entered
		if (count($name_arr) > 1) {
			$search_arr = array(':firstName' => $name_arr[0], ':lastName' => $name_arr[1]);
			$result = $dbconn->prep_execute("SELECT firstName, lastName, users.email, courses.subj AS subj, courses.crse AS crse, name FROM users, tas_courses, courses WHERE isTA = 1 AND firstName LIKE CONCAT('%', :firstName,'%')  AND lastName LIKE CONCAT('%', :lastName,'%') AND users.email = tas_courses.email AND tas_courses.crse = courses.crse AND tas_courses.subj = courses.subj", $search_arr);
			
		}
		else { //either first name or last name entered
			$search_arr = array(':firstName' => $name_arr[0], ':lastName' => $name_arr[0]);
			$result = $dbconn->prep_execute("SELECT firstName, lastName, users.email, courses.subj AS subj, courses.crse AS crse, name FROM users, tas_courses, courses WHERE isTA = 1 AND (firstName LIKE CONCAT('%', :firstName,'%') OR lastName LIKE CONCAT('%', :lastName,'%')) AND users.email = tas_courses.email AND tas_courses.crse = courses.crse AND tas_courses.subj = courses.subj", $search_arr);
	
		}

		if ($result != null && sizeof($result) > 0) {
			$all = '';
			foreach ($result as $row) {
				$chk_box_val = $row['subj'] . ' ' . $row['crse'];
        		$all .= ('<tr><td>' . $row['firstName'] . '</td><td>' . $row['lastName'] . '</td><td>' . $row['email'] . '</td><td>' . $row['subj'] . '</td><td>' . $row['crse'] .  '</td><td>' . $row['name'] . '</td><td><input type="checkbox" value="' . $chk_box_val .  '"></td></tr>');
        	}
        	$all.=('<tr><td></td><td></td><td></td><td></td><td></td><td></td><td><input type="submit" value="Add"></td></tr>');
        	echo $all;
		}
		else {
			echo '';
		}
    }

	catch (Exception $e) {
		echo "Error: " . $e->getMessage();
	}
}

if (isset($_GET['class_name']) && !empty($_GET['class_name'])) {
	try {
		$dbconn = DB::getInstance();
		$class_name = mysql_real_escape_string(stripslashes($_GET['class_name']));
		$school_name = mysql_real_escape_string(stripslashes($_GET['school_name']));

		$result = null;
		//school not selected
		
		$search_arr = array(':class_name' => $class_name);
		$result = $dbconn->prep_execute("SELECT courses.subj, courses.crse, name, firstName, lastName, users.email FROM tas_courses, courses, users WHERE courses.name LIKE CONCAT('%', :class_name,'%') AND tas_courses.subj = courses.subj AND tas_courses.crse = courses.crse AND tas_courses.email = users.email", $search_arr);
			
	
		if ($result != null && sizeof($result) > 0) {
			$all = '';
			foreach ($result as $row) {
				$chk_box_val = $row['subj'] . ' ' . $row['crse'];
        		$all .= ('<tr><td>' . $row['subj'] . '</td><td>' . $row['crse'] . '</td><td>' . $row['name'] . '</td><td>' . $row['firstName'] . ' ' . $row['lastName'] .  '</td><td>' . $row['email'] . '</td><td><input type="checkbox" value="' . $chk_box_val .  '"></td></tr>');
        	}
        	$all.=('<tr><td></td><td></td><td></td><td></td><td></td><td><input type="submit" value="Add"></td></tr>');
        	echo $all;
		}
		else {
			echo '';
		}
    }

	catch (Exception $e) {
		echo "Error: " . $e->getMessage();
	}
}

if (isset($_GET['add']) && isset($_GET['checked_vals']) && !empty($_GET['checked_vals'])) {
	$results = $_GET['checked_vals'];
	try {
		$user = User::fromDatabase($_SESSION['user']->getEmail());
		foreach ($results as $val) {
			$arr = explode(' ', $val); //[0] = subj, [1] = crse#
			if ($user->addUserCourse('student', $arr[0], (int) $arr[1])) {
				 //return true if added successfully
				echo 1;
			}
			else {
				echo 0;
			}
		}
	}
	catch(Exception $e) {
		echo "Error: " . $e->getMessage();
	}
	
}

?>