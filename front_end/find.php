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
			$result = $dbconn->prep_execute("SELECT firstName, lastName, email, courses.subj AS subj, courses.crse, AS crse, name FROM users, tas_courses, courses WHERE isTA = 1 AND firstName= :firstName AND lastName= :lastName AND users.userId = tas_courses.userId AND tas_courses.crse = courses.crse AND tas_courses.subj = courses.subj", $search_arr);
			
		}
		else { //either first name or last name entered
			$search_arr = array(':firstName' => $name_arr[0], ':lastName' => $name_arr[0]);
			$result = $dbconn->prep_execute("SELECT firstName, lastName, email, courses.subj AS subj, courses.crse AS crse, name FROM users, tas_courses, courses WHERE isTA = 1 AND (firstName= :firstName OR lastName= :lastName) AND users.userId = tas_courses.userId AND tas_courses.crse = courses.crse and tas_courses.subj = courses.subj", $search_arr);
	
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


?>