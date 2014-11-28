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
			$result = $dbconn->prep_execute("SELECT firstName, lastName, users.email, courses.subj AS subj, courses.crse AS crse, name FROM users, tas_courses, courses WHERE isTA = 1 AND firstName= :firstName AND lastName= :lastName AND users.email = tas_courses.email AND tas_courses.crse = courses.crse AND tas_courses.subj = courses.subj", $search_arr);
			
		}
		else { //either first name or last name entered
			$search_arr = array(':firstName' => $name_arr[0], ':lastName' => $name_arr[0]);
			$result = $dbconn->prep_execute("SELECT firstName, lastName, users.email, courses.subj AS subj, courses.crse AS crse, name FROM users, tas_courses, courses WHERE isTA = 1 AND (firstName= :firstName OR lastName= :lastName) AND users.email = tas_courses.email AND tas_courses.crse = courses.crse AND tas_courses.subj = courses.subj", $search_arr);
	
		}
		//SELECT firstName, lastName, email, courses.subj, courses.crse, name FROM users, tas_courses, courses WHERE isTA = 1 and users.userId = tas_courses.userID and tas_courses.crse = courses.crse and tas_courses.subj = courses.subj 
		if ($result != null && sizeof($result) > 0) {
			$all = '';
			foreach ($result as $row) {
        		$all .= ('<tr><td>' . $row['firstName'] . '</td><td>' . $row['lastName'] . '</td><td>' . $row['email'] . '</td><td>' . $row['subj'] . '</td><td>' . $row['crse'] .  '</td><td>' . $row['name'] . '</td></tr>');
        	}
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


//SELECT courses.subj, courses.crse, name, firstName, lastName, users.email FROM tas_courses, courses, users WHERE tas_courses.subj = courses.subj AND tas_courses.crse = courses.crse AND tas_courses.email = users.email

		$result = null;
		//school not selected
		
		$search_arr = array(':class_name' => $class_name);
		$result = $dbconn->prep_execute("SELECT courses.subj, courses.crse, name, firstName, lastName, users.email FROM tas_courses, courses, users WHERE courses.name = :class_name AND tas_courses.subj = courses.subj AND tas_courses.crse = courses.crse AND tas_courses.email = users.email", $search_arr);
			
	
		if ($result != null && sizeof($result) > 0) {
			$all = '';
			foreach ($result as $row) {
        		$all .= ('<tr><td>' . $row['subj'] . '</td><td>' . $row['crse'] . '</td><td>' . $row['name'] . '</td><td>' . $row['firstName'] . ' ' . $row['lastName'] .  '</td><td>' . $row['email'] . '</td></tr>');
        	}
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

?>