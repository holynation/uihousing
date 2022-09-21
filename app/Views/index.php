<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>testing the forms</title>
</head>
<body>
	<form action="<?php echo base_url('test/form'); ?>" method="post">
		<input type="text" name="username" />
		<button type="submit" name="btnSubmit">Submit</button>
	</form>
</body>
</html>