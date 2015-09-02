<?php

$AccountSid = 'AC6fa3566b9df1d9cdf6e2be82f97b7aeb';
$AuthToken = 'b7263ddf23ecb38c8cb59c4e3c56a85b';
$from = '+19252739243';
$to = "+1" . trim($_POST['to_phone_number']);
$body = trim($_POST['message']);
$url = 'https://api.twilio.com/2010-04-01/Accounts/' . $AccountSid . '/Messages.json';

    $msg = "From=".urlencode($from)."&To=".urlencode($to)."&Body=".urlencode($body);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_POST, 1); 
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg); 
    curl_setopt($ch, CURLOPT_USERPWD, $AccountSid . ':' . $AuthToken);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $res = curl_exec($ch);
    curl_close($ch);
	//print '<pre>';
	//print_r($res);
	//print '</pre>';
	$json_result = json_decode($res);
	if($json_result->status == "queued"){
		$response['message'] = "Message has been sent";
	}else{
		$response['message'] = "The following error occured<br/> " . $json_result->message;
		
	}
	print json_encode($response);
	
	