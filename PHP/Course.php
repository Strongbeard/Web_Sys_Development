<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once(SITE_ROOT . '\PHP\DB.php');

class course{
	// ############################### VARIABLES ###############################
	
	protected $subj;
    protected $crse;
	protected $name;
	protected $inDB;
	protected $ta_code;
    
	// ############################# CONSTRUCTORS ##############################
	
	private function __construct($subj, $crse, $ta_code, $name = '', $inDB = false){
		$this->setSubj($subj);
		$this->setCrse($crse);
		$this->setName($name);
		$this->setInDB($inDB);
		$this->ta_code = $ta_code;
	}
	
	public static function fromDatabase($subj, $crse) {
		$db = DB::getInstance();
		
		$courseRows = $db->prep_execute('SELECT * FROM courses WHERE subj = :subj AND crse = :crse;', array(
			':subj' => $subj,
			':crse' => $crse
		));
		
		if( empty($courseRows) ) {
			return null;
		}
		
		return new self($subj, $crse, $courseRows[0]['ta_code'], $courseRows[0]['name'], true);
	}
	
	public static function withValues($subj, $crse, $name) {
		$ta_code = COURSE::generateRandomString();
		$instance = new self( $subj, $crse, $ta_code, $name );
		
		$db = DB::getInstance();
		try {
			$result = $db->prep_execute('INSERT INTO courses (subj, crse, name, ta_code) VALUES (:subj, :crse, :name, :ta_code);', array(
				':subj' => strtoupper($subj),
				':crse' => $crse,
				':name' => strtoupper($name),
				':ta_code' => $ta_code
			));
			if( $result ) {
				$instance->setInDB(true);
			}
		}
		catch( PDOException $e ) {}
	
		return $instance;
	}
	
	public static function withTA_Code( $ta_code ) {
		if( !is_string($ta_code) || strlen($ta_code) !== 50 ) {
			throw new InvalidArgumentException('COURSE::withTA_Code(string $ta_code) => $ta_code should be a 50-character string.');
		}
		
		$db = DB::getInstance();
		
		$courseRows = $db->prep_execute('SELECT * from courses WHERE ta_code = :ta_code;', array(
			':ta_code' => $ta_code
		));
		
		return (empty($courseRows)) ? null : new self($courseRows[0]['subj'], intval($courseRows[0]['crse']), $courseRows[0]['ta_code'], $courseRows[0]['name'], true);
	}
	
	// ########################## ACCESSOR FUNCTIONS ###########################
	
	public function getSubj() {
		return $this->subj;
	}
	
