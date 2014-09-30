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

class Ai1wm_Export_Controller
{
	public static function index() {
		try {
			$is_accessible = StorageArea::getInstance()->getRootPath();
		} catch ( Exception $e ) {
			$is_accessible = false;
		}

		// Messages
		$model    = new Ai1wm_Message;
		$messages = $model->get_messages();

		Ai1wm_Template::render(
			'export/index',
			array(
				'messages'      => $messages,
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

			// Log options
			Ai1wm_Logger::debug( AI1WM_EXPORT_OPTIONS, $options );

			// Export site
			$model = new Ai1wm_Export( $options );
			$file  = $model->export();

			// Send the file to the user
			header( 'Content-Description: File Transfer' );
			header( 'Content-Type: application/octet-stream' );
			header( 'Content-Disposition: attachment; filename=' . self::filename() );
			header( 'Content-Transfer-Encoding: binary' );
			header( 'Expires: 0' );
			header( 'Cache-Control: must-revalidate' );
			header( 'Pragma: public' );
			header( 'Content-Length: ' . $file->getSize() );

			// Clear output buffering and read file content
			while ( @ob_end_clean() );

			// Load file content
			$handle = fopen( $file->getName(), 'rb' );
			while ( ! feof( $handle ) ) {
				echo fread( $handle, 8192 );
			}
			fclose( $handle );

			// Flush storage
			StorageArea::getInstance()->flush();
			exit;
		}
	}

	public static function filename() {
		$url  = parse_url( home_url() );
		$name = array();

		// Add domain
		if ( isset( $url['host'] ) ) {
			$name[] = $url['host'];
		}

		// Add path
		if ( isset( $url['path'] ) ) {
			$name[] = $url['path'];
		}

		// Add year, month and day
		$name[] = date('Ymd');

		// Add hours, minutes and seconds
		$name[] = date('His');

		return sprintf( '%s.zip', implode( '-', $name ) );
	}
}
