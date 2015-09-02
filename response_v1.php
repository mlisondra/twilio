<?php 

session_start();

   $counter = $_SESSION['counter'];
   $counter++;
   $_SESSION['counter'] = $counter;
   $_SESSION['posted_data'] = $_POST; 

$responses_array = array(
		"subscribe"=>"Hi, lets get you signed up to receive regular updates from Jews for Jesus. Please respond with your email address",
		"first_name"=>"Thank you. What is your first name?",
		"last_name"=>"Thanks FIRST_NAME. What is your last name?",
		"thanks_signedup"=>"Thanks you're singed up to receive our regular updates",
		"jewish"=>"We'd like to know some more about you FIRST_NAME. Please can you share, are you Jewish (yes or no)?",
		"believer"=>"And are you a believer in Jesus yes or no)?",
		"final_thanks"=>"Great, thanks so much. We look forward to be in touch again soon. Shalom! Jews for Jesus"
	);
	
   
$xml_response = '<?xml version="1.0" encoding="UTF-8" ?>';
$posted_data = print_r($_POST,TRUE);
$user_request = strtolower($_POST['Body']);
if(!filter_var($user_request, FILTER_VALIDATE_EMAIL) === false) {
	//$app_response = 'Thank you, have been subscribed to Realtime. '; // at this point we need to store the email address. 
	//$app_response .= 'What is your first name?';
	$app_response = $responses_array['first_name'];
	$_SESSION['last_question_asked'] = 'first_name'; 
	$_SESSION['user_email'] = $user_request; // place email in session
}else{
	switch($user_request){
		case "subscribe":
			$app_response = $responses_array['subscribe'];
			break;
		case "newsletter":
			$app_response = $responses_array['email'];
			break;
		case "realtime":
			$app_response = 'Please provide your email address';
			break;
		default: // Possibly their first name
			$app_response = $responses_array['first_name'];
			break;
	}
}

//$app_response = strtolower($_POST['Body']);
$xml_response .= '<Response>';
$xml_response .= '<Message>' . $app_response . '</Message>';
$xml_response .= '</Response>';

print $xml_response;


