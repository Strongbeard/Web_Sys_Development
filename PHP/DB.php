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
	
	public function prep_bind_execute( $query_string, $var_array = array() ) {
//		try {
			if( !is_string($query_string) ) {
				throw new Exception('DB::query() - Invalid Query Format');
			}
			$pstmt = $this->conn->prepare( $query_string );

			print_r($var_array);
			echo "<br />";
			print_r($pstmt);
			echo "<br />";

			$pstmt->execute( $var_array );
			return $pstmt->fetch(PDO::FETCH_ASSOC);
/*		}
		catch( PDOException $Exception ) {
			echo $Exception->getMessage();
			return false;
		}*/
	}
	
	public function exec( $query_string ) {
		if( !is_string($query_string) ) {
			throw new Exception('DB::exec() - Invalid Query Format');
		}
		return $this->conn->exec( $query_string );
	}
}
?>