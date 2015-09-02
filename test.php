<?php 

phpinfo();
$host = '10.5.223.9';
$db_name = 'twadmin_twilio';
$username = '';
$password = '9pyziTxBJbm0';

try {
    $mysqli = new mysqli($host, $username ,$password,$db_name);
    print_r($mysqli);
    
} catch (Exception $e) {
    print $e->getMessage();
}