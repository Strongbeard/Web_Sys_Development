<?php
require_once('./DB.php');

class User {
	// --- VARIABLES ---
	
	protected $uid = null;
	protected $username = '';
	protected $password = '';
	protected $email = '';
	protected $isAdmin = false;
	protected $isTA = false;
	protected $isTutor = false;
	protected $isStudent = false;
	
	
	// --- CONSTRUCTORS ---
	
	private function __construct( $uid, $username, $password, $email, $isStudent = false, $isTA = false, $isTutor = false, $isAdmin = false ) {
		/*if( empty($username) ) {
			throw new Exception('Empty username');
		}
		if( empty($password) ) {
			throw new Exception('Empty password');
		}
		if( empty($email) ) {
			throw new Exception('Empty email');
		}*/
		
		$this->uid = $uid;
		$this->password = password_hash( $password, PASSWORD_DEFAULT );
		if( !($this->setUsername($username) &&
		$this->setEmail($email) &&
		$this->setIsAdmin($isAdmin) &&
		$this->setIsTA($isTA) &&
		$this->setIsTutor($isTutor) &&
		$this->setIsStudent($isStudent)) ) {
			throw new Exception('User: bad input');
		}
	}
	
	public static function fromDatabase( $unique_column, $value ) {
		// Check that input is valid
		$unique_column = strtolower( $unique_column );
		if( $unique_column != 'userid' && $unique_column != 'username' && $unique_column != 'email' && $unique_column != 'rin' ) {
			throw new Exception('Column to select user from does not exist.');
		}
		
		// Extract user information from database
		$db = DB::getInstance();
		$usersRows = $db->prep_execute('SELECT * FROM users WHERE ' . $unique_column . ' = :value', array(
			':value' => $value
		));
		if( empty($usersRows) ) {
			throw new Exception('User: User does not exist in database.');
		}
		$passwordRows = $db->prep_execute('SELECT * FROM passwords WHERE userid = :userid;', array(
			':userid' => $usersRows[0]['userId']
		));
		if( empty($passwordRows) ) {
			throw new Exception('User: userId not found in password database.');
		}
		
		$instance = new self( $usersRows[0]['userId'], $usersRows[0]['username'], $passwordRows[0]['password'] , $usersRows[0]['email'], $usersRows[0]['isStudent'], $usersRows[0]['isTA'], $usersRows[0]['isTutor'], $usersRows[0]['isAdmin'] );
		return $instance;
	}
	
	public static function withValues( $username, $password, $email, $isStudent = false, $isTA = false, $isTutor = false, $isAdmin = false ) {
		$instance = new self(null, $username, $password, $email, $isStudent, $isTA, $isTutor, $isAdmin );
		return $instance;
	}
	
	
	// --- FUNCTIONS ---
	
	// Returns whether the password matches the password hash when hashed.
	public function verify_password( $password ) {
		return password_verify( $password, $this->password );
	}
	
	// Inserts or updates the user in the database
	public function store() {
		$db = DB::getInstance();
		if( $this->uid === null ) {
			$db->prep_execute('INSERT INTO users (username, email, isStudent, isTA, isTutor, isAdmin) VALUES (:username, :email, :isStudent, :isTA, :isTutor, :isAdmin) ON DUPLICATE KEY UPDATE username = VALUES(username), email = VALUES(email), isStudent = VALUES(isStudent), isTA = VALUES(isTA), isTutor = VALUES(isTutor), isAdmin = VALUES(isAdmin);',array(
				':username' => $this->username,
				':email' => $this->email,
				':isStudent' => ($this->isStudent) ? 1 : 0,
				':isTA' => ($this->isTA) ? 1 : 0,
				':isTutor' => ($this->isTutor) ? 1 : 0,
				':isAdmin' => ($this->isAdmin) ? 1 : 0
			));
			
			if( $this->uid === null ) {
				$this->uid = $db->prep_execute('SELECT userId FROM users WHERE username = :username;', array(
					':username' => $this->username
				))[0]['userId'];
				var_dump( $this->uid );
			}
		}
		else {
			$db->prep_execute('UPDATE users SET username = :username, email = :email, isStudent = :isStudent, isTa = :isTA, isTutor = :isTutor, isAdmin = :isAdmin WHERE userid = :userid;',array(
				':userid' => $this->uid,
				':username' => $this->username,
				':email' => $this->email,
				':isStudent' => ($this->isStudent) ? 1 : 0,
				':isTA' => ($this->isTA) ? 1 : 0,
				':isTutor' => ($this->isTutor) ? 1 : 0,
				':isAdmin' => ($this->isAdmin) ? 1 : 0
			));
		}
		
		$db->prep_execute('INSERT INTO passwords (userid, password) VALUES (:userid, :password) ON DUPLICATE KEY UPDATE password = VALUES(password);',array(
			':userid' => $this->uid,
			':password' => $this->password
		));
	}
	
