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
	 * [str_replace_file description]
	 * @param  [type] $fh          [description]
	 * @param  [type] $pattern     [description]
	 * @param  [type] $replacement [description]
	 * @return [type]              [description]
	 */
	public function str_replace_file( $fh, $pattern, $replacement ) {
		$_new_file = tmpfile();

		while ( ! feof( $fh ) ) {
			$line = stream_get_line( $fh, 1000000, '\n' );

			// append new line at the end of the line
			if ( strlen( $line ) < 1000000 && ! feof( $fh ) ) {
				$line .= '\n';
			}

			if (
				false === fwrite(
					$_new_file,
					str_replace( $pattern, $replacement, $line )
				)
			) {
				wp_die(
					'Writting to a file failed! Probably, there is no more free space left?',
					'Out of disk space'
				);
			}
		}

		return $_new_file;
	}

	/**
	 * Replace a file, line by line with the regex pattern and then writes the
	 * output to a new file.
	 *
	 * @param  [type] $fh          [description]
	 * @param  [type] $pattern     [description]
	 *
	 * @return [type]              [description]
	 */
	public function preg_replace_file( $fh, $pattern ) {
		$_new_file = tmpfile();

		// set filehandle to the beginning of the file
		rewind( $fh );

		while ( ! feof( $fh ) ) {
			$line = stream_get_line( $fh, 1000000, '\n' );
			// append new line at the end of the line
			if ( strlen( $line ) < 1000000 && ! feof( $fh ) ) {
				$line .= '\n';
			}

			$replaced = $this->_preg_replace( $line, $pattern );
			if (
				false === fwrite(
					$_new_file,
					$replaced
				)
			) {
				wp_die(
					'Writting to a file failed! Probably, there is no more free space left?',
					'Out of disk space'
				);
			}
		}
		return $_new_file;
	}

	/**
	 * [_preg_replace description]
	 * @param  [type] $line    [description]
	 * @param  [type] $pattern [description]
	 * @return [type]          [description]
	 */
	public function _preg_replace( $line, $pattern ) {
		//php doesn't garbage collect functions created by create_function()
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
