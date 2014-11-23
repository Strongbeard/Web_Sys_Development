<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once(SITE_ROOT . '\PHP\DB.php');

class course{
	// ############################### VARIABLES ###############################
	
	protected $subj;
    protected $crse;
	protected $name;
	protected $inDB;
    
	// ############################# CONSTRUCTORS ##############################
	
	private function __construct($subj, $crse, $name = '', $inDB = false){
		$this->setSubj($subj);
		$this->setCrse($crse);
		$this->setName($name);
		$this->setInDB($inDB);
	}
	
	public static function fromDatabase($subj, $crse) {
		$db = DB::getInstance();
		
		$courseRows = $db->prep_execute('SELECT * FROM courses WHERE subj = :subj AND crse = :crse', array(
			':subj' => $subj,
			':crse' => $crse
		));
		
		if( empty($courseRows) ) {
			return null;
		}
		
		return new self($subj, $crse, $courseRows[0]['name'],true);
	}
	
	// ########################## ACCESSOR FUNCTIONS ###########################
	
	public function getSubj() {
		return $subj;
	}
	
	public function getCrse() {
		return $crse;
	}
	
	public function getName() {
		return $name;
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
}
?>
