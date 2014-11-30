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
						<hgroup>
							<h2>Search Your TAs </h2>
							<form id="search_ta_form" method="GET">
								<p>Search by name:</p>
								<input type="search" id="search_ta" name="search_ta" placeholder="Enter name of TA"/>
								<input type="submit" value="Search"/>
							</form>
							<hr>						
							<form id="search_class_form" method="GET">
								<p>Search by Class:</p>
								<input type="search" id="search_class" name="search_class" placeholder="Enter name of class"/>
								<input type="submit" value="Search"/>
							
								<p>Select a school:</p>
								<select name="search_school" id="search_school">
									<option disabled selected>School</option>
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
			<form id="results">
				<span></span>
				<table>
					<thead>
						
					</thead>
					<tbody>
					</tbody>
				</table>
			</form>
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
			
			<footer>
				© 2014 TA Hunters
			</footer>
		</div><!-- .wrapper -->	

		<script src="resources/jquery-1.11.1.min.js"></script>
		<script>
			$('#search_ta_form').submit(function (event) {
				event.preventDefault();
				$('#results > span').empty();
				$('#results > table').empty();
				//$('#results > div').empty();
				var ta_name = $('#search_ta').val();

				$('input').removeClass('error');

				if (!ta_name) {
					$('#search_ta').addClass('error');
				}
				else {
					
					$.ajax({
		                url: "./find.php",
		                data: {'ta_name': ta_name},
		                method: 'GET',
		                success: function (data) {
		                    data = $.trim(data);
		                   	//console.log("data: " + data.length);
		                   	//console.log(data);
		                   	if (data.length > 0) {
		                   		$('#results > table').append("<thead><tr><th>First Name</th><th>Last Name</th><th>Email</th><th>Subject</th><th>Course</th><th>Name</th><th>Add?</th></tr></thead>");
		                   		$('#results > table').append("<tbody>" + data + "</tbody>");
		                   		//$('#results > div').append("<input type='submit' value='Add'>");
		                   	}
		                   	else {
		                   		$('#results > span').html("No results");
		                   	}
		          
		                 

		                },
		                error: function () {
		                    console.log("error");
		                }
		            });
				}	
			});


			$('#search_class_form').submit(function (event) {
				event.preventDefault();
				$('#results > span').empty();
				$('#results > table').empty();
				//$('#results > div').empty();
				var class_name = $('#search_class').val();
				var school_name = $("#search_school option:selected" ).val();
				//console.log(school_name);
				$('input').removeClass('error');

				if (!class_name) {
					$('#search_class').addClass('error');
				}
				else {
					//ignore if user doesn't select school - defaults to first option
					if (school_name === 'school') {
						
					}
					else {
						switch(school_name) {
							case 'Architecture':
								school_name = '';
								break;
							case 'Business':
								school_name = '';
								break;
							case 'Engineering':
								school_name = '';
								break;
							case 'HASS':
								school_name = '';
								break;
							case 'ITWS':
								school_name = '';
								break;
							case 'Science':
								school_name = '';
								break;
						}
					}
					$.ajax({
		                url: "./find.php",
		                data: {'class_name': class_name, 'school_name': school_name},
		                method: 'GET',
		                success: function (data) {
		                    data = $.trim(data);
		                   	//console.log("data: " + data.length);
		                   	//console.log(data);
		                   	if (data.length > 0) {
		                   		$('#results > table').append("<thead><tr><th>Subject</th><th>Course #</th><th>Name</th><th>TA Name</th><th>TA Email</th><th>Add?</th></tr></thead>");
		                   		$('#results > table').append("<tbody>" + data + "</tbody>");
		                   		//$('#results > div').append("<input type='submit' value='Add'>");
		                   	}
		                   	else {
		                   		$('#results > span').html("No results");
		                   	}
		          
		                 

		                },
		                error: function () {
		                    console.log("error");
		                }
		            });
				}
			
			});

			$('#results').submit(function (event) {
				event.preventDefault();
				var action = $('#results option:selected', this).val();
				var checked_vals = [];
    			$('table input:checkbox:checked').each(function() {
    				var id = $(this).val();
    				//console.log(id);
    				checked_vals.push($(this).val());
    			});
    			if (checked_vals.length > 0) {
    				$.ajax({
		                url: "./find.php",
		                data: {'add' : 1, 'checked_vals': checked_vals },
		                method: 'GET',
		                success: function (data) {
		                    data = $.trim(data);
		                   	//console.log("data: " + data.length);
		                   //console.log("data " + data);
		                   	if (data === "1") {
		                   		$('#results > span').html("Added successfully");
		                   	}
		                   	if (data === "0") {
		                   		$('#results > span').html("Course already added to student");
		               
		                   	}
		                },
		                error: function () {
		                    console.log("error");
		                }
		            });
    			}
    			else {
    				//no results checked/
    			}
    			
			});
		</script>
</body></html>