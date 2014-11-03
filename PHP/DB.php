<?php
class DB {
	private $dbname = 'TA_Hunter';
	private $username = 'TA_Hunter';
	private $password = 'web_sys_dev_user';
	private $conn;
	
	public function __construct(){
		$this->dbname = 'TA_Hunter';
		$this->username = 'TA_Hunter';
		$this->password = 'web_sys_dev_user';
		$this->conn = new PDO( 'mysql:host=localhost;dbname=' . $this->dbname, $this->username, $this->password );
	}
	
	// Returns Database Connection Singleton
	public static function getInstance() {
		static $instance = null;
		if( null === $instance ) {
			$instance = new self();
		}
		return $instance;
	}
	
	// Returns the connection to the database.
/*	public function connection() {
		return $this->conn;
	}*/
	
	public function query( $query_string ) {
		if( !is_string($query_string) ) {
			throw new Exception('DB::query() - Invalid Query Format');
		}
		return $this->conn->query( $query_string );
	}
	
	public function prep_query( $query_string, $var_array ) {
		if( !is_string($query_string) ) {
			throw new Exception('DB::query() - Invalid Query Format');
		}
		$this->conn->prepare( $query_string );
		return $this->conn->execute( $var_array );
	}
	
	public function exec( $query_string ) {
		if( !is_string($query_string) ) {
			throw new Exception('DB::exec() - Invalid Query Format');
		}
		return $this->conn->exec( $query_string );
	}
}
?>