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
class Ai1wm_Extractor extends Ai1wm_Archiver {
	/**
	 * Overloaded constructor that opens the passed file for reading
	 *
	 * @param string $file File to use as archive
	 */
	public function __construct( $file ) {
		// call parent, to initialize variables
		parent::__construct( $file );
	}

	/**
	 * Extract files from archive to specified location
	 *
	 * @param string $location Location where the files should be extracted
	 * @param int    $seek     Location in the file to start exporting data from
	 */
	public function extract_files( $location, $seek = 0 ) {

	}

	/**
	 * Get the number of files in an archive
	 *
	 * @return int Number of files found in the archive
	 * @throws \Ai1wm_Not_Accesible_Exception
	 * @throws \Ai1wm_Not_Readable_Exception
	 */
	public function get_number_of_files() {
		// files counter
		$files_found = 0;

		while ( $block = $this->read_from_handle( $this->file_handle, 4377, $this->filename ) ) {
			// end block has been reached
			if ( $block === $this->eof ) {
				continue;
			}

			// get file data from the block
			$data = $this->get_data_from_block( $block );

			// we have a file, increment the counter
			$files_found++;

			// skip file content so we can move forward to the next file
			$this->set_file_pointer( $this->file_handle, $data['size'], $this->filename );
		}

		return $files_found;
	}

	public function extract_one_file_to( $location, $exclude = array() ) {
		if ( false === file_exists( $location ) ) {
			throw new Ai1wm_Not_Readable_Exception( sprintf( __( '%s doesn\'t exist', AI1WM_PLUGIN_NAME ), $location ) );
		}

		$block = $this->read_from_handle( $this->file_handle, 4377, $this->filename );

		if ( $block === $this->eof ) {
			// we reached end of file, set the pointer to the end of the file so that feof returns true
			@fseek( $this->file_handle, 1, SEEK_END );
			@fgetc( $this->file_handle );
			return;
		}

		// get file data from header block
		$data = $this->get_data_from_block( $block );

		// set filename
		if ( $data['path'] === '.' ) {
			$filename = $data['filename'];
		} else {
			$filename = $data['path'] . '/' . $data['filename'];
		}

		// should we skip this file?
		if ( in_array( $filename, $exclude ) ) {
			// we don't have a match, skip file content
			$this->set_file_pointer( $this->file_handle, $data['size'], $this->filename );
			return;
		}

		// we need to build the path for the file
		$path = str_replace( '/', DIRECTORY_SEPARATOR, $data['path'] );

		// append prepend extract location
		$path = $location . DIRECTORY_SEPARATOR . $path;

		// check if location doesn't exist, then create it
		if ( false === file_exists( $path ) ) {
			mkdir( $path, 0755, true );
		}

		try {
			$this->extract_to( $path . DIRECTORY_SEPARATOR . $data['filename'], $data );
		} catch ( Exception $e ) {
			// we don't have file permissions, skip file content
			$this->set_file_pointer( $this->file_handle, $data['size'], $this->filename );
			return;
		}
	}

	/**
	 * Extract specific files from archive
	 *
	 * @param string $location Location where to extract files
	 * @param array  $files    Files to extract
	 */
	public function extract_by_files_array( $location, $files = array() ) {
		if ( false === file_exists( $location ) ) {
			throw new Ai1wm_Not_Readable_Exception( sprintf( __( '%s doesn\'t exist', AI1WM_PLUGIN_NAME ), $location ) );
		}

		// we read until we reached the end of the file, or the files we were looking for were found
		while (
			($block = $this->read_from_handle( $this->file_handle, 4377, $this->filename )) &&
			( count( $files ) > 0 )
		) {
			// end block has been reached and we still have files to extract
			// that means the files don't exist in the archive
			if ( $block === $this->eof ) {
				// we reached end of file, set the pointer to the end of the file so that feof returns true
				@fseek( $this->file_handle, 1, SEEK_END );
				@fgetc( $this->file_handle );
				return;
			}

			$data = $this->get_data_from_block( $block );

			// set filename
			if ( $data['path'] === '.' ) {
				$filename = $data['filename'];
			} else {
				$filename = $data['path'] . '/' . $data['filename'];
			}

			// do we have a match?
			if ( in_array( $filename, $files ) ) {
				try {
					// we have a match, let's extract the file and remove it from the array
					$this->extract_to( $location . DIRECTORY_SEPARATOR . $data['filename'], $data );
				} catch ( Exception $e ) {
					// we don't have file permissions, skip file content
					$this->set_file_pointer( $this->file_handle, $data['size'], $this->filename );
				}

				// let's unset the file from the files array
				$key = array_search( $data['filename'], $files );
				unset( $files[$key] );
			} else {
				// we don't have a match, skip file content
				$this->set_file_pointer( $this->file_handle, $data['size'], $this->filename );
			}
		}
	}

