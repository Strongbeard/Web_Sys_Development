<?php
require_once('./DB.php');

class User {
	protected $uid = null;
	protected $username = '';
	protected $password = '';
	protected $email = '';
	protected $isAdmin = false;
	protected $isTA = false;
	protected $isTutor = false;
	protected $isStudent = false;
	
	protected function __construct( $username, $password, $email, $isStudent = false, $isTA = false, $isTutor = false, $isAdmin = false ) {
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
//		print_r($db->query("SELECT * FROM users;"));
		
		$instance = new self();
		
		return $instance;
	}
	
	public static function withValues( $username, $password, $email, $isStudent = false, $isTA = false, $isTutor = false, $isAdmin = false ) {
		$instance = new self( $username, $password, $email, $isStudent, $isTA, $isTutor, $isAdmin );
		$db = DB::getInstance();
		$already_exists = $db->prep_bind_execute("SELECT * FROM users WHERE username = :username AND email = :email;",array(
			':username' => $instance->username,
			':email' => $instance->email
		));
		print_r($already_exists);
		if( empty($already_exists) ) {
			$return = $db->prep_bind_execute('INSERT INTO users (username, email, isStudent, isTA, isTutor, isAdmin) VALUES (:username, :email, :isStudent, :isTA, :isTutor, :isAdmin);',array(
				':username' => $instance->username,
				':email' => $instance->email,
				':isStudent' => $instance->isStudent,
				':isTA' => $instance->isTA,
				':isTutor' => $instance->isTutor,
				':isAdmin' => $instance->isAdmin
			));
			print_r($return);
		}
		//print_r($db);
		
		
		return $instance;
	}
	
	private function isSecure() {
		return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
	}
}
?>


<?php
	User::withValues('username2','password','email2');
?>