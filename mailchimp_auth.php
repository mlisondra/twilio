<?php
/*
$mailchimp_endpoint = 'https://us11.api.mailchimp.com/3.0/';
$username = "milder.lisondra@jewsforjesus.org";
$api_key = '1ec0c9c10a65da0f2ff2930b13158df2-us11';


$process = curl_init($mailchimp_endpoint);
curl_setopt($process, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($process, CURLOPT_HEADER, 1);
curl_setopt($process, CURLOPT_USERPWD, $username . ":" . $api_key);
curl_setopt($process, CURLOPT_TIMEOUT, 30);
curl_setopt($process, CURLOPT_POST, 1);

//print_r($process);

//curl_setopt($process, CURLOPT_POSTFIELDS, $payloadName);
curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
$return = curl_exec($process);
print_r($return);
curl_close($process);
*/
require("vendor/autoload.php");
$mc = new \VPS\MailChimp('1ec0c9c10a65da0f2ff2930b13158df2-us11');
$result = $mc->get('/lists/');
//print '<pre>'; print_r($result); print '</pre>';

$result = $mc->get('/lists/d36f7938ca/members');
//print '<pre>'; print_r($result); print '</pre>';

$list_id = 'd36f7938ca';

$result = $mc->post('/lists/'.$list_id.'/members', array(
                'email_address' => 'milder.lisondra@yahoo.com',
                'merge_fields' => array('FNAME'=>'Milder', 'LNAME'=>'Lisondra'),
                'status' => 'subscribed'
            ));
			
print '<pre>'; print_r($result); print '</pre>';