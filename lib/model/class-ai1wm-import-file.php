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
class Ai1wm_Import_File extends Ai1wm_Import_Abstract {

	public function import() {
		// Get upload file
		if ( ! isset( $_FILES['upload-file'] ) ) {
			wp_die( 'Unable to process file upload.' );
		}

		// Set chunk
		if ( isset( $this->args['chunk'] ) ) {
			$chunk = (int) $this->args['chunk'];
		} else {
			$chunk = 0;
		}

		// Set chunks
		if ( isset( $this->args['chunks'] ) ) {
			$chunks = (int) $this->args['chunks'];
		} else {
			$chunks = 1;
		}

		// Set archive
		if ( isset( $this->args['name'] ) ) {
			$this->args['archive'] = $this->args['name'];
		}

		// Has any upload error?
		if ( empty( $_FILES['upload-file']['error'] ) ) {

			// Open partial file
			$out = fopen( $this->storage()->archive(), $chunk === 0 ? 'wb' : 'ab' );
			if ( $out ) {
				// Read binary input stream and append it to temp file
				$in = fopen( $_FILES['upload-file']['tmp_name'], 'rb' );
				if ( $in ) {
					while ( $buff = fread( $in, 4096 ) ) {
						fwrite( $out, $buff );
					}
				}

				fclose( $in );
				fclose( $out );

				// Remove temporary uploaded file
				unlink( $_FILES['upload-file']['tmp_name'] );
			} else {
				status_header( 500 );
			}
		} else {
			status_header( 500 );
		}
	}
}
