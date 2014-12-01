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
		<link rel="stylesheet" type="text/css" href="./resources/table.css">
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
						$var = $_SESSION['user']->getFirstName();
						echo "<p> " . $var . " welcome to your portal page! </p>" . "<br>";	
					?>
					 <table class="imagetable" width="560">
					  <tr>
							<th>Course Name</th><th>Hours</th><th>TA Name</th><th>E-mail</th>
						</tr>
						<?php
							//u2.email, u2.firstName, u2.lastName, sc.subj, sc.crse, h.week_day, h.start_time, h.end_time
							foreach( $_SESSION['user']->getStudentTAsOfficeHours() as $ta_row ) {
								$course = COURSE::fromDatabase($ta_row['subj'], intval($ta_row['crse']));
								echo "<tr><td>" . $course->getName() . "</td><td>" . $ta_row['week_day']." ". substr($ta_row['start_time'],0,-3) . " - " . substr($ta_row['end_time'],0,-3) . "</td><td>"  . $ta_row['firstName'] . " " . $ta_row['lastName'] . "</td><td>" . $ta_row['email'] . "</td><tr>";
							}	
						?>
						</table>
						
				</figure>	
			</section>
			<!--checks if you are a student and asks you if you have a TA_CODE-->
			<?php include(SITE_ROOT . '/front_end/sidebar.php'); ?>
			
			<footer>
				© 2014 TA Hunters
			</footer>
		</div><!-- .wrapper -->
	
</body></html>