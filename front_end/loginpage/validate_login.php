
<?php
	require('../../PHP/DB.php');
	session_start();



	try {
		$dbconn = new DB();

		$empty = empty($_POST['user']) || empty($_POST['pass']); 
		if( isset($_POST['user'], $_POST['pass']) && $empty == false) {

			$username = mysql_real_escape_string(stripslashes($_POST['user']));
			$password = mysql_real_escape_string(stripslashes($_POST['pass']));

			$salt = "\0\x06\x08\0f"; 
			$hashpass = sha1($password . $salt); 	 //use hashpass once inserted correct pass
			
			
			$query = $dbconn->query("SELECT * FROM (SELECT * FROM users) user, (SELECT * FROM passwords) pass WHERE user.email='$username' AND pass.userId = user.userId AND password='$password'");

			if ($row = $query->fetch()) { //successfully logged in
				$_SESSION["user"] = $username;
				//$_SESSION["email"] = $row['email'];
				//$_SESSION["phone"] = $row['phone'];
				echo "1";
				exit();
			}
			else {
				echo "2";
			}
		}
		else {
			echo "form not completed";
		}
	}
	catch (Exception $e) {
		echo "Error: " . $e->getMessage();
	}




?>
