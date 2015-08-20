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
class Ai1wm_Export_Controller {

	public static function index() {
		// Get message model
		$model = new Ai1wm_Message;

		Ai1wm_Template::render(
			'export/index',
			array(
				'messages' => $model->get_messages(),
			)
		);
	}

	public static function export( $args = array() ) {
		try {

			// Set arguments
			if ( empty( $args ) ) {
				$args = $_REQUEST;
			}

			// Set storage path
			if ( empty( $args['storage'] ) ) {
				$args['storage'] = uniqid();
			}

			// Set secret key
			$secret_key = null;
			if ( isset( $args['secret_key'] ) ) {
				$secret_key = $args['secret_key'];
			}

			// Verify secret key by using the value in the database, not in cache
			if ( $secret_key !== get_site_option( AI1WM_SECRET_KEY, false, false ) ) {
				throw new Ai1wm_Export_Exception(
					sprintf(
						__( 'Unable to authenticate your request with secret_key = %s', AI1WM_PLUGIN_NAME ),
						$secret_key
					)
				);
			}

			// Set provider
			$provider = null;
			if ( isset( $args['provider'] ) ) {
				$provider = $args['provider'];
			}

			$class = "Ai1wm_Export_$provider";
			if ( ! class_exists( $class ) ) {
				throw new Ai1wm_Export_Exception(
					sprintf(
						__( 'Unknown provider: <strong>"%s"</strong>', AI1WM_PLUGIN_NAME ),
						$class
					)
				);
			}

			// Set method
			$method = null;
			if ( isset( $args['method'] ) ) {
				$method = $args['method'];
			}

			// Initialize provider
			$provider = new $class( $args );
			if ( ! method_exists( $provider, $method ) ) {
				throw new Ai1wm_Export_Exception(
					sprintf(
						__( 'Unknown method: <strong>"%s"</strong>', AI1WM_PLUGIN_NAME ),
						$method
					)
				);
			}

			// Invoke method
			echo json_encode( $provider->$method() );
			exit;
		} catch ( Exception $e ) {
			// Log the error
			Ai1wm_Log::error( 'Exception while exporting: ' . $e->getMessage() );

			// Set the status to failed
			Ai1wm_Status::set(
				array(
					'type'    => 'error',
					'title'   => __( 'Unable to export', AI1WM_PLUGIN_NAME ),
					'message' => $e->getMessage(),
				)
			);

			// End the process
			wp_die( 'Exception while exporting: ' . $e->getMessage() );
		}
	}

	public static function buttons() {
		return array(
			apply_filters( 'ai1wm_export_file', Ai1wm_Template::get_content( 'export/button-file' ) ),
			apply_filters( 'ai1wm_export_ftp', Ai1wm_Template::get_content( 'export/button-ftp' ) ),
			apply_filters( 'ai1wm_export_dropbox', Ai1wm_Template::get_content( 'export/button-dropbox' ) ),
			apply_filters( 'ai1wm_export_gdrive', Ai1wm_Template::get_content( 'export/button-gdrive' ) ),
			apply_filters( 'ai1wm_export_s3', Ai1wm_Template::get_content( 'export/button-s3' ) ),
		);
	}
}
