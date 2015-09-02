<html>
<head>
	<style type="text/css">
		textarea {
			width: 200px;
			height: 120px;
			resize:none;
		}
	</style>
</head>
<body>
	<form name="sendtext" id="sendtext" method="post">
		Phone number: <input type="text" name="to_phone_number" id="to_phone_number"><br/><br/>
		Text message<br/><textarea name="message" id="message"></textarea><br/><br/>
		<input type="submit" value="Submit">
	</form>
	<div id="notification"></div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script>
	$(document).ready(function(){
		$("#sendtext").submit(function(e){
			e.preventDefault();
			var params = $(this).serialize();
			console.log(params);
			$.post("controller.php",params,function(data){
				console.log(data);
				$("#notification").html(data.message);
				
			},"json");
		});
	});
</script>
</html>