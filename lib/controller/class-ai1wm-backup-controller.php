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
class Ai1wm_Backup_Controller {

	public static function index() {
		$model = new Ai1wm_Backup;

		// Username
		if ( isset( $_POST['ai1wm-username'] ) ) {
			update_site_option( AI1WM_AUTH_USER, $_POST['ai1wm-username'] );
		}

		// Password
		if ( isset( $_POST['ai1wm-password'] ) ) {
			update_site_option( AI1WM_AUTH_PASSWORD, $_POST['ai1wm-password'] );
		}

		Ai1wm_Template::render(
			'backup/index',
			array(
				'backups'     => $model->get_files(),
				'free_space'  => $model->get_free_space(),
				'total_space' => $model->get_total_space(),
				'username'    => get_site_option( AI1WM_AUTH_USER, false ),
				'password'    => get_site_option( AI1WM_AUTH_PASSWORD, false ),
			)
		);
	}

	public static function delete() {
		$response = array( 'errors' => array() );

		// Set file
		$file = null;
		if ( isset( $_POST['file'] ) ) {
			$file = trim( $_POST['file'] );
		}

		$model  = new Ai1wm_Backup;

		try {
			// Delete file
			$model->delete_file( $file );
		} catch ( Exception $e ) {
			$response['errors'][] = $e->getMessage();
		}

		echo json_encode( $response );
		exit;
	}
}
