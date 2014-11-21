<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once(SITE_ROOT . '\PHP\DB.php');

class User {
	// --- VARIABLES ---
	
	protected $uid = null;
	protected $password = '';
	protected $email = '';
	protected $isAdmin = false;
	protected $isTA = false;
	protected $isTutor = false;
	protected $isStudent = false;
	protected $firstName = null;
	protected $lastName = null;
	
	
	// --- CONSTRUCTORS ---
	
	// Private constructor helper function. Called by fromDatabase and
	// withValues
	private function __construct( $uid, $email, $isStudent = false, $isTA = false, $isTutor = false, $isAdmin = false, $firstName = null, $lastName = null ) {
		$this->uid = $uid;
		if( !($this->setEmail($email) &&
		$this->setIsAdmin($isAdmin) &&
		$this->setIsTA($isTA) &&
		$this->setIsTutor($isTutor) &&
		$this->setIsStudent($isStudent) &&
		$this->setFirstName($firstName) &&
		$this->setLastName($lastName)) ) {
			return null;
		}
	}
	
	// Constructor loads user data from the database using a unique key
	// Returns null if user not found.
	public static function fromDatabase( $unique_column, $value ) {
		// Check that input is valid
		if( !USER::validUniqueColumn($unique_column) ) {
			return null;
		}
		
		// Extract user information from database
		$db = DB::getInstance();
		$usersRows = $db->prep_execute('SELECT * FROM users WHERE ' . $unique_column . ' = :value', array(
			':value' => $value
		));
		if( empty($usersRows) ) {
			return null;
		}
		$passwordRows = $db->prep_execute('SELECT * FROM passwords WHERE userid = :userid;', array(
			':userid' => $usersRows[0]['userId']
		));
		if( empty($passwordRows) ) {
			return null;
		}
		
		$instance = new self( $usersRows[0]['userId'], $usersRows[0]['email'], (bool)$usersRows[0]['isStudent'], (bool)$usersRows[0]['isTA'], (bool)$usersRows[0]['isTutor'], (bool)$usersRows[0]['isAdmin'], $usersRows[0]['firstName'], $usersRows[0]['lastName'] );
		if( empty($passwordRows[0]['password']) ) {
			return null;
		}
		else {
			$instance->password = $passwordRows[0]['password'];
		}
		return $instance;
	}
	
	// Constructor builds a new user from parameters
	public static function withValues( $email, $password, $isStudent = false, $isTA = false, $isTutor = false, $isAdmin = false, $firstName = null, $lastName = null ) {
		$instance = new self(null, $email, $isStudent, $isTA, $isTutor, $isAdmin, $firstName, $lastName );

							
		if( !$instance->setPassword($password) ) {
			return null;
		}
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
		// Check if session exists before destroying it
		if( isset($_SESSION) && isset($_SESSION['user']) ) {
			// Erase local session data
			$_SESSION = array();
			
			// Remove client's cookie
			if (ini_get("session.use_cookies")) {
				$params = session_get_cookie_params();
				setcookie(session_name(), '', time() - 42000,
					$params["path"], $params["domain"],
					$params["secure"], $params["httponly"]
				);
			}

			// Destroy the session
			session_destroy();
			return true;
		}
		return false;
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
			$user_string = 'INSERT IGNORE INTO users (email, isStudent, isTA, isTutor, isAdmin, firstName, lastName) VALUES (:email, :isStudent, :isTA, :isTutor, :isAdmin, :firstName, :lastName);';
			$password_string = 'INSERT IGNORE INTO passwords (userid,password) VALUES (:userid,:password);';
		}
		else{
			$user_string .= 'INSERT INTO users (email, isStudent, isTA, isTutor, isAdmin, firstName, lastName) VALUES (:email, :isStudent, :isTA, :isTutor, :isAdmin, :firstName, :lastName) ON DUPLICATE KEY UPDATE email = VALUES(email), isStudent = VALUES(isStudent), isTA = VALUES(isTA), isTutor = VALUES(isTutor), isAdmin = VALUES(isAdmin), firstName = VALUES(firstName), lastName = VALUES(lastName)';
			$password_string .= 'INSERT INTO passwords (userid,password) VALUES (:userid,:password) ON DUPLICATE KEY UPDATE password = VALUES(password)';
		}
		
		$user_result = $db->prep_execute( $user_string . ';', array(
			':email' => $this->email,
			':isStudent' => ($this->isStudent) ? 1 : 0,
			':isTA' => ($this->isTA) ? 1 : 0,
			':isTutor' => ($this->isTutor) ? 1 : 0,
			':isAdmin' => ($this->isAdmin) ? 1 : 0,
			':firstName' => $this->firstName,
			':lastName' => $this->lastName
		));
		
		$password_result = 0;
		if( $this->updateUID() ) {
			$password_result = $db->prep_execute($password_string . ';',array(
				':userid' => $this->uid,
				':password' => $this->password
			));
		}
		else {
			return false;
		}
			
		if( $user_result || $password_result ) {
			return true;
		}
		
		return false;
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
	
	public function getFirstName() {
		return $this->firstName;
	}
	
	public function getLastName() {
		return $this->lastName;
	}
	
