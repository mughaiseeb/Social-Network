<?php
include('./Class/DB.php');
include('./Class/Login.php');


if(Login::isLoggedIn())
{
	echo "Logged in :) ...Welcome <p \> ";
	echo (Login::isLoggedIn());
}else
{
	echo "Not Logged in :(";
}

?>