	// GET FUNCTIONS
	
	public function getEmail() {
		return $this->email;
	}
	
	public function getIsAdmin() {
		return $this->isAdmin;
	}
	
	public function getIsStudent() {
		return $this->isStudent;
	}
	
	public function getIsTA() {
		return $this->isTA;
	}
	
	public function getIsTutor() {
		return $this->isTutor;
	}
	
	public function getUserId() {
		return $this->uid;
	}
	
	public function getUsername() {
		return $this->username;
	}
	
	
	// SET FUNCTIONS
	
	public function setEmail( $email ) {
		if( is_string($email) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) ) {
			$this->email = $email;
			return true;
		}
		return false;
	}
	
	public function setIsAdmin( $isAdmin ) {
		if( filter_var( $isAdmin, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) !== null ) {
			$this->isAdmin = $isAdmin;
			return true;
		}
		return false;
	}
	
	public function setIsTA( $isTA ) {
		if( filter_var( $isTA, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) !== null ) {
			$this->isTA = $isTA;
			return true;
		}
		return false;
	}
	
	public function setIsTutor( $isTutor ) {
		if( filter_var( $isTutor, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) !== null ) {
			$this->isTutor = $isTutor;
			return true;
		}
		return false;
	}
	
	public function setIsStudent( $isStudent ) {
		if( filter_var( $isStudent, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) !== null ) {
			$this->isStudent = $isStudent;
			return true;
		}
		return false;
	}
	
	public function setUsername( $username ) {
		$username = filter_var($username, FILTER_VALIDATE_REGEXP, array(
			'options'=>array(
				'regexp' => '/^[A-Za-z0-9_]+$/'
			)
		));
		if( is_string($username) && !empty($username) ) {
			$this->username = $username;
			return true;
		}
		return false;
	}
	
/*	public static function withValues( $username, $password, $email, $isStudent = false, $isTA = false, $isTutor = false, $isAdmin = false ) {
		$instance = new self( $username, $password, $email, $isStudent, $isTA, $isTutor, $isAdmin );
		$db = DB::getInstance();
		$already_exists = $db->prep_execute("SELECT * FROM users WHERE username = :username AND email = :email;",array(
			':username' => $instance->username,
			':email' => $instance->email
		));


		if( empty($already_exists) ) {
			$db->prep_execute('INSERT INTO users (username, email, isStudent, isTA, isTutor, isAdmin) VALUES (:username, :email, :isStudent, :isTA, :isTutor, :isAdmin);',array(
				':username' => $instance->username,
				':email' => $instance->email,
				':isStudent' => $instance->isStudent,
				':isTA' => $instance->isTA,
				':isTutor' => $instance->isTutor,
				':isAdmin' => $instance->isAdmin
			));
		}
		else {
			throw new Exception('USER ALREADY EXISTS IN DATABASE');
		}
		
		
		return $instance;
	}
	
	private function isSecure() {
		return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
	}*/
}
?>


<?php
	//User::withValues('username5','password','email5');
/*	echo password_hash('password',PASSWORD_DEFAULT);
	echo '<br />';
	$user = User::fromDatabase('userid',12);
	echo $user->setIsStudent(false);
	echo '<br />';
	echo $user->getIsStudent();
	echo 'setUsername return: ' . $user->setUsername('bla');
	print_r( $user2 = user::withValues('uname','pass','email@gmail.com') );*/
	//$user2 = user::withValues('mahonk3','passs','mahonk3@rpi.edu');
	$user2 = user::fromDatabase('userid',12);
	$user2->setIsAdmin(false);
	$user2->store();
?>