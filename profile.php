<?php

/*** begin the session ***/
session_start();

if(!isset($_SESSION['user_id'])){
	$message = 'You must be logged in to access this page';
}else{
	try{
		/***connect to database***/
		/*** mysql hostname ***/
		$mysql_hostname = 'localhost';
		
		/*** mysql username ***/
		$mysql_username = 'kirk';
		
		/*** mysql password ***/
		$mysql_password = 'io98IO98';
		
		/*** database name ***/
		$mysql_dbname = 'auth';
		
		/*** select the users name from the database ***/
		$dbh = new PDO("mysql:host=$mysql_hostname;dbname=$mysql_dbname", $mysql_username, $mysql_password);
		
		/*** set the error mode to exceptions ***/
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		/*** prepare the select ***/
		$stmt = $dbh->prepare("SELECT username FROM users WHERE user_id = :user_id");
		
		/*** bind the parameters ***/
		$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		
		/*** execute the prepared statment ***/
		$stmt->execute();
		
		/*** check for a result ***/
		$username = $stmt->fetchColumn();
		
		/*** if we have no somthing is wrong ***/
		if($username == false){
			$message = 'Access Error';
		}else{
			$message = 'Welcome '.$username;
		}
	}
	catch (Exception $e){
		/*** if we are here, something is wrong in the database ***/
		$message = 'We are unable to process your request. Please try again later.';
	}
}
?>
<html>
	<head>
		<title>Profile Page</title>
	</head>
	<body>
		<h2>
			<?php echo $message; ?>
		</h2>
	</body>
</html>