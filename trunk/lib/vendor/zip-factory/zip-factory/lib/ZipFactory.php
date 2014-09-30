<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Zip Factory main file
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
 * @category  Utilities
 * @package   ZipFactory
 * @author    Yani Iliev <yani@iliev.me>
 * @copyright 2014 Yani Iliev
 * @license   https://raw.github.com/yani-/zip-factory/master/LICENSE The MIT License (MIT)
 * @version   GIT: 1.2.0
 * @link      https://github.com/yani-/zip-factory/
 */

/**
 * ZipFactory class
 *
 * @category  Tests
 * @package   ZipFactory
 * @author    Yani Iliev <yani@iliev.me>
 * @copyright 2014 Yani Iliev
 * @license   https://raw.github.com/yani-/zip-factory/master/LICENSE The MIT License (MIT)
 * @link      https://github.com/yani-/zip-factory/
 */
class ZipFactory
{
    /**
     * Create instance of Zip or Pcl archiver
     *
     * @param string  $file   Path to file
     * @param boolean $pclZip Use Pcl archiver library
     * @param boolean $write  Open archive for write
     *
     * @return mixed
     */
    public static function makeZipArchiver($file, $pclZip = false, $write = false)
    {
        if ($pclZip) {
            include_once dirname(__FILE__) .
                         DIRECTORY_SEPARATOR .
                         'ArchiverPclZip.php';
            return new ArchiverPclZip($file, $write);
        } else {
            include_once dirname(__FILE__) .
                         DIRECTORY_SEPARATOR .
                         'ArchiverZipArchive.php';
            return new ArchiverZipArchive($file, $write);
        }
    }
}
