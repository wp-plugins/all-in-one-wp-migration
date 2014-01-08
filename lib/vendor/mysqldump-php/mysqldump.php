<?php

class Mysqldump
{
	const MAXLINESIZE = 1000000;

	// This can be set both on constructor or manually
	public $host;
	public $user;
	public $pass;
	public $db;
	public $fileName = 'dump.sql';

	// Internal stuff
	private $settings = array();
	private $tables = array();
	private $views = array();
	private $dbHandler;
	private $defaultSettings = array(
		'include-tables' => array(),
		'exclude-tables' => array(),
		'compress' => CompressMethod::NONE,
		'no-data' => false,
		'add-drop-table' => false,
		'single-transaction' => true,
		'lock-tables' => false,
		'add-locks' => true,
		'extended-insert' => true
	);
	private $compressManager;

	/**
	 * Constructor of Mysqldump. Note that in the case of an SQLite database connection, the filename must be in the $db parameter.
	 *
	 * @param string $db        Database name
	 * @param string $user      SQL account username
	 * @param string $pass      SQL account password
	 * @param string $host      SQL server to connect to
	 * @return null
	 */
	public function __construct($db = '', $user = '', $pass = '', $host = 'localhost', $type="mysql", $settings = null, $pdo_options = array(PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION))
	{
		$this->db = $db;
		$this->user = $user;
		$this->pass = $pass;
		$this->host = $host;
		$this->type = strtolower($type);
		$this->pdo_options = $pdo_options;
		$this->set($settings);
	}

	/**
	 * jquery style extend, merges arrays (without errors if the passed
	 * values are not arrays)
	 *
	 * @param array $args       default settings
	 * @param array $extended   user settings
	 *
	 * @return array $extended  merged user settings with default settings
	 */
	public function extend()
	{
		$args = func_get_args();
		$extended = array();
		if (is_array($args) && count($args) > 0) {
			foreach ($args as $array) {
				if (is_array($array)) {
					$extended = array_merge($extended, $array);
				}
			}
		}

		return $extended;
	}


	/**
	 * Set new settings
	 *
	 * @return void
	 */
	public function set($settings)
	{
		$this->settings = $this->extend($this->defaultSettings, $settings);
	}

