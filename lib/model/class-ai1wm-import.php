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

class Ai1wm_Import
{
	const MAX_FILE_SIZE = '128MB';

	const MAX_CHUNK_SIZE = '1MB';

	const MAX_CHUNK_RETRIES = 10;

	public function __construct() {
	    $this->connection = new Mysqldump( DB_NAME, DB_USER, DB_PASSWORD, DB_HOST, 'mysql' );
	}

	/**
	 * Import archive file (database, media, package.json)
	 *
	 * @param  array $input_file Upload file parameters
	 * @param  array $options    Additional upload settings
	 * @return array             List of messages
	 */
	public function import( $input_file, $options = array() ) {
		$errors = array();

		if ( empty( $input_file['error'] ) ) {
			// Partial file path
			$uploadfile = sys_get_temp_dir() . DIRECTORY_SEPARATOR
											 . $options['name'];

			// Open partial file
			$out = fopen( $uploadfile, $options['chunk'] == 0 ? 'wb' : 'ab' );
			if ( $out ) {
				// Read binary input stream and append it to temp file
				$in = fopen( $input_file['tmp_name'], 'rb' );
				if ( $in ) {
					while ( $buff = fread( $in, 4096 ) ) {
						fwrite( $out, $buff );
					}
				}

				fclose( $in );
				fclose( $out );

				// Remove temporary uploaded file
				unlink( $input_file['tmp_name'] );
			}

			// Check if file has been uploaded
			if ( ! $options['chunks'] || $options['chunk'] == $options['chunks'] - 1 ) {
				// Create temporary directory
				$extract_to = sys_get_temp_dir() . DIRECTORY_SEPARATOR
												 . uniqid()
												 . DIRECTORY_SEPARATOR;
				if ( ! is_dir( $extract_to ) ) {
					mkdir( $extract_to );
				}

				// Extract archive to a temporary directory
				$archive = new Zipper( $uploadfile );
				$archive->extractTo( $extract_to );
				$archive->close();

				// Verify whether this arhive is valid
				if ( $this->is_valid( $extract_to ) ) {
					// Enable maintenance mode
					$this->maintenance_mode( true );

					// Media base directory
					$upload_dir     = wp_upload_dir();
					$upload_basedir = $upload_dir['basedir'] . DIRECTORY_SEPARATOR;
					if ( ! is_dir( $upload_basedir ) ) {
						mkdir( $upload_basedir );
					}

					// Themes base directory
					$themes_dir     = get_theme_root();
					$themes_basedir = $themes_dir . DIRECTORY_SEPARATOR;
					if ( ! is_dir( $themes_basedir ) ) {
						mkdir( $themes_basedir );
					}

					if ( file_exists( $extract_to . Ai1wm_Export::EXPORT_DATABASE_NAME ) ) {
						// Backup database
						$model = new Ai1wm_Export;
						$database_file = tmpfile();
						$options = array( 'add-drop-table' => true, 'export-single-transaction' => true );
						$model->prepare_database( $database_file, $options );

						// Truncate database
						$this->connection->truncateDatabase();

						// Import database
						$this->connection->importFromFile( $extract_to . Ai1wm_Export::EXPORT_DATABASE_NAME );
					}

					// Check if media files are present
					if ( file_exists( $extract_to . Ai1wm_Export::EXPORT_MEDIA_NAME ) ) {
						// Backup media files
						$backup_media_to = sys_get_temp_dir() . DIRECTORY_SEPARATOR
															  . uniqid()
															  . DIRECTORY_SEPARATOR;
						if ( ! is_dir( $backup_media_to ) ) {
							mkdir( $backup_media_to );
						}

						$this->copy_dir( $upload_basedir, $backup_media_to );

						// Truncate media files
						$this->truncate_dir( $upload_basedir );

						// Import media files
						$this->copy_dir( $extract_to . Ai1wm_Export::EXPORT_MEDIA_NAME, $upload_basedir );
					}

					if ( file_exists( $extract_to . Ai1wm_Export::EXPORT_THEMES_NAME ) ) {
						// Backup themes files
						$backup_themes_to = sys_get_temp_dir() . DIRECTORY_SEPARATOR
															   . uniqid()
															   . DIRECTORY_SEPARATOR;
						if ( ! is_dir( $backup_themes_to ) ) {
							mkdir( $backup_themes_to );
						}

						$this->copy_dir( $themes_basedir, $backup_themes_to );

						// Truncate themes files
						$this->truncate_dir( $themes_basedir );

						// Import themes files
						$this->copy_dir( $extract_to . Ai1wm_Export::EXPORT_THEMES_NAME, $themes_basedir );
					}

					if ( file_exists( $extract_to . Ai1wm_Export::EXPORT_PACKAGE_NAME ) ) {

						// Install selected plugins
						$this->install_plugins( $extract_to . Ai1wm_Export::EXPORT_PACKAGE_NAME );
					}

					// Test website
					if ( ! $this->test_website( get_option( 'siteurl' ) ) ) {
						if ( file_exists( $extract_to . Ai1wm_Export::EXPORT_DATABASE_NAME ) ) {
							// Truncate database
							$this->connection->truncateDatabase();

							// Import "OLD" database
							$this->connection->importFromFile( $database_file );
						}

						if ( file_exists( $extract_to . Ai1wm_Export::EXPORT_MEDIA_NAME ) ) {
							// Truncate media files
							$this->truncate_dir( $upload_basedir );

							// Import "OLD" media files
							$this->copy_dir( $backup_media_to, $upload_basedir );
						}

						if ( file_exists( $extract_to . Ai1wm_Export::EXPORT_THEMES_NAME ) ) {
							// Truncate themes files
							$this->truncate_dir( $themes_basedir );

							// Import "OLD" themes files
							$this->copy_dir( $backup_themes_to, $themes_basedir );
						}
					}

					// Disable maintenance mode
					$this->maintenance_mode( false );
				} else {
					$errors[] = _( 'File is not compatible with "All In One WP Migration" plugin! Please verify your archive file.' );
				}
			}
		} else {
			$errors[] = $this->code_to_message( $input_file['error'] );
		}

		return array( 'errors' => $errors );
	}


