<?php
/**
 * Copyright (C) 2013 ServMask LLC
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

class Ai1wm_File
{

	/**
	 * Replace a file, line by line with the str pattern and then writes the
	 * output to a new file.
	 *
	 * @param  StorageArea $storage     Storage instance
	 * @param  StorageFile $file        StorageFile instance
	 * @param  string      $pattern     Find and replace pattern
	 * @param  string      $replacement Replace term
	 * @return StorageFile              StorageFile instance
	 */
	public function str_replace_file( StorageArea $storage, StorageFile $file, $pattern, $replacement ) {
		$new_file     = $storage->makeFile();
		$current_file = $file->getAs( 'resource' );

		while ( ! feof( $current_file ) ) {
			$line = stream_get_line( $current_file, 1000000, '\n' );

			// append new line at the end of the line
			if ( strlen( $line ) < 1000000 && ! feof( $current_file ) ) {
				$line .= '\n';
			}

			if ( false === fwrite( $new_file->getAs( 'resource' ), str_replace( $pattern, $replacement, $line ) ) ) {
				wp_die(
					'Writting to a file failed! Probably, there is no more free space left?',
					'Out of disk space'
				);
			}
		}

		return $new_file;
	}

	/**
	 * Replace a file, line by line with the regex pattern and then writes the
	 * output to a new file.
	 *
	 * @param  StorageArea $storage Storage instance
	 * @param  StorageFile $file    StorageFile instance
	 * @param  string      $pattern Find and replace pattern
	 * @return StorageFile          StorageFile instance
	 */
	public function preg_replace_file( StorageArea $storage, StorageFile $file, $pattern ) {
		$new_file     = $storage->makeFile();
		$current_file = $file->getAs( 'resource' );

		// Set file handle to the beginning of the file
		rewind( $current_file );

		while ( ! feof( $current_file ) ) {
			$line = stream_get_line( $current_file, 1000000, '\n' );
			// Append new line at the end of the line
			if ( strlen( $line ) < 1000000 && ! feof( $current_file ) ) {
				$line .= '\n';
			}

			$replaced = $this->_preg_replace( $line, $pattern );
			if ( false === fwrite( $new_file->getAs( 'resource' ), $replaced ) ) {
				wp_die(
					'Writting to a file failed! Probably, there is no more free space left?',
					'Out of disk space'
				);
			}
		}
		return $new_file;
	}

	/**
	 * Find and replace line by line with pattern
	 *
	 * @param  string $line    Line to replace
	 * @param  string $pattern Pattern
	 * @return string          New line
	 */
	public function _preg_replace( $line, $pattern ) {
		// PHP doesn't garbage collect functions created by create_function()
		static $callback = null;

		if ( $callback === null ) {
			$callback = create_function(
				'$matches',
				"return isset(\$matches[3]) ? 's:' .
					strlen( Ai1wm_Export::unescape_mysql( \$matches[3] ) ) .
					':\"'.
					Ai1wm_Export::unescape_quotes( \$matches[3] ) .
					'\";' : \$matches[0];
				"
			);
		}

		return preg_replace_callback( $pattern, $callback, $line );
	}
}
