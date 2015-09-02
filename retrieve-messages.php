<?php

$AccountSid = 'AC6fa3566b9df1d9cdf6e2be82f97b7aeb';
$AuthToken = 'b7263ddf23ecb38c8cb59c4e3c56a85b';
$from = '+19252739243';
$to = '+19259808485';
$body = 'Test via CURL from local';
$url = 'https://api.twilio.com/2010-04-01/Accounts/' . $AccountSid . '/Messages.json';

    //$msg = "From=".urlencode($from)."&To=".urlencode($to)."&Body=".urlencode($body);
	$msg = "From=+19259808485";
	
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
	print '<pre>';
	print_r($res);
	$twilio_request = json_decode($res);
	print '</pre>';
	
	foreach($twilio_request as $key=>$obj){
		//print gettype($obj);
		//print "key: "  . $key;
		if($key == "messages"){
			//print '<pre>'; print_r($obj); print '</pre>';
			foreach($obj as $message_obj){
				 print '<pre>';print_r($message_obj);  print '</pre>';
				//print $message_obj->from;
				if($message_obj->from == "+19259808485"){
					print $message_obj->body; print '<br/>';
					/*
					$AccountSid = 'AC6fa3566b9df1d9cdf6e2be82f97b7aeb';
					$AuthToken = 'b7263ddf23ecb38c8cb59c4e3c56a85b';
					$from = '+19252739243';
					$to = '+19259808485';
					$body = 'Hi Milder';
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
						print '<pre>';
						print_r($res);
						print '</pre>';
						*/
	
				}
			}
		}
		
		//print '<pre>'; print_r($obj['messages']); print '</pre>';
	}
	
	