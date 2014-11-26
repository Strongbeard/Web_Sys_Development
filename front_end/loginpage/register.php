<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Registration Page</title>
		<link rel="stylesheet" type="text/css" href="login.css">
		<style>
			.url {
				width: 290px !important;
			}

		</style>
	</head>

	<body>
		<div id="centre">
			<h1><img src="<?php echo SITE_URL; ?>/front_end/loginpage/images/johnny'sapple.png" height="38px" width="38px"> TA Scheduler</h1>
			<section id="formBox_password">
				<h2>Registration</h2>
				<p>Registration is easy! Enter your information to create a TA Scheduler account. Then select submit.</p>
				<section id="login_error"></section>

				<form class="loginForm" > 
					<label>
						<p style="padding-top: 26px;">Name:<p>
						<input type="text" name="name" class="url" id="name" placeholder="Enter first name and last name"/>
					</label>
					<label>
						<p>Email: <p>
						<input type="text" name="email" class="url" id="email" placeholder="E-mail Address"/>
						<img id="url_user" src="<?php echo SITE_URL; ?>/front_end/loginpage/images/mailicon.png" alt="">
					</label>
					<label>
						Password:
						<input type="password" name="password" class="url" id="password" placeholder="Password must contain at least 9 characters">
						<img id="url_password" src="<?php echo SITE_URL; ?>/front_end/loginpage/images/passicon.png" alt="">

					</label>
					<label>
						Confirm Password:
						<input type="password" name="confirm_password" class="url" id="confirm_password" placeholder="Confirm password">
						<img id="url_password" src="<?php echo SITE_URL; ?>/front_end/loginpage/images/passicon.png" alt="">

					</label>
					<section id="chkbox">
						Are you a:
						<input type="checkbox" id="isStudent" value="student"> <span class="checkbox">Student</span>
  						<input type="checkbox" id="isTA" value="TA"><span class="checkbox">TA</span>
  						<input type="checkbox" id="isTutor" value="tutor"><span class="checkbox">Tutor</span>
  					</section>
				

					<div id="submit">
						<input type="image" src="<?php echo SITE_URL; ?>/front_end/loginpage/images/forgot.png" id="forgot" value="Sign In">
					</div>
					
					<div id="links_right"><a href="login.php" id="back_to_sign"> back to Sign in Page</a></div>


				</form>
			</section>


			
			
		</div>

		<script src="../resources/jquery-1.11.1.min.js"></script>
		<script type="text/javascript">
			//move to seperate js later
			$('.loginForm').submit(function (event) {
				event.preventDefault();
				console.log("logging in ....");

				var error = false;
				var name = $('#name').val();
				var first_name;
				var last_name;
				var email = $('#email').val();
				var password = $('#password').val();
				var confirm_password = $('#confirm_password').val();
				var isTA = $('#isTA').is(':checked');
				var isStudent = $('#isStudent').is(':checked');
				var isTutor = $('#isTutor').is(':checked');
				console.log ('checkboxes ' + isStudent + isTA + isTutor);

				$('input').removeClass('error');
				$('#login_error').empty();

				if (!name || !email || !password || (isTA || isStudent || isTutor) === false) {
					//highlight fields that aren't completed
					if (!name) {
						$('#name').addClass('error');
					}
					if (!email) {
						$('#email').addClass('error');
					}
					if (!password) {
						$('#password').addClass('error');
					}
					if (!confirm_password) {
						$('#confirm_password').addClass('error');
					}
					if ((isTA || isStudent || isTutor)===false) {
						$('#chkbox').addClass('error');
						$('#login_error').html("Please select whether you are a student, TA, or tutor");
					}
					$('#login_error').html("Field(s) blank");
					error = true;
				}

				//check if first and last name are entered. Takes precedence over unmatching passwords.
				var names_ = name.split(" ");
				if (names_.length == 2 && error == false && names_[1] === "") { //checks for string followed by space
					$('#login_error').html("Please enter first name and last name only, seperated by a space");
					$('#name').addClass('error');
					error = true;
				}
				else if (names_.length !== 2 && error == false) { //checks if length is 2
					$('#login_error').html("Please enter first name and last name only, seperated by a space");
					$('#name').addClass('error');
					error = true;
				}
				else {
					first_name = names_[0];
					last_name = names_[1];
				}
				//console.log(names_.length + "-" + names_[0] + "-" + names_[1] + "-"); //debugging


				//check for matching passwords. If there are empty fields, empty fields error takes precedence
				if (password !== confirm_password && error == false) {
					$('#login_error').html("Passwords do not match!");
					$('#password').addClass('error');
					$('#confirm_password').addClass('error');
					error = true;
				}


				if (error === false) {

					$.ajax({
		                url: "validate_login.php",
		                data: { 'register': '1', 'firstName': first_name, 'lastName': last_name, 'email': email, 'password': password, 'isTA': isTA, 'isStudent':isStudent, 'isTutor' : isTutor},
		                method: 'POST',
		                success: function (data) {
		                    data = $.trim(data);

		                    console.log("data: " + data);

		                    /*
		                      data is codes sent by validate_login.php
		                      2 is success
		                      1 is login error - email already exists
		                      0 is login error - password too short

		                    */
							if (data === "2") { 
		                    	console.log("login successful")
		                        document.location.href = '../student.php';
		                    }
		                    if (data === "1") { 
		                         $('#login_error').html("email already exists in system or not an rpi.edu email");
		                    }
		                    if (data === "0") {
		                    	$('#login_error').html("Password must be at least 9 characters");
		                    }

		                },
		                error: function () {
		                    console.log("error");
		                }
		            });

				}
			});
		</script>
	</body>
</html>