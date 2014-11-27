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
							<form id="search_ta_form" method="GET">
								<p>Search by name:</p>
								<input type="search" id="search_ta" name="search_ta" placeholder="Enter name of TA"/>
								<input type="submit" value="Search"/>
							</form>
							<hr>						
							<form id="search_class_form">
								<p>Search by Class:</p>
								<input type="search" id="search_class" name="search_class" placeholder="Enter name of class"/>
								<input type="submit" value="Search"/>
							
								<p>Select a school:</p>
								<select name="search_school" id="search_school">
									<option>Architecture</option>
									<option>Business</option>
									<option>Engineering</option>
									<option>HASS</option>
									<option>ITWS</option>
									<option>Science</option>
								</select>
							</form>
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
			<section>
				<table id="results">
					<thead>
						
					</thead>
					<tbody>
					</tbody>
				</table>
			</section>
			<footer>
				© 2014 TA Hunters
			</footer>
		</div><!-- .wrapper -->	

		<script src="resources/jquery-1.11.1.min.js"></script>
		<script>
			$('#search_ta_form').submit(function (event) {
				event.preventDefault();

				var ta_name = $('#search_ta').val();
				//var search_class = $('#search_class').val();
				//var search_school = $("#search_school option:selected" ).val();

				$('input').removeClass('error');

				if (!ta_name) {
					$('#search_ta').addClass('error');
				}
				else {
					$('#results > tbody').empty();
					$.ajax({
		                url: "./find.php",
		                data: {'ta_name': ta_name},
		                method: 'GET',
		                success: function (data) {
		                    data = $.trim(data);
		                   	console.log("data: " + data.length);
		                   	if (data.length > 0) {
		                   		$('#results > thead').append("<tr><th>First Name</th><th>Last Name</th><th>Email</th><th>Subject</th><th>Course</th><th>Name</th></tr>");
		                   		$('#results > tbody').append(data);
		                   	}
		          
		                 

		                },
		                error: function () {
		                    console.log("error");
		                }
		            });
				}
			
			});
		</script>
</body></html>