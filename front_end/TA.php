<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
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
			<header>
				<div class="upperright"> 
					<?php	$firstname = $_SESSION['user']->getFirstName();
						$lastname = $_SESSION['user']->getLastName();
						echo "Welcome " . $firstname . " " . $lastname . " ";	
					?> 
					<a href="logout.php">Logout</a>
				</div>
				<h1><a href=""><img src="./resources/johnny'sapple.png" height="38px" width="38px"> TA Scheduler</a></h1>
				<nav>
					<ul>
						<?php
						$t = $_SESSION['user']->getIsAdmin();

						if ($t == true) {
								echo "<li><a href='Admin.php'>Admin</a><li>";
						}
						?>
						<?php
						$t = $_SESSION['user']->getIsTA();

						if ($t == true) {
								echo "<li><a href='student.php'>Student</a><li>";
						}
						?>
						<?php
						$t = $_SESSION['user']->getIsTA();

						if ($t == true) {
								echo "<li><a href='TA.php' class='current'> TA</a><li>";
						}
						?>
						<li><a href="search_add.php">Search/Add</a></li>
						<li><a href="_profile.php">Profile</a></li>
					</ul>
				</nav>
			</header>
			<section class="courses">
				
					<figure>
					 <?php
						$var = $_SESSION['user']->getFirstName();
						echo "<p> " . $var . " welcome to your portal page! </p>" . "<br>";	
					 ?>
					 <p> The calendar will come here? </p>
					 <p> What else? </p>
						
					</figure>
				
				
			</section>
			<aside>
				<section class="popular-recipes">
					<?php
						$t = $_SESSION['user']->getIsStudent();

						if ($t == true) {
								echo "<h2>Are You a Student?</h2>";
								echo "<input type='text' name='firstname' placeholder='Enter your code'>";
						    echo "<input type='submit' value='Submit'>";
						}
					?>
				</section>
			</aside>
			<footer>
				© 2014 TA Hunters
			</footer>
		</div><!-- .wrapper -->
	
</body></html>