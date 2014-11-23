<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once(SITE_ROOT . '/PHP/User.php');

session_start();

if(!(isset($_SESSION['user']) && $_SESSION['user'] != null )) {
	header('Location: ' . SITE_LOGIN_PAGE);
}
?>