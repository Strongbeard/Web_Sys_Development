<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require(SITE_ROOT . '/php/check_logged_in.php');
require(SITE_ROOT . '/PHP/Course.php');
require(SITE_ROOT . '/PHP/relations.php');

$courses = COURSE::getAllCourses();
$users = USER::getAllUsers();

$message = '';
$message_class = 'hidden';

if( isset($_POST['form']) ) {
	switch ($_POST['form']) {
		case 'AddTAOfficeHours':
			try {
				$user = USER::fromDatabase($_POST['email']);
				list($subj, $crse) = split('-', $_POST['course']);
				if( $user->addTAOfficeHours($subj, intval($crse), $_POST['week_day'], $_POST['startTime'], $_POST['endTime']) ) {
					$message = 'Success!';
					$message_class = 'success';
				}
				else {
					$message = 'ERROR: could not add TA office hours to database.';
					$message_class = 'error';
				}
			}
			catch( Exception $e ) {
				$message = $e->getMessage();
				$message_class = 'error';
			}
			break;
		case 'DeleteTAOfficeHours':
			list( $subj, $crse, $email, $week_day ) = split( ' ', $_POST['ta_hours'] );
			try {
				if( USER::fromDatabase($email)->removeTAOfficeHours( $subj, intval($crse), $week_day ) ) {
					$message = 'Success!';
					$message_class = 'success';
				}
				else {
					$message = 'ERROR: could not delete TA office hours from database.';
					$message_class = 'error';
				}
			}
			catch( Exception $e ) {
				$message = $e->getMessage();
				$message_class = 'error';
			}
			break;
	}		
}
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
						echo "<p> " . $var . ": Add, View, or Delete your office hours! </p>" . "<br>";	
					 ?>
					
					<h3>Add Your Office Hours</h3>
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
					
					<br><br>
					<h3>View Your Office Hours</h3>
					<?PHP
					$TAemail = $_SESSION['user']->getEmail();
					//connect to server
					$connect = mysql_connect("localhost","root","");
					
					//connect to database
					mysql_select_db("ta_hunter");
					
					//query the database
					$query = mysql_query("SELECT * FROM ta_hours WHERE email = '$TAemail' ");
					
					//fetch the results/convert results into an array
						WHILE($rows = mysql_fetch_array($query)):
							$email = $rows['email'];
							$subj = $rows['subj'];
							$crse = $rows['crse'];
							$week_day = $rows['week_day'];
							$start_time = $rows['start_time'];
							$end_time = $rows['end_time'];
						
						echo "<table border=\"1\">
						<tr>
							<td><b>SUBJ</b></td>
							<td><b>CRSE</b></td>
							<td><b>Day</b></td>
							<td><b>Start Time</b></td>
							<td><b>End Time</b></td>
						</tr>";
						echo "<tr><td>$subj</td><td>$crse</td><td>$week_day</td><td>$start_time</td><td>$end_time</td></tr>";
						endwhile;
					
					
					?>
					
					
					<br><br>
					<!--
					<h3>Delete Your Office Hours</h3>
						<form id="DeleteTACourseRelation" action="#" method="POST">
							<h2>Remove a TA's Course</h2>
							<input type="hidden" name="form" value="DeleteTACourseRel" />
							<div class="input_block">
								<label for="DeleteTACourse_Rel">Course</label>
								<select id="DeleteTACourse_Rel" name="ta_course" required>
								<?php foreach(getAllTAsCourses() as $mappings) : ?>
									<option value="<?php echo $mappings['course']->getSubj() . ' ' . $mappings['course']->getCrse() . ' ' . $mappings['user']->getEmail(); ?>"><?php echo $mappings['user']->getLastName() . ', ' . $mappings['user']->getFirstName() . ' (' . $mappings['user']->getEmail() . ') - ' . $mappings['course']->getSubj() . ' ' . $mappings['course']->getCrse() . ' ' . $mappings['course']->getName(); ?></option>
								<?php endforeach; ?>
								</select>
							</div>
							<input class="input_block" type="submit" value="Delete TA Course Relationship" />
						</form>
					-->
					</figure>
				
				
			</section>
			<footer>
				© 2014 TA Hunters
			</footer>
		</div><!-- .wrapper -->
	
</body></html>