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

$list_id = 'd36f7938ca'; // personal
$list_id = '4ec624cff2'; // JFJ account

require("vendor/autoload.php");
$mc = new \VPS\MailChimp('1ec0c9c10a65da0f2ff2930b13158df2-us11'); // personal
$mc = new \VPS\MailChimp('585fd4605ba0afbb77335bbcef033dca-us10'); // JFJ account

//$result = $mc->get('/lists/');
//print '<pre>'; print_r($result); print '</pre>';

$result = $mc->get('/lists/' . $list_id . '/members');
//print '<pre>'; print_r($result); print '</pre>';


/*
$result = $mc->post('/lists/'.$list_id.'/members', array(
                'email_address' => 'mlisondra@yahoo.com',
                //'merge_fields' => array('FNAME'=>'Milder', 'LNAME'=>'Lisondra'),
                'status' => 'subscribed'
            ));
	*/		
	$endpoint = '/lists/'.$list_id.'/members/9331217d96301e6221cf5b0ee7de690d';

	//$email_md5_hash = md5('milder.lisondra@yahoo.com'); 
$result = $mc->delete($endpoint);
print '<pre>'; print_r($result); print '</pre>';
