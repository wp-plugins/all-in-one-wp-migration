<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * MysqlDumpSQL class
 *
 * PHP version 5
 *
 * LICENSE: Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @category  Databases
 * @package   MysqlDumpFactory
 * @author    Yani Iliev <yani@iliev.me>
 * @author    Bobby Angelov <bobby@servmask.com>
 * @copyright 2014 Yani Iliev, Bobby Angelov
 * @license   https://raw.github.com/yani-/mysqldump-factory/master/LICENSE The MIT License (MIT)
 * @version   GIT: 2.2.0
 * @link      https://github.com/yani-/mysqldump-factory/
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'MysqlDumpInterface.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'MysqlFileAdapter.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'MysqlUtility.php';

/**
 * MysqlDumpSQL class
 *
 * @category  Databases
 * @package   MysqlDumpFactory
 * @author    Yani Iliev <yani@iliev.me>
 * @author    Bobby Angelov <bobby@servmask.com>
 * @copyright 2014 Yani Iliev, Bobby Angelov
 * @license   https://raw.github.com/yani-/mysqldump-factory/master/LICENSE The MIT License (MIT)
 * @version   GIT: 2.2.0
 * @link      https://github.com/yani-/mysqldump-factory/
 */
class MysqlDumpSQL implements MysqlDumpInterface
{
	protected $hostname           = null;

	protected $username           = null;

	protected $password           = null;

	protected $database           = null;

	protected $fileName           = 'database.sql';

	protected $fileAdapter        = null;

	protected $connection         = null;

	protected $oldTablePrefix     = null;

	protected $newTablePrefix     = null;

	protected $oldReplaceValues   = array();

	protected $newReplaceValues   = array();

	protected $queryClauses       = array();

	protected $tablePrefixColumns = array();

	/**
	 * Define MySQL credentials for the current connection
	 *
	 * @param  string $hostname MySQL Hostname
	 * @param  string $username MySQL Username
	 * @param  string $password MySQL Password
	 * @param  string $database MySQL Database
	 * @return void
	 */
	public function __construct($hostname = 'localhost', $username = '', $password = '', $database = '')
	{
		// Set MySQL credentials
		$this->hostname = $hostname;
		$this->username = $username;
		$this->password = $password;
		$this->database = $database;
	}

	/**
	 * Export database into a file
	 *
	 * @return void
	 */
	public function export()
	{
		// Set File Adapter
		$this->fileAdapter = new MysqlFileAdapter();

		// Set output file
		$this->fileAdapter->open($this->getFileName());

		// Write Headers Formatting dump file
		$this->fileAdapter->write($this->getHeader());

		// Listing all tables from database
		$tables = $this->listTables();

		// Export Tables
		foreach ($tables as $table) {
			$isTable = $this->getTableStructure($table);
			if ($isTable) {
				$this->listValues($table);
			}
		}

		// Close File Adapter
		$this->fileAdapter->close();
	}

	/**
	 * Set output file name
	 *
	 * @param  string $fileName Name of the output file
	 * @return MysqlDumpSQL
	 */
	public function setFileName($fileName)
	{
		$this->fileName = $fileName;

		return $this;
	}

	/**
	 * Get output file name
	 *
	 * @return string
	 */
	public function getFileName()
	{
		return $this->fileName;
	}

	/**
	 * Set old table prefix
	 *
	 * @param  string $prefix Name of the table prefix
	 * @return MysqlDumpSQL
	 */
	public function setOldTablePrefix($prefix)
	{
		$this->oldTablePrefix = $prefix;

		return $this;
	}

	/**
	 * Get old table prefix
	 *
	 * @return string
	 */
	public function getOldTablePrefix()
	{
		return $this->oldTablePrefix;
	}

	/**
	 * Set new table prefix
	 *
	 * @param  string $prefix Name of the table prefix
	 * @return MysqlDumpSQL
	 */
	public function setNewTablePrefix($prefix)
	{
		$this->newTablePrefix = $prefix;

		return $this;
	}

	/**
	 * Get new table prefix
	 *
	 * @return string
	 */
	public function getNewTablePrefix()
	{
		return $this->newTablePrefix;
	}

	/**
	 * Set old replace values
	 *
	 * @param  array $values List of values
	 * @return MysqlDumpSQL
	 */
	public function setOldReplaceValues($values)
	{
		$this->oldReplaceValues = $values;

		return $this;
	}

	/**
	 * Get old replace values
	 *
	 * @return array
	 */
	public function getOldReplaceValues()
	{
		return $this->oldReplaceValues;
	}

	/**
	 * Set new replace values
	 *
	 * @param  array $values List of values
	 * @return MysqlDumpSQL
	 */
	public function setNewReplaceValues($values)
	{
		$this->newReplaceValues = $values;

		return $this;
	}

