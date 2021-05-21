<!DOCTYPE HTML>
<html>
<head>
	<title>Forms</title>
	<link rel="stylesheet" type="text/css" href="assets/style.css"/>
	<script type="text/javascript" src="assets/jquery.min.js"></script>
</head>
<body>
	<h1>Form Manipulations</h1>
	<form name="first-form" method="post" action='handler.php'>
		<legend>Form Name Here</legend>
		<input type="url" name="website" placeholder="Website url..." class="first-form" required="1"/><br/>
		<input type="email" name="email" placeholder="user@example.com" class="first-form" required="1"/><br/>
		<input type="tel" name="phone" placeholder="+265881014250" class="second-form"/><br/>
		<input type="text" name="name" placeholder="Your name" class="second-form"/><br/>
		<input type='hidden' name='nounce' value="^ahqbhbhew09*721"/>
		<input type="submit" value="Submit"/>
	</form>

<script type="text/javascript" src="assets/main.js"></script>
</body>
</html>
