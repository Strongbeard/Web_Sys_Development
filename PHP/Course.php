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
		if( !is_string($subj) || empty($subj) ) {
			throw new InvalidArgumentException('COURSE::__constructor(string $subj, int $crse, string $name, bool $inDB) => $subj should be a non-empty string.');
		}
		$this->subj = strtoupper($subj);
	}
	
	public function setCrse($crse) {
		if( !is_int($crse) ) {
			throw new InvalidArgumentException('COURSE::__constructor(string $subj, int $crse, string $name, bool $inDB) => $crse should be an integer.');
		}
		$this->crse = $crse;
	}
	
	public function setName($name) {
		if( !is_string($name) ) {
			throw new InvalidArgumentException('COURSE::__constructor(string $subj, int $crse, string $name, bool $inDB) => $name should be a string. Default is empty string.');
		}
		$this->name = strtoupper($name);
	}
	
	public function setInDB($flag) {
		if( !is_bool($flag) ) {
			throw new InvalidArgumentException('COURSE::__constructor(string $subj, int $crse, string $name, bool $inDB) => $inDB should be boolean flag. Default is false.');
		}
		$this->inDB = $flag;
	}
	
	public function store($update = false) {
		
	}
}
?>
