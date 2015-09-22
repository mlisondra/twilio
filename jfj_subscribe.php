<?php 
session_start();
require("vendor/autoload.php");

$_SESSION['user_email'] = 'milder2@yahoo.com';
$_SESSION['first_name'] = 'Milder John Bernard';
$field = 'FNAME';

// Personal use
//define("LIST_ID","d36f7938ca");
//define("MAILCHIMP_API_KEY","1ec0c9c10a65da0f2ff2930b13158df2-us11");

// JFJ
define("LIST_ID","4ec624cff2");
define("MAILCHIMP_API_KEY","585fd4605ba0afbb77335bbcef033dca-us10");


$jfj_obj = new JFJ_subscribe();
$jfj_obj->get_user('milder2@yahoo.com');
//$jfj_obj->save_mailchimp($_SESSION['user_email'], $_SESSION['first_name'], $field);

class JFJ_subscribe{
	
	public $mc;
	public $user_email;
	
	public function __construct(){
		// This needs to be done here in the construct
		$this->mc = new \VPS\MailChimp(MAILCHIMP_API_KEY); // API key: personal
		$this->user_email = md5($_SESSION['user_email']);
		
	}
	
	/*
	* Get user information for given id/email
	* @param string $email User email
	*/
	public function get_user($email){
		$email_md5_hash = md5($email); 
		$endpoint = '/lists/'. LIST_ID . '/members/'. $email_md5_hash;
		$result = $this->mc->get($endpoint);
		if($result['status'] == '404'){
			print 'user does not exist';
		}else{
			print '<pre>'; print_r($result); print '</pre>';
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
	* @param string $email 
	* @param string $attribute User information to be changed
	* @param string $field The corresponding field in Mailchimp that relates to the attribute (FNAME, LNAME, etc.)
	*
	*/
	public function save_mailchimp($email,$attribute, $field){
		
		$email_md5_hash = md5($email); 
		$endpoint = '/lists/'. LIST_ID . '/members/'. $email_md5_hash;
		
		$result = $this->mc->patch($endpoint,array('merge_fields' => array($field=>$attribute)));
		
	}
}


