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
class Ai1wm_Compressor extends Ai1wm_Archiver {

	/**
	 * Overloaded constructor that opens the passed file for writing
	 *
	 * @param string $file File to use as archive
	 */
	public function __construct( $file ) {
		// call parent, to initialize variables
		parent::__construct( $file, true );
	}

	/**
	 * Add a file to the archive
	 *
	 * @param string $file File to add to the archive
	 * @param string $new_filename Write the file with a different name
	 *
	 * @throws \Ai1wm_Not_Accesible_Exception
	 * @throws \Ai1wm_Not_Readable_Exception
	 * @throws \Ai1wm_Not_Writable_Exception
	 */
	public function add_file( $file, $new_filename = '' ) {
		// open the file for reading in binary mode
		$handle = $this->open_file_for_reading( $file );

		// get file block header of the file we are trying to archive
		$block = $this->get_file_block( $file, $new_filename );

		// write file block header to our archive file
		$this->write_to_handle( $this->file_handle, $block, $this->filename );

		// read the file in 512KB chunks
		while ( false === feof( $handle ) ) {
			$content = $this->read_from_handle( $handle, 512000, $file );
			// write file contents
			$this->write_to_handle(
				$this->file_handle,
				$content,
				$this->filename
			);
		}
		// close the handle
		fclose( $handle );
	}

	/**
	 * Generate binary block header for a file
	 *
	 * @param string $file Filename to generate block header for
	 * @param string $new_filename Write the file with a different name
	 *
	 * @return string
	 * @throws \Ai1wm_Not_Accesible_Exception
	 */
	private function get_file_block( $file, $new_filename = '' ) {
		// get stats about the file
		$stat = stat( $file );
		if ( false === $stat ) {
			// unable to get file data
			throw new Ai1wm_Not_Accesible_Exception( __( 'Unable to get properties of file ' . $file, AI1WM_PLUGIN_NAME ) );
		}

		// get path details
		$pathinfo = pathinfo( $file );

		if ( ! empty( $new_filename ) ) {
			// get path details
			$pathinfo = pathinfo( $new_filename );
		}

		// filename of the file we are accessing
		$name   = $pathinfo['basename'];
		// content length in bytes of the file
		$length = $stat['7'];
		// last time the file was modified
		$date   = $stat['9'];

		// replace DIRECTORY_SEPARATOR with / in path, we want to always have /
		$path = str_replace( DIRECTORY_SEPARATOR, "/", $pathinfo['dirname'] );

		// concatenate block format parts
		$format = implode( "", $this->block_format );

		// pack file data into binary string
		return pack( $format, $name, $length, $date, $path );
	}
}
