<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require(SITE_ROOT . '/PHP/User.php');
require(SITE_ROOT . '/PHP/Course.php');
require(SITE_ROOT . '/PHP/check_logged_in.php');

$message = '';
$message_class = 'hidden';

if( isset($_POST['form']) ) {
	switch ($_POST['form']) {
		case 'AddCourse':
			try{
				if( COURSE::withValues($_POST['AddCourse_Subj'], intval($_POST['AddCourse_Crse']), $_POST['AddCourse_Name'])->getInDB() ) {
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
		case 'AddUser':
			break;
		case 'DeleteCourse':
			list($subj, $crse) = split('-', $_POST['DeleteCourse_Course']);
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
		case 'DeleteUser':
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
				<h2>Add A User</h2>
				<input type="hidden" name="form" value="AddUser" />
				<div class="input_block">
					<label for="">Email</label>
					<input id="AddUser_Email" type="text" name="AddUser_Email" required />
				</div>
				<div class="input_block">
					<label for="AddUser_FirstName">First Name</label>
					<input id="AddUser_FirstName" type="text" name="AddUser_FirstName" />
				</div>
				<div class="input_block">
					<label for="AddUser_LastName">Last Name</label>
					<input id="AddUser_LastName" type="text" name="AddUser_LastName" />
				</div>
				<div class="input_block">
					<label for="AddUser_Password">Password</label>
					<input id="AddUser_Password" type="password" name="AddUser_Password" required />
				</div>
				<div class="input_inline">
					<input id="AddUser_Admin" type="checkbox" name="AddUser_Admin"/>
					<label for="AddUser_Admin">Admin</label>
				</div>
				<div class="input_inline">
					<input id="AddUser_Student" type="checkbox" name="AddUser_Student" />
					<label for="AddUser_Student">Student</label>
				</div>
				<div class="input_inline">
					<input id="AddUser_TA" type="checkbox" name="AddUser_TA" />
					<label for="AddUser_TA">TA<label>
				</div>
				<div class="input_inline">
					<input id="AddUser_Tutor" type="checkbox" name="AddUser_Tutor" />
					<label for="AddUser_Tutor">Tutor</label>
				</div>
				<input class="input_block" type="submit" value="Add User" />
			</form>
			<form id="DeleteUser" action="#" method="POST">
				<h2>Delete A User</h2>
				<input type="hidden" name="form" value="DeleteUser" />
				<label for="DeleteUser_User">User</label>
				<select id="DeleteUser_User" name="DeleteUser_User" required>
				<?php foreach(USER::getAllUsers() as $user) : ?>
					<option value="<?php echo $user->getEmail(); ?>"><?php echo $user->getLastName() . ', ' . $user->getFirstName() . ' - ' . $user->getEmail(); ?></option>
				<?php endforeach; ?>
				</select>
				<input class="input_block" type="submit" value="Delete User" />
			</form>
			<form id="AddCourse" action="#" method="POST">
				<h2>Add A Course</h2>
				<input type="hidden" name="form" value="AddCourse" />
				<div class="input_block">
					<label for="AddCourse_Subj">Subj</label>
					<input id="AddCourse_Subj" type="text" name="AddCourse_Subj" required />
				</div>
				<div class="input_block">
					<label for="AddCourse_Crse">Crse</label>
					<input id="AddCourse_Crse" type="text" name="AddCourse_Crse" required />
				</div>
				<div class="input_block">
					<label for="AddCourse_Name">Name</label>
					<input id="AddCourse_Name" type="text" name="AddCourse_Name" />
				</div>
				<input class="input_block" type="submit" value="Add Course" />
			</form>
			<form id="DeleteCourse" action="#" method="POST">
				<h2>Delete A Course</h2>
				<input type="hidden" name="form" value="DeleteCourse" />
				<label for="DeleteCourse_Course">Course</label>
				<select id="DeleteCourse_Course" name="DeleteCourse_Course" required>
				<?php foreach(COURSE::getAllCourses() as $course) : ?>
					<option value="<?php echo $course->getSubj() . '-' . $course->getCrse(); ?>"><?php echo $course->getSubj() . ' ' . $course->getCrse() . ' - ' . $course->getName(); ?></option>
				<?php endforeach; ?>
				</select>
				<input class="input_block" type="submit" value="Delete Course" />
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