	public function getStudentCourses() {
		if( $this->isStudent && $this->uid !== null) {
			$db = DB::getInstance();
			return $db->prep_execute('SELECT subj, crse FROM students_courses WHERE userid = :userid', array(
				':userid' => $this->uid
			));
		}
		return false;
	}
	
	public function getStudentTAs() {
		if( $this->isStudent && $this->uid !== null ) {
			$db = DB::getInstance();
			return $db->prep_execute('SELECT u2.email, u2.firstName, u2.lastName, sc.subj, sc.crse FROM users as u1 INNER JOIN students_courses AS sc ON u1.userid = sc.userid INNER JOIN tas_courses AS tc ON sc.subj = tc.subj AND sc.crse = tc.crse INNER JOIN users as u2 ON tc.userId = u2.userId WHERE u1.userid = :userid', array(
				':userid' => $this->uid
			));
		}
		return false;
	}
	
	
	// SET FUNCTIONS
	
	public function addUserCourse( $rel, $subj, $crse ) {
		// Argument Type Error Handling
		if( !is_string($rel) ) {
			throw new InvalidArgumentException('USER::addStudentCourse(string $rel, string $subj, int $crse) => $rel should be one of the following strings: "student", "ta"');
		}
		else {
			$rel = strtolower($rel);
			if( $rel !== 'student' && $rel !== 'ta' ) {
				throw new InvalidArgumentException('USER::addStudentCourse(string $rel, string $subj, int $crse) => $rel should be one of the following strings: "student", "ta"');
			}
		}
		if( !is_string($subj) ) {
			throw new InvalidArgumentException('USER::addStudentCourse(string $rel, string $subj, int $crse) => $subj should be a string.');
		}
		if( !is_int($crse) ) {
			throw new InvalidArgumentException('USER::addStudentCourse(string $rel, string $subj, int $crse) => $crse should be an integer.');
		}
		
		if( ($rel === 'student' && !$this->isStudent) || ($rel === 'ta' && !$this->isTA) ) {
			return false;
		}
	
		if($this->uid !== null ) {
			$db = DB::getInstance();
			try {
				return $db->prep_execute('INSERT INTO ' . $rel . 's_courses (userid, subj, crse) VALUES (:userid, :subj, :crse)', array(
					':userid' => $this->uid,
					':subj' => strtoupper($subj),
					':crse' => $crse
				));
			}
			catch( PDOException $Exception ) {
				return false;
			}
		}
		return false;
	}
	
	public function removeUserCourse( $rel, $subj, $crse ) {
		// Argument Type Error Handling
		if( !is_string($rel) ) {
			throw new InvalidArgumentException('USER::remoteStudentCourse(string $rel, string $subj, int $crse) => $rel should be one of the following strings: "student", "ta"');
		}
		else {
			$rel = strtolower($rel);
			if( $rel !== 'student' && $rel !== 'ta' ) {
				throw new InvalidArgumentException('USER::remoteStudentCourse(string $rel, string $subj, int $crse) => $rel should be one of the following strings: "student", "ta"');
			}
		}
		if( !is_string($subj) ) {
			throw new InvalidArgumentException('USER::remoteStudentCourse(string $rel, string $subj, int $crse) => $subj should be a string.');
		}
		if( !is_int($crse) ) {
			throw new InvalidArgumentException('USER::remoteStudentCourse(string $rel, string $subj, int $crse) => $crse should be an integer.');
		}
	
		if( $this->uid !== null ) {
			$db = DB::getInstance();
			try {
				return $db->prep_execute('DELETE FROM ' . $rel . 's_courses WHERE userid = :userid AND subj = :subj AND crse = :crse', array(
					':userid' => $this->uid,
					':subj' => strtoupper($subj),
					':crse' => $crse
				));
			}
			catch( PDOException $Exception ) {
				return false;
			}
		}
		return false;
	}
	
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
	
	public function setPassword( $password ) {
		if( strlen($password) > 8 ) {
			$this->password = password_hash( $password, PASSWORD_DEFAULT );
			return true;
		}
		return false;
	}
	
	public function setFirstName( $firstname ) {
		if( is_string($firstname) && !empty($firstname) ) {
			$this->firstName = $firstname;
			return true;
		}
		return false;
	}
	
	public function setLastName( $lastname ) {
		if( is_string($lastname) && !empty($lastname) ) {
			$this->lastName = $lastname;
			return true;
		}
		return false;
	}
	
	// --- PRIVATE HELPER FUNCTIONS ---
	
	// Checks if unique column is a valid value
	private static function validUniqueColumn( $unique_column ) {
		$unique_column = strtolower( $unique_column );
		if( $unique_column != 'userid' && $unique_column != 'email' ) {
			return false;
		}
		return true;
	}
	
	// Fetched the userID from the database & updates the class
	private function updateUID() {
		$db = DB::getInstance();
		$result = $db->prep_execute( 'SELECT userid FROM users WHERE email = :email;', array(
			':email' => $this->email
		));
		if( isset($result) && isset($result[0]) && isset($result[0]['userid']) ) {
			$this->uid = $result[0]['userid'];
			return true;
		}
		return false;
	}
}
?>