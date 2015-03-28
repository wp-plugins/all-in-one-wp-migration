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
class Ai1wm_Status {

	public static function get( $key = null ) {
		// have we got a status file yet?
		if ( is_file( AI1WM_STATUS_FILE ) ) {
			// open status file for reading
			$handle = @fopen( AI1WM_STATUS_FILE, 'r' );
			if ( false === $handle ) {
				throw new Ai1wm_Not_Accesible_Exception(
					sprintf(
						__( 'Unable to open <strong>"%s"</strong> for reading.', AI1WM_PLUGIN_NAME ),
						AI1WM_STATUS_FILE
					)
				);
			}

			// holds the content of the file
			$content = '';
			while ( false === feof( $handle ) ) {
				$chunk = @fread( $handle, 512 );
				if ( false === $chunk ) {
					throw new Ai1wm_Not_Readable_Exception(
						sprintf(
							__( 'Unable to read from <strong>"%s"</strong>', AI1WM_PLUGIN_NAME ),
							AI1WM_STATUS_FILE
						)
					);
				}

				// append the chunk we read from the file to our content
				$content .= $chunk;
			}

			// do we have something to parse?
			if ( empty( $content ) ) {
				// file is empty, return empty array
				return array();
			}

			// close status file handle
			fclose( $handle );

			// decode status
			$data = @json_decode( $content, true );

			if ( is_null( $data ) ) {
				throw new Exception(
					sprintf(
						__( 'Unable to decode <strong>"%s"</strong> with json_decode', AI1WM_PLUGIN_NAME ),
						$content
					)
				);
			}

			// are we asking for particular key?
			if ( false === is_null( $key ) ) {
				// is the key available in the data array?
				if ( false === isset( $data[$key] ) ) {
					// key is not available :/
					return false;
				}

				// give the user the value they asked for
				return $data[$key];
			}

			// return the full status array
			return $data;
		}

		// no status file yet, return empty array
		return array();
	}

	public static function set( $status ) {
		// get old status before truncating file
		$status_old = self::get();

		// open status file for writing, truncate its content
		$handle = @fopen( AI1WM_STATUS_FILE, 'w' );
		if ( false === $handle ) {
			throw new Ai1wm_Not_Accesible_Exception(
				sprintf(
					__( 'Unable to open <strong>"%s"</strong> for writing.', AI1WM_PLUGIN_NAME ),
					AI1WM_STATUS_FILE
				)
			);
		}

		// create the status that we want to write to the status file
		$content = json_encode( $status + $status_old ); // union the statuses

		if ( false === $content ) {
			throw new Exception(
				sprintf(
					__( 'Unable to encode <strong>"%s"</strong> with json_encode', AI1WM_PLUGIN_NAME ),
					print_r( $status + $status_old, true )
				)
			);
		}

		// write the new status in status file
		$result = @fwrite( $handle, $content );

		// check if we were able to write to the file
		if ( false === $result ) {
			throw new Ai1wm_Not_Writable_Exception(
				sprintf(
					__( 'Unable to write <strong>"%s"</strong> in <strong>"%s"</strong>.', AI1WM_PLUGIN_NAME ),
					$content,
					AI1WM_STATUS_FILE
				)
			);
		}

		// close status file handle
		fclose( $handle );
	}
}
