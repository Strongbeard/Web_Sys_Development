
<?php
	
	require(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require(SITE_ROOT . '\PHP\DB.php');
	require(SITE_ROOT . '\PHP\User.php');


	try {
		//$dbconn = new DB();
		$dbconn = DB::getInstance();

		if (isset($_POST['login'])) {
			$empty = empty($_POST['user']) || empty($_POST['pass']); 
			if( isset($_POST['user'], $_POST['pass']) && $empty == false) {

				$username = mysql_real_escape_string(stripslashes($_POST['user']));
				$password = mysql_real_escape_string(stripslashes($_POST['pass']));

				
				$user = User::fromDatabase($username);
				if ($user) {
					if ($user->login($password)) {
						echo 1;
					}
					else {
						echo 0;
					}
				}
				else {
					echo 0;
				}
				
				exit();
				
			}
			else {
				echo "form not completed";
			}
		}

		if (isset($_POST['register'])) {
			$empty = empty($_POST['firstName']) || empty($_POST['lastName']) || empty($_POST['email']) || empty($_POST['password']) 
					|| empty($_POST['isStudent']) || empty($_POST['isTA']) || empty($_POST['isTutor']); 
			if( isset($_POST['firstName'], $_POST['lastName'], $_POST['email'], $_POST['password'], $_POST['isStudent'], $_POST['isTA'], $_POST['isTutor']) && $empty == false) { 

				$firstName = mysql_real_escape_string(stripslashes($_POST['firstName']));
				$lastName = mysql_real_escape_string(stripslashes($_POST['lastName']));
				$email = mysql_real_escape_string(stripslashes($_POST['email']));
				$password = mysql_real_escape_string(stripslashes($_POST['password']));
				$isStudent = mysql_real_escape_string(stripslashes($_POST['isStudent']));
				$isTA = mysql_real_escape_string(stripslashes($_POST['isTA']));
				$isTutor = mysql_real_escape_string(stripslashes($_POST['isTutor']));
				
				// Transforms strings into boolean values
				$isStudent = ($isStudent === 'true') ? true : false;
				$isTA = ($isTA === 'true') ? true : false;
				$isTutor = ($isTutor === 'true') ? true : false;

				$user = User::withValues($email, $password, $isStudent, $isTA, $isTutor, false, $firstName, $lastName);

				if ($user === null) { //check if error instantiating user (password too short)
					echo 0;
				}
				else {
					if ($user->store() === false) //check if user already exists in system
						echo 1;
					else {

						echo 2;
					}

				}
			}
		}
	}
	catch (Exception $e) {
		echo "Error: " . $e->getMessage();
	}




?>
