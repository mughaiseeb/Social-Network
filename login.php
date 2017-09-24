<?php
include('Class/DB.php');
include('./Class/Login.php');

if (Login::isLoggedIn()) {
        die("You are already logged in ...if you want to login with another account please logout first");
}

if(isset($_POST['login']))
{
	$username = $_POST['username'];
	$password = $_POST['password'];

	if (DB::quary('SELECT username FROM users WHERE username =:username',array(':username'=>$username )))
	{
		if(password_verify($password, DB::quary('SELECT password FROM users WHERE username =:username',array(':username'=>$username ))[0]['password']))
		{
			echo "Logged in :) welcome ";
			$cstrong = True;
			$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
			$user_id = DB::quary('SELECT id FROM users WHERE username = :username',array(':username'=>$username))[0]['id'];
			DB::quary('INSERT INTO login_tokens VALUES (\'\', :token , :user_id) ' ,array(':token'=>sha1($token) , ':user_id'=>$user_id));
			setcookie("SNID",$token, time()+ 60*60*24*7 , '/' ,NULL, NULL , TRUE );
			setcookie("SIND_",'1',time()+ 60*60*24*3 , '/' ,NULL, NULL , TRUE );
			
		}else
			{
				echo "Incorrect Password ...";
			}

	}else {echo "User dosn't exists!";}
}

?>







<h1>Login to your account</h1>
<form action="login.php" method="post">
	<input type="text" name="username" placeholder="Username..."><p />
	<input type="password" name="password" placeholder="Password..."><p />
	<input type="submit" name="login" value="Login"><p />
</form>