<?php
require_once(dirname(dirname(__FILE__)) . '\config.php');

class DB {
	// --- VARIABLES ---
	
	private $dbname = 'TA_Hunter';
	private $username = 'TA_Hunter';
	private $password = 'web_sys_dev_user';
	private $conn;
	
	
	// --- CONSTRUCTORS ---
	
	public function __construct(){
		$this->dbname = 'TA_Hunter';
		$this->username = 'TA_Hunter';
		$this->password = 'web_sys_dev_user';
		$this->conn = new PDO( 'mysql:host=localhost;dbname=' . $this->dbname, $this->username, $this->password );
		$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	// Returns Database Connection Singleton
	public static function getInstance() {
		static $instance = null;
		if( null === $instance ) {
			$instance = new self();
		}
		return $instance;
	}
	
	
	// --- FUNCTIONS ---
	
	public function prep_execute( $query_string, $var_array = array() ) {
		// Begin Transaction
		$this->conn->beginTransaction();
		
		// Try to complete statement. If an error occurs, rollback and throw
		// the same PDOException caught
		try {
			$pstmt = $this->conn->prepare( $query_string );
			$pstmt->execute( $var_array );
			$this->conn->commit();
		}
		catch( PDOException $Exception ) {
			$this->conn->rollBack();
			throw $Exception;
		}

		// Return either all rows from a SELECT statement or the # of rows
		// affected by the statement. Try-Catch block because no way to check
		// that rows have been returned by the execution.
		try {
			$rows = $pstmt->fetchAll(PDO::FETCH_ASSOC);
		}
		catch( PDOException $Exception ) {
			$rows = $pstmt->rowCount();
		}
		return $rows;
	}
	
	public function multi_prep_execute( $query_strings, $var_arrays ) {
		if( count($query_strings) !== count($var_arrays) ) {
			throw new InvalidArgumentException('DB::multi_prep_execute(array<string> $query_strings, array<array<>> $var_arrays) => The $query_strings array should be the same size as the $var_arrays array.');
		}
		
		try {
			$prepared_array = array();
			foreach( $query_strings as $pstmt ) {
				$prepared_array[] = $this->conn->prepare( $pstmt );
			}
			
			$this->conn->beginTransaction();
			
			foreach($prepared_array as $index => $pstmt) {
				$pstmt->execute($var_arrays[$index]);
			}
			
			$this->conn->commit();
		}
		catch( PDOException $e ) {
			$this->conn->rollBack();
			throw $e;
		}
		
		$rows = 0;
		try {
			$rows = $pstmt->fetchAll(PDO::FETCH_ASSOC);
		}
		catch( PDOException $Exception ) {
			$rows = $pstmt->rowCount();
		}
		return $rows;
	}
}
?>