<?php
	namespace SQLRequests;

	include 'includes/connect.php';

	//Gets PDO class from Global namespace
	use \PDO as PDO;

	class Get{
		//List of SQL requests
		private $sqlr = array(
			'advert'  => 'SELECT id, title, text, startDate, endDate, categoryId, image, email, phone FROM adverts WHERE id = :sp LIMIT 0,1',
			'adverts' => 'SELECT id, title, text, startDate, endDate, categoryId, image, email, phone FROM adverts WHERE endDate > CURDATE()',
			'categories' => 'SELECT id, loc_ru, loc_ua, loc_en FROM categories',
			'users' => 'SELECT id, login, password, role, name, dob, email, number FROM users',
			'ip' => 'SELECT COUNT(*) FROM ips WHERE ip = :sp AND timestamp > (NOW() - INTERVAL 1 DAY)'
		);

		private function execSQL($request='', $secondParam=''){
			try{

				if($request !== ''){

					//Connect $pdo variable from connect.php
					global $pdo;

					//Making PDO SQL request

					$stmt = $pdo -> prepare($this -> sqlr[$request]);

					if($secondParam !== ''){ 
						// $getID = intval($getID);
						$stmt -> bindParam( ':sp', $secondParam, PDO::PARAM_STR ); 
					}

					$stmt -> execute();

					//Temp array
					$temp = array();

					if($stmt -> rowCount() > 0){ //check if more than 0 record found
							
							//Iterate through the SQL Answer
							while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
								array_push($temp, $row);
							}
							return $temp;
					}else{
						// Return empty array
						return array();
					}

				}else{
					//Return empty array
					return array();
				}

			}catch(PDOException $exception){ //to handle error
				return array('status' => 418, 'errorMessage' => 'I\'m a teapot!');
			}

		}
			
		public function categories(){
			return $this -> execSQL('categories');
		}

		public function adverts(){
			return $this -> execSQL('adverts');
		}

		public function advert($id){
			return $this -> execSQL('advert', $id);
		}

		public function ip($ip){
			return $this -> execSQL('ip', $ip)[0]["COUNT(*)"];
		}	

	}
	
	class Add{

		private $sqlr = array(
			'advert' => 'INSERT INTO adverts SET title = :title,  text = :text,  endDate = :endDate,  categoryId = :categoryId,  email = :email,  phone = :phone, startDate = :startDate',
			'ip' => 'INSERT INTO ips SET ip = :ip'
		);

		public function advert(){
			try{
		   		//Connect $pdo variable from connect.php
				global $pdo;

				//Making PDO SQL request
				$stmt = $pdo -> prepare($this -> sqlr['advert']);
				$now = date('Y-m-d');

				/*---------- PDO BIND PARAMS ----------*/
				$stmt -> bindParam( ':startDate', $now, PDO::PARAM_STR );
				$stmt -> bindParam( ':title', $_POST['title'], PDO::PARAM_STR );
				$stmt -> bindParam( ':text', $_POST['text'], PDO::PARAM_STR );
				$stmt -> bindParam( ':endDate', $_POST['endDate'], PDO::PARAM_STR );
				$stmt -> bindParam( ':categoryId', $_POST['categoryId'], PDO::PARAM_STR );
				$stmt -> bindParam( ':email', $_POST['email'], PDO::PARAM_STR );
				$stmt -> bindParam( ':phone', $_POST['phone'], PDO::PARAM_STR );
				/*-------- PDO BIND PARAMS ENDS --------*/

				$stmt -> execute();
				
				return array('status' => 200, 'id' => $pdo -> lastInsertId());

			}catch(PDOException $exception){ //to handle error
				return array('status' => 418, 'errorMessage' => 'I\'m a teapot!');
			}
		}

		public function ip($ip){
		   		//Connect $pdo variable from connect.php
				global $pdo;

				//Making PDO SQL request
				$stmt = $pdo -> prepare($this -> sqlr['ip']);

				$stmt -> bindParam( ':ip', $ip, PDO::PARAM_STR );

				$stmt -> execute();
		}
	}

?>