	/**
	 * Get new replace values
	 *
	 * @return array
	 */
	public function getNewReplaceValues()
	{
		return $this->newReplaceValues;
	}

	/**
	 * Set query clauses
	 *
	 * @param  array $clauses List of SQL query clauses
	 * @return MysqlDumpSQL
	 */
	public function setQueryClauses($clauses)
	{
		$this->queryClauses = $clauses;

		return $this;
	}

	/**
	 * Get query clauses
	 *
	 * @return array
	 */
	public function getQueryClauses()
	{
		return $this->queryClauses;
	}

	/**
	 * Set table prefix columns
	 *
	 * @param  string $table   Table name
	 * @param  array  $columns Table columns
	 * @return MysqlDumpSQL
	 */
	public function setTablePrefixColumns($table, $columns)
	{
		foreach ($columns as $column) {
			$this->tablePrefixColumns[$table][$column] = true;
		}

		return $this;
	}

	/**
	 * Get table prefix columns
	 *
	 * @param  string $table Table name
	 * @return array
	 */
	public function getTablePrefixColumns($table)
	{
		if (isset($this->tablePrefixColumns[$table])) {
			return $this->tablePrefixColumns[$table];
		}

		return array();
	}

	/**
	 * Get MySQL version
	 *
	 * @return string
	 */
	public function getVersion() {
		if (($result = mysql_unbuffered_query("SELECT @@version AS VersionName", $this->getConnection()))) {
			while ($row = mysql_fetch_assoc($result)) {
				if (isset($row['VersionName'])) {
					return $row['VersionName'];
				}
			}
		} else {
			$result = mysql_unbuffered_query("SHOW VARIABLES LIKE 'version'", $this->getConnection());
			while ($row = mysql_fetch_row($result)) {
				if (isset($row[1])) {
					return $row[1];
				}
			}
		}
	}

	/**
	 * Get MySQL max allowed packaet
	 *
	 * @return integer
	 */
	public function getMaxAllowedPacket() {
		if (($result = mysql_unbuffered_query("SELECT @@max_allowed_packet AS MaxAllowedPacket", $this->getConnection()))) {
			while ($row = mysql_fetch_assoc($result)) {
				if (isset($row['MaxAllowedPacket'])) {
					return $row['MaxAllowedPacket'];
				}
			}
		} else {
			$result = mysql_unbuffered_query("SHOW VARIABLES LIKE 'max_allowed_packet'", $this->getConnection());
			while ($row = mysql_fetch_row($result)) {
				if (isset($row[1])) {
					return $row[1];
				}
			}
		}
	}

	/**
	 * Get MySQL collation name
	 *
	 * @param  string $collationName Collation name
	 * @return string
	 */
	public function getCollation($collationName) {
		// Get collation name
		$result = mysql_unbuffered_query(
			"SELECT COLLATION_NAME AS CollationName FROM `INFORMATION_SCHEMA`.`COLLATIONS` WHERE COLLATION_NAME = '$collationName'",
			$this->getConnection()
		);

		if ($result) {
			while ($row = mysql_fetch_assoc($result)) {
				if (isset($row['CollationName'])) {
					return $row['CollationName'];
				}
			}
		} else {
			$result = mysql_unbuffered_query("SHOW COLLATION LIKE '$collationName'", $this->getConnection());
			while ($row = mysql_fetch_row($result)) {
				if (isset($row[0])) {
					return $row[0];
				}
			}
		}
	}

	/**
	 * Flush database
	 *
	 * @return void
	 */
	public function flush()
	{
		$dropTables = array();
		foreach ($this->listTables() as $tableName) {
			$dropTables[] = "DROP TABLE IF EXISTS `$tableName`";
		}

		// Drop tables
		foreach ($dropTables as $dropQuery) {
			mysql_unbuffered_query($dropQuery, $this->getConnection());
		}
	}

