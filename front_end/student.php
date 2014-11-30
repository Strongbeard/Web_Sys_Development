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
							<th>Course Name</th><th>Hours</th><th>TA Name</th>
						</tr>
						<?php
							$var = $_SESSION['user']->getStudentTAs();
							//subj, crse 
							foreach( $var as $ta_row ) {
								$course = COURSE::fromDatabase($ta_row['subj'], intval($ta_row['crse']));
								echo "<tr><td>" . $course->getName() . "</td><td> HOUR NEEDs TO BE FIXED </td><td>"  . $ta_row['firstName'] . " " . $ta_row['lastName'] . "</td><tr>";
							}	
						?>
						</table>
				</figure>	
			</section>
			
			<aside>
				<!--checks if you are a student and asks you if you ha a TA_CODE-->
				<section>
					<?php
						$t = $_SESSION['user']->getIsTA();
						$s = $_SESSION['user']->getIsStudent();
						if ($t == false && $s == true ) {
								echo "<h2>Are You a TA?</h2>";
								echo "<input type='text' name='firstname' placeholder='Enter your ta_code'>";
						    echo "<input type='submit' value='Submit'>";
						}
						else if ($t == true && $s == true ) {
								echo "<h2>Are You a TA for another class?</h2>";
								echo "<input type='text' name='firstname' placeholder='Enter your ta_code'>";
						    echo "<input type='submit' value='Submit'>";
						}
					?>
				</section>
				<!--link to ALAC website-->
				<section >
					<h2>ALAC Hours</h2>
					<a href="http://alac.rpi.edu/update.do?artcenterkey=4">alac.rpi.edu</a>
				</section>
			</aside>
			
			<footer>
				© 2014 TA Hunters
			</footer>
		</div><!-- .wrapper -->
	
</body></html>