	/**
	 * Connect with PDO
	 *
	 * @return bool
	 */
	private function connect()
	{
		// Connecting with PDO
		try {
			switch ($this->type){
				case 'sqlite':
					$this->dbHandler = new PDO("sqlite:" . $this->db, null, null, $this->pdo_options);
					break;

				case 'mysql': case 'pgsql': case 'dblib':
					$this->dbHandler = new PDO($this->type . ":host=" . $this->host.";dbname=" . $this->db, $this->user, $this->pass, $this->pdo_options);
					// Fix for always-unicode output
					$this->dbHandler->exec("SET NAMES utf8");
					break;

				default:
					throw new \Exception("Unsupported database type: " . $this->type, 3);
			}
		} catch (PDOException $e) {
			throw new \Exception("Connection to " . $this->type . " failed with message: " .
			$e->getMessage(), 3);
		}

		$this->dbHandler->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL);
		$this->adapter = new TypeAdapter($this->type);
	}

	/**
	 * Main call
	 *
	 * @param string $filename  Name of file to write sql dump to
	 * @param array  $clauses   Query parameters
	 * @return bool
	 */
	public function start($filename = '', $clauses = array())
	{
		// Output file can be redefined here
		if ( !empty($filename) ) {
			$this->fileName = $filename;
		}

		// We must set a name to continue
		if ( empty($this->fileName) ) {
			throw new \Exception("Output file name is not set", 1);
		}

		// Connect to database
		$this->connect();

		// Create a new compressManager to manage compressed output
		$this->compressManager = CompressManagerFactory::create($this->settings['compress']);

		if (! $this->compressManager->open($this->fileName)) {
			throw new \Exception("Output file is not writable", 2);
		}

		// Formating dump file
		$this->compressManager->write($this->getHeader());

		// Listing all tables from database
		$this->tables = array();
		foreach ($this->dbHandler->query($this->adapter->show_tables($this->db)) as $row) {
			if (empty($this->settings['include-tables']) || (! empty($this->settings['include-tables']) && in_array(current($row), $this->settings['include-tables'], true))) {
				array_push($this->tables, current($row));
			}
		}

		// Exporting tables one by one
		foreach ($this->tables as $table) {
			if (in_array($table, $this->settings['exclude-tables'], true)) {
				continue;
			}

			$is_table = $this->getTableStructure($table);
			if (true === $is_table) {
				if (false === $this->settings['no-data']) {
					$this->listValues($table, $clauses);
				} else if (isset($clauses[$table])) {
					$this->listValues($table, $clauses);
				}
			}
		}

		// Exporting views one by one
		foreach ($this->views as $view) {
			$this->compressManager->write($view);
		}

		//$this->compressManager->close();
	}

	/**
	 * Get current file name
	 *
	 * @return string
	 */
	public function getFileName()
	{
		return $this->fileName;
	}

	/**
	 * Truncate database
	 *
	 * @return void
	 */
	public function truncateDatabase()
	{
		// Connect to database
		$this->connect();

		foreach ($this->dbHandler->query($this->adapter->show_tables($this->db)) as $row) {
			// Drop table
			$this->dbHandler->query($this->adapter->drop_table($row['tbl_name']));
		}
	}

	/**
	 * Import database from file
	 *
	 * @return void
	 */
	public function importFromFile($file)
	{
		if (!is_resource($file)) {
			$file = fopen($file, 'r');
		}

		// Read database file
		$sql = stream_get_contents($file);

		return $this->dbHandler->query($sql);
	}

	/**
	 * Returns list of tables
	 *
	 * @return array
	 */
	public function listTables()
	{
		// Connect to database
		$this->connect();

		$result = array();
		foreach ($this->dbHandler->query($this->adapter->show_tables($this->db)) as $row) {
			$result[] = $row['tbl_name'];
		}

		return $result;
	}

	/**
	 * Returns header for dump file
	 *
	 * @return null
	 */
	private function getHeader()
	{
		// Some info about software, source and time
		$header = "-- All In One WP Migration SQL Dump\n" .
				"-- http://servmask.com/\n" .
				"--\n" .
				"-- Host: {$this->host}\n" .
				"-- Generation Time: " . date('r') . "\n\n" .
				"--\n" .
				"-- Database: `{$this->db}`\n" .
				"--\n\n";

		return $header;
	}

	/**
	 * Table structure extractor
	 *
	 * @param string $tablename  Name of table to export
	 * @return null
	 */
	private function getTableStructure($tablename)
	{
		$stmt = $this->adapter->show_create_table($tablename);
		foreach ($this->dbHandler->query($stmt) as $r) {
			if (isset($r['Create Table'])) {
				$this->compressManager->write("-- " .
					"--------------------------------------------------------" .
					"\n\n" .
					"--\n" .
					"-- Table structure for table `$tablename`\n--\n\n");

				if ($this->settings['add-drop-table']) {
					$this->compressManager->write("DROP TABLE IF EXISTS `$tablename`;\n\n");
				}

				$this->compressManager->write($r['Create Table'] . ";\n\n");

				return true;
			}
			if ( isset($r['Create View']) ) {
				$view  = "-- " .
						"--------------------------------------------------------" .
						"\n\n";
				$view .= "--\n-- Table structure for view `$tablename`\n--\n\n";
				$view .= $r['Create View'] . ";\n\n";
				$this->views[] = $view;

				return false;
			}
		}
	}

	/**
	 * Table rows extractor
	 *
	 * @param string $tablename  Name of table to export
	 * @param array  $clauses    Query parameters
	 * @return null
	 */
	private function listValues($tablename, $clauses = array())
	{
		$this->compressManager->write(
			"--\n" .
			"-- Dumping data for table `$tablename`\n" .
			"--\n\n"
		);

		if ($this->settings['single-transaction']) {
			$this->dbHandler->exec($this->adapter->start_transaction());
		}

		if ($this->settings['lock-tables']) {
			$lockstmt = $this->adapter->lock_table($tablename);
			if(strlen($lockstmt)){
				$this->dbHandler->exec($lockstmt);
			}
		}

		if ( $this->settings['add-locks'] ) {
			$this->compressManager->write($this->adapter->start_add_lock_table($tablename));
		}

		$onlyOnce = true; $lineSize = 0;
		$stmt = "SELECT * FROM `$tablename` ";

		// Add query parameters
		if (isset($clauses[$tablename]) && ($clause_query = $clauses[$tablename])) {
			$stmt .= $clause_query;
		}

		foreach ($this->dbHandler->query($stmt, PDO::FETCH_NUM) as $r) {
			$vals = array();
			foreach ($r as $val) {
				$vals[] = is_null($val) ? "NULL" :
				$this->dbHandler->quote($val);
			}
			if ($onlyOnce || !$this->settings['extended-insert'] ) {
				$lineSize += $this->compressManager->write("INSERT INTO `$tablename` VALUES (" . implode(",", $vals) . ")");
				$onlyOnce = false;
			} else {
				$lineSize += $this->compressManager->write(",(" . implode(",", $vals) . ")");
			}
			if ( ($lineSize > Mysqldump::MAXLINESIZE) ||
					!$this->settings['extended-insert'] ) {
				$onlyOnce = true;
				$lineSize = $this->compressManager->write(";\n");
			}
		}

		if (! $onlyOnce) {
			$this->compressManager->write(";\n");
		}

		if ($this->settings['add-locks']) {
			$this->compressManager->write($this->adapter->end_add_lock_table($tablename));
		}

		if ($this->settings['single-transaction']) {
			$this->dbHandler->exec($this->adapter->commit_transaction());
		}

		if ($this->settings['lock-tables']) {
			$lockstmt = $this->adapter->unlock_table($tablename);
			if(strlen($lockstmt)){
				$this->dbHandler->exec($lockstmt);
			}
		}
	}
}

