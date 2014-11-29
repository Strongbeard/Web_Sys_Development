<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require(SITE_ROOT . '/PHP/User.php');
require(SITE_ROOT . '/PHP/Course.php');
require(SITE_ROOT . '/PHP/relations.php');
require(SITE_ROOT . '/PHP/check_logged_in.php');

$users = USER::getAllUsers();
$courses = COURSE::getAllCourses();

$message = '';
$message_class = 'hidden';

if( isset($_POST['form']) ) {
	switch ($_POST['form']) {
		case 'AddCourse':
			try{
				if( COURSE::withValues($_POST['subj'], intval($_POST['crse']), $_POST['name'])->getInDB() ) {
					$message = 'Success!';
					$message_class = 'success';
				}
				else {
					$message = 'ERROR: could not add course to database.';
					$message_class = 'error';
				}
			}
			catch( Exception $e ) {
				$message = $e->getMessage();
				$message_class = 'error';
			}
			break;
		case 'AddStudentCourseRel':
			try {
				$user = USER::fromDatabase($_POST['email']);
				list($subj, $crse) = split('-', $_POST['course']);
				if( $user->addUserCourse('student', $subj, intval($crse) ) ) {
					$message = 'Success!';
					$message_class = 'success';
				}
				else {
					$message = 'ERROR: could not add student - course relationship to database.';
					$message_class = 'error';
				}
			}
			catch( Exception $e ) {
				$message = $e->getMessage();
				$message_class = 'error';
			}
			break;
		case 'AddTACourseRel':
			try {
				$user = USER::fromDatabase($_POST['email']);
				list($subj, $crse) = split('-', $_POST['course']);
				if( $user->addUserCourse('ta', $subj, intval($crse) ) ) {
					$message = 'Success!';
					$message_class = 'success';
				}
				else {
					$message = 'ERROR: could not add student - course relationship to database.';
					$message_class = 'error';
				}
			}
			catch( Exception $e ) {
				$message = $e->getMessage();
				$message_class = 'error';
			}
			break;
		case 'AddUser':
			try {
				$admin = ($_POST['admin'] === 'on') ? true : false;
				$student = ($_POST['student'] === 'on') ? true : false;
				$ta = ($_POST['ta'] === 'on') ? true : false;
				$tutor = ($_POST['tutor'] === 'on') ? true : false;
				if( USER::withValues($_POST['email'], $_POST['password'], $student, $ta, $tutor, $admin, $_POST['firstname'], $_POST['lastname'])->getInDB() ) {
					$message = 'Success!';
					$message_class = 'success';
				}
				else {
					$message = 'ERROR: could not add user to database.';
					$message_class = 'error';
				}
			}
			catch( Exception $e ) {
				$message = $e->getMessage();
				$message_class = 'error';
			}
			break;
		case 'DeleteCourse':
			list($subj, $crse) = split('-', $_POST['course']);
			try {
				if( COURSE::deleteFromDB($subj, intval($crse)) ) {
					$message = 'Success!';
					$message_class = 'success';
				}
				else {
					$message = 'ERROR: could not delete course from database.';
					$message_class = 'error';
				}
			}
			catch( Exception $e ) {
				$message = $e->getMessage();
				$message_class = 'error';
			}
			break;
		case 'DeleteStudentCourseRel':
			list( $subj, $crse, $email ) = split( ' ', $_POST['student_course'] );
			try {
				if( USER::fromDatabase($email)->removeUserCourse( 'student', $subj, intval($crse) ) ) {
					$message = 'Success!';
					$message_class = 'success';
				}
				else {
					$message = 'ERROR: could not delete student - course relationship from database.';
					$message_class = 'error';
				}
			}
			catch( Exception $e ) {
				$message = $e->getMessage();
				$message_class = 'error';
			}
			break;
		case 'DeleteTACourseRel':
			list( $subj, $crse, $email ) = split( ' ', $_POST['ta_course'] );
			try {
				if( USER::fromDatabase($email)->removeUserCourse( 'ta', $subj, intval($crse) ) ) {
					$message = 'Success!';
					$message_class = 'success';
				}
				else {
					$message = 'ERROR: could not delete ta - course relationship from database.';
					$message_class = 'error';
				}
			}
			catch( Exception $e ) {
				$message = $e->getMessage();
				$message_class = 'error';
			}
			break;
		case 'DeleteUser':
			try {
				if( USER::deleteFromDB($_POST['email']) ) {
					$message = 'Success!';
					$message_class = 'success';
				}
				else {
					$message = 'ERROR: could not delete user from database.';
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
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>TA Scheduler</title>
	<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL; ?>/front_end/resources/user.css">
	<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL; ?>/front_end/resources/admin.css">
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
		<?php include(SITE_ROOT . '/front_end/header.php') ?>
		<section id="Message" class="<?php echo $message_class ?>">
			<?php echo $message ?>
		</section>
		<section class="courses">
			<form id="AddUser" action="#" method="POST">
				<h2>Add a User</h2>
				<input type="hidden" name="form" value="AddUser" />
				<div class="input_block">
					<label for="AddUser_Email">Email</label>
					<input id="AddUser_Email" type="text" name="email" required />
				</div>
				<div class="input_block">
					<label for="AddUser_FirstName">First Name</label>
					<input id="AddUser_FirstName" type="text" name="firstname" />
				</div>
				<div class="input_block">
					<label for="AddUser_LastName">Last Name</label>
					<input id="AddUser_LastName" type="text" name="lastname" />
				</div>
				<div class="input_block">
					<label for="AddUser_Password">Password</label>
					<input id="AddUser_Password" type="password" name="password" required />
				</div>
				<div class="input_inline">
					<input id="AddUser_Admin" type="checkbox" name="admin"/>
					<label for="AddUser_Admin">Admin</label>
				</div>
				<div class="input_inline">
					<input id="AddUser_Student" type="checkbox" name="student" />
					<label for="AddUser_Student">Student</label>
				</div>
				<div class="input_inline">
					<input id="AddUser_TA" type="checkbox" name="ta" />
					<label for="AddUser_TA">TA<label>
				</div>
				<div class="input_inline">
					<input id="AddUser_Tutor" type="checkbox" name="tutor" />
					<label for="AddUser_Tutor">Tutor</label>
				</div>
				<input class="input_block" type="submit" value="Add User" />
			</form>
			<form id="DeleteUser" action="#" method="POST">
				<h2>Delete a User</h2>
				<input type="hidden" name="form" value="DeleteUser" />
				<label for="DeleteUser_User">User</label>
				<select id="DeleteUser_User" name="email" required>
				<?php foreach($users as $user) : ?>
					<option value="<?php echo $user->getEmail(); ?>"><?php echo $user->getLastName() . ', ' . $user->getFirstName() . ' (' . $user->getEmail() . ')'; ?></option>
				<?php endforeach; ?>
				</select>
				<input class="input_block" type="submit" value="Delete User" />
			</form>
			<form id="AddCourse" action="#" method="POST">
				<h2>Add a Course</h2>
				<input type="hidden" name="form" value="AddCourse" />
				<div class="input_block">
					<label for="AddCourse_Subj">Subj</label>
					<input id="AddCourse_Subj" type="text" name="subj" required />
				</div>
				<div class="input_block">
					<label for="AddCourse_Crse">Crse</label>
					<input id="AddCourse_Crse" type="text" name="crse" required />
				</div>
				<div class="input_block">
					<label for="AddCourse_Name">Name</label>
					<input id="AddCourse_Name" type="text" name="name" />
				</div>
				<input class="input_block" type="submit" value="Add Course" />
			</form>
			<form id="DeleteCourse" action="#" method="POST">
				<h2>Delete a Course</h2>
				<input type="hidden" name="form" value="DeleteCourse" />
				<label for="DeleteCourse_Course">Course</label>
				<select id="DeleteCourse_Course" name="course" required>
				<?php foreach($courses as $course) : ?>
					<option value="<?php echo $course->getSubj() . '-' . $course->getCrse(); ?>"><?php echo $course->getSubj() . ' ' . $course->getCrse() . ' - ' . $course->getName(); ?></option>
				<?php endforeach; ?>
				</select>
				<input class="input_block" type="submit" value="Delete Course" />
			</form>
			<form id="AddStudentCourseRelation" action="#" method="POST">
				<h2>Give a Student a Course</h2>
				<input type="hidden" name="form" value="AddStudentCourseRel" />
				<div class="input_block">
					<label for="AddStudentCourse_Student">Student</label>
					<select id="AddStudentCourse_Student" name="email">
					<?php foreach($users as $user ) : if($user->getIsStudent()) : ?>
						<option value="<?php echo $user->getEmail(); ?>"><?php echo $user->getLastName() . ', ' . $user->getFirstName() . ' (' . $user->getEmail() . ')'; ?></option>
					<?php endif; endforeach; ?>
					</select>
				</div>
				<div class="input_block">
					<label for="AddStudentCourse_Course">Course</label>
					<select id="AddStudentCourse_Course" name="course">
						<?php foreach($courses as $course) : ?>
							<option value="<?php echo $course->getSubj() . '-' . $course->getCrse(); ?>"><?php echo $course->getSubj() . ' ' . $course->getCrse() . ' - ' . $course->getName(); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<input type="submit" name="submit" value="Add Student Course Relationship" />
			</form>
			<form id="DeleteStudentCourseRelation" action="#" method="POST">
				<h2>Remove a Student's Course</h2>
				<input type="hidden" name="form" value="DeleteStudentCourseRel" />
				<div class="input_block">
					<label for="DeleteStudentCourse_Rel">Course</label>
					<select id="DeleteStudentCourse_Rel" name="student_course" required>
					<?php foreach(getAllStudentsCourses() as $mappings) : ?>
						<option value="<?php echo $mappings['course']->getSubj() . ' ' . $mappings['course']->getCrse() . ' ' . $mappings['user']->getEmail(); ?>"><?php echo $mappings['user']->getLastName() . ', ' . $mappings['user']->getFirstName() . ' (' . $mappings['user']->getEmail() . ') - ' . $mappings['course']->getSubj() . ' ' . $mappings['course']->getCrse() . ' ' . $mappings['course']->getName(); ?></option>
					<?php endforeach; ?>
					</select>
				</div>
				<input class="input_block" type="submit" value="Delete Student Course Relationship" />
			</form>
			<form id="AddTACourseRelation" action="#" method="POST">
				<h2>Give a TA a Course</h2>
				<input type="hidden" name="form" value="AddTACourseRel" />
				<div class="input_block">
					<label for="AddTACourse_TA">Student</label>
					<select id="AddTACourse_TA" name="email">
					<?php foreach($users as $user ) : if($user->getIsTA()) : ?>
						<option value="<?php echo $user->getEmail(); ?>"><?php echo $user->getLastName() . ', ' . $user->getFirstName() . ' (' . $user->getEmail() . ')'; ?></option>
					<?php endif; endforeach; ?>
					</select>
				</div>
				<div class="input_block">
					<label for="AddStudentTA_TA">Course</label>
					<select id="AddStudentTA_TA" name="course">
						<?php foreach($courses as $course) : ?>
							<option value="<?php echo $course->getSubj() . '-' . $course->getCrse(); ?>"><?php echo $course->getSubj() . ' ' . $course->getCrse() . ' - ' . $course->getName(); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<input class="input_block" type="submit" value="Add TA Course Relationship" />
			</form>
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
</body>
</html>