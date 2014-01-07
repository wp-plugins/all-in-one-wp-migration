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

class Ai1wm_Import_Controller
{
	public static function index() {
		Ai1wm_Template::render( 'import/index' );
	}

	public static function upload_file() {
		$result = array();

		if ( isset( $_FILES['input_file'] ) && ( $input_file = $_FILES['input_file'] ) ) {
			$model = new Ai1wm_Import;
			$result = $model->import( $input_file );
		}

		echo json_encode( $result );
		exit;
	}
}