/**
 * Enum with all available compression methods
 *
 */
abstract class CompressMethod
{
	const NONE = 0;
	const GZIP = 1;
	const BZIP2 = 2;

	public static $enums = array(
		"None",
		"Gzip",
		"Bzip2"
	);

	public static function isValid($c)
	{
		return in_array($c, self::$enums);
	}
}

abstract class CompressManagerFactory
{
	private $fileHandle = null;

	public static function create($c)
	{
		$c = ucfirst(strtolower($c));
		if (! CompressMethod::isValid($c)) {
			throw new \Exception("Compression method is invalid", 1);
		}

		$method = "Compress" . $c;

		return new $method();
	}
}

class CompressBzip2 extends CompressManagerFactory
{
	public function __construct()
	{
		if (! function_exists("bzopen")) {
			throw new \Exception("Compression is enabled, but bzip2 lib is not installed or configured properly", 1);
		}
	}

	public function open($filename)
	{
		$this->fileHandler = bzopen($filename, "w");
		if (false === $this->fileHandler) {
			return false;
		}

		return true;
	}

	public function write($str)
	{
		$bytesWritten = 0;
		if (false === ($bytesWritten = bzwrite($this->fileHandler, $str))) {
			throw new \Exception("Writting to file failed! Probably, there is no more free space left?", 4);
		}

		return $bytesWritten;
	}

	public function close()
	{
		return bzclose($this->fileHandler);
	}
}

class CompressGzip extends CompressManagerFactory
{
	public function __construct()
	{
		if (! function_exists("gzopen") ) {
			throw new \Exception("Compression is enabled, but gzip lib is not installed or configured properly", 1);
		}
	}

	public function open($filename)
	{
		$this->fileHandler = gzopen($filename, "wb");
		if (false === $this->fileHandler) {
			return false;
		}

		return true;
	}

	public function write($str)
	{
		$bytesWritten = 0;
		if (false === ($bytesWritten = gzwrite($this->fileHandler, $str))) {
			throw new \Exception("Writting to file failed! Probably, there is no more free space left?", 4);
		}

		return $bytesWritten;
	}

	public function close()
	{
		return gzclose($this->fileHandler);
	}
}

class CompressNone extends CompressManagerFactory
{
	public function open($filename)
	{
		$this->fileHandler = fopen($filename, "wb");
		if (false === $this->fileHandler) {
			return false;
		}

		return true;
	}

	public function write($str)
	{
		$bytesWritten = 0;
		if (false === ($bytesWritten = fwrite($this->fileHandler, $str))) {
			throw new \Exception("Writting to file failed! Probably, there is no more free space left?", 4);
		}

		return $bytesWritten;
	}

	public function close()
	{
		return fclose($this->fileHandler);
	}
}

class TypeAdapter
{
	public function __construct($type){
		$this->type = $type;
	}

	public function show_create_table($tablename){
		switch($this->type){
			case 'sqlite':
				return "select tbl_name as 'Table', sql as 'Create Table' from sqlite_master where type='table' and tbl_name='$tablename'";
			default:
				return "SHOW CREATE TABLE `$tablename`";
		}
	}

	public function drop_table($tablename){
		switch($this->type){
			case 'sqlite':
				return "drop table if exists '$tablename'";
			default:
				return "DROP TABLE IF EXISTS `$tablename`";
		}
	}

	public function show_tables($dbName){
		switch($this->type){
			case 'sqlite':
				return "SELECT tbl_name FROM sqlite_master where type='table'";
			default:
				return "SELECT TABLE_NAME AS tbl_name FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE='BASE TABLE' AND TABLE_SCHEMA='$dbName'";
		}
	}

	public function start_transaction(){
		switch($this->type){
			case 'sqlite':
				return "BEGIN EXCLUSIVE";
			default:
				return "SET GLOBAL TRANSACTION ISOLATION LEVEL REPEATABLE READ; START TRANSACTION";
		}
	}

	public function commit_transaction(){
		switch($this->type){
			case 'sqlite':
				return "COMMIT";
			default:
				return "SET GLOBAL TRANSACTION ISOLATION LEVEL REPEATABLE READ; START TRANSACTION";
		}
	}

	public function lock_table($tablename){
		switch($this->type){
			case 'sqlite':
				return "";
			default:
				return "LOCK TABLES `$tablename` READ LOCAL";
		}
	}

	public function unlock_table($tablename){
		switch($this->type){
			case 'sqlite':
				return "";
			default:
				return "UNLOCK TABLES";
		}
	}

	public function start_add_lock_table($tablename){
		switch($this->type){
			case 'sqlite':
				return "\n";
			default:
				return "LOCK TABLES `$tablename` WRITE;\n";
		}
	}

	public function end_add_lock_table($tablename){
		switch($this->type){
			case 'sqlite':
				return "\n";
			default:
				return "UNLOCK TABLES;\n";
		}
	}
}
