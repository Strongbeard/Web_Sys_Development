<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once(SITE_ROOT . '/PHP/User.php');
require_once(SITE_ROOT . '/PHP/Course.php');

$message = '';
$message_class = '';

if( isset($_POST['form']) ) {
		try{
			if($_POST['form'] === 'TA_CODE' && !empty($_POST['ta_code']) && $_SESSION['user']->addTACourseWithTA_Code($_POST['ta_code']) ) {
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
}
?>

<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL ?>/front_end/resources/user.css">

<aside>
	<section id="Message" class="<?php echo $message_class ?>">
			<?php echo $message ?>
		</section>
	<!--checks if you are a student and asks you if you have a TA_CODE-->
	<section>
		<?php if( !$_SESSION['user']->getIsTA() ) : ?>
			<h2>Are You a TA?</h2>
		<?php else : ?>
			<h2>TAing more courses?</h2>
		<?php endif; ?>
		<form id="ta_code" action="#" method="POST">
			<input type="hidden" name="form" value="TA_CODE" />
			<input type='text' name='ta_code' placeholder='Enter your ta_code' />
			<input type='submit' value='Submit' />
		</form>
	</section>

	<!--link to ALAC website-->
	<section >
		<h2>ALAC Hours</h2>
		<a href="http://alac.rpi.edu/update.do?artcenterkey=4">alac.rpi.edu</a>
	</section>
</aside>