	public function set_file_pointer( $handle = null, $offset = 0, $file = '' ) {
		// if null is used, we use the archive handle
		if ( is_null( $handle ) ) {
			$handle = $this->file_handle;
		}

		// if filename is empty, we use archive filename
		if ( empty( $file ) ) {
			$file = $this->filename;
		}

		// do we have offset to apply?
		if ( $offset > 0 ) {
			// set position to current location plus offset
			$result = fseek( $handle, $offset, SEEK_CUR );

			if ( -1 === $result ) {
				throw new Ai1wm_Not_Accesible_Exception(
					sprintf(
						__( 'Unable to seek to offset %d on %s', AI1WM_PLUGIN_NAME ),
						$offset,
						$file
					)
				);
			}
		}
	}

	private function extract_to( $file, $data, $overwrite = true ) {
		// local file handle
		$handle = null;

		// should the extract overwrite the file if it exists?
		if ( $overwrite ) {
			$handle = $this->open_file_for_overwriting( $file );
		} else {
			$handle = $this->open_file_for_writing( $file );
		}

		// is the filesize more than 0 bytes?
		while ( $data['size'] > 0 ) {
			// read the file in chunks of 512KB
			$length = $data['size'] > 512000 ? 512000 : $data['size'];
			// read the file in chunks of 512KB from archiver
			$content = $this->read_from_handle( $this->file_handle, $length, $this->filename );
			// remote the amount of bytes we read
			$data['size'] -= $length;

			// write file contents
			$this->write_to_handle( $handle, $content, $file );
		}

		// close the handle
		fclose( $handle );

		// let's apply last modified date
		$this->set_mtime_of_file( $file, $data['mtime'] );

		// all files should chmoded to 755
		$this->set_file_mode( $file, 0644 );
	}

	private function set_mtime_of_file( $file, $mtime ) {
		return @touch( $file, $mtime );
	}

	private function set_file_mode( $file, $mode = 0644 ) {
		return @chmod( $file, $mode );
	}

	private function get_data_from_block( $block ) {
		// prepare our array keys to unpack
		$format = array(
			$this->block_format[0] . 'filename/',
			$this->block_format[1] . 'size/',
			$this->block_format[2] . 'mtime/',
			$this->block_format[3] . 'path',
		);
		$format = implode( '', $format );

		$data = unpack( $format, $block );

		$data['filename'] = trim( $data['filename'] );
		$data['size']     = trim( $data['size'] );
		$data['mtime']    = trim( $data['mtime'] );
		$data['path']     = trim( $data['path'] );

		return $data;
	}

	/**
	 * Check if file has reached end of file
	 * Returns true if file has NOT reached eof, false otherwise
	 *
	 * @return bool
	 */
	public function has_not_reached_eof() {
		return ! feof( $this->file_handle );
	}

	public function get_file_pointer() {
		$result = ftell( $this->file_handle );

		if ( false === $result ) {
			throw new Ai1wm_Not_Accesible_Exception(
				sprintf(
					__( 'Unable to get current pointer position of %s', AI1WM_PLUGIN_NAME ),
					$this->filename
				)
			);
		}

		return $result;
	}
}
