<?php
namespace Mrxlc\Connection;

use Symfony\Component\Dotenv\Dotenv;

use \PDO;

class Connection
{
	


	public function __construct($hostName = null, $dbName = null, $dbUsername = null, $dbPassword = null)
	{
		$dotenv = new Dotenv();
		$dotenv->load(__DIR__.'/.env');
		$_hostName = $hostName ?: $_ENV('DB_HOSTNAME');
		$_dbName = $dbName ?: $_ENV('DB_NAME');
		$_dbUsername = $dbUsername ?: $_ENV('DB_USERNAME');
		$_dbPassword = $dbPassword ?: $_ENV('DB_PASSWORD');


		try
		{
			# MySQL with PDO_MYSQL
			$this->connection = new \PDO("mysql:host=$_hostName; dbname=$_dbName", $_dbUsername, $_dbPassword);
			$this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			$this->sentence("SET time_zone='-04:00';");
		}
		catch (PDOException $e)
		{
			$this->connection = null;
			die($e->getMessage());
		}
	}


	public function sentence($query)
	{
		# create a prepared statement
		$stmt = $this->connection->prepare($query);

		if($stmt)
		{
			# execute query
			$stmt->execute();
			return $stmt;
		}
		else
		{
			return self::get_error();
		}
	}

	#display error
	public function get_error()
	{
		$this->connection->errorInfo();
	}

	# closes the database connection when object is destroyed.
	public function __destruct()
	{
		$this->connection = null;
	}

	public function lastInsertId(){
		return $this->connection->lastInsertId();
	}
}