<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * MysqlFileAdapter class
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
 * MysqlFileAdapter class
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
class MysqlFileAdapter
{
    protected $fileHandler = null;

    public function open($fileName)
    {
        $this->fileHandler = fopen($fileName, 'wb');
        if (false === $this->fileHandler) {
            throw new Exception('Output file is not writable', 2);
        }
    }

    public function write($str)
    {
        $bytesWritten = 0;
        if (false === ($bytesWritten = fwrite($this->fileHandler, $str))) {
            throw new Exception('Writting to file failed! Probably, there is no more free space left?', 4);
        }

        return $bytesWritten;
    }

    public function close()
    {
        return fclose($this->fileHandler);
    }
}
