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

class Ai1wm_Import_Controller
{
	public static function index() {
		try {
			$is_accessible = StorageArea::getInstance()->getRootPath();
		} catch ( Exception $e ) {
			$is_accessible = false;
		}

		Ai1wm_Template::render(
			'import/index',
			array(
				'is_accessible' => $is_accessible,
				'max_file_size' => apply_filters( 'ai1wm_max_file_size', AI1WM_MAX_FILE_SIZE ),
			)
		);
	}

	public static function import() {
		global $wp_rewrite;

		// Set default handlers
		set_error_handler( array( 'Ai1wm_Error', 'error_handler' ) );
		set_exception_handler( array( 'Ai1wm_Error', 'exception_handler' ) );

		// Verify capabilities
		if ( ! current_user_can( 'import' ) ) {
			wp_die( 'Unable to process the request.' );
		}

		$messages = array();

		if ( isset( $_FILES['upload-file'] ) || isset( $_REQUEST['force'] ) ) {
			$options = array(
				'chunk'   => 0,
				'chunks'  => 1,
				'import'  => array(
					'file'  => null,
					'force' => null,
				),
			);

			// Ordinal number of the current chunk in the set (starts with zero)
			if ( isset( $_REQUEST['chunk'] ) ) {
				$options['chunk'] = intval( $_REQUEST['chunk'] );
			}

			// Total number of chunks in the file
			if ( isset( $_REQUEST['chunks'] ) ) {
				$options['chunks'] = intval( $_REQUEST['chunks'] );
			}

			// Import file
			if ( isset( $_REQUEST['name'] ) ) {
				$options['import']['file'] = $_REQUEST['name'];
			}

			// Force file
			if ( isset( $_REQUEST['force'] ) ) {
				$options['import']['force'] = $_REQUEST['force'];
			}

			try {
				// Upload file
				if ( self::upload( $options ) ) {

					// Import site
					$model = new Ai1wm_Import( $options );
					if ( $model->import() ) {
						$messages[] = array(
							'type' => 'success',
							'text' => sprintf(
								_(
									'Your data has been imported successfuly!<br />' .
									'You need to perform two more steps:<br />' .
									'<strong>1. You must save your permalinks structure twice. <a class="ai1wm-no-underline" href="%s#submit" target="_blank">Permalinks Settings</a></strong> (opens a new window)<br />' .
									'<strong>2. <a class="ai1wm-no-underline" href="https://wordpress.org/support/view/plugin-reviews/all-in-one-wp-migration?rate=5#postform" target="_blank">Review the plugin</a>.</strong> (opens a new window)'
								),
								admin_url( 'options-permalink.php' )
							),
						);

						// Flush storage
						StorageArea::getInstance()->flush();
					}
				}
			} catch ( Exception $e ) {
				$messages[] = array(
					'type' => 'error',
					'text' => $e->getMessage(),
				);
			}
		}

		// Regenerate permalinks
		$wp_rewrite->flush_rules( true );

		// Display messages
		echo json_encode( $messages );
		exit;
	}

	public static function upload( $options ) {
		$storage = StorageArea::getInstance();

		// Partial upload file
		$partial_file = $storage->makeFile( $options['import']['file'] );

		// Upload file
		if ( isset( $_FILES['upload-file'] ) ) {

			// Has any upload error?
			if ( empty( $_FILES['upload-file']['error'] ) ) {

				// Flush storage
				if ( $options['chunk'] === 0 ) {
					$storage->flush();
				}

				// Open partial file
				$out = fopen( $partial_file->getName(), $options['chunk'] == 0 ? 'wb' : 'ab' );
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
					throw new Ai1wm_Import_Exception(
						sprintf(
							_(
								'Site could not be imported!<br />' .
								'Please make sure that storage directory <strong>%s</strong> has read and write permissions.'
							),
							AI1WM_STORAGE_PATH
						)
					);

					// Flush storage
					$storage->flush();
				}
			} else {
				throw new Ai1wm_Import_Exception(
					sprintf(
						_(
							'Site could not be imported!<br />' .
							'Please contact ServMask Support and report the following error code: %d'
						),
						$_FILES['upload-file']['error']
					)
				);
			}
		}

		// Upload completed?
		if ( ! $options['chunks'] || $options['chunk'] == $options['chunks'] - 1 ) {
			return $partial_file;
		}
		exit;
	}
}
