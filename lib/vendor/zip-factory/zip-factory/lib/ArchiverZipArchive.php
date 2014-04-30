<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ArchiverZipArchive class file
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

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ArchiverInterface.php';

if (class_exists('ZipArchive')) {
    /**
     * ArchiverZipArchive class
     *
     * @category  Tests
     * @package   ZipFactory
     * @author    Yani Iliev <yani@iliev.me>
     * @copyright 2014 Yani Iliev
     * @license   https://raw.github.com/yani-/zip-factory/master/LICENSE The MIT License (MIT)
     * @link      https://github.com/yani-/zip-factory/
     */
    class ArchiverZipArchive extends ZipArchive implements ArchiverInterface
    {
        /**
         * [$archive description]
         * @var [type]
         */
        protected $archive  = null;

        /**
         * [$root_dir description]
         * @var [type]
         */
        protected $root_dir = null;

        /**
         * Create instance of Zip archiver
         *
         * @param string  $file  Path to file
         * @param boolean $write Open archive for write
         *
         * @return void
         */
        public function __construct($file, $write = false)
        {
            if (is_resource($file)) {
                $meta = stream_get_meta_data($file);
                $this->archive = $meta['uri'];
            } else {
                $this->archive = $file;
            }

            // Open Archive File for read/write
            if ($write) {
                if (($code = $this->open($this->archive, ZipArchive::CREATE | ZipArchive::OVERWRITE)) !== true) {
                    throw new Exception('Archive file cound not be created. Return code: ' . $code);
                }
            } else {
                if (($code = $this->open($this->archive)) !== true) {
                    throw new Exception('Archive file cound not be opened. Return code: ' . $code);
                }
            }
        }

        /**
         * [addFile description]
         *
         * @param [type] $filepath  [description]
         * @param [type] $entryname [description]
         * @param [type] $start     [description]
         * @param [type] $length    [description]
         *
         * @return  null [description]
         */
        public function addFile(
            $filepath,
            $entryname = null,
            $start = null,
            $length = null
        ) {
            if (is_resource($filepath)) {
                $meta     = stream_get_meta_data($filepath);
                $filepath = $meta['uri'];
            }
            parent::addFile($filepath, $entryname, $start, $length);
        }

        /**
         * Add directory to archive
         *
         * @param string $path       Path to directory
         * @param string $parent_dir Parent path name
         * @param array  $include    Include specific directories
         *
         * @return void
         */
        public function addDir($path, $parent_dir = null, $include = array())
        {
            // Use Recursive functions
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path),
                RecursiveIteratorIterator::SELF_FIRST
            );

            // Prepare filter pattern
            $filter_pattern = null;
            if (is_array($include)) {
                $filters = array();
                foreach ($include as $filter) {
                    $filters[] = sprintf(
                        '(%s(%s.*)?)',
                        preg_quote( $filter, '/' ),
                        preg_quote( DIRECTORY_SEPARATOR, '/' )
                    );
                }

                $filter_pattern = implode( '|', $filters );
            }

            foreach ($iterator as $item) {
                // Skip dots
                if ($iterator->isDot()) {
                    continue;
                }

                // Validate filter pattern
                if ($filter_pattern) {
                    if (!preg_match('/^' . $filter_pattern . '$/', $iterator->getSubPathName())) {
                        continue;
                    }
                }

                // Add to archive
                if ($item->isDir()) {
                    $this->addEmptyDir(
                        $parent_dir .
                        DIRECTORY_SEPARATOR .
                        $iterator->getSubPathName()
                    );
                } else {
                    $this->addFile(
                        $item->getPathname(),
                        $parent_dir .
                        DIRECTORY_SEPARATOR .
                        $iterator->getSubPathName()
                    );
                }
            }
        }

        /**
         * [addFromString description]
         *
         * @param [type] $name    [description]
         * @param [type] $content [description]
         *
         * @return null [description]
         */
        public function addFromString($name, $content)
        {
            parent::addFromString($name, $content);
        }

        /**
         * [getArchive description]
         *
         * @return [type] [description]
         */
        public function getArchive()
        {
            return $this->archive;
        }

        /**
         * [extractTo description]
         *
         * @param string $pathto Path to extract to
         * @param mixed  $files  Optional files parameter
         *
         * @return [type]              [description]
         */
        public function extractTo($pathto, $files = null)
        {
            parent::extractTo($pathto);
        }

        /**
         * [close description]
         *
         * @return [type] [description]
         */
        public function close()
        {
            parent::close();
        }
    }
}
