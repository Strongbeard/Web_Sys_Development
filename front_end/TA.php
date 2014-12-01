<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require(SITE_ROOT . '/php/check_logged_in.php');
require(SITE_ROOT . '/PHP/Course.php');
require(SITE_ROOT . '/PHP/relations.php');

$courses = $_SESSION['user']->getTACourses();
$TAhours = $_SESSION['user']->getTAOfficeHours();

if( isset($_POST['form']) ) {
	switch ($_POST['form']) {
		case 'AddTAOfficeHours':
			try {
				list($subj, $crse) = split('-', $_POST['course']);
				$_SESSION['user']->addTAOfficeHours($subj, intval($crse), $_POST['week_day'], $_POST['startTime'], $_POST['endTime']);
			}
			catch( Exception $e ) {
			}
			break;
			
		case 'DeleteTAOfficeHours':
			list( $subj, $crse, $week_day ) = split( ' ', $_POST['ta_hours'] );
			try {
				$_SESSION['user']->removeTAOfficeHours( $subj, intval($crse), $week_day );
			}
			catch( Exception $e ) {
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
						echo "<h2> Add, View, or Delete your office hours! </h2>" . "<br>";	
					 ?>
					
					<h3>Add Your Office Hours</h3>
						<form id="AddCourse" action="#" method="POST">
							<input type="hidden" name="form" value="AddTAOfficeHours" />
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
								<input id="AddTAOfficeHours_StartTime" type="time" name="startTime" required="required"/>
							</div>
							<div class="input_block">
								<label for="AddTAOfficeHours_EndTime">End Time</label>
								<input id="AddTAOfficeHours_EndTime" type="time" name="endTime" required="required"/>
							</div>
							<br>
							<input class="input_block" type="submit" value="Add TA Office Hours" />
						</form>
					
					<br><br>
					<h3>View Your Office Hours</h3>
					
					 <table class="imagetable" width="560">
					  <tr>
							<th>SUBJ</th><th>CRSE</th><th>DAY</th><th>Start</th><th>End</th>
						</tr>
						<?php
							//u2.email, u2.firstName, u2.lastName, sc.subj, sc.crse, h.week_day, h.start_time, h.end_time
							foreach( $_SESSION['user']->getTAOfficeHours() as $hours ) {
								echo "<tr><td>" . $hours['course']->getSubj() . "</td><td>" . $hours['course']->getCrse() . "</td><td>" . $hours['week_day'] . "</td><td>" . $hours['startTime'] . "</td><td>" . $hours['endTime'] . "</td></tr>";
							}	
						?>
						</table>
						<br><br>
					
					<h3>Delete Your Office Hours</h3>
					<form id="DeleteTAOfficeHours" action="#" method="POST">
						<input type="hidden" name="form" value="DeleteTAOfficeHours" />
						<div class="input_block">
							<label for="DeleteTAOfficeHours_hours">Hours</label>
							<select id="DeleteTAOfficeHours_hours" name="ta_hours">
								<?php foreach( $TAhours as $ta_hours ) : ?>
								<option value="<?php echo $ta_hours['course']->getSubj() . ' ' . $ta_hours['course']->getCrse() . ' ' . $ta_hours['week_day']; ?>"><?php echo $ta_hours['course']->getSubj() . ' ' . $ta_hours['course']->getCrse() . ' : ' . $ta_hours['course']->getName() . ' - ' . $ta_hours['week_day'] . ' ' . $ta_hours['startTime'] . '-' . $ta_hours['endTime']; ?></option>
								<?php endforeach; ?>
							</select>
							<br><br>
							<input class="input_block" type="submit" value="Delete TA Office Hours" />
						</div>
					</form>
					
					</figure>
			</section>
			<?php include(SITE_ROOT . '/front_end/sidebar.php'); ?>

			<footer>
				© 2014 TA Hunters
			</footer>
		</div><!-- .wrapper -->
	
</body></html>