
<?php
    //automatically destroys user session data
    session_start();
    $_SESSION = array();
    session_destroy();
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Logged Out</title>
		<link rel="stylesheet" type="text/css" href="./resources/index.css">
	</head>
	<body>
		<h1>You have successfully logged out!</h1>
	
		<p class="logoutLink"><a href="./loginpage/loginpage/login.html">Return to login page</a></p>
		<p class="logoutLink"><a href="JavaScript:window.close()">Close Window</a></p>
	</body>
</html>