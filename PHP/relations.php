<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once(SITE_ROOT . '\PHP\DB.php');
require_once(SITE_ROOT . '\PHP\User.php');
require_once(SITE_ROOT . '\PHP\Course.php');

function getAllStudentsCourses() {
	// Get student - course key mappings
	$db = DB::getInstance();
	$students_courses = $db->prep_execute('SELECT * FROM students_courses;', array());
	
	// Local list of user objects. Prevents unnecessary DB reads.
	$users = array();
	
	// Local list of course objects. Prevents unnecessary DB reads.
	$courses = array();
	
	// Array of user - course object pair mappings to be returned.
	$return = array();
	
	// Loop through all student - course key mappings
	foreach( $students_courses as $row ) {
		// Read user from DB and add to user array if not found in array
		if( !isset($users[$row['email']]) ) {
			$users[$row['email']] = USER::fromDatabase($row['email']);
		}

		// Read course from DB and add to user array if not found in array
		if( !isset($courses[$row['subj'] . '-' . $row['crse']]) ) {
			$courses[$row['subj'] . '-' . $row['crse']] = COURSE::fromDatabase( $row['subj'], intval($row['crse']) );
		}

		// Add student - course object pair to return array
		$return[] = [
			'user' => $users[$row['email']],
			'course' => $courses[$row['subj'] . '-' . $row['crse']]
		];
	}

	return $return;
}

function getAllTAsCourses() {
	// Get student - course key mappings
	$db = DB::getInstance();
	$tas_courses = $db->prep_execute('SELECT * FROM tas_courses;', array());
	
	// Local list of user objects. Prevents unnecessary DB reads.
	$users = array();
	
	// Local list of course objects. Prevents unnecessary DB reads.
	$courses = array();
	
	// Array of user - course object pair mappings to be returned.
	$return = array();
	
	// Loop through all student - course key mappings
	foreach( $tas_courses as $row ) {
		// Read user from DB and add to user array if not found in array
		if( !isset($users[$row['email']]) ) {
			$users[$row['email']] = USER::fromDatabase($row['email']);
		}

		// Read course from DB and add to user array if not found in array
		if( !isset($courses[$row['subj'] . '-' . $row['crse']]) ) {
			$courses[$row['subj'] . '-' . $row['crse']] = COURSE::fromDatabase( $row['subj'], intval($row['crse']) );
		}

		// Add student - course object pair to return array
		$return[] = [
			'user' => $users[$row['email']],
			'course' => $courses[$row['subj'] . '-' . $row['crse']]
		];
	}
	
	return $return;
}

?>