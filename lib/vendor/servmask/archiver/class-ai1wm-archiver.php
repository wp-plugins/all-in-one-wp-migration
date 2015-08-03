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
abstract class Ai1wm_Archiver {

	/**
	 * Header block format of a file
	 *
	 * Field Name    Offset    Length    Contents
	 * name               0       255    filename (no path, no slash)
	 * size             255        14    length of file contents
	 * mtime            269        12    last modification time
	 * prefix           281      4096    path name, no trailing slashes
	 *
	 * @type string
	 */
	protected $block_format = array(
		'a255', // filename
		'a14',  // length of file contents
		'a12',  // last time modified
		'a4096' // path
	);

	/**
	 * Filename including path to the file
	 *
	 * @type string
	 */
	protected $filename = null;

	/**
	 * Handle to the file
	 *
	 * @type resource
	 */
	protected $file_handle = null;

	/**
	 * End Of File block string
	 *
	 * @type string
	 */
	protected $eof = null;

	/**
	 * Default constructor
	 *
	 * Initializes filename and end of file block
	 *
	 * @param string $filename Archive file
	 */
	public function __construct( $filename, $write = false ) {
		// initialize file
		$this->filename = $filename;

		// initialize end of file
		$this->eof = pack( 'a4377', '' );

		if ( $write ) {
			$this->file_handle = $this->open_file_for_writing( $filename );
		} else {
			$this->file_handle = $this->open_file_for_reading( $filename );
		}
	}

	/**
	 * Open the archive for reading
	 *
	 * @param string $file File to open
	 *
	 * @return resource
	 * @throws \Ai1wm_Not_Accesible_Exception
	 */
	protected function open_file_for_reading( $file ) {
		return $this->open_file_in_mode( $file, 'rb' );
	}

	/**
	 * Open the archive for writing/appending
	 *
	 * @param string $file File to open
	 *
	 * @return resource
	 * @throws \Ai1wm_Not_Accesible_Exception
	 */
	protected function open_file_for_writing( $file ) {
		return $this->open_file_in_mode( $file, 'ab' );
	}

	/**
	 * Open the archive for writing and truncate the file if it exist
	 *
	 * @param string $file File to open
	 *
	 * @return resource
	 * @throws \Ai1wm_Not_Accesible_Exception
	 */
	protected function open_file_for_overwriting( $file ) {
		return $this->open_file_in_mode( $file, 'wb' );
	}

	/**
	 * Opens file in the passed mode
	 *
	 * @param string $file File to be opened
	 * @param string $mode Mode to openthe file in
	 *
	 * @return resource
	 * @throws \Ai1wm_Not_Accesible_Exception
	 */
	protected function open_file_in_mode( $file, $mode ) {
		// open the file for writing in binary mode
		$file_handle = @fopen( $file, $mode );

		// check if we have a handle
		if ( false === $file_handle ) {
			// we couldn't open the file
			throw new Ai1wm_Not_Accesible_Exception( sprintf( __( 'Unable to open %s' . AI1WM_PLUGIN_NAME ), $file ) );
		}

		return $file_handle;
	}

	/**
	 * Write data to a handle and check if the data has been written
	 *
	 * @param resource $handle File handle
	 * @param string $data Data to be written - binary
	 * @param string $file Filename that the file handle belongs to
	 *
	 * @throws \Ai1wm_Not_Writable_Exception
	 */
	protected function write_to_handle( $handle, $data, $file ) {
		$result = @fwrite( $handle, $data );
		if ( false === $result || ( ! empty( $data ) && 0 === $result ) ) {
			throw new Ai1wm_Not_Writable_Exception( sprintf( __( 'Unable to write %s', AI1WM_PLUGIN_NAME ), $file ) );
		}
	}

	/**
	 * Read data from a handle
	 *
	 * @param resource $handle File handle
	 * @param int size Length of data to be read in bytes
	 * @param string $file Filename that the file handle belongs to
	 *
	 * @return string Content that was read
	 * @throws \Ai1wm_Not_Readable_Exception
	 */
	protected function read_from_handle( $handle, $size, $file ) {
		$result = @fread( $handle, $size );
		if ( false === $result ) {
			throw new Ai1wm_Not_Readable_Exception( sprintf( __( 'Unable to read %s', AI1WM_PLUGIN_NAME ), $file ) );
		}

		return $result;
	}


	/**
	 * Appends end of file block to the archive
	 *
	 * @throws \Ai1wm_Not_Writable_Exception
	 */
	protected function append_eof() {
		$this->write_to_handle( $this->file_handle, $this->eof, $this->filename );
	}

	/**
	 * Closes the archive file
	 *
	 * We either close the file or append the end of file block if complete argument is set to tru
	 *
	 * @param bool $complete Flag to append end of file block
	 *
	 * @throws \Ai1wm_Not_Accesible_Exception
	 * @throws \Ai1wm_Not_Writable_Exception
	 */
	public function close( $complete = false ) {
		// are we done appending to the file?
		if ( true === $complete ) {
			$this->append_eof();
		}

		// close the file
		$result = fclose( $this->file_handle );

		if ( false === $result ) {
			// unable to close the file
			throw new Ai1wm_Not_Accesible_Exception( sprintf( __( 'Unable to close %s', AI1WM_PLUGIN_NAME ), $this->filename ) );
		}
	}

}