	public function getCrse() {
		return $this->crse;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getTA_Code() {
		return $this->ta_code;
	}
	
	public function getInDB() {
		return $this->inDB;
	}
	
	public function validate_ta_code( $code ) {
		return ($this->ta_code === $code) ? true : false;
	}
	
	// ########################## MODIFIER FUNCTIONS ###########################
	
	public function setSubj($subj, $setDB = false) {
		if( !is_string($subj) || empty($subj) || strlen($subj) !== 4 ) {
			throw new InvalidArgumentException('COURSE::setSubj(string $subj, bool $setDB) => $subj should be a non-empty 4-letter string.');
		}
		if( !is_bool($setDB) ) {
			throw new InvalidArgumentException('COURSE::setSubj(string $subj, bool $setDB) => $setDB should be a boolean.');
		}

		if( $setDB ) {
			$db = DB::getInstance();
			try {
				$result = $db->prep_execute('UPDATE courses SET subj = :new_subj WHERE subj = :old_subj AND crse = :crse;', array(
					':new_subj' => strtoupper($subj),
					':old_subj' => $this->subj,
					':crse' => $this->crse
				));
				if( !$result ) {
					return false;
				}
			}
			catch( PDOException $e) {
				return false;
			}
		}
		else {
			$this->setInDB(false);
		}
		
		$this->subj = strtoupper($subj);
		return true;
	}
	
	public function setCrse($crse, $setDB = false) {
		if( !is_int($crse) ) {
			throw new InvalidArgumentException('COURSE::setCrse(string $subj, int $crse, string $name, bool $inDB) => $crse should be an integer.');
		}
		if( !is_bool($setDB) ) {
			throw new InvalidArgumentException('COURSE::setCrse(string $subj, bool $setDB) => $setDB should be a boolean.');
		}
		
		if( $setDB ) {
			$db = DB::getInstance();
			try {
				$result = $db->prep_execute('UPDATE courses SET crse = :new_crse WHERE subj = :subj AND crse = :old_crse;', array(
					':subj' => $this->subj,
					':new_crse' => $crse,
					':old_crse' => $this->crse
				));
				if( !$result ) {
					return false;
				}
			}
			catch( PDOException $e) {
				return false;
			}
		}
		else {
			$this->setInDB(false);
		}
		
		$this->crse = $crse;
		return true;
	}
	
	public function setName($name, $setDB = false) {
		if( !is_string($name) ) {
			throw new InvalidArgumentException('COURSE::setName(string $subj, int $crse, string $name, bool $inDB) => $name should be a string. Default is empty string.');
		}
		if( !is_bool($setDB) ) {
			throw new InvalidArgumentException('COURSE::setName(string $subj, bool $setDB) => $setDB should be a boolean.');
		}
		
		if( $setDB ) {
			$db = DB::getInstance();
			try {
				$result = $db->prep_execute('UPDATE courses SET name = :new_name WHERE subj = :subj AND crse = :crse;', array(
					':new_name' => $name,
					':subj' => $this->subj,
					':crse' => $this->crse
				));
				if( !$result ) {
					return false;
				}
			}
			catch( PDOException $e) {
				return false;
			}
		}
		else {
			$this->setInDB(false);
		}
		
		$this->name = strtoupper($name);
		return true;
	}
	
	public function setInDB($flag) {
		if( !is_bool($flag) ) {
			throw new InvalidArgumentException('COURSE::setInDB(string $subj, int $crse, string $name, bool $inDB) => $inDB should be boolean flag. Default is false.');
		}
		$this->inDB = $flag;
		return true;
	}
	
	public function store($update = false) {
		$db = DB::getInstance();
	}
	
	
	// ########################## STATIC DB FUNCTIONS ##########################
	
	public static function search($subj,$crse = -1,$name = '') {
		// INVALID ARGUMENT ERRORS
		if( !is_string($subj) || empty($subj) || strlen($subj) !== 4 ) {
			throw new InvalidArgumentException('COURSE::search(string $subj, int $crse, string $name) => $subj should be a non-empty 4-letter string.');
		}
		if( !is_int($crse) ) {
			throw new InvalidArgumentException('COURSE::search(string $subj, int $crse, string $name) => $crse should be an integer.');
		}
		if( !is_string($name) ) {
			throw new InvalidArgumentException('COURSE::search(string $subj, int $crse, string $name) => $name should be a string. Default is empty string.');
		}
		
		// Create the prepared statement and array for database query
		$db = DB::getInstance();
		$pstmt = 'SELECT * FROM courses WHERE subj = :subj';
		$pstmt_array = array(
			':subj' => $subj
		);
		if( $crse != -1 ) {
			$pstmt .= ' AND crse = :crse';
			$pstmt_array[':crse'] = $crse;
		}
		if( $name !== '' ) {
			$pstmt .= ' AND name LIKE :name';
			$pstmt_array[':name'] = strtoupper('%' . $name . '%');
		}
		
		return $db->prep_execute($pstmt, $pstmt_array);
	}
	
	public static function getAllCourses() {
		$allCourses = array();
		$db = DB::getInstance();
		$course_rows = $db->prep_execute('SELECT subj, crse FROM courses ORDER BY subj, crse, name;', array());
		foreach( $course_rows as $row ) {
			$allCourses[] = COURSE::fromDatabase($row['subj'], intval($row['crse']));
		}
		return $allCourses;
	}
	
	public static function deleteFromDB($subj, $crse) {
		// INVALID ARGUMENT ERRORS
		if( !is_string($subj) || empty($subj) || strlen($subj) !== 4 ) {
			throw new InvalidArgumentException('COURSE::search(string $subj, int $crse, string $name) => $subj should be a non-empty 4-letter string.');
		}
		if( !is_int($crse) ) {
			throw new InvalidArgumentException('COURSE::search(string $subj, int $crse, string $name) => $crse should be an integer.');
		}
		
		$db = DB::getInstance();
		try {
			$result = $db->prep_execute('DELETE FROM courses WHERE subj = :subj AND crse = :crse;', array(
				':subj' => $subj,
				':crse' => $crse
			));
		}
		catch( PDOException $e ) {
			return false;
		}
		
		if( $result ) {
			return true;
		}
		return false;
	}
	
	
	// ########################### PRIVATE FUNCTIONS ###########################
	
	private static function generateRandomString($length = 50) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
}
?>
