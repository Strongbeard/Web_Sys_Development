<?php

require_once(dirname(__FILE__) . '\config.php');

function install($user, $pass) {
	$dbh = new PDO("mysql:host=localhost",$user,$pass);

	$filehandle = fopen(SITE_ROOT . '\SQL\init.sql', 'r') or die("Unable to open init.sql.");
	$init_sql = fread($filehandle, filesize(SITE_ROOT . '\SQL\init.sql'));
	fclose($filehandle);
	$filehandle = fopen(SITE_ROOT . '\SQL\populate.sql', 'r') or die("Unable to open populate.sql.");
	$populate_sql = fread($filehandle, filesize(SITE_ROOT . '\SQL\populate.sql'));
	fclose($filehandle);

	$dbh->exec($init_sql);
	$dbh->exec($populate_sql);
	
	return true;
}

$message = "";
if( isset($_POST) && !empty($_POST['username']) && isset($_POST['password']) ) {
	try {
		if( install($_POST['username'], $_POST['password']) ) {
			$message = "Success!";
		}
	}
	catch( Exception $e ) {
		$message = "ERROR: " . $e->getMessage();
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Install</title>
</head>
<body>
	<?php if( $message === "" ) : ?>
	<p>Please enter the root or admin username and password for MySQL to add the TA Scheduler database and user.</p>
	<form action="#" method="POST">
		<label>Username</label>
		<input id="username" type="text" name="username" />
		<label>Password</label>
		<input id="password" type="password" name="password" />
		<input type="submit" value="Submit" />
	</form>
	<?php else : ?>
	<p><?php echo $message; ?></p>
	<?php endif; ?>
</body>
</html>