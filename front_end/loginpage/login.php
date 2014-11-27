<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(SITE_ROOT . '../PHP/User.php');
require_once(SITE_ROOT . '../PHP/Course.php');

session_start();
if( isset($_SESSION) && isset($_SESSION['user']) ) {	
	$t = $_SESSION['user']->getIsAdmin();
	if ($t == true) {
		header( 'Location: ' . SITE_URL . '/front_end/Admin.php' ) ;
	}

	$t = $_SESSION['user']->getIsTA();
	if ($t == true) {
		header( 'Location: ' . SITE_URL . '/front_end/TA.php' ) ;
	}

	$t = $_SESSION['user']->getIsStudent();
	if ($t == true) {
		header( 'Location: ' . SITE_URL . '/front_end/student.php') ;
	}
}

?>

<!DOCTYPE html>
<html>
	<head>
		<title>Sign In Page</title>
		<link rel="stylesheet" type="text/css" href="login.css">
	</head>

	<body>
		<div id="centre">
			<h1><img src="<?php echo SITE_URL; ?>/front_end/loginpage/images/johnny'sapple.png" height="38px" width="38px"> TA Scheduler</h1>
			<section id="formBox">
				<h2>Sign In</h2>

				<section id="login_error"></section>

				<form class="loginForm"> 
					<label>
						<p>Email: <a href="forgot_username.html" id="forgotUser"></a><p>
						<input type="text" name="username" class="url" id="email" placeholder="E-mail Address"/>
						<img id="url_user" src="<?php echo SITE_URL; ?>/front_end/loginpage/images/mailicon.png" alt="">
					</label>
					<label>
						Password: <a href="forgot_password.html" id="forgotPassword">Forgot Password?</a>
						<input type="password" name="password" class="url" id="pass" placeholder="Password">
						<img id="url_password" src="<?php echo SITE_URL; ?>/front_end/loginpage/images/passicon.png" alt="">

					</label>
					<!--just to have two options for the login button-->
					<!--<a href="#" id="loginBtn">Login</a> -->
					
					<!--this needs help with the link whenever I change the type to image it will not let me reference
					to another page but if I change the type to link, it will let me reference to another page-->
					<!--<a href="http://www.google.com"><input type="image" src="./images/submit_hover.png" ></a>-->

					<div id="submit">
						<input type="image" src="<?php echo SITE_URL; ?>/front_end/loginpage/images/submit_hover.png" id="submit1" value="Sign In">
						<input type="image" src="<?php echo SITE_URL; ?>/front_end/loginpage/images/submit.png" id="submit2" value="Sign In">
					</div>
					
					<div id="links_right"><a href="register.php">Not a Member Yet?</a></div>


				</form>
			</section>
			
		</div>

		<script src="http://code.jquery.com/jquery-1.11.1.js"></script>
		<script type="text/javascript">
			//move to seperate js later
			$('.loginForm').submit(function (event) {
				event.preventDefault();
				console.log("logging in...");
				var user = $('#email').val();
				var pass = $('#pass').val();

				$('input').removeClass('error');
				$('#login_error').empty();

				if (!user || !pass) {
					//highlight fields that aren't completed
					if (!user) {
						$('#email').addClass('error');
					}
					if (!pass) {
						$('#pass').addClass('error');
					}
					$('#login_error').html("Field(s) blank");
					console.log("error");
				}
				else {

					$.ajax({
		                url: "<?php echo SITE_URL; ?>/front_end/loginpage/validate_login.php",
		                data: { 'login': '1', 'user': user, 'pass': pass },
		                method: 'POST',
		                success: function (data) {
		                    data = $.trim(data);

		                    console.log("data: " + data);

		                    /*
		                      data is codes sent by validate_login.php
		                      1 is success
		                      0 is login error
		                    */
		                    if (data === "1") { 
		                    	console.log("login successful")
		                        document.location.href = '<?php echo SITE_URL; ?>/front_end/student.php';
		                    }
		                    if (data === "0") {
		                    	console.log("can't log in");
		                        $('#login_error').html("User name and/or password invalid");
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