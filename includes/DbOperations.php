<?php

	/**
	 * 
	 */
	class DbOperations
	{
		private $con;

		function __construct()
		{
			require_once dirname(__FILE__).'/DbConnect.php';
			$db = new DbConnect;
			$this->con = $db->connect();	
		}

		//create users
		public function createUser($username, $email, $password){
			//if the email do not exists then create him	
			echo '!! the create user is running ';
			if(!$this->isEmailExists($email)) {
				echo ' | email  exists ';
				//prepared statements
				//https://www.w3schools.com/php/php_mysql_prepared_statements.asp
				//A prepared statement is a feature used to execute the same (or similar) SQL statements repeatedly with high efficiency.
				$stmt = $this->con->prepare("insert into users(username,email,password) values (?,?,?)");
				$stmt->bind_param("sss", $username, $email, $password);
				echo ' ||TTTT||'.$username;
				if($stmt->execute()){
					return USER_CREATED;
				}else{
					return USER_FAILURE;
				}
			}
			 return USER_EXISTS;
		}

		//check if the user's email exists
		private function isEmailExists($email){
			 $stmt = $this->con->prepare("SELECT id FROM  users WHERE email = ?");
			 $stmt->bind_param("s", $email);
			 $stmt->execute();
			 $stmt->store_result();
			 echo ' |is email exists running ';
			 $rows=  $stmt->num_rows();
			return $rows > 0;

		}
	}

	?>