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
		<div class="upperright"> 
			<?php	$firstname = $_SESSION['user']->getFirstName();
						$lastname = $_SESSION['user']->getLastName();
						echo "Welcome " . $firstname . " " . $lastname . " ";	
			?> 
			<a href="logout.php">Logout</a>
		</div>
		<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	</head>
	<body>
		<div class="wrapper">
			<header>
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
						$t = $_SESSION['user']->getIsStudent();

						if ($t == true) {
								echo "<li><a href='student.php'>Student</a><li>";
						}
						?>
						<?php
						$t = $_SESSION['user']->getIsTA();

						if ($t == true) {
								echo "<li><a href='TA.php'> TA</a><li>";
						}
						?>
						<li><a href="search_add.php" class="current">Search/Add</a></li>
						<li><a href="_profile.php">Profile</a></li>
					</ul>
				</nav>
			</header>
			
			<section class="courses">
					<figure>
						<hgroup>
							<h2>Search Your TAs </h2>
							<form>
								<p>Search by name:</p>
								<input type="search" name="search" placeholder="Enter name of TA"/>
								<input type="submit" value="Search"/>
							</form>
							<hr>						
							<form>
								<p>Search by Class:</p>
								<input type="search" name="search" placeholder="Enter name of TA"/>
								<input type="submit" value="Search"/>
							</form>
								<p>Select a school:</p>
							<select>
								<option>Architecture</option>
								<option>Business</option>
								<option>Engineering</option>
								<option>HASS</option>
								<option>ITWS</option>
								<option>Science</option>
							</select>
						</hgroup>
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