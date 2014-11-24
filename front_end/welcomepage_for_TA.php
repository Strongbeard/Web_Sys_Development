<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
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
						echo $firstname . " " . $lastname . "<br>";	
			?> 
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
						<li><a href="welcomepage_for_TA.php" class="current">home</a></li>
						<li><a href="search_add_for_TA.php">Search/Add Student</a></li>
						<li><a href="_profile_for_TA.php">Profile</a></li>
						<li><a href="logout.php">Logout</a></li>
						<li><a href=""></a></li>
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
					<h2>Classes Currently Assisting?</h2>
					<a href="#">Earth and Fire</a>
					<a href="#">General Psychology</a>
				</section>
			</aside>
			<footer>
				© 2014 TA Hunters
			</footer>
		</div><!-- .wrapper -->
	
</body></html>