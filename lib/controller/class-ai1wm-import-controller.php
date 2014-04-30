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

class Ai1wm_Import_Controller
{
	public static function index() {
		try {
			$storage       = new StorageArea;
			$is_accessible = $storage->makeFile();
			$storage->flush();
		} catch ( Exception $e ) {
			$is_accessible = false;
		}

		Ai1wm_Template::render(
			'import/index',
			array(
				'is_accessible' => $is_accessible,
			)
		);
	}

	public static function upload_file() {
		global $wp_rewrite;

		// Set default handlers
		set_error_handler( array( 'Ai1wm_Error', 'error_handler' ) );
		set_exception_handler( array( 'Ai1wm_Error', 'exception_handler' ) );

		$result = array();

		// Get options
		if ( isset( $_FILES['input_file'] ) && ( $input_file = $_FILES['input_file'] ) ) {
			$options = array(
				'chunk'  => 0,
				'chunks' => 0,
				'name'   => null,
			);

			// Ordinal number of the current chunk in the set (starts with zero)
			if ( isset( $_REQUEST['chunk'] ) ) {
				$options['chunk'] = intval( $_REQUEST['chunk'] );
			}

			// Total number of chunks in the file
			if ( isset( $_REQUEST['chunks'] ) ) {
				$options['chunks'] = intval( $_REQUEST['chunks'] );
			}

			// Name of partial file
			if ( isset( $_REQUEST['name'] ) ) {
				$options['name'] = $_REQUEST['name'];
			}

			$model = new Ai1wm_Import;
			$result = $model->import( $input_file, $options );

			// Regenerate permalinks
			$wp_rewrite->flush_rules( true );
		}

		echo json_encode( $result );
		exit;
	}
}
