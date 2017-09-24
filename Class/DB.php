<?php

class DB {
	private static function connect(){
			$pdo = new PDO('mysql:host=127.0.0.1;dbname=sndb;charset=utf8','root','');
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $pdo;

	} 

	public static function quary ($quary , $params =  array())
	{
		$statement = self ::connect()->prepare($quary);
		$statement->execute($params);


		if(explode(' ',$quary)[0]=='SELECT')
		{
			$data = $statement->fetchall();
			return $data;
		}
	} 

}