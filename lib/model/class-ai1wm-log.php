<?php
/**
 * Copyright (C) 2014 ServMask Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ███████╗███████╗██████╗ ██╗   ██╗███╗   ███╗ █████╗ ███████╗██╗  ██╗
 * ██╔════╝██╔════╝██╔══██╗██║   ██║████╗ ████║██╔══██╗██╔════╝██║ ██╔╝
 * ███████╗█████╗  ██████╔╝██║   ██║██╔████╔██║███████║███████╗█████╔╝
 * ╚════██║██╔══╝  ██╔══██╗╚██╗ ██╔╝██║╚██╔╝██║██╔══██║╚════██║██╔═██╗
 * ███████║███████╗██║  ██║ ╚████╔╝ ██║ ╚═╝ ██║██║  ██║███████║██║  ██╗
 * ╚══════╝╚══════╝╚═╝  ╚═╝  ╚═══╝  ╚═╝     ╚═╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝
 */

class Ai1wm_Log {

	/**
	 * Write log message of INFO type
	 *
	 * @param  string $message Log message
	 * @return void
	 */
	public static function info( $message ) {
		self::write( $message, 'INFO' );
	}

	/**
	 * Write log message of INFO type
	 *
	 * @param  string $message Log message
	 * @return void
	 */
	public static function error( $message ) {
		self::write( $message, 'ERROR' );
	}

	/**
	 * Write log message with the specified type
	 *
	 * @param  string $message Log message
	 * @param  string $type    Log type
	 * @return void
	 */
	public static function write( $message, $type = 'INFO' ) {
		// Set date to UTC
		@date_default_timezone_set( 'UTC' );

		// Build message array
		$_message = array();

		// Append the date
		$_message[] = '[' . date( 'M d Y H:i:s' ) . ']';

		// Append the type
		$_message[] = $type;

		// Append the message
		$_message[] = $message;

		// Append new line
		$_message[] = PHP_EOL;

		// Convert message to string
		$_message = implode( ' ', $_message );

		// Append the message to our error.log and close the file handle
		// only if we can get a handle
		if ( $handle = @fopen( AI1WM_LOG_FILE, 'a' ) ) {
			@fwrite( $handle, $_message );
			@fclose( $handle );
		}
	}

	/**
	 * Error handler
	 *
	 * @param  int    $errno   Error level
	 * @param  string $errstr  Error message
	 * @param  string $errfile Error file
	 * @param  int    $errline Error line
	 * @return void
	 */
	public static function error_handler( $errno, $errstr, $errfile, $errline ) {
		// Only log errors and warnings
		if ( in_array( $errno, array( E_ERROR, E_WARNING ) ) ) {
			// Build message array
			$message = array();

			// Add an empty line
			$message[] = '';
			$message[] = 'Number:  ' . $errno;
			$message[] = 'Message: ' . $errstr;
			$message[] = 'File:    ' . $errfile;
			$message[] = 'Line:    ' . $errline;
			$message[] = '--------------------------------------------';

			$message = implode( PHP_EOL, $message );

			self::write( $message, 'ERROR_HANDLER' );
		}
	}

	/**
	 * Exception handler
	 *
	 * @param  Exception $exception Exception object
	 * @return void
	 */
	public static function exception_handler( $exception ) {
		$message = array();

		// Add an empty line
		$message[] = '';
		$message[] = 'Number:  ' . $exception->getCode();
		$message[] = 'Message: ' . $exception->getMessage();
		$message[] = 'File:    ' . $exception->getFile();
		$message[] = 'Line:    ' . $exception->getLine();
		$message[] = '--------------------------------------------';

		$message = implode( PHP_EOL, $message );

		self::write( $message, 'EXCEPTION_HANDLER' );
	}
}