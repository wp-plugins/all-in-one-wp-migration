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
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'MysqlQueryAdapter.php';
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
	protected $hostname            = null;

	protected $username            = null;

	protected $password            = null;

	protected $database            = null;

	protected $fileName            = 'database.sql';

	protected $fileAdapter         = null;

	protected $queryAdapter        = null;

	protected $connection          = null;

	protected $oldTablePrefix      = null;

	protected $newTablePrefix      = null;

	protected $oldReplaceValues    = array();

	protected $newReplaceValues    = array();

	protected $queryClauses        = array();

	protected $ignoreTableReplaces = array();

	protected $includeTables       = array();

	protected $excludeTables       = array();

	protected $noTableData         = false;

	protected $addDropTable        = false;

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

		// Set Query Adapter
		$this->queryAdapter = new MysqlQueryAdapter('mysql');
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
		$tables = array();
		foreach ($this->listTables() as $table) {
			if (count($this->getIncludeTables()) === 0 || in_array($table, $this->getIncludeTables())) {
				$tables[] = $table;
			}
		}

		// Export Tables
		foreach ($tables as $table) {
			if (in_array($table, $this->getExcludeTables())) {
				continue;
			}

			$isTable = $this->getTableStructure($table);
			if (true === $isTable) {
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
	 * @return MysqlDumpPDO
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
	 * @return MysqlDumpPDO
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
	 * Set ignore table replaces
	 *
	 * @param  array $tables List of SQL tables
	 * @return MysqlDumpPDO
	 */
	public function setIgnoreTableReplaces($tables)
	{
		$this->ignoreTableReplaces = $tables;

		return $this;
	}

	/**
	 * Get ignore table replaces
	 *
	 * @return array
	 */
	public function getIgnoreTableReplaces()
	{
		return $this->ignoreTableReplaces;
	}

	/**
	 * Set include tables
	 *
	 * @param  array $tables List of tables
	 * @return MysqlDumpSQL
	 */
	public function setIncludeTables($tables)
	{
		$this->includeTables = $tables;

		return $this;
	}

	/**
	 * Get include tables
	 *
	 * @return array
	 */
	public function getIncludeTables()
	{
		return $this->includeTables;
	}

	/**
	 * Set exclude tables
	 *
	 * @param  array $tables List of tables
	 * @return MysqlDumpSQL
	 */
	public function setExcludeTables($tables)
	{
		$this->excludeTables = $tables;

		return $this;
	}

	/**
	 * Get exclude tables
	 *
	 * @return array
	 */
	public function getExcludeTables()
	{
		return $this->excludeTables;
	}

	/**
	 * Set no table data flag
	 *
	 * @param  bool $flag Do not export table data
	 * @return MysqlDumpSQL
	 */
	public function setNoTableData($flag)
	{
		$this->noTableData = (bool) $flag;

		return $this;
	}

	/**
	 * Get no table data flag
	 *
	 * @return bool
	 */
	public function getNoTableData()
	{
		return $this->noTableData;
	}

	/**
	 * Set add drop table flag
	 *
	 * @param  bool $flag Add drop table SQL clause
	 * @return MysqlDumpSQL
	 */
	public function setAddDropTable($flag)
	{
		$this->addDropTable = (bool) $flag;

		return $this;
	}

	/**
	 * Get add drop table flag
	 *
	 * @return bool
	 */
	public function getAddDropTable()
	{
		return $this->addDropTable;
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
		$deleteTables = array();
		foreach ($this->listTables() as $table) {
			$deleteTables[] = $this->queryAdapter->drop_table($table);
		}

		// Drop tables
		foreach ($deleteTables as $delete) {
			mysql_unbuffered_query($delete, $this->getConnection());
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
		$collation = $this->getCollation('utf8mb4_unicode_ci');

		$fileHandler = fopen($fileName, 'r');
		if ($fileHandler) {
			$query = null;

			// Read database file line by line
			while (($line = fgets($fileHandler)) !== false) {
				// Replace create table prefix
				$line = $this->replaceCreateTablePrefix($line);

				// Replace insert into prefix
				$line = $this->replaceInsertIntoPrefix($line);

				// Replace table values
				$line = $this->replaceTableValues($line);

				// Replace table collation
				if (empty($collation)) {
					$line = $this->replaceTableCollation($line);
				}

				$query .= $line;
				if (preg_match('/;\s*$/', $line)) {
					// Run SQL query
					$result = mysql_unbuffered_query($query, $this->getConnection());
					if ($result === false) {
						// Log the error
						Ai1wm_Log::error(
							sprintf(
								'Exception while importing: %s with query: %s',
								 mysql_error($this->getConnection()),
								 $query
							 )
						);
					}

					// Empty query
					$query = null;
				}
			}

			return true;
		}
	}

	/**
	 * Get list of tables
	 *
	 * @return array
	 */
	public function listTables()
	{
		$tables = array();

		$query = $this->queryAdapter->show_tables_information_schema($this->database);
		if (($result = mysql_unbuffered_query($query, $this->getConnection()))) {
			while ($row = mysql_fetch_assoc($result)) {
				if (isset($row['table_name'])) {
					$tables[] = $row['table_name'];
				}
			}
		} else {
			$query = $this->queryAdapter->show_tables($this->database);
			$result = mysql_unbuffered_query($query, $this->getConnection());
			while ($row = mysql_fetch_row($result)) {
				if (isset($row[0])) {
					$tables[] = $row[0];
				}
			}
		}

		return $tables;
	}

	/**
	 * Replace table values
	 *
	 * @param  string $input Table value
	 * @return string
	 */
	public function replaceTableValues($input)
	{
		$old = $this->getOldReplaceValues();
		$new = $this->getNewReplaceValues();

		$oldValues = array();
		$newValues = array();

		// Replace strings
		for ($i = 0; $i < count($old); $i++) {
			if (!empty($old[$i]) && ($old[$i] != $new[$i]) && !in_array($old[$i], $oldValues)) {
				$oldValues[] = $old[$i];
				$newValues[] = $new[$i];
			}
		}

		// Replace table prefix
		$oldValues[] = $this->getOldTablePrefix();
		$newValues[] = $this->getNewTablePrefix();

		// Replace table values
		$input = str_replace($oldValues, $newValues, $input);

		// Verify serialization
		return MysqlUtility::pregReplace(
			$input,
			'/s:(\d+):([\\\\]?"[\\\\]?"|[\\\\]?"((.*?)[^\\\\])[\\\\]?");/'
		);
	}

	/**
	 * Replace table name prefix
	 *
	 * @param  string $input Table name
	 * @return string
	 */
	public function replaceTableNamePrefix($input)
	{
		$pattern = '/^(' . preg_quote($this->getOldTablePrefix(), '/') . ')(.+)/i';
		$replace = $this->getNewTablePrefix() . '\2';

		return preg_replace($pattern, $replace, $input);
	}

	/**
	 * Replace create table prefix
	 *
	 * @param  string $input SQL statement
	 * @return string
	 */
	public function replaceCreateTablePrefix($input)
	{
		$pattern = '/^CREATE TABLE `(' . preg_quote($this->getOldTablePrefix(), '/') . ')(.+)`/Ui';
		$replace = 'CREATE TABLE `' . $this->getNewTablePrefix() . '\2`';

		return preg_replace($pattern, $replace, $input);
	}

	/**
	 * Replace insert into prefix
	 *
	 * @param  string $input SQL statement
	 * @return string
	 */
	public function replaceInsertIntoPrefix($input)
	{
		$pattern = '/^INSERT INTO `(' . preg_quote($this->getOldTablePrefix(), '/') . ')(.+)`/Ui';
		$replace = 'INSERT INTO `' . $this->getNewTablePrefix() . '\2`';

		return preg_replace($pattern, $replace, $input);
	}

	/**
	 * Replace table collation
	 *
	 * @param  string $input SQL statement
	 * @return string
	 */
	public function replaceTableCollation($input)
	{
		$pattern = array('utf8mb4_unicode_ci', 'utf8mb4');
		$replace = array('utf8_general_ci', 'utf8');

		return str_replace($pattern, $replace, $input);
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
				$query = $this->queryAdapter->set_names('utf8');
				mysql_unbuffered_query($query, $connection);

				// Set foreign key
				$query = $this->queryAdapter->set_foreign_key(0);
				mysql_unbuffered_query($query, $connection);
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
		$query = $this->queryAdapter->show_create_table($tableName);
		$result = mysql_unbuffered_query($query, $this->getConnection());
		while ($row = mysql_fetch_assoc($result)) {
			if (isset($row['Create Table'])) {
				// Replace table name prefix
				$tableName = $this->replaceTableNamePrefix($tableName);

				$this->fileAdapter->write("-- " .
					"--------------------------------------------------------" .
					"\n\n" .
					"--\n" .
					"-- Table structure for table `$tableName`\n--\n\n");

				if ($this->getAddDropTable()) {
					$this->fileAdapter->write("DROP TABLE IF EXISTS `$tableName`;\n\n");
				}

				// Replace create table prefix
				$createTable = $this->replaceCreateTablePrefix($row['Create Table']);

				// Strip table constraints
				$createTable = $this->stripTableConstraints($createTable);

				$this->fileAdapter->write($createTable . ";\n\n");

				return true;
			}
		}
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

		// No table data
		if ($this->getNoTableData() && !isset($clauses[$tableName])) {
			return;
		}

		// Get results
		$result = mysql_unbuffered_query($query, $this->getConnection());

		// Get ignore table replaces
		$ignoreTableReplaces = $this->getIgnoreTableReplaces();

		// Generate insert statements
		if (isset($ignoreTableReplaces[$tableName])) {

			// Replace table name prefix
			$tableName = $this->replaceTableNamePrefix($tableName);

			$this->fileAdapter->write(
				"--\n" .
				"-- Dumping data for table `$tableName`\n" .
				"--\n\n"
			);

			// Generate insert statements
			while ($row = mysql_fetch_row($result)) {
				$items = array();
				foreach ($row as $value) {
					$items[] = is_null($value) ? 'NULL' : "'" . mysql_real_escape_string($value) . "'";
				}

				// Set table values
				$tableValues = implode(',', $items);

				// Write insert statements
				$this->fileAdapter->write("INSERT INTO `$tableName` VALUES ($tableValues);\n");
			}

			// Close result cursor
			mysql_free_result($result);

		} else {

			// Replace table name prefix
			$tableName = $this->replaceTableNamePrefix($tableName);

			$this->fileAdapter->write(
				"--\n" .
				"-- Dumping data for table `$tableName`\n" .
				"--\n\n"
			);

			// Generate insert statements
			while ($row = mysql_fetch_row($result)) {
				$items = array();
				foreach ($row as $value) {
					$items[] = is_null($value) ? 'NULL' : "'" . mysql_real_escape_string($this->replaceTableValues($value)) . "'";
				}

				// Set table values
				$tableValues = implode(',', $items);

				// Write insert statements
				$this->fileAdapter->write("INSERT INTO `$tableName` VALUES ($tableValues);\n");
			}

			// Close result cursor
			mysql_free_result($result);
		}
	}
}
