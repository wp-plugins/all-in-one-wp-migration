<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Archiver interface file
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
 * @version   GIT: 1.0.4
 * @link      https://github.com/yani-/zip-factory/
 */

/**
 * Archiver Interface
 *
 * @category  Tests
 * @package   ZipFactory
 * @author    Yani Iliev <yani@iliev.me>
 * @copyright 2014 Yani Iliev
 * @license   https://raw.github.com/yani-/zip-factory/master/LICENSE The MIT License (MIT)
 * @link      https://github.com/yani-/zip-factory/
 */
interface ArchiverInterface
{
    /**
     * Create instance of Zip or Pcl archiver
     *
     * @param string  $file  Path to file
     * @param boolean $write Open archive for write
     *
     * @return void
     */
    public function __construct($file, $write = false);

    /**
     * [addFile description]
     *
     * @param [type] $filepath  [description]
     * @param [type] $entryname [description]
     * @param [type] $start     [description]
     * @param [type] $length    [description]
     *
     * @return null [description]
     */
    public function addFile(
        $filepath,
        $entryname = null,
        $start = null,
        $length = null
    );

    /**
     * [addDir description]
     *
     * @param [type] $path       [description]
     * @param [type] $parent_dir [description]
     * @param array  $include    [description]
     *
     * @return null [description]
     */
    public function addDir($path, $parent_dir = null, $include = array());

    /**
     * [addFromString description]
     *
     * @param [type] $name    [description]
     * @param [type] $content [description]
     *
     * @return null [description]
     */
    public function addFromString($name, $content);

    /**
     * [getArchive description]
     *
     * @return [type] [description]
     */
    public function getArchive();

    /**
     * [extractTo description]
     *
     * @param string $pathto Path to extract to
     * @param mixed  $files  Optional files parameter
     *
     * @return [type]              [description]
     */
    public function extractTo($pathto, $files = null);

    /**
     * [close description]
     *
     * @return [type] [description]
     */
    public function close();
}
