<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Utility class file
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
 * Utility class
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
class MysqlUtility
{
    /**
     * Find and replace input with pattern
     *
     * @param  string $input   Value
     * @param  string $pattern Pattern
     * @return string
     */
    public static function pregReplace($input, $pattern) {
        // PHP doesn't garbage collect functions created by create_function()
        static $callback = null;

        if ($callback === null) {
            $callback = create_function(
                '$matches',
                "return isset(\$matches[3]) ? 's:' .
                    strlen(MysqlUtility::unescapeMysql(\$matches[3])) .
                    ':\"' .
                    MysqlUtility::unescapeQuotes(\$matches[3]) .
                    '\";' : \$matches[0];
                "
            );
        }

        return preg_replace_callback($pattern, $callback, $input);
    }

    /**
     * Unescape to avoid dump-text issues
     *
     * @param  string $input Text
     * @return string
     */
    public static function unescapeMysql($input) {
        return str_replace(
            array('\\\\', '\\0', "\\n", "\\r", '\Z', "\'", '\"'),
            array('\\', '\0', "\n", "\r", "\x1a", "'", '"'),
            $input
        );
    }

    /**
     * Fix strange behaviour if you have escaped quotes in your replacement
     *
     * @param  string $input Text
     * @return string
     */
    public static function unescapeQuotes($input) {
        return str_replace('\"', '"', $input);
    }
}
