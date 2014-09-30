<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * StorageDirectory class
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

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'StorageAbstract.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'StorageUtility.php';

/**
 * StorageDirectory class
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
class StorageDirectory extends StorageAbstract
{
    protected $directory = null;

    /**
     * CTOR
     */
    public function __construct($name = null, $path = null) {
        if (empty($name)) {
            $this->directory = $path . DIRECTORY_SEPARATOR . uniqid() . DIRECTORY_SEPARATOR;
        } else {
            $this->directory = $path . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR;
        }

        // Create directory
        if (!is_dir($this->directory)) {
            mkdir($this->directory);
        }
    }

    /**
     * Get directory name
     *
     * @return string
     */
    public function getName() {
        return $this->directory;
    }

    /**
     * Get directory resource
     *
     * @return resource
     */
    public function getResource() {
        return opendir($this->directory);
    }

    /**
     * Delete directory
     *
     * @return boolean
     */
    public function delete() {
        // Remove child files and directories
        if (StorageUtility::flush($this->directory)) {
            return rmdir($this->directory);
        }
    }
}
