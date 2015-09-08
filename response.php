<?php 

$host = '10.5.223.9';
$db_name = 'twadmin_twilio';
$username = 'twadmin_twilio';
$password = '9pyziTxBJbm0';


try {
    $mysqli_conn = new mysqli($host, $username ,$password,$db_name);
} catch (Exception $e) {
    print $e->getMessage();
}



if(strtolower(trim($_POST['Body'])) == "clear"){
	mail("milder.lisondra@jewsforjesus.org","from JFJ sms response page","session cleared");
	session_destroy();
}
session_start();

   $counter = $_SESSION['counter'];
   $counter++;
   $_SESSION['counter'] = $counter;

$responses_array = array(
		"subscribe"=>"Hi, lets get you signed up to receive regular updates from Jews for Jesus. Please respond with your email address.",
		"first_name"=>"Thank you. What is your first name?",
		"last_name"=>"Thanks FIRST_NAME. What is your last name?",
		"thanks_signedup"=>"Thanks you're singed up to receive our regular updates",
		"jewish"=>"We'd like to know some more about you FIRST_NAME. Please can you share, are you Jewish (yes or no)?",
		"believer"=>"And are you a believer in Jesus (yes or no)?",
		"final_thanks"=>"Great, thanks so much. We look forward to be in touch again soon. Shalom! Jews for Jesus"
	);
	

$user_request = trim($_POST['Body']);


	// First response to user; system asks for users email
	if(strtolower($user_request) == "subscribe"){ 
		$app_response = $responses_array['subscribe'];
		$_SESSION['last_question_asked'] = 'email';
		
		mail("milder.lisondra@jewsforjesus.org","from JFJ sms response page",$user_request);
	}elseif( $_SESSION['last_question_asked'] == "email" ){
		if(!filter_var(strtolower($user_request), FILTER_VALIDATE_EMAIL) === false) { // validate email address
			$_SESSION['user_email'] = strtolower($user_request);
			save_user_details("email", $_SESSION['user_email']);
			$app_response = $responses_array['first_name']; // system will ask for user first name
			$_SESSION['last_question_asked'] = 'first_name';
		}else{
			$app_response = 'Please enter a valid email address';
			$_SESSION['last_question_asked'] = 'email';
		}
		
	// Third response to user; system asks for users last name
	}elseif( isset($_SESSION['user_email']) && $_SESSION['last_question_asked'] == "first_name" ){
		$_SESSION['first_name'] = $user_request;
		save_user_details("first_name", $_SESSION['first_name'],true);
		mail("milder.lisondra@jewsforjesus.org","from JFJ sms response page",$_SESSION['first_name']);
		$app_response = str_replace("FIRST_NAME", $_SESSION['first_name'],$responses_array['last_name']);
		$_SESSION['last_question_asked'] = 'last_name';

	// Fourth question; system asks if user is Jewish; maybe last question if user response is 'no'
	}elseif( isset($_SESSION['user_email']) && !empty($_SESSION['first_name']) && $_SESSION['last_question_asked'] == "last_name" ){
		$_SESSION['last_name'] = $user_request;
		save_user_details("last_name", $_SESSION['last_name'],true);
		$app_response = str_replace("LAST_NAME", $_SESSION['last_name'],$responses_array['thanks_signedup']);
		$app_response .= ". " . str_replace("FIRST_NAME", $_SESSION['first_name'],$responses_array['jewish']);
		$_SESSION['last_question_asked'] = 'jewish';
	// Fifth question; system asks if user is a Believer; if user response is 'no', end of questions
	}elseif( isset($_SESSION['user_email']) && !empty($_SESSION['first_name']) && !empty($_SESSION['last_name']) && $_SESSION['last_question_asked'] == "jewish" ){
		$_SESSION['yes_no'] = strtolower($user_request);
		$_SESSION['jewish'] = strtolower($user_request);
		save_user_details("jewish", $_SESSION['jewish'],true);
		$app_response = $responses_array['believer']; 
		$_SESSION['last_question_asked'] = 'believer';

	}
	elseif( isset($_SESSION['user_email']) && !empty($_SESSION['first_name']) && !empty($_SESSION['last_name']) && !empty($_SESSION['jewish']) && $_SESSION['last_question_asked'] == "believer" ){
		$_SESSION['believer'] = strtolower($user_request);
		save_user_details("believer", $_SESSION['believer'],true);
		$app_response = $responses_array['final_thanks'];
		
		session_destroy();		
	}

//$app_response .= print_r(session_id(),true);
$xml_response = '<?xml version="1.0" encoding="UTF-8" ?>';
$xml_response .= '<Response>';
$xml_response .= '<Message>' . $app_response . '</Message>';
$xml_response .= '</Response>';

print $xml_response;

//mail("milder.lisondra@jewsforjesus.org","from JFJ sms response page",$app_response);



// Maybe this should be a class?

/**
* save_user_details
* @param array $args Key-value pairs of user provided information
* @return boolean
* Example ("first_name"=>"King Arthur")
* 
*
*/
function save_user_details($field, $value, $update = false){
	global $host;
	global $db_name;
	global $username;
	global $password;
	global $mysqli_conn;

	extract($args);
	if( $update === false){
		
		// prepare and bind
		$stmt = $mysqli_conn->prepare("INSERT INTO visitors (" . $field . ") VALUES (?)");
		$stmt->bind_param("s", $value);

		
		$stmt->execute();
		
		//$sql = "INSERT INTO visitors (". $field . ") VALUES ('" . $value . "')"; // enter email
	}else{
		$sql = "UPDATE visitors SET " . $field . " = '". $value ."' WHERE `email` = '" . $_SESSION['user_email'] . "'"; // update user record with optional information
		if ($mysqli_conn->query($sql) === TRUE) {
			$result = $field . " : " . $value . " " . $_SESSION['user_email'];
		} else {
			$result = "Error: " . $sql . "<br>" . $mysqli_conn->error;
		}
	}

	
	
	mail("milder.lisondra@jewsforjesus.org","User detail saved ",$result);
}