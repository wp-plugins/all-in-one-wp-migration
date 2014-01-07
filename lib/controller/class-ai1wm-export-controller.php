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
 */

class Ai1wm_Export_Controller
{
	public static function index() {
		$model = new Ai1wm_Export;
		Ai1wm_Template::render( 'export/index' );
	}

	public static function export() {
		if ( isset( $_POST['options'] ) && ( $options = $_POST['options'] ) ) {
			$output_file = tmpfile();

			// Export archive
			$model = new Ai1wm_Export;
			$file  = $model->export( $output_file, $options );

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
			header( 'Content-Length: ' . filesize( $file ) );

			// Clear output buffering and read file content
			if ( ob_get_length() ) {
				ob_end_clean();
			}
			readfile( $file );
			exit;
		}
	}
}
