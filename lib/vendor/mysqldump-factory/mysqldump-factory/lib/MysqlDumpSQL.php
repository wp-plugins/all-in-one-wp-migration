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
 * @version   GIT: 1.0.10
 * @link      https://github.com/yani-/mysqldump-factory/
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'MysqlDumpInterface.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'MysqlQueryAdapter.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'MysqlFileAdapter.php';

/**
 * MysqlDumpSQL class
 *
 * @category  Databases
 * @package   MysqlDumpFactory
 * @author    Yani Iliev <yani@iliev.me>
 * @author    Bobby Angelov <bobby@servmask.com>
 * @copyright 2014 Yani Iliev, Bobby Angelov
 * @license   https://raw.github.com/yani-/mysqldump-factory/master/LICENSE The MIT License (MIT)
 * @version   GIT: 1.0.10
 * @link      https://github.com/yani-/mysqldump-factory/
 */
class MysqlDumpSQL implements MysqlDumpInterface
{
    protected $hostname         = null;

    protected $username         = null;

    protected $password         = null;

    protected $database         = null;

    protected $fileName         = 'dump.sql';

    protected $fileAdapter      = null;

    protected $queryAdapter     = null;

    protected $connection       = null;

    protected $oldTablePrefix   = null;

    protected $newTablePrefix   = null;

    protected $queryClauses     = array();

    protected $includeTables    = array();

    protected $excludeTables    = array();

    protected $noTableData      = false;

    protected $addDropTable     = false;

    protected $extendedInsert   = true;

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
     * Dump database into a file
     *
     * @return void
     */
    public function dump()
    {
        // Set File Adapter
        $this->fileAdapter = new MysqlFileAdapter();

        // Set output file
        $this->fileAdapter->open($this->getFileName());

        // Write Headers Formating dump file
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
        $this->noTableData = $flag;

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
        $this->addDropTable = $flag;

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
     * Set extended insert flag
     *
     * @param  bool $flag Add extended insert SQL clause
     * @return MysqlDumpSQL
     */
    public function setExtendedInsert($flag)
    {
        $this->extendedInsert = $flag;

        return $this;
    }

    /**
     * Get extended insert flag
     *
     * @return bool
     */
    public function getExtendedInsert()
    {
        return $this->extendedInsert;
    }

    /**
     * Truncate database
     *
     * @return void
     */
    public function truncateDatabase()
    {
        $query = $this->queryAdapter->show_tables($this->database);
        $result = mysql_unbuffered_query($query, $this->getConnection());
        $_deleteTables = array();
        while ($row = mysql_fetch_assoc($result)) {
            // Drop table
            $_deleteTables[] = $this->queryAdapter->drop_table($row['table_name']);
        }
        foreach ($_deleteTables as $delete) {
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
        $fileHandler = fopen($fileName, 'r');
        if ($fileHandler) {
            $query = null;

            // Read database file line by line
            while (($line = fgets($fileHandler)) !== false) {
                // Replace table prefix
                $line = $this->replaceTablePrefix($line, false);

                $query .= $line;
                if (preg_match('/;\s*$/', $line)) {
                    // Run SQL query
                    $result = mysql_unbuffered_query($query, $this->getConnection());
                    if ($result) {
                        $query = null;
                    }
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

        $query = $this->queryAdapter->show_tables($this->database);
        $result = mysql_unbuffered_query($query, $this->getConnection());
        while ($row = mysql_fetch_assoc($result)) {
            $tables[] = $row['table_name'];
        }

        return $tables;
    }

    /**
     * Get MySQL connection (lazy loading)
     *
     * @return resource
     */
    protected function getConnection()
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
                $query = $this->queryAdapter->set_names('utf8');
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
                // Replace table prefix
                $tableName = $this->replaceTablePrefix($tableName);

                $this->fileAdapter->write("-- " .
                    "--------------------------------------------------------" .
                    "\n\n" .
                    "--\n" .
                    "-- Table structure for table `$tableName`\n--\n\n");

                if ($this->getAddDropTable()) {
                    $this->fileAdapter->write("DROP TABLE IF EXISTS `$tableName`;\n\n");
                }

                // Replace table prefix
                $createTable = $this->replaceTablePrefix($row['Create Table'], false);

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
        $insertFirst = true;
        $lineSize = 0;
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

        // Replace table prefix
        $tableName = $this->replaceTablePrefix($tableName);

        $this->fileAdapter->write(
            "--\n" .
            "-- Dumping data for table `$tableName`\n" .
            "--\n\n"
        );

        // Generate insert statements
        $result = mysql_unbuffered_query($query, $this->getConnection());
        while ($row = mysql_fetch_row($result)) {
            $items = array();
            foreach ($row as $value) {
                if ($value) {
                    $value = $this->replaceTablePrefix($value);
                }
                $items[] = is_null($value) ? 'NULL' : "'" . mysql_real_escape_string($value) . "'";
            }

            if ($insertFirst || !$this->getExtendedInsert()) {
                $lineSize += $this->fileAdapter->write("INSERT INTO `$tableName` VALUES (" . implode(',', $items) . ')');
                $insertFirst = false;
            } else {
                $lineSize += $this->fileAdapter->write(',(' . implode(',', $items) . ')');
            }

            if (($lineSize > MysqlDumpInterface::MAXLINESIZE) || !$this->getExtendedInsert()) {
                $insertFirst = true;
                $lineSize = $this->fileAdapter->write(";\n");
            }
        }

        if (!$insertFirst) {
            $this->fileAdapter->write(";\n");
        }
    }

    /**
     * Replace table prefix (old to new one)
     *
     * @param  string $tableName Name of table
     * @param  bool   $start     Match start of string, or start of line
     * @return string
     */
    protected function replaceTablePrefix($tableName, $start = true) {
        $pattern = preg_quote($this->getOldTablePrefix(), '/');
        if ($start) {
            return preg_replace('/^' . $pattern . '/i', $this->getNewTablePrefix(), $tableName);
        } else {
            return preg_replace('/' . $pattern . '/i', $this->getNewTablePrefix(), $tableName);
        }
    }
}
