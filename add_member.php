<?php 

$apikey = '1ec0c9c10a65da0f2ff2930b13158df2-us11';
            $auth = base64_encode( 'milder.lisondra@jewsforjesus.org:'.$apikey );
$email = 'milder.lisondra@lisondraconsulting.com';

            $data = array(
                'apikey'        => $apikey,
                'email_address' => $email,
                'status'        => 'subscribed'
            );
            $json_data = json_encode($data);
//print_r($json_data);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://u1.api.mailchimp.com/3.0/lists/d36f7938ca/members/');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                                                        'Authorization: Basic '.$auth));
            curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);                                                                                                                  
//print_r($ch);
            $result = curl_exec($ch);
//print_r($result);
            //var_dump($result);
           // die('Mailchimp executed');
require("vendor/autoload.php");			
use \DrewM\MailChimp\MailChimp;

$MailChimp = new \Drewm\MailChimp('1ec0c9c10a65da0f2ff2930b13158df2-us11');
print_r($MailChimp->get('lists'));