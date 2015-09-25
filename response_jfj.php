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

// Mailchimp Wrapper
require("vendor/autoload.php");
$mc = new \VPS\MailChimp('1ec0c9c10a65da0f2ff2930b13158df2-us11'); // API key: personal
$mc = new \VPS\MailChimp('585fd4605ba0afbb77335bbcef033dca-us10'); // API key: JFJ account
$list_id = 'd36f7938ca'; // personal
$list_id = '4ec624cff2'; // JFJ account

// JFJ
define("LIST_ID","4ec624cff2");
define("MAILCHIMP_API_KEY","585fd4605ba0afbb77335bbcef033dca-us10");

$jfj_obj = new JFJ_subscribe();

				
				
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
		"final_thanks"=>"Great, thanks so much. We look forward to be in touch again soon. Shalom! Jews for Jesus",
		"invalid"=>"I'm sorry, I did not understand your request. Let's start over. Please text SUBSCRIBE.",
		"exists"=>"That email address is already subscribed."
	);
	
$user_phone = trim($_POST['From']);
$user_request = trim($_POST['Body']);


	// First response to user; system asks for users email
	if(strtolower($user_request) == "subscribe"){ 
		$app_response = $responses_array['subscribe'];
		$_SESSION['last_question_asked'] = 'email';
		
	}elseif( $_SESSION['last_question_asked'] == "email" ){
		if(!filter_var(strtolower($user_request), FILTER_VALIDATE_EMAIL) === false) { // validate email address
			$_SESSION['user_email'] = strtolower($user_request);
			
			// Check to see if the email exists within MailChimp
			$result = $jfj_obj->get_user($_SESSION['user_email']);
			
			if($result === false){  // email does not exist
				save_user_details("email", $_SESSION['user_email']); // saving to local database
				
				$jfj_obj->save_mailchimp($_SESSION['user_email'], $_SESSION['user_email'], 'EMAIL',$user_phone);
				
				$app_response = $responses_array['first_name']; // Second response to user; asks for first name
				$_SESSION['last_question_asked'] = 'first_name';				
			}
		}else{
			$app_response = 'Please enter a valid email address';
			$_SESSION['last_question_asked'] = 'email';
		}
		
	// Third response to user; system asks for users last name
	}elseif( isset($_SESSION['user_email']) && $_SESSION['last_question_asked'] == "first_name" ){
		$_SESSION['first_name'] = $user_request;
		save_user_details("first_name", $_SESSION['first_name'],true);
		
		// Update MailChimp
		$jfj_obj->save_mailchimp($_SESSION['user_email'], $_SESSION['first_name'], 'FNAME');
				
		$app_response = str_replace("FIRST_NAME", $_SESSION['first_name'],$responses_array['last_name']);
		$_SESSION['last_question_asked'] = 'last_name';

	// Fourth question; system asks if user is Jewish; maybe last question if user response is 'no'
	}elseif( isset($_SESSION['user_email']) && !empty($_SESSION['first_name']) && $_SESSION['last_question_asked'] == "last_name" ){
		$_SESSION['last_name'] = $user_request;
		save_user_details("last_name", $_SESSION['last_name'],true);
		$app_response = str_replace("LAST_NAME", $_SESSION['last_name'],$responses_array['thanks_signedup']);
		$app_response .= ". " . str_replace("FIRST_NAME", $_SESSION['first_name'],$responses_array['jewish']);
		$_SESSION['last_question_asked'] = 'jewish';
		
		// Update MailChimp
		$jfj_obj->save_mailchimp($_SESSION['user_email'], $_SESSION['last_name'], 'LNAME');
		
	// Fifth question; system asks if user is a Believer; if user response is 'no', end of questions
	}elseif( isset($_SESSION['user_email']) && !empty($_SESSION['first_name']) && !empty($_SESSION['last_name']) && $_SESSION['last_question_asked'] == "jewish" ){
		$_SESSION['yes_no'] = strtolower($user_request);
		$_SESSION['jewish'] = strtolower($user_request);
		save_user_details("jewish", $_SESSION['jewish'],true);
		$app_response = $responses_array['believer']; 
		$_SESSION['last_question_asked'] = 'believer';

		// Update MailChimp
		$jfj_obj->save_mailchimp($_SESSION['user_email'], $_SESSION['jewish'], 'JEWISH');
	}
	elseif( isset($_SESSION['user_email']) && !empty($_SESSION['first_name']) && !empty($_SESSION['last_name']) && !empty($_SESSION['jewish']) && $_SESSION['last_question_asked'] == "believer" ){
		$_SESSION['believer'] = strtolower($user_request);
		save_user_details("believer", $_SESSION['believer'],true);
		$app_response = $responses_array['final_thanks'];
		
		// Update MailChimp
		$jfj_obj->save_mailchimp($_SESSION['user_email'], $_SESSION['believer'], 'BELIEVER');
		
		session_destroy();		
	}else{
		$app_response = $responses_array['invalid'];
	
	}

