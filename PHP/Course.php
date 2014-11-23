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
		return new self( $subj, $crse, COURSE::generateRandomString(), $name );
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
		return $ta_code;
	}
	
	public function validate_ta_code( $code ) {
		return ($this->ta_code === $code) ? true : false;
	}
	
	// ########################## MODIFIER FUNCTIONS ###########################
	
	public function setSubj($subj) {
		if( !is_string($subj) || empty($subj) || strlen($subj) !== 4 ) {
			throw new InvalidArgumentException('COURSE::setSubj(string $subj, int $crse, string $name, bool $inDB) => $subj should be a non-empty 4-letter string.');
		}
		$this->subj = strtoupper($subj);
	}
	
	public function setCrse($crse) {
		if( !is_int($crse) ) {
			throw new InvalidArgumentException('COURSE::setCrse(string $subj, int $crse, string $name, bool $inDB) => $crse should be an integer.');
		}
		$this->crse = $crse;
	}
	
	public function setName($name) {
		if( !is_string($name) ) {
			throw new InvalidArgumentException('COURSE::setName(string $subj, int $crse, string $name, bool $inDB) => $name should be a string. Default is empty string.');
		}
		$this->name = strtoupper($name);
	}
	
	public function setInDB($flag) {
		if( !is_bool($flag) ) {
			throw new InvalidArgumentException('COURSE::setInDB(string $subj, int $crse, string $name, bool $inDB) => $inDB should be boolean flag. Default is false.');
		}
		$this->inDB = $flag;
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
