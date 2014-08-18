<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * StorageArea class main file
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
 * @category  FileSystem
 * @package   StorageFactory
 * @author    Yani Iliev <yani@iliev.me>
 * @author    Bobby Angelov <bobby@servmask.com>
 * @copyright 2014 Yani Iliev, Bobby Angelov
 * @license   https://raw.github.com/borislav-angelov/storage-factory/master/LICENSE The MIT License (MIT)
 * @version   GIT: 2.6.0
 * @link      https://github.com/borislav-angelov/storage-factory/
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'StorageFile.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'StorageDirectory.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'StorageUtility.php';

/**
 * StorageArea Main class
 *
 * @category  FileSystem
 * @package   StorageFactory
 * @author    Yani Iliev <yani@iliev.me>
 * @author    Bobby Angelov <bobby@servmask.com>
 * @copyright 2014 Yani Iliev, Bobby Angelov
 * @license   https://raw.github.com/borislav-angelov/storage-factory/master/LICENSE The MIT License (MIT)
 * @version   GIT: 2.6.0
 * @link      https://github.com/borislav-angelov/storage-factory/
 */
class StorageArea
{
    protected static $instance = null;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __construct() {
        // Singleton
    }


    /**
     * Get storage absolute path
     *
     * @return string
     */
    public function getRootPath() {
        if (defined('AI1WM_STORAGE_PATH')) {
            if (!is_dir(AI1WM_STORAGE_PATH)) {
                @mkdir(AI1WM_STORAGE_PATH);
            }

            // Verify permissions
            if (StorageUtility::isAccessible(AI1WM_STORAGE_PATH)) {
                if (defined('AI1WM_STORAGE_INDEX')) {
                    $index = AI1WM_STORAGE_PATH . DIRECTORY_SEPARATOR . AI1WM_STORAGE_INDEX;
                    if (!is_file($index)) {
                        @touch($index);
                    }
                }

                return AI1WM_STORAGE_PATH . DIRECTORY_SEPARATOR;
            } else {
                throw new Exception('Storage directory is not accessible (read/write).');
            }
        } else {
            throw new Exception('AI1WM_STORAGE_PATH is not defined.');
        }
    }

    /**
     * Create a file with unique name
     *
     * @param  string      $name Custom file name
     * @return StorageFile       StorageFile instance
     */
    public function makeFile($name = null) {
        return new StorageFile($name, $this->getRootPath());
    }

    /**
     * Create a directory with unique name
     *
     * @param  string           $name Custom directory name
     * @return StorageDirectory       StorageDirectory instance
     */
    public function makeDirectory($name = null) {
        return new StorageDirectory($name, $this->getRootPath());
    }

    /**
     * Delete all files and directories in the storage
     *
     * @return boolean
     */
    public function flush() {
        if (defined('AI1WM_STORAGE_INDEX')) {
            return StorageUtility::flush($this->getRootPath(), array(AI1WM_STORAGE_INDEX));
        }

        return StorageUtility::flush($this->getRootPath());
    }
}
