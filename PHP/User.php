<?php
class User {
	protected uid = null;
	protected username = '';
	protected password = '';
	protected email = '';
	protected isAdmin = false;
	protected isTA = false;
	protected isTutor = false;
	protected isStudent = false;
	
	protected function __construct( $username, $password, $email, $isAdmin = false, $isTA = false, $isTutor = false, $isStudent = false ) {
		if( empty($username) ) {
			throw new Exception('Empty username');
		}
		if( empty($password) ) {
			throw new Exception('Empty password');
		}
		if( empty($email) ) {
			throw new Exception('Empty email');
		}
		
		
		$this->username = $username;
		$this->password = $password;
		$this->email = $email;
		$this->isAdmin = $isAdmin;
		$this->isTA = $isTA;
		$this->isTutor = $isTutor;
		$this->isStudent = $isStudent;
	}
	
	public static function withUID() {
		
	}
	
	private function isSecure() {
		return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
	}
}



/*if( $_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['username']) && !empty($_POST['password']) ) {
	
}*/
?>