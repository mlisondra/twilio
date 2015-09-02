<?php


error_reporting('E_ERROR');

require('/vendor/twilio/sdk/Services/Twilio.php');

 
// set your AccountSid and AuthToken from www.twilio.com/user/account
//$AccountSid = "ACfa7310f4bdaf06eab59910df396e40eb";
//$AuthToken = "b7db011c187b61e9012554f00fb476d9";

$account_sid = 'AC6fa3566b9df1d9cdf6e2be82f97b7aeb'; 
$auth_token = 'b7263ddf23ecb38c8cb59c4e3c56a85b'; 
$client = new Services_Twilio($account_sid, $auth_token); 
 print_r($client);
$client->account->messages->create(array( 
	'To' => "+19259808485", 
	'From' => "+19252739243", 
	'Body' => "test",   
));

 print_r($client);
?>
