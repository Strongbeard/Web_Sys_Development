<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once(SITE_ROOT . '/PHP/User.php');
require_once(SITE_ROOT . '/PHP/Course.php');
require(SITE_ROOT . '/php/check_logged_in.php');
?>
<!DOCTYPE html>
<html><head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>TA Scheduler</title>
		<link rel="stylesheet" type="text/css" href="./resources/user.css">
		<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	</head>
	<body>
		<div class="wrapper">
			<?php	$firstname = $_SESSION['user']->getFirstName();
						$lastname = $_SESSION['user']->getLastName();
						echo "<div id= 'namesize' align='right'> Welcome " . $firstname . " " . $lastname . " </div>";	
					?>
		<div id="upperright"> 
			<a href="logout.php">Logout</a>
		</div>
		<?php include(SITE_ROOT . '/front_end/header.php') ?>
			
			<section class="courses">
				<figure>
					<?php
						$firstname = $_SESSION['user']->getFirstName();
						$lastname = $_SESSION['user']->getLastName();
						 echo "Name: " . $firstname . " " . $lastname . "<br>";
						 
						$email = $_SESSION['user']->getEmail();
							echo "<p>Email: ". $email . "</a>";
					?>
				</figure>
			</section>
			
			<aside>
				<!--<section class="popular-recipes">
					<?php/*
						$t = $_SESSION['user']->getIsTA();

						if ($t == true) {
								echo "<h2>Are You a TA?</h2>";
								echo "<input type='text' name='firstname' placeholder='Enter your code'>";
						    echo "<input type='submit' value='Submit'>";
						}*/
					?>
				</section>-->
			</aside>
			<footer>
				© 2014 TA Hunters
			</footer>
		</div><!-- .wrapper -->
	
</body></html>