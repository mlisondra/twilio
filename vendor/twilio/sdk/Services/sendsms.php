<?php
 //ini_set('display_errors',1);
//error_reporting('E_ALL');

//require "Twilio.php";
require('/vendor/twilio/sdk/Services/Twilio.php'); 

 
// set your AccountSid and AuthToken from www.twilio.com/user/account
$AccountSid = "ACfa7310f4bdaf06eab59910df396e40eb";
$AuthToken = "b7db011c187b61e9012554f00fb476d9";

$client = new Services_Twilio($AccountSid, $AuthToken);
print '<pre>'; print_r(); print '</pre>';

$client->account->messages->create(array( 
	'To' => "+19259808485", 
	'From' => "+19252739243", 
	'Body' => "Test via web page",   
));

print '<pre>'; print_r(); print '</pre>';