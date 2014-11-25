<?php
function curPageName() {
	return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
}

if( !isset($_SESSION) ) {
	session_start();
} 
?>

<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL ?>/front_end/resources/header.css">
<header>
	<h1>
		<a href="">
			<img src="./resources/johnny'sapple.png" height="38px" width="38px"> TA Scheduler
		</a>
	</h1>
	<nav>
		<ul>
			<?php if( $_SESSION['user']->getIsAdmin() ) : ?>
			<li><a href="admin.php" <?php if( curPageName() === 'admin.php' ) : ?>class="current" <?php endif; ?>>Admin</a></li>
			<?php endif; ?>
			<?php if( $_SESSION['user']->getIsStudent() ) : ?>
			<li><a href="student.php" <?php if( curPageName() === 'student.php' ) : ?>class="current" <?php endif; ?>>Student</a></li>
			<?php endif; ?>
			<?php if( $_SESSION['user']->getIsTA() ) : ?>
			<li><a href="ta.php" <?php if( curPageName() === 'ta.php' ) : ?> class="current" <?php endif; ?>>TA<a></li>
			<?php endif; ?>
			<?php if( $_SESSION['user']->getIsStudent() ) : ?>
			<li><a href="search_add.php">Search/Add TA</a></li>
			<?php endif; ?>
			<li><a href="_profile.php">Profile</a></li>
			<li><a href="logout.php">Logout</a></li>
			<li><a href=""></a></li>
		</ul>
	</nav>
</header>