	/**
	 * Import database from file
	 *
	 * @param  string $fileName Name of file
	 * @return bool
	 */
	public function import($fileName)
	{
		// Set collation name
		$collation = $this->getCollation('utf8mb4_general_ci');

		// Set max allowed packet
		$maxAllowedPacket = $this->getMaxAllowedPacket();

		// Set file handler
		$fileHandler = fopen($fileName, 'r');
		if ($fileHandler === false) {
			throw new Exception('Unable to open database file');
		}

		$passed = 0;
		$failed = 0;
		$query  = null;

		// Read database file line by line
		while (($line = fgets($fileHandler)) !== false) {
			$query .= $line;

			// End of query
			if (preg_match('/;\s*$/', $query)) {

				// Check max allowed packet
				if (strlen($query) <= $maxAllowedPacket) {

					// Replace table prefix
					$query = $this->replaceTablePrefix($query);

					// Replace table values
					$query = $this->replaceTableValues($query, true);

					// Replace table collation
					if (empty($collation)) {
						$query = $this->replaceTableCollation($query);
					}

					// Run SQL query
					$result = mysql_unbuffered_query($query, $this->getConnection());
					if ($result === false) {
						$failed++;

						// Log the error
						Ai1wm_Log::error(
							sprintf(
								'Exception while importing: %s with query: %s',
								 mysql_error($this->getConnection()),
								 $query
							 )
						);
					} else {
						$passed++;
					}

				} else {
					$failed++;
				}

				$query = null;
			}
		}

		// Close file handler
		fclose($fileHandler);

		// Check failed queries
		if ((($failed / $passed) * 100) > 2) {
			return false;
		}

		return true;
	}

	/**
	 * Get list of tables
	 *
	 * @return array
	 */
	public function listTables()
	{
		$tables = array();

		// Get list of tables
		$result = mysql_unbuffered_query(
			"SELECT TABLE_NAME AS TableName FROM `INFORMATION_SCHEMA`.`TABLES` WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA = '{$this->database}'",
			$this->getConnection()
		);

		if ($result) {
			while ($row = mysql_fetch_assoc($result)) {
				if (isset($row['TableName'])) {
					$tables[] = $row['TableName'];
				}
			}
		} else {
			$result = mysql_unbuffered_query("SHOW TABLES FROM `{$this->database}`", $this->getConnection());
			while ($row = mysql_fetch_row($result)) {
				if (isset($row[0])) {
					$tables[] = $row[0];
				}
			}
		}

		return $tables;
	}

	/**
	 * Replace table prefix
	 *
	 * @param  string $input Table value
	 * @param  boolean $first Replace first occurrence
	 * @param  boolean $start Replace start occurrence
	 * @return string
	 */
	public function replaceTablePrefix($input, $first = false, $start = false)
	{
		// Get table prefix
		$search = $this->getOldTablePrefix();
		$replace = $this->getNewTablePrefix();

		// Replace first occurrence
		if ($first) {
			$pos = strpos($input, $search);
			if ($pos !== false) {
				return substr_replace($input, $replace, $pos, strlen($search));
			}

			return $input;
		} else if ($start) {
			$pos = strpos($input, $search);
			if ($pos === 0) {
				return substr_replace($input, $replace, $pos, strlen($search));
			}

			return $input;
		}

		// Replace all occurrences
		return str_replace($search, $replace, $input);
	}

	/**
	 * Replace table values
	 *
	 * @param  string  $input Table value
	 * @param  boolean $parse Parse value
	 * @return string
	 */
	public function replaceTableValues($input, $parse = false)
	{
		// Get replace values
		$old = $this->getOldReplaceValues();
		$new = $this->getNewReplaceValues();

		$oldValues = array();
		$newValues = array();

		// Prepare replace values
		for ($i = 0; $i < count($old); $i++) {
			if (strpos($input, $old[$i]) !== false) {
				$oldValues[] = $old[$i];
				$newValues[] = $new[$i];
			}
		}

		// Do replace values
		if ($oldValues) {
			if ($parse) {
				// Parse and replace serialized values
				$input = $this->parseSerializedValues($input);

				// Replace values
				return MysqlUtility::replaceValues($oldValues, $newValues, $input);
			}

			return MysqlUtility::replaceSerializedValues($oldValues, $newValues, $input);
		}

		return $input;
	}

	/**
	 * Parse serialized values
	 *
	 * @param  string $input Table value
	 * @return string
	 */
	public function parseSerializedValues($input)
	{
		// Serialization format
		$array  = '(a:\d+:{.*?})';
		$string = '(s:\d+:".*?")';
		$object = '(O:\d+:".+":\d+:{.*})';

		// Replace serialized values
		return preg_replace_callback("/'($array|$string|$object)'/", array($this, 'replaceSerializedValues'), $input);
	}

	/**
	 * Replace serialized values (callback)
	 *
	 * @param  array  $matches List of matches
	 * @return string
	 */
	public function replaceSerializedValues($matches)
	{
		// Unescape MySQL special characters
		$input = MysqlUtility::unescapeMysql($matches[1]);

		// Replace serialized values
		$input = MysqlUtility::replaceSerializedValues($this->getOldReplaceValues(), $this->getNewReplaceValues(), $input);

		// Prepare query values
		return "'" . mysql_real_escape_string($input, $this->getConnection()) . "'";
	}

	/**
	 * Replace table collation
	 *
	 * @param  string $input SQL statement
	 * @return string
	 */
	public function replaceTableCollation($input)
	{
		return str_replace('utf8mb4', 'utf8', $input);
	}