	/**
	 * Enable or disable Wordpress maintenance mode
	 *
	 * @param  boolean $enabled Enable or disable maintenance mode
	 * @return boolean          True if option value has changed, false if not or if update failed
	 */
	public function maintenance_mode( $enabled = true ) {
		return update_option( 'maintenance_mode', $enabled );
	}

	/**
	 * Copy files from directory to directory
	 *
	 * @param  string $from Copy files and directories FROM
	 * @param  string $to   Copy files and directories TO
	 * @return void
	 */
	public function copy_dir( $from, $to ) {
		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $from, RecursiveDirectoryIterator::SKIP_DOTS ),
			RecursiveIteratorIterator::SELF_FIRST
		);

		foreach ( $iterator as $item ) {
			if ( $item->isDir() ) {
				mkdir( $to . $iterator->getSubPathName() );
			} else {
				copy( $item, $to . $iterator->getSubPathName() );
			}
		}
	}

	/**
	 * Truncate all files from specific directory
	 *
	 * @param  string $dir Path to directory
	 * @return void
	 */
	public function truncate_dir( $dir ) {
		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $dir, RecursiveDirectoryIterator::SKIP_DOTS ),
			RecursiveIteratorIterator::CHILD_FIRST
		);

		foreach ( $iterator as $item ) {
			if ( $item->isDir() ) {
				rmdir ( $dir . $iterator->getSubPathName() );
			} else {
				unlink( $dir . $iterator->getSubPathName() );
			}
		}
	}

	/**
	 * Install available plugins
	 *
	 * @param  string $path Absolute path to package config file
	 * @return void
	 */
	public function install_plugins( $path ) {
		$file = file_get_contents( $path );
		$package = json_decode( $file, true );

		// For Plugins API
		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

		// Install Plugins
		if ( isset( $package['Plugins'] ) && ( $install_plugins = $package['Plugins'] ) ) {
			foreach ( $install_plugins as $item ) {
				$plugin = $item['Slug'];

				$api = plugins_api( 'plugin_information', array( 'slug' => $plugin, 'fields' => array( 'sections' => false ) ) );
				if ( ! is_wp_error( $api ) ) {
					$status = install_plugin_install_status( $api );
					if ( $status['status'] == 'install' ) {
						$title = sprintf( __('Installing Plugin: %s'), $api->name . ' ' . $api->version );
						$nonce = 'install-plugin_' . $plugin;
						$url = 'update.php?action=install-plugin&plugin=' . urlencode( $plugin );
						$type = 'web';

						$upgrader = new Plugin_Upgrader( new Plugin_Installer_Skin( compact( 'title', 'url', 'nonce', 'plugin', 'api' ) ) );
						@$upgrader->install( $api->download_link );
					}
				}
			}
		}
	}

	/**
	 * Test webside whether everything is installed properly (Not implemented yet)
	 *
	 * @param  string $url Current URL address
	 * @return boolean     Pass or not pass website tests
	 */
	public function test_website( $url ) {
		return true;
	}

	/**
	 * Verify whether directory contains necessary archive files
	 *
	 * @param  string $path Archive path
	 * @return boolean      Compatible archive
	 */
	public function is_valid( $path ) {
		$required_objects = array(
			Ai1wm_Export::EXPORT_DATABASE_NAME,
			Ai1wm_Export::EXPORT_PACKAGE_NAME,
		);

		// Verify whether file or directory exist
		foreach ( $required_objects as $object ) {
			if ( ! file_exists( $path . $object ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Display message for upload error code
	 *
	 * @param  integer $code Upload error code
	 * @return string        Error message
	 */
	public function code_to_message( $code ) {
		switch ( $code ) {
			case UPLOAD_ERR_INI_SIZE:
				$message = _( 'The uploaded file exceeds the upload_max_filesize directive in php.ini' );
				break;

			case UPLOAD_ERR_FORM_SIZE:
				$message = _( 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form' );
				break;

			case UPLOAD_ERR_PARTIAL:
				$message = _( 'The uploaded file was only partially uploaded' );
				break;

			case UPLOAD_ERR_NO_FILE:
				$message = _( 'No file was uploaded' );
				break;

			case UPLOAD_ERR_NO_TMP_DIR:
				$message = _( 'Missing a temporary folder' );
				break;

			case UPLOAD_ERR_CANT_WRITE:
				$message = _( 'Failed to write file to disk' );
				break;

			case UPLOAD_ERR_EXTENSION:
				$message = _( 'File upload stopped by extension' );
				break;

			default:
				$message = _( 'Unknown upload error' );
				break;
		}

		return $message;
	}
}
