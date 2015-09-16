<?php 

$host = '199.101.145.32';
$db_name = 'twadmin_twilio';
$username = 'twadmin_twilio';
$password = '9pyziTxBJbm0';

date_default_timezone_set('America/Denver');

try {
    $mysqli_conn = new mysqli($host, $username ,$password,$db_name);
} catch (Exception $e) {
    print $e->getMessage();
}

			$sql = "SELECT * FROM visitors WHERE `email` = 'milder.lisondra@yahoo.com'";
			$result = $mysqli_conn->query($sql);
			//print_r($result);
			if($result->num_rows == 1){
				extract($result->fetch_array(MYSQLI_ASSOC));
				//$text = $last_modified;
				

				$datetime1 = new DateTime($last_modified); 
				//date_default_timezone_set('America/Los Angeles');
				$datetime2 = new DateTime(date("Y-m-d h:i:s",time())); 
				$interval = $datetime1->diff($datetime2);
				echo $interval->format('%h hours');
			}
			