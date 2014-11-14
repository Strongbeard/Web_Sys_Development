<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once(SITE_ROOT . '\PHP\DB.php');

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
	
	// Private constructor helper function. Called by fromDatabase and
	// withValues
	private function __construct( $uid, $username, $password, $email, $isStudent = false, $isTA = false, $isTutor = false, $isAdmin = false ) {
		$this->uid = $uid;
		if( !($this->setUsername($username) &&
		$this->setPassword($password) &&
		$this->setEmail($email) &&
		$this->setIsAdmin($isAdmin) &&
		$this->setIsTA($isTA) &&
		$this->setIsTutor($isTutor) &&
		$this->setIsStudent($isStudent)) ) {
			throw new Exception('User: bad input');
		}
	}
	
	// Constructor loads user data from the database using a unique key
	public static function fromDatabase( $unique_column, $value ) {
		// Check that input is valid
		if( !USER::validUniqueColumn($unique_column) ) {
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
	
	// Constructor builds a new user from parameters
	public static function withValues( $username, $password, $email, $isStudent = false, $isTA = false, $isTutor = false, $isAdmin = false ) {
		$instance = new self(null, $username, $password, $email, $isStudent, $isTA, $isTutor, $isAdmin );
		return $instance;
	}
	
	
	// --- FUNCTIONS ---
	
	// Removes user with unique id from database
	public static function deleteFromDB($unique_column, $value) {
		if( !USER::validUniqueColumn($unique_column) ) {
			throw new Exception('Column to select user from does not exist.');
		}
		
		$db = DB::getInstance();
		$result = $db->prep_execute('DELETE FROM users WHERE ' . $unique_column . ' = :value;', array(
			':value' => $value
		));
		
		if($result) {
			return true;
		}
		return false;
	}
	
	// Returns whether the password matches the password hash when hashed.
	public function verify_password( $password ) {
		return password_verify( $password, $this->password );
	}
	
	// Creates a user session after verifying their password. User class is stored
	// in $_SESSION['user']. Returns true on successful login, otherwise false.
	public function login( $password ) {
		if( $this->uid !== null && $this->verify_password( $password ) ) {
			session_start();
			$_SESSION['user'] = $this;
			return true;
		}
		return false;
	}
	
	// Destroys the current user session
	public function logout() {
		session_destroy();
	}
	
	// Inserts the user in the database. If the user exists and the update flag
	// is set, it updates the user if the information is different.
	public function store($update = False) {
		$db = DB::getInstance();
		
		// Craft prepared statement strings to replace or not replace user info
		// if the user already exists in the database
		$user_string = '';
		$password_string = '';
		if( !$update ) {
			$user_string = 'INSERT IGNORE INTO users (username, email, isStudent, isTA, isTutor, isAdmin) VALUES (:username, :email, :isStudent, :isTA, :isTutor, :isAdmin);';
			$password_string = 'INSERT IGNORE INTO passwords (userid,password) VALUES (:userid,:password);';
		}
		else{
			$user_string .= 'INSERT INTO users (username, email, isStudent, isTA, isTutor, isAdmin) VALUES (:username, :email, :isStudent, :isTA, :isTutor, :isAdmin) ON DUPLICATE KEY UPDATE username = VALUES(username), email = VALUES(email), isStudent = VALUES(isStudent), isTA = VALUES(isTA), isTutor = VALUES(isTutor), isAdmin = VALUES(isAdmin)';
			$password_string .= 'INSERT INTO passwords (userid,password) VALUES (:userid,:password) ON DUPLICATE KEY UPDATE password = VALUES(password)';
		}
		
		$user_result = $db->prep_execute( $user_string . ';', array(
			':username' => $this->username,
			':email' => $this->email,
			':isStudent' => ($this->isStudent) ? 1 : 0,
			':isTA' => ($this->isTA) ? 1 : 0,
			':isTutor' => ($this->isTutor) ? 1 : 0,
			':isAdmin' => ($this->isAdmin) ? 1 : 0
		));
		
		if( !$user_result ) {
			return false;
		}
		
		$this->updateUID();
		$password_result = $db->prep_execute($password_string . ';',array(
			':userid' => $this->uid,
			':password' => $this->password
		));
			
		if( !$password_result ) {
			return false;
		}
		
		return true;
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
	
	public function setPassword( $password ) {
		if( strlen($password) > 8 ) {
			$this->password = password_hash( $password, PASSWORD_DEFAULT );
			return true;
		}
		return false;
	}
	
	// --- PRIVATE HELPER FUNCTIONS ---
	
	// Checks if unique column is a valid value
	private static function validUniqueColumn( $unique_column ) {
		$unique_column = strtolower( $unique_column );
		if( $unique_column != 'userid' && $unique_column != 'username' && $unique_column != 'email' && $unique_column != 'rin' ) {
			return false;
		}
		return true;
	}
	
	// Fetched the userID from the database & updates the class
	private function updateUID() {
		$db = DB::getInstance();
		$result = $db->prep_execute( 'SELECT userid FROM users WHERE username = :username;', array(
			':username' => $this->username
		));
		$this->uid = $result[0]['userid'];
	}
}
?>