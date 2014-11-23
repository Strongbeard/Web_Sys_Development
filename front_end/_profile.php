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
				<h1><a href=""><img src="./resources/johnny'sapple.png" height="38px" width="38px"> TA Scheduler</a></h1>
				<nav>
					<ul>
						<li><a href="welcomepage.php">home</a></li>
						<li><a href="search_add.php">Search/Add TA</a></li>
						<li><a href="" class="current">Profile</a></li>
						<li><a href="logout.php">Logout</a></li>
						<li><a href=""></a></li>
					</ul>
				</nav>
			</header>
			<section class="courses">
				<figure>
					 <p> What is profile supposed to do?</p>
					 <p>Name: Eric Example</p>
						<p>Email: exampe@rpi.edu</p>
				</figure>
			</section>
			<aside>
				<section class="popular-recipes">
					<h2>Your TAs</h2>
					<!--link to the TA's profile?-->
					<a href="#">Alfred Hitman</a>
					<a href="#">Tom Madrid</a>
					<a href="#">Memo Kamikase</a>
					<a href="#">Rick Platinin</a>
				</section>
				<section class="contact-details">
					<!--I'm not sure if a category should be here-->
					<h2>Are you also a:</h2>
					<a href="welcomepage_for_TA.php">Teacher Assistant?</a>
				</section>
			</aside>
			<footer>
				© 2014 TA Hunters
			</footer>
		</div><!-- .wrapper -->
	
</body></html>