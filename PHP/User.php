<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once(SITE_ROOT . '\PHP\DB.php');

class User {
	// ############################### VARIABLES ###############################
	
	protected $inDB = false;
	protected $password = '';
	protected $email = '';
	protected $isAdmin = false;
	protected $isTA = false;
	protected $isTutor = false;
	protected $isStudent = false;
	protected $firstName = null;
	protected $lastName = null;
	
	
	// ############################# CONSTRUCTORS ##############################
	
	// Private constructor helper function. Called by fromDatabase and
	// withValues
	private function __construct( $email, $isStudent = false, $isTA = false, $isTutor = false, $isAdmin = false, $firstName = null, $lastName = null, $inDB = false ) {
		// Calls set functions of all variables for invalid argument errors
		if( !($this->setEmail($email) &&
		$this->setIsAdmin($isAdmin) &&
		$this->setIsTA($isTA) &&
		$this->setIsTutor($isTutor) &&
		$this->setIsStudent($isStudent) &&
		$this->setFirstName($firstName) &&
		$this->setLastName($lastName)) ) {
			return null;
		}
		$this->inDB = $inDB;
	}
	
	// Constructor loads user data from the database using a unique key
	// Returns null if user not found.
	public static function fromDatabase( $email ) {
		
		// Get user info from database's user table
		$db = DB::getInstance();
		try {
			$usersRows = $db->prep_execute('SELECT u.email, u.firstName, u.lastName, u.isAdmin, u.isTA, u.isTutor, u.isStudent, p.password FROM users AS u INNER JOIN passwords AS p ON u.email = p.email WHERE u.email = :value', array(
				':value' => $email
			));
		}
		catch( PDOException $e ) {
			return null;
		}
		
		// Return null if no user found
		if( empty($usersRows) ) {
			return null;
		}
		
		// Call private user constructor with database information
		$instance = new self( $usersRows[0]['email'], (bool)$usersRows[0]['isStudent'], (bool)$usersRows[0]['isTA'], (bool)$usersRows[0]['isTutor'], (bool)$usersRows[0]['isAdmin'], $usersRows[0]['firstName'], $usersRows[0]['lastName'], true );
		
		// Set Password
		$instance->password = $usersRows[0]['password'];
		
		// Return new user object
		return $instance;
	}
	
	// Constructor builds a new user from parameters
	public static function withValues( $email, $password, $isStudent = false, $isTA = false, $isTutor = false, $isAdmin = false, $firstName = null, $lastName = null ) {
		// Calls private user constructor with provided arguments
		$instance = new self($email, $isStudent, $isTA, $isTutor, $isAdmin, $firstName, $lastName );

		// Sets password. Returns null on error
		try {
			$instance->setPassword($password); 
		}
		catch (InvalidArgumentException $e) {
			return null;
		}
		
		$db = DB::getInstance();
		try {
			$result = $db->multi_prep_execute(['INSERT INTO users (email, isAdmin, isTA, isTutor, isStudent, firstName, lastName) VALUES (:email, :isAdmin, :isTA, :isTutor, :isStudent, :firstName, :lastName);', 'INSERT INTO passwords (email, password) VALUES (:email, :password);'], array(
				[
					':email' => $instance->getEmail(),
					':isAdmin' => $instance->getIsAdmin(),
					':isStudent' => $instance->getIsStudent(),
					':isTA' => $instance->getIsTA(),
					':isTutor' => $instance->getIsTutor(),
					':firstName' => $instance->getFirstName(),
					':lastName' => $instance->getLastName()
				],
				[
					':email' => $instance->getEmail(),
					':password' => $instance->password
				]
			));
			if( $result ) {
				$instance->setInDB(true);
			}
		}
		catch( PDOException $e ) {}
		
		// Returns new user object
		return $instance;
	}
	
	
	// ########################## ACCESSOR FUNCTIONS ###########################
	
	// Returns whether the password matches the password hash when hashed.
	public function verify_password( $password ) {
		return password_verify( $password, $this->password );
	}
	
