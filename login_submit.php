<?php

/*** begin our sesssion ***/
session_start();

/*** check if the user is already logged in ***/
if(isset($_SESSION['user_id'])){
	$message = 'User is already logged in';
}

/*** check that both the username, password have been submitted ***/
if(!isset($_POST['username'], $_POST['password'])){
	$message = 'Please enter a valid username and password';
}

/*** check the username is the correct length ***/
elseif(strlen($_POST['username']) > 20 || strlen($_POST['username']) < 4){
	$message = 'Incorrect length for username';
}

/*** check the password is the correct length ***/
elseif(strlen($_POST['password']) > 20 || strlen($_POST['password']) < 4){
	$message = 'Incorrect length for password';
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
	/*** if we are here the data is valid and we can select it ***/
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
		
		/*** prepare the select ***/
		$stmt = $dbh->prepare("SELECT user_id, username, password FROM users WHERE username = :username AND password = :password");
		
		/*** bind parameters ***/
		$stmt->bindParam(':username', $username, PDO::PARAM_STR);
		$stmt->bindParam(':password', $password, PDO::PARAM_STR, 40);
		
		/*** execute the prepared statement ***/
		$stmt->execute();
		
		/*** check for a result ***/
		$user_id = $stmt->fetchColumn();
		
		/*** if we have no result then fail boat ***/
		if($user_id == false){
			$message = 'Login Failed';
		}
		/*** if we do have a result, all is well ***/
		else{
			/*** set the session user_id variable ***/
			$_SESSION['user_id'] = $user_id;
			
			/*** tell the user we are logged in ***/
			$message = 'You are now logged in';
		}
	} catch(Exception $e){
		/*** Something has gone wrong in the database ***/
		$message = 'We are unable to complete your request. Please try again later.';
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