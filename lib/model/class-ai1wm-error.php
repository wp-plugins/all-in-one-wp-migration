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

class Ai1wm_Error
{
	const ERROR_LIMIT     = 10;
	const EXCEPTION_LIMIT = 10;

	/**
	 * Custom Error Handler
	 *
	 * @param  integer $code    Error Code
	 * @param  string  $message Error Message
	 * @param  string  $file    Error File
	 * @param  integer $line    Error Line
	 * @return void
	 */
	public static function error_handler( $code, $message, $file, $line ) {
		$errors = get_option( AI1WM_ERROR_HANDLER, array() );

		// Limit errors
		if ( count( $errors ) > self::ERROR_LIMIT ) {
			array_shift( $errors );
		}

		// Add error
		$errors[] = array(
			'code'    => $code,
			'message' => $message,
			'file'    => $file,
			'line'    => $line,
			'time'    => time(),
		);

		Ai1wm_Logger::error( AI1WM_ERROR_HANDLER, $errors );
	}

	/**
	 * Custom Exception Handler
	 *
	 * @param  Exception $e Exception Object
	 * @return void
	 */
	public static function exception_handler( $e ) {
		$exceptions = get_option( AI1WM_EXCEPTION_HANDLER, array() );

		// Limit errors
		if ( count( $exceptions ) > self::EXCEPTION_LIMIT ) {
			array_shift( $exceptions );
		}

		// Add exception
		$exceptions[] = array(
			'code'    => $e->getCode(),
			'message' => $e->getMessage(),
			'file'    => $e->getFile(),
			'line'    => $e->getLine(),
			'time'    => time(),
		);

		Ai1wm_Logger::error( AI1WM_EXCEPTION_HANDLER, $exceptions );
	}
}
