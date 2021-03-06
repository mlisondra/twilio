<?php 

// Stores user provided data (ie. email, name, etc.)
$host = 'localhost';
$db_name = 'twiliojf_twilio';
$username = 'twiliojf_twilio';
$password = 'fbv696pwsw';


try {
    $mysqli_conn = new mysqli($host, $username ,$password,$db_name);
} catch (Exception $e) {
    print $e->getMessage();
}

// Mailchimp Wrapper
require("vendor/autoload.php");
$mc = new \VPS\MailChimp('aea7952e0fbea388f1352ac1b8a7d098-us10'); // API key: JFJ account

// JFJ
define("LIST_ID","5a70adfde6"); // JFJ account; live list. Jews for Jesus, United States
define("MAILCHIMP_API_KEY","aea7952e0fbea388f1352ac1b8a7d098-us10");

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


	// First response to user; system asks for user's email
	if(strtolower(str_replace(".","",$user_request)) == "subscribe" || strtolower(str_replace(".","",$user_request)) == "messiah"){ 
		$app_response = $responses_array['subscribe'];
		$_SESSION['last_question_asked'] = 'email';
		
		if(strtolower(str_replace(".","",$user_request)) == "messiah"){		
			$_SESSION['messiah'] = true;
		}
		
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

	// Fourth question; system asks if user is Jewish
	}elseif( isset($_SESSION['user_email']) && !empty($_SESSION['first_name']) && $_SESSION['last_question_asked'] == "last_name" ){
		$_SESSION['last_name'] = $user_request;
		save_user_details("last_name", $_SESSION['last_name'],true);
		if($_SESSION['messiah']){
		
			save_user_details("jewish", 'yes',true);
			save_user_details("believer", 'no',true);
			$app_response = $responses_array['final_thanks'];
			$_SESSION['last_question_asked'] = 'last_name';
			
			$ccode = 'UJ';
			$jfj_obj->save_mailchimp($_SESSION['user_email'], $ccode, 'CCODE');
			$jfj_obj->save_mailchimp($_SESSION['user_email'], 'messiah', 'INTERESTS');
			
			
		}else{
			$jfj_obj->save_mailchimp($_SESSION['user_email'], 'nonmessiah', 'INTERESTS');
			$app_response = str_replace("LAST_NAME", $_SESSION['last_name'],$responses_array['thanks_signedup']);
			$app_response .= ". " . str_replace("FIRST_NAME", $_SESSION['first_name'],$responses_array['jewish']);
			$_SESSION['last_question_asked'] = 'jewish';
		}

		// Update MailChimp
		$jfj_obj->save_mailchimp($_SESSION['user_email'], $_SESSION['last_name'], 'LNAME');
		
		if($_SESSION['messiah']){
			session_destroy();
		}
	// Fifth question; system asks if user is a Believer; if user response is 'no', end of questions
	}elseif( isset($_SESSION['user_email']) && !empty($_SESSION['first_name']) && !empty($_SESSION['last_name']) && $_SESSION['last_question_asked'] == "jewish" ){
		$_SESSION['yes_no'] = strtolower($user_request);
		$_SESSION['jewish'] = strtolower($user_request);
		save_user_details("jewish", $_SESSION['jewish'],true);
		$app_response = $responses_array['believer']; 
		$_SESSION['last_question_asked'] = 'believer';

	}elseif( isset($_SESSION['user_email']) && !empty($_SESSION['first_name']) && !empty($_SESSION['last_name']) && !empty($_SESSION['jewish']) && $_SESSION['last_question_asked'] == "believer" ){
		$_SESSION['believer'] = strtolower($user_request);
		save_user_details("believer", $_SESSION['believer'],true);
		$app_response = $responses_array['final_thanks'];
		
		if(($_SESSION['jewish'] == 'no') && ($_SESSION['believer'] == "yes")){
			$ccode = 'GB'; // gentile believer
		}elseif( ($_SESSION['jewish'] == 'no') && ($_SESSION['believer'] == "no") ){
			$ccode = 'UG'; // unbelieving gentile
		}elseif( ($_SESSION['jewish'] == 'yes') && ($_SESSION['believer'] == "no") ){
			$ccode = 'UJ'; // unbelieving jew
		}elseif( ($_SESSION['jewish'] == 'yes') && ($_SESSION['believer'] == "yes") ){
			$ccode = 'JB'; // jewish believer
		}
		// Update MailChimp
		$jfj_obj->save_mailchimp($_SESSION['user_email'], $ccode, 'CCODE');
		
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
* Save user details to local database
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
}



// Class to manage ineraction with MailChimp API
class JFJ_subscribe{
	
	public $mc;
	public $user_email;
	
	public function __construct(){

		$this->mc = new \VPS\MailChimp(MAILCHIMP_API_KEY); // API key
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
	* save_mailchimp
	* Saves or updates user data to MailChimp. If the $field parameter is 'email', the function will add a new member
	* @param string $email 
	* @param string $attribute User information to be changed
	* @param string $field The corresponding field in Mailchimp that relates to the attribute (FNAME, LNAME, etc.)
	*
	*/
	public function save_mailchimp($email,$attribute, $field, $user_phone = 0){
		
		$email_md5_hash = md5($email); 
		
		if($field == 'EMAIL'){
			$endpoint = '/lists/'. LIST_ID . '/members/';
			$result = $this->mc->post($endpoint,
							array('email_address'=>$email, 'merge_fields' => array($field=>$attribute,'PHONE'=>$user_phone), 'status' => 'subscribed'));
		}elseif($field == 'INTERESTS'){
			$endpoint = '/lists/'. LIST_ID . '/members/'. $email_md5_hash;
			if($attribute == "messiah"){
				$result = $this->mc->patch($endpoint,array('interests' => array('393025b2aa'=>true))); // user is assigned to Group 'Issues'	
			}else{
				$result = $this->mc->patch($endpoint,array('interests' => array('07c37fbfaf'=>true,'eea9b73e6a'=>true))); // User is assigned to Groups 'General Communications' and 'Realtime'
			}
		}else{
			$endpoint = '/lists/'. LIST_ID . '/members/'. $email_md5_hash;
			$result = $this->mc->patch($endpoint,array('merge_fields' => array($field=>$attribute)));
		}
		
	}
}