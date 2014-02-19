<?php

extract($_POST);

// MODIFY THESE CONFIGS
define('DB_HOST', 'localhost');
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASS', '');
define('ADMIN_EMAIL', 'admin@website.com');
//--------------------------------------------



try {
	// Connect to mysql.
	$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
} catch(Exception $e) {
	die('db_failed');
}

// check if table exists 
$db->exec('CREATE TABLE IF NOT EXISTS newsletter (email VARCHAR(255), time VARCHAR(255))');


// Check if email is valid 
if ( isset($email) && validEmail($email) ) {
		
	/* check if exists */
	$query = $db->prepare('SELECT COUNT(*) AS count FROM newsletter WHERE email = :email');  
	$query->execute(array(':email' => $email));
	$result = $query->fetch();
			
	if ( $result['count'] == 0 ) { // Email does not exist.
	
		$query = $db->prepare('INSERT INTO newsletter (email, time) VALUES (:email, :time)');  
		$query->execute(array('email' => $email, 'time' => date('Y-m-d H:i:s')));
		
		// Send notification to admin
		$admin_email = 'admin@website.com'; // your email
		$subject = 'New subscriber'; // Subject
		$message = 'Hi admin, you have one new subscriber. This is his/her e-mail address: ' . $email . '.'; // Message
		$headers = "From:" . $email;
		mail($admin_email,$subject,$message,$headers);
	
		echo 'success';
		
	} else { // E-mail exists.
		echo 'subscribed';
	}
	
} else {
	echo 'invalid';
}
	


function validEmail($email=NULL)
{
	return preg_match( "/^[\d\w\/+!=#|$?%{^&}*`'~-][\d\w\/\.+!=#|$?%{^&}*`'~-]*@[A-Z0-9][A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z]{2,6}$/ix", $email );
}
