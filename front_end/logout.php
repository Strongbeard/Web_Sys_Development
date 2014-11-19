<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once(SITE_ROOT . '\PHP\User.php');
	session_start();
	
    //automatically destroys user session data
	if( isset($_SESSION) && !empty($_SESSION['user']) ) {
		$_SESSION['user']->logout();
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Logged Out</title>
		<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL; ?>/front_end/resources/index.css">
	</head>
	<body>
		<h1>You have successfully logged out!</h1>
	
		<p class="logoutLink"><a href="<?php echo SITE_URL; ?>/front_end/loginpage/login.html">Return to login page</a></p>
		<p class="logoutLink"><a href="JavaScript:window.close()">Close Window</a></p>
	</body>
</html>