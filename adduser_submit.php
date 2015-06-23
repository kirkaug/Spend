<?php
/*** bein our session ***/
session_start();

/*** first check that both the username, password and form token have been sent ***/
if(!isset($_POST['username'],$_POST['password'],$_POST['form_token'])){
	$message = 'Please enter a valid username and password';
}

/*** check the form token is valid ***/
elseif($_POST['form_token'] != $_SESSION['form_token']){
	$message = 'Invalid form submission';
}

/*** check the username is correct length ***/
elseif(strlen($_POST['username']) > 20 || strlen($_POST['username']) < 4){
	$message = 'Incorrect Length for Username';
}

/*** check the username has only alphanumeric characters ***/
elseif(ctype_alnum($_POST['username']) != true){
	$message = 'Username must be alpha numeric';
}

/*** check the password has only apha numeric characters ***/
elseif(ctype_alnum($_POST['password']) != true){
	$message = 'Password must be alpha numeric';
}
else{
	/*** if we are here the data is valid and we can insert it ***/
	$username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
	$password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
	
	/*** now we can encrypt the password ***/
	$password = sha1($password);
	
	/*** connect to database ***/
	/**** MySQL variables ****/
	$mysql_hostname = 'localhost';
	$mysql_username = 'kirk';
	$mysql_password = 'io98IO98';
	$mysql_dbname = 'auth';
	
	try{
		$dbh = new PDO("mysql:host=$mysql_hostname;dbname=$mysql_dbname;", $mysql_username, $mysql_password);
		
		/*** set the error mode to exceptions ***/
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		/*** prepare the insert ***/
		$stmt = $dbh->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
		
		/*** bind parameters ***/
		$stmt->bindParam(':username', $username, PDO::PARAM_STR);
		$stmt->bindParam(':password', $password, PDO::PARAM_STR, 40);
		
		/*** execute the prepared statement ***/
		$stmt->execute();
		
		/*** unset the form token session variable ***/
		unset($_SESSION['form_token']);
		
		/*** if all is done, say thanks ***/
		$message = 'New user added';
	} catch(Exception $e){
		/*** check if the username already exists ***/
		if ($e->getCode() == 23000){
			$message = 'Username already exists';
		} else {
			/*** if we are here, somthing has gone wrong with the database ***/
			$message = 'We are unable to process your request. Please try again later. (code:'.$e.')';
		}
	}
}
?>
<html>
	<head>
		<title>Login</title>
	</head>
	<body>
		<p>
			<?php echo $message; ?>
		</p>
	</body>
</html>