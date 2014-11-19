
<?php
	
	require('../../PHP/DB.php');
	require('../../PHP/User.php');


	try {
		//$dbconn = new DB();
		$dbconn = DB::getInstance();
		$empty = empty($_POST['user']) || empty($_POST['pass']); 
		if( isset($_POST['user'], $_POST['pass']) && $empty == false) {

			$username = mysql_real_escape_string(stripslashes($_POST['user']));
			$password = mysql_real_escape_string(stripslashes($_POST['pass']));

			
			$user = User::fromDatabase('email', $username);
			if ($user) {
				if ($user->login($password)) {
					session_start();
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
	catch (Exception $e) {
		echo "Error: " . $e->getMessage();
	}




?>
