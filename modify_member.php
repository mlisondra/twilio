<?php

$list_id = 'd36f7938ca'; // personal
$list_id = '4ec624cff2'; // JFJ account

require("vendor/autoload.php");
$mc = new \VPS\MailChimp('1ec0c9c10a65da0f2ff2930b13158df2-us11'); // personal
$mc = new \VPS\MailChimp('585fd4605ba0afbb77335bbcef033dca-us10'); // JFJ account

// Get specific list
/*
print 'Getting list ' . $list_id;
$endpoint = '/lists/'.$list_id;
$result = $mc->get($endpoint);
print '<pre>'; print_r($result); print '</pre>';
*/

//$endpoint = '/lists/'.$list_id.'/members/9331217d96301e6221cf5b0ee7de690d';
//print $endpoint;
//print '<br/>';

$email = 'mlisondra@me.com';
$email_md5_hash = md5($email); 
$endpoint = '/lists/'.$list_id . '/members/'.$email_md5_hash;
print $endpoint;
	//$email_md5_hash = md5('milder.lisondra@yahoo.com'); 
$result = $mc->patch($endpoint,array('merge_fields' => array('FNAME'=>'Milder John', 'LNAME'=>'Lisondra','JEWISH'=>'No','BELIEVER'=>'Yes')
            ));
print '<pre>'; print_r($result); print '</pre>';
