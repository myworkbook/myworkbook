﻿<?php
/**
 *  DB - A simple database class 
 *
 * @author		Author: Vivek Wicky Aswal. (https://twitter.com/#!/VivekWickyAswal)
 * @git 		https://github.com/indieteq-vivek/PHP-MySQL-PDO-Database-Class
 * @version      0.2ab
 *
 */
require("log.class.php");
  // Datenbank Klasse einbinden

//echo '<br>connect/race.settings.ini.php';



class DB
{
	private $pdo;						# @object, The PDO object
	private $sQuery;					# @object, PDO statement object
	private $bConnected = false;		# @bool ,  Connected to the database
	private $log;						# @object, Object for logging exceptions
	private $parameters;				# @array, The parameters of the SQL query
		
    /**
	*   Default Constructor 
	*	1. Instantiate log class.
	*	2. Connect to database.
	*	3. Creates the parameter array.
	*/
	public function __construct()
	{ 			
		$this->log = new Log();	
		$this->Connect();
		$this->parameters = array();
	}
	
    /** This method makes connection to the database.
	*	
	*	1. Reads the database settings from a ini file. 
	*
	*	Die Datenbank muss jeweils im Modul/Widget mitgegeben werden ! ICR
	*
	*	2. Puts  the ini content into the settings array.
	*	3. Tries to connect to the database.
	*	4. If connection failed, exception is displayed and a log file gets created.
	*/
	private function Connect()
	{
		$host = '178.63.38.102';  // 'localhost';
		$port = '3306';
		$user = 'mwbuser';
		$password = 'aVNtYsW9Z6RfdHss';
		$dbname = 'myworkbook';
		
		$dsn = 'mysql:dbname='.$dbname.';port='.$port.';host='.$host.'';
		try 
		{
			# Read settings from INI file
			$this->pdo = new PDO($dsn, $user, $password);
			# We can now log any exceptions on Fatal error. 
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			# Disable emulation of prepared statements, use REAL prepared statements instead.
			$this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			# Connection succeeded, set the boolean to true.
			$this->bConnected = true;
		}
		catch (PDOException $e) 
		{
			# Write into log
			echo $this->ExceptionLog($e->getMessage());
			die();
		}
	}
    /** Every method which needs to execute a SQL query uses this method.
	*	
	*	1. If not connected, connect to the database.
	*	2. Prepare Query.
	*	3. Parameterize Query.
	*	4. Execute Query.	
	*	5. On exception : Write Exception into the log + SQL query.
	*	6. Reset the Parameters.
	*/	
	private function Init($query,$parameters = "")
	{
	# Connect to database
	if(!$this->bConnected) { $this->Connect(); }
	try {
			# Prepare query
			$this->sQuery = $this->pdo->prepare($query);
			
			# Add parameters to the parameter array	
			$this->bindMore($parameters);

			# Bind parameters
			if(!empty($this->parameters)) {
				foreach($this->parameters as $param)
				{
					$parameters = explode("\x7F",$param);
					$this->sQuery->bindParam($parameters[0],$parameters[1]);
				}		
			}

			# Execute SQL 
			$this->succes 	= $this->sQuery->execute();		
		}
		catch(PDOException $e)
		{
				# Write into log and display Exception
				echo $this->ExceptionLog($e->getMessage(),$this->sQuery->queryString);
				die();
		}

		# Reset the parameters
		$this->parameters = array();
	}
		
				
    /**	@void 
	*
	*	Add the parameter to the parameter array
	*	@param string $para  
	*	@param string $value 
	*/	
	public function bind($para, $value)
	{	
		$this->parameters[sizeof($this->parameters)] = ":" . $para . "\x7F" . $value;
	}
    /**
	*	@void
	*	
	*	Add more parameters to the parameter array
	*	@param array $parray
	*/	
	public function bindMore($parray)
	{
		if(empty($this->parameters) && is_array($parray)) {
			$columns = array_keys($parray);
			foreach($columns as $i => &$column)	{
				$this->bind($column, $parray[$column]);
			}
		}
	}
    /**   	If the SQL query  contains a SELECT statement it returns an array containing all of the result set row
	*	If the SQL statement is a DELETE, INSERT, or UPDATE statement it returns the number of affected rows
	*
	*   @param  string $query
	*	@param  array  $params
	*	@param  int    $fetchmode
	*	@return mixed
	*/			
	public function query($query,$params = null,$fetchmode = PDO::FETCH_ASSOC)
	{
		$query = trim($query);

		$this->Init($query,$params);

		if (stripos($query, 'select') === 0){
			return $this->sQuery->fetchAll($fetchmode);
		}
		elseif (stripos($query, 'insert') === 0 ||  stripos($query, 'update') === 0 || stripos($query, 'delete') === 0) {
			return $this->sQuery->rowCount();	
		}	
		else {
			return NULL;
		}
	}		
    /**
	*	Returns an array which represents a column from the result set 
	*
	*	@param  string $query
	*	@param  array  $params
	*	@return array
	*/	
	public function column($query,$params = null)
	{
		$this->Init($query,$params);
		$Columns = $this->sQuery->fetchAll(PDO::FETCH_NUM);		
		
		$column = null;

		foreach($Columns as $cells) {
			$column[] = $cells[0];
		}

		return $column;
		
	}	
    /**
	*	Returns an array which represents a row from the result set 
	*
	*	@param  string $query
	*	@param  array  $params
	*   @param  int    $fetchmode
	*	@return array
	*/	
	public function row($query,$params = null,$fetchmode = PDO::FETCH_ASSOC)
	{				
		$this->Init($query,$params);
		return $this->sQuery->fetch($fetchmode);			
	}
    /**
	*	Returns the value of one single field/column
	*
	*	@param  string $query
	*	@param  array  $params
	*	@return string
	*/	
	public function single($query,$params = null)
	{
		$this->Init($query,$params);
		return $this->sQuery->fetchColumn();
	}
    /**	
	* Writes the log and returns the exception
	*
	* @param  string $message
	* @param  string $sql
	* @return string
	*/
	private function ExceptionLog($message , $sql = "")
	{
		$exception  = 'Unhandled Exception. <br />';
		$exception .= $message;
		$exception .= "<br /> You can find the error back in the log.";

		if(!empty($sql)) {
			# Add the Raw SQL to the Log
			$message .= "\r\nRaw SQL : "  . $sql;
		}
			# Write into log
			$this->log->write($message);

		return $exception;
	}			
}


