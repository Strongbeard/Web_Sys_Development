<?php
require_once('./DB.php');

$db = DB::getInstance();

class course{
	protected $courseid=null;
    protected $courseinfo
    

	protected function __construct($courseid){
		if( empty($course) ) {
			throw new Exception('Empty courseid');
		}
		$this->courseid = $courseid;
		$command="SELECT * FROM Courses WHERE courseId='$courseid'";
		$result=mysql_query($command);
		$this->courseinfo=mysql_fetch_array($result);
		

	}
	
		

	
	public function searchCoursename(){
		return $this->courseinfo['courseName'];
	}

	private function isSecure() {
		return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
	}
}

   




}
?>
