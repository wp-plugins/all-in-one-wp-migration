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
 * @version   GIT: 2.2.0
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
 * @version   GIT: 2.2.0
 * @link      https://github.com/yani-/mysqldump-factory/
 */
class MysqlUtility
{
	/**
	 * Replace all occurrences of the search string with the replacement string.
	 * This function is case-sensitive.
	 *
	 * @param  array  $from List of string we're looking to replace.
	 * @param  array  $to   What we want it to be replaced with.
	 * @param  string $data Data to replace.
	 * @return mixed        The original string with all elements replaced as needed.
	 */
	public static function replaceValues($from = array(), $to = array(), $data = '')
	{
		return str_replace($from, $to, $data);
	}

	/**
	 * Take a serialized array and unserialize it replacing elements as needed and
	 * unserializing any subordinate arrays and performing the replace on those too.
	 * This function is case-sensitive.
	 *
	 * @param  array $from       List of string we're looking to replace.
	 * @param  array $to         What we want it to be replaced with.
	 * @param  mixed $data       Used to pass any subordinate arrays back to in.
	 * @param  bool  $serialized Does the array passed via $data need serializing.
	 * @return mixed             The original array with all elements replaced as needed.
	 */
	public static function replaceSerializedValues($from = array(), $to = array(), $data = '', $serialized = false)
	{
		// Some unserialized data cannot be re-serialized eg. SimpleXMLElements
		try {

			if (is_string($data) && ($unserialized = @unserialize($data)) !== false) {
				$data = self::replaceSerializedValues($from, $to, $unserialized, true);
			} else if (is_array($data)) {
				$tmp = array();
				foreach ($data as $key => $value) {
					$tmp[$key] = self::replaceSerializedValues($from, $to, $value, false);
				}

				$data = $tmp;
				unset($tmp);
			} elseif (is_object($data)) {
				$tmp = $data;
				$props = get_object_vars($data);
				foreach ($props as $key => $value) {
					$tmp->$key = self::replaceSerializedValues($from, $to, $value, false);
				}

				$data = $tmp;
				unset($tmp);
			} else {
				if (is_string($data)) {
					$data = str_replace($from, $to, $data);
				}
			}

			if ($serialized) {
				return serialize($data);
			}

		} catch (Exception $e) {
			// pass
		}

		return $data;
	}

	/**
	 * Unescape MySQL special characters
	 *
	 * @param  string $data Data to replace.
	 * @return string
	 */
	public static function unescapeMysql($data) {
		return str_replace(
			array('\\\\', '\\0', "\\n", "\\r", '\Z', "\'", '\"'),
			array('\\', '\0', "\n", "\r", "\x1a", "'", '"'),
			$data
		);
	}
	/**
	 * Unescape quote characters
	 *
	 * @param  string $data Data to replace.
	 * @return string
	 */
	public static function unescapeQuotes($data) {
		return str_replace('\"', '"', $data);
	}
}
