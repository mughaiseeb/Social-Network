<?php
include('./Class/DB.php');
if (isset($_POST['resetpassword'])) {
        $cstrong = True;
        $token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
        $email = $_POST['email'];
        //when we work on online server we shoud use mail(to, subject, message)
        //but now we are working on localhost so we are going to use token method 

        $user_id = DB::quary('SELECT id FROM users WHERE email=:email', array(':email'=>$email))[0]['id'];
        DB::quary('INSERT INTO password_tokens VALUES (\'\', :token, :user_id)', array(':token'=>sha1($token), ':user_id'=>$user_id));
        echo 'Email sent!';
        echo '<br />';
        echo $token;
}
?>
<h1>Forgot Password</h1>
<form action="forgot-password.php" method="post">
        <input type="text" name="email" value="" placeholder="Email ..."><p />
        <input type="submit" name="resetpassword" value="Reset Password">
</form>