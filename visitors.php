<?php

$host = '199.101.145.32';
$db_name = 'twadmin_twilio';
$username = 'twadmin_twilio';
$password = '9pyziTxBJbm0';

try {
    $mysqli_conn = new mysqli($host, $username ,$password,$db_name);
} catch (Exception $e) {
    print $e->getMessage();
}

$sql = "SELECT * FROM visitors";
$result = $mysqli_conn->query($sql);


?>

<body>
<head>
	<style type="text/css">
		td.heading {
			width: 180px;
			font-weight:bold;
		}
	</style>
</head>
<div id="main_content">
	<h2>Twilio text submissions</h2>
<table>
<?php
if ($result->num_rows > 0) {
    // output data of each row
	?>
	<tr>
		<td class="heading">Email</td>
		<td class="heading">First Name</td>
		<td class="heading">Last Name</td>
		<td class="heading">Jewish</td>
		<td class="heading">Believer</td>
		<td class="heading">Last updated</td>
	</tr>
	<?php
    while($row = $result->fetch_array(MYSQLI_ASSOC)) {
		extract($row);
	?>
		<tr>
		<td><?php print $email; ?></td>
		<td><?php print $first_name; ?></td>
		<td><?php print $last_name; ?></td>
		<td><?php print $jewish; ?></td>
		<td><?php print $believer; ?></td>
		<td><?php print $last_modified; ?></td>
		</tr>
	<?php
    }
} else { ?>
   <tr><td>No records found</td></tr>
<?php } ?>

</table>
</div>
</body>