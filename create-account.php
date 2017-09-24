<?php

include('Class/DB.php');

if (isset($_POST['createaccount']))
{
	// assing text felds to vars
	$username = $_POST['username'];
	$password = $_POST['password'];
	$email = $_POST['email'];
	

	//check if user name is exists or not 
	if ( !DB::quary('SELECT username FROM users WHERE username =:username',array(':username'=>$username )))//check the username if exists
			{
			if(strlen($username)>=3 && strlen($username)<=32){ //check the length of username
				if(preg_match('/[a-zA-Z0-9_]+/',$username)){	//check the chars in username
					if (strlen($password)>=6 && strlen($password)<= 60  ) //check the length of password
						{	
						
						if(filter_var($email,FILTER_VALIDATE_EMAIL)){ //check the email if correct
						   if ( !DB::quary('SELECT email FROM users WHERE email =:email',array(':email'=>$email ))){ //check the email if exists
					
					
								//add new user with encrypted password 
								DB::quary('INSERT INTO users VALUES (\'\',:username , :password ,:email , \'0\')', array(':username'=>$username ,':password'=>password_hash($password,PASSWORD_BCRYPT) , ':email'=> $email));
								echo "Success Creating New Account!";
							}	
							else {
							echo"The E-mail address is used for another account please try new one";
							}

						}else {echo "invaled Email";}
					}else {echo"invaled password ...must be more than 6 character and less than 60";}
				}else{echo"invaled user name!";}
			 }else {echo "User name must be more than 3 and less than 32";}

			}

			else {
				echo"The user name is already exists...Try another name";

				 }


}

?>




<h1>Register</h1>
<form action="create-account.php" method="post">

<input type="text" name="username" value="" placeholder="Username..."><p />
<input type="password" name="password" value="" placeholder="Password..."><p />
<input type="email" name="email" value="" placeholder="someone@somesite.com..."><p />
<input type="submit" name="createaccount" value="Create Account">
</form>