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

class Ai1wm_Export_Controller
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
			'export/index',
			array(
				'list_plugins'  => get_plugins(),
				'is_accessible' => $is_accessible,
			)
		);
	}

	public static function export() {
		// Set default handlers
		set_error_handler( array( 'Ai1wm_Error', 'error_handler' ) );
		set_exception_handler( array( 'Ai1wm_Error', 'exception_handler' ) );

		// Get options
		if ( isset( $_POST['options'] ) && ( $options = $_POST['options'] ) ) {
			$storage = new StorageArea;

			// Export archive
			$model = new Ai1wm_Export;
			$file  = $model->export( $storage, $options );

			// Send the file to the user
			header( 'Content-Description: File Transfer' );
			header( 'Content-Type: application/octet-stream' );
			header(
				sprintf(
					'Content-Disposition: attachment; filename=%s-%s.%s',
					Ai1wm_Export::EXPORT_ARCHIVE_NAME,
					time(),
					'zip'
				)
			);
			header( 'Content-Transfer-Encoding: binary' );
			header( 'Expires: 0' );
			header( 'Cache-Control: must-revalidate' );
			header( 'Pragma: public' );
			header( 'Content-Length: ' . filesize( $file->getAs( 'string' ) ) );

			// Clear output buffering and read file content
			if ( ob_get_length() > 0 ) {
				@ob_end_clean();
			}
			readfile( $file->getAs( 'string' ) );
			$storage->flush();
			exit;
		}
	}
}