	// Creates a user session after verifying their password. User class is stored
	// in $_SESSION['user']. Returns true on successful login, otherwise false.
	public function login( $password ) {
		if( $this->inDB && $this->verify_password( $password ) ) {
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
		
		$pstmt = 'INSERT INTO users (email, isStudent, isTA, isTutor, isAdmin, firstName, lastName) VALUES (:email, :isStudent, :isTA, :isTutor, :isAdmin, :firstName, :lastName)';
		if( $update ) {
			$pstmt .= ' ON DUPLICATE KEY UPDATE email = VALUES(email), isStudent = VALUES(isStudent), isTA = VALUES(isTA), isTutor = VALUES(isTutor), isAdmin = VALUES(isAdmin), firstName = VALUES(firstName), lastName = VALUES(lastName)';
		}
		$pstmt .= ';';
		$pstmt_array[] = $pstmt;
		$pstmt = 'INSERT INTO passwords (email,password) VALUES (:email,:password)';
		if( $update ) {
			$pstmt .= ' ON DUPLICATE KEY UPDATE password = VALUES(password)';
		}
		$pstmt .= ';';
		$pstmt_array[] = $pstmt;
		
		try{
			$results = $db->multi_prep_execute( $pstmt_array, array(
				[
					':email' => $this->email,
					':isStudent' => ($this->isStudent) ? 1 : 0,
					':isTA' => ($this->isTA) ? 1 : 0,
					':isTutor' => ($this->isTutor) ? 1 : 0,
					':isAdmin' => ($this->isAdmin) ? 1 : 0,
					':firstName' => $this->firstName,
					':lastName' => $this->lastName
				],
				[
					':email' => $this->email,
					':password' => $this->password
				]
			));
		}
		catch( PDOException $e ) {
			return false;
		}
		
		return $results;
	}
	
	// GET FUNCTIONS
	
	// Return email
	public function getEmail() {
		return $this->email;
	}
	
	public function getInDB() {
		return $this->inDB;
	}
	
	// Return admin flag
	public function getIsAdmin() {
		return $this->isAdmin;
	}
	
	// Return student flag
	public function getIsStudent() {
		return $this->isStudent;
	}
	
	// Return TA flag
	public function getIsTA() {
		return $this->isTA;
	}
	
	// Return Tutor flag
	public function getIsTutor() {
		return $this->isTutor;
	}
	
	// Return User's first name
	public function getFirstName() {
		return $this->firstName;
	}
	
	// Return User's last name
	public function getLastName() {
		return $this->lastName;
	}
	
	// Return an array with database course rows that the student is enrolled in
	public function getStudentCourses() {
		require_once(SITE_ROOT . '\PHP\Course.php');
		$courses = array();
		if( $this->isStudent && $this->inDB ) {
			$db = DB::getInstance();
			$result = $db->prep_execute('SELECT subj, crse FROM students_courses WHERE email = :email', array(
				':email' => $this->email
			));
			
			foreach($result as $row) {
				$courses[] = COURSE::fromDatabase( $row['subj'], intval($row['crse']) );
			}
		}
		return $courses;
	}
	
	// Return an array with database TAs that are mapped to student's courses
	public function getStudentTAs() {
		if( $this->isStudent && $this->inDB ) {
			$db = DB::getInstance();
			return $db->prep_execute('SELECT u2.email, u2.firstName, u2.lastName, sc.subj, sc.crse FROM users as u1 INNER JOIN students_courses AS sc ON u1.email = sc.email INNER JOIN tas_courses AS tc ON sc.subj = tc.subj AND sc.crse = tc.crse INNER JOIN users as u2 ON tc.email = u2.email WHERE u1.email = :email', array(
				':email' => $this->email
			));
		}
		return false;
	}
	
	
	// ########################## MODIFIER FUNCTIONS ###########################
	
	// Creates a mapping in the database table students_courses or tas_courses
	// depending on $rel's value. Maps students or tas to courses.
	public function addUserCourse( $rel, $subj, $crse ) {
		// --- Argument Type Error Handling ---
		
		// $rel is a string
		if( !is_string($rel) ) {
			throw new InvalidArgumentException('USER::addStudentCourse(string $rel, string $subj, int $crse) => $rel should be one of the following strings: "student", "ta"');
		}
		else { // $rel is 'student' or 'ta'
			$rel = strtolower($rel);
			if( $rel !== 'student' && $rel !== 'ta' ) {
				throw new InvalidArgumentException('USER::addStudentCourse(string $rel, string $subj, int $crse) => $rel should be one of the following strings: "student", "ta"');
			}
		}
		// $subj is a string
		if( !is_string($subj) ) {
			throw new InvalidArgumentException('USER::addStudentCourse(string $rel, string $subj, int $crse) => $subj should be a string.');
		}
		// $crse is an int
		if( !is_int($crse) ) {
			throw new InvalidArgumentException('USER::addStudentCourse(string $rel, string $subj, int $crse) => $crse should be an integer.');
		}
		// User's ta or student flag is true for the corresponding $rel option
		if( ($rel === 'student' && !$this->isStudent) || ($rel === 'ta' && !$this->isTA) ) {
			return false;
		}

		// If user has uid (is in database), add user-course mapping to database
		if($this->inDB ) {
			$db = DB::getInstance();
			try {
				return $db->prep_execute('INSERT INTO ' . $rel . 's_courses (email, subj, crse) VALUES (:email, :subj, :crse)', array(
					':email' => $this->email,
					':subj' => strtoupper($subj),
					':crse' => $crse
				));
			}
			catch( PDOException $Exception ) {
				// Return false if there is an error
				return false;
			}
		}
		// Return false if user not in database
		return false;
	}
	
	public function addTACourseWithTA_Code( $ta_code ) {
		if( $this->inDB ) {
			require_once(SITE_ROOT . '/PHP/Course.php');
			$course = COURSE::withTA_Code( $ta_code );
			
			$db = DB::getInstance();
			try {
				if( $course !== null ) {
					$result = $db->prep_execute( 'INSERT INTO tas_courses (email, subj, crse) VALUES (:email, :subj, :crse)', array(
						':email' => $this->email,
						':subj' => $course->getSubj(),
						':crse' => $course->getCrse()
					));
					
					if( !empty($result) ) {
						return $this->setTA(true, true);
					}
				}
			}
			catch( PDOException $Exception ) {
				// Return false if there is an error
				return false;
			}
		}
		return false;
	}
	
	// Removes mapping in the database table students_courses or tas_courses
	// depending on $rel's value.
	public function removeUserCourse( $rel, $subj, $crse ) {
		// Argument Type Error Handling
		
		// $rel is a string
		if( !is_string($rel) ) {
			throw new InvalidArgumentException('USER::remoteStudentCourse(string $rel, string $subj, int $crse) => $rel should be one of the following strings: "student", "ta"');
		}
		else { // $rel is either 'student' or 'ta'
			$rel = strtolower($rel);
			if( $rel !== 'student' && $rel !== 'ta' ) {
				throw new InvalidArgumentException('USER::remoteStudentCourse(string $rel, string $subj, int $crse) => $rel should be one of the following strings: "student", "ta"');
			}
		}
		// $subj is a string
		if( !is_string($subj) ) {
			throw new InvalidArgumentException('USER::remoteStudentCourse(string $rel, string $subj, int $crse) => $subj should be a string.');
		}
		// $crse is an int
		if( !is_int($crse) ) {
			throw new InvalidArgumentException('USER::remoteStudentCourse(string $rel, string $subj, int $crse) => $crse should be an integer.');
		}

		// If user is in database, remove user-course mapping to database
		if( $this->inDB ) {
			$db = DB::getInstance();
			try {
				return $db->prep_execute('DELETE FROM ' . $rel . 's_courses WHERE email = :email AND subj = :subj AND crse = :crse', array(
					':email' => $this->email,
					':subj' => strtoupper($subj),
					':crse' => $crse
				));
			}
			catch( PDOException $Exception ) {
				// Return false if a database error occures
				return false;
			}
		}
		// Return false if user not in database
		return false;
	}
	
	// Validates and sets the email address of the user. DOES NOT STORE IN
	// DATABASE! Call USER::store(bool) to store in database.
	public function setEmail( $email, $setDB = false ) {
		if( !is_string($email) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
			throw new InvalidArgumentException('USER::setEmail(string $email, bool $setDB) => $email should be a valid email address.');
		}
		
		if( !$this->setVarInDB($setDB, 'users', 'email', $email) ) {
			return false;
		}
		
		$this->email = $email;
		return true;
	}
	
	public function setInDB( $inDB ) {
		if( !is_bool($inDB) ) {
			throw new InvalidArgumentException('USER::setInDB(string $isAdmin, bool $setDB) => $inDB should be a boolean.');
		}
		
		$this->inDB = $inDB;
	}
	
	// Validates and sets the admin flag of the user. DOES NOT STORE IN
	// DATABASE! Call USER::store(bool) to store in database.
	public function setIsAdmin( $isAdmin, $setDB = false ) {
		if( !is_bool($isAdmin) ) {
			throw new InvalidArgumentException('USER::setIsAdmin(string $isAdmin, bool $setDB) => $isAdmin should be a boolean.');
		}
		
		if( !$this->setVarInDB($setDB, 'users', 'isAdmin', $isAdmin) ) {
			return false;
		}
		
		$this->isAdmin = $isAdmin;
		return true;
	}
	
	// Validates and sets the TA flag of the user. DOES NOT STORE IN
	// DATABASE! Call USER::store(bool) to store in database.
	public function setIsTA( $isTA, $setDB = false ) {
		if( !is_bool($isTA) ) {
			throw new InvalidArgumentException('USER::setIsTA(string $isTA, bool $setDB) => $isTA should be a boolean.');
		}
		
		if( !$this->setVarInDB( $setDB, 'users', 'isTA', $isTA ) ) {
			return false;
		}
		
		$this->isTA = $isTA;
		return true;
	}
	
	// Validates and sets the tutor flag of the user. DOES NOT STORE IN
	// DATABASE! Call USER::store(bool) to store in database.
	public function setIsTutor( $isTutor, $setDB = false ) {
		if( !is_bool($isTutor) ) {
			throw new InvalidArgumentException('USER::setIsTutor(string $isTutor, bool $setDB) => $isTutor should be a boolean.');
		}
		
		if( !$this->setVarInDB( $setDB, 'users', 'isTutor', $isTutor ) ) {
			return false;
		}
		
		$this->isTutor = $isTutor;
		return true;
	}
	
	// Validates and sets the student flag of the user. DOES NOT STORE IN
	// DATABASE! Call USER::store(bool) to store in database.
	public function setIsStudent( $isStudent, $setDB = false ) {
		if( !is_bool($isStudent) ) {
			throw new InvalidArgumentException('USER::setIsStudent(string $isStudent, bool $setDB) => $isStudent should be a boolean.');
		}
		
		if( !$this->setVarInDB( $setDB, 'users', 'isStudent', $isStudent ) ) {
			return false;
		}
		
		$this->isStudent = $isStudent;
		return true;
	}
	
	// Validates, hashes, and sets the password of the user. DOES NOT STORE IN
	// DATABASE! Call USER::store(bool) to store in database.
	public function setPassword( $password, $setDB = false ) {
		if( strlen($password) <= 8 ) {
			throw new InvalidArgumentException('USER::setPassword(string $password, bool $setDB) => $password should be at least 8 characters long.');
		}
		
		// Hash password with random salt
		$password = password_hash( $password, PASSWORD_DEFAULT );
		
		// Store hashed password in database if $setDB = true
		if( !$this->setVarInDB( $setDB, 'passwords', 'password', $password ) ) {
			return false;
		}

		// Set object password to hashed password
		$this->password = $password;
		return true;
	}
	
	// Validates and sets the first name of the user. DOES NOT STORE IN 
	// DATABASE! Call USER::store(bool) to store in database.
	public function setFirstName( $firstname, $setDB = false ) {
		if( !is_string($firstname) || empty($firstname) ) {
			throw new InvalidArgumentException('USER::setFirstName(string $firstname, bool $setDB) => $firstname should be a non-empty string.');
		}
		
		if( !$this->setVarInDB( $setDB, 'users', 'firstName', $firstname ) ) {
			return false;
		}
		
		$this->firstName = $firstname;
		return true;
	}
	
	// Validates and sets the last name of the user. DOES NOT STORE IN 
	// DATABASE! Call USER::store(bool) to store in database.
	public function setLastName( $lastname, $setDB = false ) {
		if( !is_string($lastname) || empty($lastname) ) {
			throw new InvalidArgumentException('USER::setFirstName(string $lastname, bool $setDB) => $lastname should be a non-empty string.');
		}
		
		if( !$this->setVarInDB( $setDB, 'users', 'lastName', $lastname ) ) {
			return false;
		}
		
		$this->lastName = $lastname;
		return true;
	}
	
	
	// ########################### STATIC FUNCTIONS ############################
	
	public static function getAllUsers() {
		$allUsers = array();
		$db = DB::getInstance();
		$user_rows = $db->prep_execute('SELECT email FROM users ORDER BY lastName, firstName, email;', array());
		foreach( $user_rows as $row ) {
			$allUsers[] = USER::fromDatabase($row['email']);
		}
		return $allUsers;
	}
	
	// Return an array of User objects of all the Studentss in the database.
	public static function getAllStudents() {
		$allTAs = array();
		$db = DB::getInstance();
		$ta_rows = $db->prep_execute('SELECT email FROM users WHERE isStudent = 1;', array());
		foreach( $ta_rows as $row ) {
			$allTAs[] = USER::fromDatabase($row['email']);
		}
		return $allTAs;
	}
	
	// Return an array of User objects of all the TAs in the database.
	public static function getAllTAs() {
		$allTAs = array();
		$db = DB::getInstance();
		$ta_rows = $db->prep_execute('SELECT email FROM users WHERE isTA = 1;', array());
		foreach( $ta_rows as $row ) {
			$allTAs[] = USER::fromDatabase($row['email']);
		}
		return $allTAs;
	}
	
		
	// Removes user with unique id from database
	public static function deleteFromDB($email) {
		// Argument Validation
		if( !is_string($email) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
			throw new InvalidArgumentException('USER::deleteFromDB(string $email) => $email should be a valid email address.');
		}
		
		// Removed the user row with unique column from database
		$db = DB::getInstance();
		$result = $db->prep_execute('DELETE FROM users WHERE email = :email;', array(
			':email' => $email
		));
		
		// Return true if a user was deleted. Otherwise, return false.
		if($result) {
			return true;
		}
		return false;
	}
	
	
	// ########################### PRIVATE FUNCTIONS ###########################
	
	// Update a variable in the user or password database tables. Used to
	// prevent repeat code in all the 'set' functions.
	private function setVarInDB($setDB, $table, $var_name, $var_value) {
		if( $setDB ) {
			$db = DB::getInstance();
			try {
				$result = $db->prep_execute('UPDATE ' . $table . ' SET ' . $var_name . ' = :new_' . $var_name . ' WHERE email = :email;', array(
					':email' => $this->email,
					':new_' . $var_name => $var_value
				));
				if( !$result ) {
					$result = $db->prep_execute('SELECT ' . $var_name . ' FROM ' . $table);
					if( $result[0][$var_name] !== $var_value ) {
						$this->setInDB(false);
					}
				}
			}
			catch( PDOException $e) {
				return false;
			}
		}
		else {
			$this->setInDB(false);
		}
		
		return true;
	}
}
?>