	/**
	 * Strip table constraints
	 *
	 * @param  string $input SQL statement
	 * @return string
	 */
	public function stripTableConstraints($input)
	{
		$pattern = array(
			'/\s+CONSTRAINT(.+)REFERENCES(.+),/i',
			'/,\s+CONSTRAINT(.+)REFERENCES(.+)/i',
		);
		$replace = '';

		return preg_replace($pattern, $replace, $input);
	}

	/**
	 * Get MySQL connection (lazy loading)
	 *
	 * @return resource
	 */
	public function getConnection()
	{
		if ($this->connection === null) {
			// Make connection (Socket)
			$this->connection = $this->makeConnection();

			if ($this->connection === false) {
				// Make connection (TCP)
				$this->connection = $this->makeConnection(false);

				// Unable to connect to MySQL database server
				if ($this->connection === false) {
					throw new Exception('Unable to connect to MySQL database server: ' . mysql_error($this->connection));
				}
			}
		}

		return $this->connection;
	}

	/**
	 * Make MySQL connection
	 *
	 * @param  bool $useSocket Use socket or TCP connection
	 * @return resource
	 */
	protected function makeConnection($useSocket = true)
	{
		// Use Socket or TCP
		$hostname = ($useSocket ? $this->hostname : gethostbyname($this->hostname));

		// Make connection
		$connection = @mysql_pconnect($hostname, $this->username, $this->password);

		// Select database and set default encoding
		if ($connection) {
			if (mysql_select_db($this->database, $connection)) {
				// Set default encoding
				mysql_unbuffered_query("SET NAMES 'utf8'", $connection);

				// Set foreign key
				mysql_unbuffered_query("SET FOREIGN_KEY_CHECKS = 0", $connection);
			} else {
				throw new Exception('Could not select MySQL database: ' . mysql_error($connection));
			}
		}

		return $connection;
	}

	/**
	 * Returns header for dump file
	 *
	 * @return string
	 */
	protected function getHeader()
	{
		// Some info about software, source and time
		$header = "-- All In One WP Migration SQL Dump\n" .
				"-- http://servmask.com/\n" .
				"--\n" .
				"-- Host: {$this->hostname}\n" .
				"-- Generation Time: " . date('r') . "\n\n" .
				"--\n" .
				"-- Database: `{$this->database}`\n" .
				"--\n\n";

		return $header;
	}

	/**
	 * Table structure extractor
	 *
	 * @param  string $tableName Name of table to export
	 * @return bool
	 */
	protected function getTableStructure($tableName)
	{
		$result = mysql_unbuffered_query("SHOW CREATE TABLE `$tableName`", $this->getConnection());
		while ($row = mysql_fetch_assoc($result)) {
			if (isset($row['Create Table'])) {

				// Replace create table prefix
				$createTable = $this->replaceTablePrefix($row['Create Table'], true);

				// Strip table constraints
				$createTable = $this->stripTableConstraints($createTable);

				// Write table structure
				$this->fileAdapter->write($createTable);

				// Write end of statement
				$this->fileAdapter->write(";\n\n");

				return true;
			}
		}

		return false;
	}

	/**
	 * Table rows extractor
	 *
	 * @param  string $tableName Name of table to export
	 * @return void
	 */
	protected function listValues($tableName)
	{
		// Set query
		$query = "SELECT * FROM `$tableName` ";

		// Apply additional query clauses
		$clauses = $this->getQueryClauses();
		if (isset($clauses[$tableName]) && ($queryClause = $clauses[$tableName])) {
			$query .= $queryClause;
		}

		// Apply additional table prefix columns
		$columns = $this->getTablePrefixColumns($tableName);

		// Get results
		$result = mysql_unbuffered_query($query, $this->getConnection());

		// Replace table name prefix
		$tableName = $this->replaceTablePrefix($tableName, true);

		// Generate insert statements
		while ($row = mysql_fetch_assoc($result)) {
			$items = array();
			foreach ($row as $key => $value) {
				// Replace table prefix columns
				if (isset($columns[$key])) {
					$value = $this->replaceTablePrefix($value, false, true);
				}

				// Replace table values
				$items[] = is_null($value) ? 'NULL' : "'" . mysql_real_escape_string($this->replaceTableValues($value), $this->getConnection()) . "'";
			}

			// Set table values
			$tableValues = implode(',', $items);

			// Write insert statements
			$this->fileAdapter->write("INSERT INTO `$tableName` VALUES ($tableValues);\n");
		}

		// Write end of statements
		$this->fileAdapter->write("\n");

		// Close result cursor
		mysql_free_result($result);
	}
}
