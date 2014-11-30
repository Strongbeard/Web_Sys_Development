<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require(SITE_ROOT . '/php/check_logged_in.php');
require(SITE_ROOT . '/PHP/Course.php');

$courses = COURSE::getAllCourses();
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
					
						<form id="AddCourse" action="#" method="POST">
							<div class="input_block">
								<label for="AddTAOfficeHours_Course">Course</label>
								<select id="AddTAOfficeHours_Course" name="course" required>
									<?php foreach($courses as $course) : ?>
									<option value="<?php echo $course->getSubj() . '-' . $course->getCrse(); ?>"><?php echo $course->getSubj() . ' ' . $course->getCrse() . ' - ' . $course->getName(); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="input_block">
								<label for="AddTAOfficeHours_WeekDay">Day </label>
								<select id="AddTAOfficeHours_WeekDay" name="week_day">
									<option value="SUNDAY">SUNDAY</option>
									<option value="MONDAY">MONDAY</option>
									<option value="TUESDAY">TUESDAY</option>
									<option value="WEDNESDAY">WEDNESDAY</option>
									<option value="THURSDAY">THURSDAY</option>
									<option value="FRIDAY">FRIDAY</option>
									<option value="SATURDAY">SATURDAY</option>
								</select>
							</div>
							<div class="input_block">
								<label for="AddTAOfficeHours_StartTime">Start Time</label>
								<input id="AddTAOfficeHours_StartTime" type="time" name="startTime" />
							</div>
							<div class="input_block">
								<label for="AddTAOfficeHours_EndTime">End Time</label>
								<input id="AddTAOfficeHours_EndTime" type="time" name="endTime" />
							</div>
							<br>
							<input class="input_block" type="submit" value="Add TA Office Hours" />
						</form>
					</figure>
				
				
			</section>
			<!--
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
			</aside> -->
			
			<footer>
				© 2014 TA Hunters
			</footer>
		</div><!-- .wrapper -->
	
</body></html>