$xml_response = '<?xml version="1.0" encoding="UTF-8" ?>';
$xml_response .= '<Response>';
$xml_response .= '<Message>' . $app_response . '</Message>';
$xml_response .= '</Response>';

print $xml_response;


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
		
		$stmt->execute(); //execute sql query

	}else{
		$sql = "UPDATE visitors SET " . $field . " = '". $value ."' WHERE `email` = '" . $_SESSION['user_email'] . "'"; // update user record with optional information
		if ($mysqli_conn->query($sql) === TRUE) {
			$result = $field . " : " . $value . " " . $_SESSION['user_email'];
		} else {
			$result = "Error: " . $sql . "<br>" . $mysqli_conn->error;
		}
	}
	
	//$result .= print_r($_SESSION,true);
	//mail("milder.lisondra@jewsforjesus.org","User detail saved ",$result);
}



// class JFJ_subscribe
class JFJ_subscribe{
	
	public $mc;
	public $user_email;
	
	public function __construct(){
		// This needs to be done here in the construct
		$this->mc = new \VPS\MailChimp(MAILCHIMP_API_KEY); // API key: personal
		//$this->user_email = md5($_SESSION['user_email']);
		$this->user_email = strtolower(trim(md5($_SESSION['user_email']))); 
		
	}
		
	/*
	* Get user information for given id/email
	* @param string $email User email
	*/
	public function get_user($email){
		$email_md5_hash = strtolower(trim(md5($email))); 
		$endpoint = '/lists/'. LIST_ID . '/members/'. $this->user_email;
		$result = $this->mc->get($endpoint);
		if($result['status'] == '404'){
			return false;
		}else{
			return true;
		}
		
	}
	
	/*
	* save_user_details
	*/
	public function save_user_details(){
		//print get_class();
	}
	
	/*
	* save_mailchimp
	* Saves or updates user data to MailChimp. If the $field parameter is 'email', the function will add a new member
	* @param string $email 
	* @param string $attribute User information to be changed
	* @param string $field The corresponding field in Mailchimp that relates to the attribute (FNAME, LNAME, etc.)
	*
	*/
	public function save_mailchimp($email,$attribute, $field, $user_phone = 0){
		
		$email_md5_hash = md5($email); 
		
		if(strtolower($field) == 'email'){
			$endpoint = '/lists/'. LIST_ID . '/members/';
			$result = $this->mc->post($endpoint,
							array('email_address'=>$email, 'merge_fields' => array($field=>$attribute,'PHONE'=> $user_phone), 'status' => 'subscribed'));
		}else{
			$endpoint = '/lists/'. LIST_ID . '/members/'. $email_md5_hash;
			$result = $this->mc->patch($endpoint,array('merge_fields' => array($field=>$attribute)));
		}
	}
}


