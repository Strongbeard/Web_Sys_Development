<?php
$root=pathinfo($_SERVER['SCRIPT_FILENAME']);
define ('BASE_FOLDER', basename($root['dirname']));
define ('SITE_ROOT',    realpath(dirname(__FILE__)));
if( !empty($_SERVER['HTTPS']) ) {
	define ('SITE_URL',    'https://'.$_SERVER['HTTP_HOST']);
}
else {
	define ('SITE_URL',    'http://'.$_SERVER['HTTP_HOST']);
}
?>