// NEU SB 6.11.2013
class DWHDB
{
	private $pdo;						# @object, The PDO object
	private $sQuery;					# @object, PDO statement object
	private $bConnected = false;		# @bool ,  Connected to the database
	private $log;						# @object, Object for logging exceptions
	private $parameters;				# @array, The parameters of the SQL query
		
    /**
	*   Default Constructor 
	*	1. Instantiate log class.
	*	2. Connect to database.
	*	3. Creates the parameter array.
	*/
	public function __construct()
	{ 			
		$this->log = new Log();	
		$this->Connect();
		$this->parameters = array();
	}
	
    /** This method makes connection to the database.
	*	
	*	1. Reads the database settings from a ini file. 
	*
	*	Die Datenbank muss jeweils im Modul/Widget mitgegeben werden ! ICR
	*
	*	2. Puts  the ini content into the settings array.
	*	3. Tries to connect to the database.
	*	4. If connection failed, exception is displayed and a log file gets created.
	*/
	private function Connect()
	{
		$host = '127.0.0.1';  // 'localhost';
		$port = '3306';
		$user = 'root';
		$password = 'fhwi91re';
		$dbname = 'DWHwind';
		
		$dsn = 'mysql:dbname='.$dbname.';port='.$port.';host='.$host.'';
		try 
		{
			# Read settings from INI file
			$this->pdo = new PDO($dsn, $user, $password);
			# We can now log any exceptions on Fatal error. 
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			# Disable emulation of prepared statements, use REAL prepared statements instead.
			$this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			# Connection succeeded, set the boolean to true.
			$this->bConnected = true;
		}
		catch (PDOException $e) 
		{
			# Write into log
			echo $this->ExceptionLog($e->getMessage());
			die();
		}
	}
    /** Every method which needs to execute a SQL query uses this method.
	*	
	*	1. If not connected, connect to the database.
	*	2. Prepare Query.
	*	3. Parameterize Query.
	*	4. Execute Query.	
	*	5. On exception : Write Exception into the log + SQL query.
	*	6. Reset the Parameters.
	*/	
	private function Init($query,$parameters = "")
	{
	# Connect to database
	if(!$this->bConnected) { $this->Connect(); }
	try {
			# Prepare query
			$this->sQuery = $this->pdo->prepare($query);
			
			# Add parameters to the parameter array	
			$this->bindMore($parameters);

			# Bind parameters
			if(!empty($this->parameters)) {
				foreach($this->parameters as $param)
				{
					$parameters = explode("\x7F",$param);
					$this->sQuery->bindParam($parameters[0],$parameters[1]);
				}		
			}

			# Execute SQL 
			$this->succes 	= $this->sQuery->execute();		
		}
		catch(PDOException $e)
		{
				# Write into log and display Exception
				echo $this->ExceptionLog($e->getMessage(),$this->sQuery->queryString);
				die();
		}

		# Reset the parameters
		$this->parameters = array();
	}
		
				
    /**	@void 
	*
	*	Add the parameter to the parameter array
	*	@param string $para  
	*	@param string $value 
	*/	
	public function bind($para, $value)
	{	
		$this->parameters[sizeof($this->parameters)] = ":" . $para . "\x7F" . $value;
	}
    /**
	*	@void
	*	
	*	Add more parameters to the parameter array
	*	@param array $parray
	*/	
	public function bindMore($parray)
	{
		if(empty($this->parameters) && is_array($parray)) {
			$columns = array_keys($parray);
			foreach($columns as $i => &$column)	{
				$this->bind($column, $parray[$column]);
			}
		}
	}
    /**   	If the SQL query  contains a SELECT statement it returns an array containing all of the result set row
	*	If the SQL statement is a DELETE, INSERT, or UPDATE statement it returns the number of affected rows
	*
	*   @param  string $query
	*	@param  array  $params
	*	@param  int    $fetchmode
	*	@return mixed
	*/			
	public function query($query,$params = null,$fetchmode = PDO::FETCH_ASSOC)
	{
		$query = trim($query);

		$this->Init($query,$params);

		if (stripos($query, 'select') === 0){
			return $this->sQuery->fetchAll($fetchmode);
		}
		elseif (stripos($query, 'insert') === 0 ||  stripos($query, 'update') === 0 || stripos($query, 'delete') === 0) {
			return $this->sQuery->rowCount();	
		}	
		else {
			return NULL;
		}
	}		
    /**
	*	Returns an array which represents a column from the result set 
	*
	*	@param  string $query
	*	@param  array  $params
	*	@return array
	*/	
	public function column($query,$params = null)
	{
		$this->Init($query,$params);
		$Columns = $this->sQuery->fetchAll(PDO::FETCH_NUM);		
		
		$column = null;

		foreach($Columns as $cells) {
			$column[] = $cells[0];
		}

		return $column;
		
	}	
    /**
	*	Returns an array which represents a row from the result set 
	*
	*	@param  string $query
	*	@param  array  $params
	*   @param  int    $fetchmode
	*	@return array
	*/	
	public function row($query,$params = null,$fetchmode = PDO::FETCH_ASSOC)
	{				
		$this->Init($query,$params);
		return $this->sQuery->fetch($fetchmode);			
	}
    /**
	*	Returns the value of one single field/column
	*
	*	@param  string $query
	*	@param  array  $params
	*	@return string
	*/	
	public function single($query,$params = null)
	{
		$this->Init($query,$params);
		return $this->sQuery->fetchColumn();
	}
    /**	
	* Writes the log and returns the exception
	*
	* @param  string $message
	* @param  string $sql
	* @return string
	*/
	private function ExceptionLog($message , $sql = "")
	{
		$exception  = 'Unhandled Exception. <br />';
		$exception .= $message;
		$exception .= "<br /> You can find the error back in the log.";

		if(!empty($sql)) {
			# Add the Raw SQL to the Log
			$message .= "\r\nRaw SQL : "  . $sql;
		}
			# Write into log
			$this->log->write($message);

		return $exception;
	}			
}
?>