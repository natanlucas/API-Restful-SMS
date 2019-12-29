<?php

abstract class Conection{

	protected function conectaDB()
	{
	   $db = [
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'db' => 'db_sms'
        ];

        try {
            $conn = new PDO("mysql:host={$db['host']};dbname={$db['db']}", $db['username'], $db['password']);

            // set to PDO error modo exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conn;
        } catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }
}

?>