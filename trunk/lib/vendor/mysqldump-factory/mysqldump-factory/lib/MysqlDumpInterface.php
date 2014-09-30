<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * MysqlDump interface
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
 * @version   GIT: 1.9.0
 * @link      https://github.com/yani-/mysqldump-factory/
 */

/**
 * MysqlDump interface
 *
 * @category  Databases
 * @package   MysqlDumpFactory
 * @author    Yani Iliev <yani@iliev.me>
 * @author    Bobby Angelov <bobby@servmask.com>
 * @copyright 2014 Yani Iliev, Bobby Angelov
 * @license   https://raw.github.com/yani-/mysqldump-factory/master/LICENSE The MIT License (MIT)
 * @version   GIT: 1.9.0
 * @link      https://github.com/yani-/mysqldump-factory/
 */
interface MysqlDumpInterface
{
    const MAXLINESIZE = 200000;

    /**
     * Define MySQL credentials for the current connection
     *
     * @param  string $hostname MySQL Hostname
     * @param  string $username MySQL Username
     * @param  string $password MySQL Password
     * @param  string $database MySQL Database
     * @return void
     */
    public function __construct($hostname = 'localhost', $username = '', $password = '', $database = '');

    /**
     * Export database into a file
     *
     * @return void
     */
    public function export();

    /**
     * Set output file name
     *
     * @param  string $fileName Name of the output file
     * @return MysqlDumpInterface
     */
    public function setFileName($fileName);

    /**
     * Get output file name
     *
     * @return string
     */
    public function getFileName();

    /**
     * Set old table prefix
     *
     * @param  string $prefix Name of the table prefix
     * @return MysqlDumpInterface
     */
    public function setOldTablePrefix($prefix);

    /**
     * Get old table prefix
     *
     * @return string
     */
    public function getOldTablePrefix();

    /**
     * Set new table prefix
     *
     * @param  string $prefix Name of the table prefix
     * @return MysqlDumpInterface
     */
    public function setNewTablePrefix($prefix);

    /**
     * Get new table prefix
     *
     * @return string
     */
    public function getNewTablePrefix();

    /**
     * Set old replace values
     *
     * @param  array $values List of values
     * @return MysqlDumpPDO
     */
    public function setOldReplaceValues($values);

    /**
     * Get old replace values
     *
     * @return array
     */
    public function getOldReplaceValues();

    /**
     * Set new replace values
     *
     * @param  array $values List of values
     * @return MysqlDumpPDO
     */
    public function setNewReplaceValues($values);

    /**
     * Get new replace values
     *
     * @return array
     */
    public function getNewReplaceValues();

    /**
     * Set query clauses
     *
     * @param  array $clauses List of SQL query clauses
     * @return MysqlDumpInterface
     */
    public function setQueryClauses($clauses);

    /**
     * Get query clauses
     *
     * @return array
     */
    public function getQueryClauses();

    /**
     * Set include tables
     *
     * @param  array $tables List of tables
     * @return MysqlDumpInterface
     */
    public function setIncludeTables($tables);

    /**
     * Get include tables
     *
     * @return array
     */
    public function getIncludeTables();

    /**
     * Set exclude tables
     *
     * @param  array $tables List of tables
     * @return MysqlDumpInterface
     */
    public function setExcludeTables($tables);

    /**
     * Get exclude tables
     *
     * @return array
     */
    public function getExcludeTables();

    /**
     * Set no table data flag
     *
     * @param  bool $flag Do not export table data
     * @return MysqlDumpInterface
     */
    public function setNoTableData($flag);

    /**
     * Get no table data flag
     *
     * @return bool
     */
    public function getNoTableData();

    /**
     * Set add drop table flag
     *
     * @param  bool $flag Add drop table SQL clause
     * @return MysqlDumpInterface
     */
    public function setAddDropTable($flag);

    /**
     * Get add drop table flag
     *
     * @return bool
     */
    public function getAddDropTable();

    /**
     * Set extended insert flag
     *
     * @param  bool $flag Add extended insert SQL clause
     * @return MysqlDumpInterface
     */
    public function setExtendedInsert($flag);

    /**
     * Get extended insert flag
     *
     * @return bool
     */
    public function getExtendedInsert();

    /**
     * Flush database
     *
     * @return void
     */
    public function flush();

    /**
     * Import database from file
     *
     * @param  string $fileName Name of file
     * @return bool
     */
    public function import($fileName);

    /**
     * Get list of tables
     *
     * @return array
     */
    public function listTables();

    /**
     * Replace table values
     *
     * @param  string $input Table value
     * @return string
     */
    public function replaceTableValues($input);

    /**
     * Replace table name prefix
     *
     * @param  string $input Table name
     * @return string
     */
    public function replaceTableNamePrefix($input);

    /**
     * Replace create table prefix
     *
     * @param  string $input SQL statement
     * @return string
     */
    public function replaceCreateTablePrefix($input);

    /**
     * Replace insert into prefix
     *
     * @param  string $input SQL statement
     * @return string
     */
    public function replaceInsertIntoPrefix($input);

    /**
     * Strip table constraints
     *
     * @param  string $input SQL statement
     * @return string
     */
    public function stripTableConstraints($input);

    /**
     * Get MySQL connection (lazy loading)
     *
     * @return resource
     */
    public function getConnection();
}
