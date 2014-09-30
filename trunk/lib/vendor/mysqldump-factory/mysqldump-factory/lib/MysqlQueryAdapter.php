<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * MysqlQueryAdapter class
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
 * MysqlQueryAdapter class
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
class MysqlQueryAdapter
{
    public function __construct($type)
    {
        $this->type = $type;
    }

    public function set_names($encoding = 'utf8')
    {
        return "SET NAMES '$encoding'";
    }

    public function show_create_table($tableName)
    {
        return "SHOW CREATE TABLE `$tableName`";
    }

    public function drop_table($tableName)
    {
        return "DROP TABLE IF EXISTS `$tableName`";
    }

    public function show_tables($databaseName)
    {
        return "SELECT TABLE_NAME AS table_name FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA = '$databaseName'";
    }

    public function show_views($databaseName)
    {
        return "SELECT VIEW_NAME AS view_name FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'VIEW' AND TABLE_SCHEMA='$databaseName'";
    }

    public function start_transaction()
    {
        return "SET GLOBAL TRANSACTION ISOLATION LEVEL REPEATABLE READ; START TRANSACTION";
    }

    public function commit_transaction()
    {
        return "SET GLOBAL TRANSACTION ISOLATION LEVEL REPEATABLE READ; START TRANSACTION";
    }

    public function lock_table($tableName)
    {
        return "LOCK TABLES `$tableName` READ LOCAL";
    }

    public function unlock_tables()
    {
        return "UNLOCK TABLES";
    }

    public function start_add_lock_table($tableName)
    {
        return "LOCK TABLES `$tableName` WRITE;\n";
    }

    public function end_add_lock_tables()
    {
        return "UNLOCK TABLES;\n";
    }
}
