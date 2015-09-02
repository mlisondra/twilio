<?php

$mailchimp_endpoint = 'https://<dc>.api.mailchimp.com/3.0/';
$username = "milder.lisondra@jewsforjesus.org";
$api_key = 'fbc0f66b2b2a96c5743114c0d10cf4a2-us11';


$process = curl_init($host);
curl_setopt($process, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($process, CURLOPT_HEADER, 1);
curl_setopt($process, CURLOPT_USERPWD, $username . ":" . $api_key);
curl_setopt($process, CURLOPT_TIMEOUT, 30);
curl_setopt($process, CURLOPT_POST, 1);
curl_setopt($process, CURLOPT_POSTFIELDS, $payloadName);
curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
$return = curl_exec($process);
curl_close($process);