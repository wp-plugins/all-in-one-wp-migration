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

class Ai1wm_Import
{
	const MAX_FILE_SIZE     = '512MB';
	const MAX_CHUNK_SIZE    = '500KB';
	const MAX_CHUNK_RETRIES = 100;
	const MAINTENANCE_MODE  = 'ai1wm_maintenance_mode';

	/**
	 * Import archive file (database, media, package.json)
	 *
	 * @param  array $input_file Upload file parameters
	 * @param  array $options    Additional upload settings
	 * @return array             List of messages
	 */
	public function import( $input_file, $options = array() ) {
		global $wpdb;
		$errors = array();

		if ( empty( $input_file['error'] ) ) {
			try {
				$storage = new StorageArea;

				// Flush storage directory
				if ( $options['chunk'] === 0 ) {
					StorageDirectory::flush( AI1WM_STORAGE_PATH, array( '.gitignore' ) );
				}

				// Partial file path
				$upload_file = $storage->makeFile( $options['name'] )->getAs( 'string' );

				// Open partial file
				$out = fopen( $upload_file, $options['chunk'] == 0 ? 'wb' : 'ab' );
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
				} else {
					$errors[] = sprintf(
						_(
							'Site could not be imported!<br />
							Please make sure that storage directory <strong>%s</strong> has read and write permissions.'
						),
						AI1WM_STORAGE_PATH
					);

					// Clear storage
					$storage->flush();
				}
			} catch ( Exception $e ) {
				$errors[] = sprintf(
					_(
						'Site could not be imported!<br />
						Please make sure that storage directory <strong>%s</strong> has read and write permissions.'
					),
					AI1WM_STORAGE_PATH
				);

				// Clear storage
				$storage->flush();
			}

			// Check if file has been uploaded
			if ( empty( $errors ) && ( ! $options['chunks'] || $options['chunk'] == $options['chunks'] - 1 ) ) {
				// Create temporary directory
				$extract_to = $storage->makeDirectory()->getAs( 'string' );

				// Extract archive to a temporary directory
				try {
					try {
						$archive = ZipFactory::makeZipArchiver( $upload_file, ! class_exists( 'ZipArchive' ) );
						$archive->extractTo( $extract_to );
						$archive->close();
					} catch ( Exception $e ) {
						$archive = ZipFactory::makeZipArchiver( $upload_file, true );
						$archive->extractTo( $extract_to );
						$archive->close();
					}
				} catch ( Exception $e ) {
					$errors[] = _(
						'Archive file is broken or is not compatible with
						"All In One WP Migration" plugin! Please verify your archive file.'
					);
				}

				if ( empty( $errors ) ) {
					// Verify whether this archive is valid
					if ( $this->is_valid( $extract_to ) ) {
						// Enable maintenance mode
						$this->maintenance_mode( true );

						// Parse package config file
						$config = $this->parse_package( $extract_to . Ai1wm_Export::EXPORT_PACKAGE_NAME );

						// Database import
						if ( is_file( $extract_to . Ai1wm_Export::EXPORT_DATABASE_NAME ) ) {
							// Backup database
							$model         = new Ai1wm_Export;
							$database_file = $model->prepare_database( $storage );

							try {
								$db = MysqlDumpFactory::makeMysqlDump(
									DB_HOST,
									DB_USER,
									DB_PASSWORD,
									DB_NAME,
									(
										class_exists(
											'PDO'
										) && in_array( 'mysql', PDO::getAvailableDrivers() )
									)
								);
								$db->getConnection();
							} catch ( Exception $e ) {
								// Use "old" mysql adapter
								$db = MysqlDumpFactory::makeMysqlDump( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, false );
							}

							// Flush database
							$db->flush();

							$old_values = array();
							$new_values = array();

							// Get Site URL
							if ( isset( $config['SiteURL'] ) && ( $config['SiteURL'] != site_url() ) ) {
								$old_values[] = $config['SiteURL'];
								$new_values[] = site_url();
							}

							// Get Home URL
							if ( isset( $config['HomeURL'] ) && ( $config['HomeURL'] != home_url() ) ) {
								$old_values[] = $config['HomeURL'];
								$new_values[] = home_url();
							}

							// Get Domain
							if ( isset( $config['Domain'] ) && ( $config['Domain'] != ( $domain = parse_url( home_url(), PHP_URL_HOST ) ) ) ) {
								$old_values[] = $config['Domain'];
								$new_values[] = $domain;
							}

							$file          = new Ai1wm_File;
							$database_file = $storage->makeFile( Ai1wm_Export::EXPORT_DATABASE_NAME, $extract_to );

							// Replace Old/New Values
							if ( $old_values && $new_values ) {
								$database_file = $file->str_replace_file(
									$storage,
									$database_file,
									$old_values,
									$new_values
								);

								$database_file = $file->preg_replace_file(
									$storage,
									$database_file,
									'/s:(\d+):([\\\\]?"[\\\\]?"|[\\\\]?"((.*?)[^\\\\])[\\\\]?");/'
								);
							}

							// Import database
							$db->setOldTablePrefix( AI1WM_TABLE_PREFIX )
							   ->setNewTablePrefix( $wpdb->prefix )
							   ->import( $database_file->getAs( 'string' ) );
						}

						// Media import
						if ( is_dir( $extract_to . Ai1wm_Export::EXPORT_MEDIA_NAME ) ) {
							// Media base directory
							$upload_dir     = wp_upload_dir();
							$upload_basedir = $upload_dir['basedir'] . DIRECTORY_SEPARATOR;
							if ( ! is_dir( $upload_basedir ) ) {
								mkdir( $upload_basedir );
							}

							// Backup media files
							$backup_media_to = $storage->makeDirectory()->getAs( 'string' );

							StorageDirectory::copy( $upload_basedir, $backup_media_to );

							// Flush media files
							StorageDirectory::flush( $upload_basedir );

							// Import media files
							StorageDirectory::copy( $extract_to . Ai1wm_Export::EXPORT_MEDIA_NAME, $upload_basedir );
						}

						// Themes import
						if ( is_dir( $extract_to . Ai1wm_Export::EXPORT_THEMES_NAME ) ) {
							// Themes base directory
							$themes_dir     = get_theme_root();
							$themes_basedir = $themes_dir . DIRECTORY_SEPARATOR;
							if ( ! is_dir( $themes_basedir ) ) {
								mkdir( $themes_basedir );
							}

							// Backup themes files
							$backup_themes_to = $storage->makeDirectory()->getAs( 'string' );

							StorageDirectory::copy( $themes_basedir, $backup_themes_to );

							// Flush themes files
							StorageDirectory::flush( $themes_basedir );

							// Import themes files
							StorageDirectory::copy( $extract_to . Ai1wm_Export::EXPORT_THEMES_NAME, $themes_basedir );
						}

						// Plugins import
						if ( is_dir( $extract_to . Ai1wm_Export::EXPORT_PLUGINS_NAME ) ) {
							// Backup plugin files
							$backup_plugins_to = $storage->makeDirectory()->getAs( 'string' );

							StorageDirectory::copy( WP_PLUGIN_DIR, $backup_plugins_to, array( AI1WM_PLUGIN_NAME ) );

							// Flush plugin files
							StorageDirectory::flush( WP_PLUGIN_DIR, array( AI1WM_PLUGIN_NAME ) );

							// Import plugin files
							StorageDirectory::copy( $extract_to . Ai1wm_Export::EXPORT_PLUGINS_NAME, WP_PLUGIN_DIR );
						}

						// Disable maintenance mode
						$this->maintenance_mode( false );
					} else {
						$errors[] = _( 'File is not compatible with "All In One WP Migration" plugin! Please verify your archive file.' );
					}
				}

				// Clear storage
				$storage->flush();
			}
		} else {
			$errors[] = $this->code_to_message( $input_file['error'] );
		}

		return array( 'errors' => $errors );
	}


	/**
	 * Enable or disable WordPress maintenance mode
	 *
	 * @param  boolean $enabled Enable or disable maintenance mode
	 * @return boolean          True if option value has changed, false if not or if update failed
	 */
	public function maintenance_mode( $enabled = true ) {
		return update_option( self::MAINTENANCE_MODE, $enabled );
	}

	/**
	 * Verify whether directory contains necessary archive files
	 *
	 * @param  string $path Archive path
	 * @return boolean      Compatible archive
	 */
	public function is_valid( $path ) {
		$required_objects = array(
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
	 * Parse package config file
	 *
	 * @param  string $file Path to package config file
	 * @return array        Config parameters
	 */
	public function parse_package( $file ) {
		// Get config file
		$data = file_get_contents( $file );

		return json_decode( $data, true );
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
