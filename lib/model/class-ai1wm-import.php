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

class Ai1wm_Import
{
	const MAX_FILE_SIZE     = '512MB';
	const MAX_CHUNK_SIZE    = '1MB';
	const MAX_CHUNK_RETRIES = 10;
	const MAINTENANCE_MODE  = 'ai1wm_maintenance_mode';

	public function __construct() {
		$this->connection = MysqlDumpFactory::makeMysqlDump(
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
	}

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

				try {
					// Extract archive to a temporary directory
					$archive = ZipFactory::makeZipArchiver( $upload_file, ! class_exists( 'ZipArchive' ) );
					$archive->extractTo( $extract_to );
					$archive->close();
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

						// Database import
						if ( file_exists( $extract_to . Ai1wm_Export::EXPORT_DATABASE_NAME ) ) {
							// Backup database
							$model         = new Ai1wm_Export;
							$database_file = $model->prepare_database( $storage );

							// Truncate database
							$this->connection->truncateDatabase();

							// Import database
							$this->connection->setOldTablePrefix( AI1WM_TABLE_PREFIX )
											 ->setNewTablePrefix( $wpdb->prefix )
											 ->import( $extract_to . Ai1wm_Export::EXPORT_DATABASE_NAME );
						}

						// Media import
						if ( is_dir( $extract_to . Ai1wm_Export::EXPORT_MEDIA_NAME ) ) {
							// Backup media files
							$backup_media_to = $storage->makeDirectory()->getAs( 'string' );

							$this->copy_dir( $upload_basedir, $backup_media_to );

							// Truncate media files
							$this->truncate_dir( $upload_basedir );

							// Import media files
							$this->copy_dir( $extract_to . Ai1wm_Export::EXPORT_MEDIA_NAME, $upload_basedir );
						}

						// Themes import
						if ( is_dir( $extract_to . Ai1wm_Export::EXPORT_THEMES_NAME ) ) {
							// Backup themes files
							$backup_themes_to = $storage->makeDirectory()->getAs( 'string' );

							$this->copy_dir( $themes_basedir, $backup_themes_to );

							// Truncate themes files
							$this->truncate_dir( $themes_basedir );

							// Import themes files
							$this->copy_dir( $extract_to . Ai1wm_Export::EXPORT_THEMES_NAME, $themes_basedir );
						}

						// Plugins import
						if ( is_dir( $extract_to . Ai1wm_Export::EXPORT_PLUGINS_NAME ) ) {
							// Backup plugin files
							$backup_plugins_to = $storage->makeDirectory()->getAs( 'string' );

							$this->copy_dir( WP_PLUGIN_DIR, $backup_plugins_to, array( AI1WM_PLUGIN_NAME ) );

							// Truncate plugin files
							$this->truncate_dir( WP_PLUGIN_DIR, array( AI1WM_PLUGIN_NAME ) );

							// Import plugin files
							$this->copy_dir( $extract_to . Ai1wm_Export::EXPORT_PLUGINS_NAME, WP_PLUGIN_DIR );
						}

						// Test website
						if ( ! $this->test_website( get_option( 'siteurl' ) ) ) {

							// Database import
							if ( file_exists( $extract_to . Ai1wm_Export::EXPORT_DATABASE_NAME ) ) {
								// Truncate database
								$this->connection->truncateDatabase();

								// Import "OLD" database
								$this->connection->setOldTablePrefix( AI1WM_TABLE_PREFIX )
												 ->setNewTablePrefix( $wpdb->prefix )
												 ->import( $database_file->getAs( 'string' ) );
							}

							// Media import
							if ( is_dir( $extract_to . Ai1wm_Export::EXPORT_MEDIA_NAME ) ) {
								// Truncate media files
								$this->truncate_dir( $upload_basedir );

								// Import "OLD" media files
								$this->copy_dir( $backup_media_to, $upload_basedir );
							}

							// Themes import
							if ( is_dir( $extract_to . Ai1wm_Export::EXPORT_THEMES_NAME ) ) {
								// Truncate themes files
								$this->truncate_dir( $themes_basedir );

								// Import "OLD" themes files
								$this->copy_dir( $backup_themes_to, $themes_basedir );
							}

							// Plugins import
							if ( is_dir( $extract_to . Ai1wm_Export::EXPORT_PLUGINS_NAME ) ) {
								// Truncate plugin files
								$this->truncate_dir( WP_PLUGIN_DIR, array( AI1WM_PLUGIN_NAME ) );

								// Import "OLD" plugin files
								$this->copy_dir( $backup_plugins_to, WP_PLUGIN_DIR, array( AI1WM_PLUGIN_NAME ) );
							}
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
	 * Copy files from directory to directory
	 *
	 * @param  string $from    Copy files and directories FROM
	 * @param  string $to      Copy files and directories TO
	 * @param  array  $exclude List of directories to exclude
	 * @return void
	 */
	public function copy_dir( $from, $to, $exclude = array() ) {
		$from = trailingslashit( $from );
		$to   = trailingslashit( $to );

		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $from ),
			RecursiveIteratorIterator::SELF_FIRST
		);

		// Prepare filter pattern
		$filter_pattern = null;
		if ( is_array( $exclude ) ) {
			$filters = array();
			foreach ( $exclude as $filter ) {
				$filters[] = sprintf(
					'(%s(%s.*)?)',
					preg_quote( $filter, '/' ),
					preg_quote( DIRECTORY_SEPARATOR, '/' )
				);
			}

			$filter_pattern = implode( '|', $filters );
		}

		foreach ( $iterator as $item ) {
			// Skip dots
			if ( $iterator->isDot() ) {
				continue;
			}

			// Validate filter pattern
			if ( $filter_pattern ) {
				if ( preg_match( '/^' . $filter_pattern . '$/', $iterator->getSubPathName() ) ) {
					continue;
				}
			}

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
	 * @param  string $dir     Path to directory
	 * @param  array  $exclude List of directories to exclude
	 * @return void
	 */
	public function truncate_dir( $dir, $exclude = array() ) {
		$dir = trailingslashit( $dir );
		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $dir ),
			RecursiveIteratorIterator::CHILD_FIRST
		);

		// Prepare filter pattern
		$filter_pattern = null;
		if ( is_array( $exclude ) ) {
			$filters = array();
			foreach ( $exclude as $filter ) {
				$filters[] = sprintf(
					'(%s(%s.*)?)',
					preg_quote( $filter, '/' ),
					preg_quote( DIRECTORY_SEPARATOR, '/' )
				);
			}

			$filter_pattern = implode( '|', $filters );
		}

		foreach ( $iterator as $item ) {
			// Skip dots
			if ( $iterator->isDot() ) {
				continue;
			}

			// Validate filter pattern
			if ( $filter_pattern ) {
				if ( preg_match( '/^' . $filter_pattern . '$/', $iterator->getSubPathName() ) ) {
					continue;
				}
			}

			if ( $item->isDir() ) {
				rmdir( $dir . $iterator->getSubPathName() );
			} else {
				unlink( $dir . $iterator->getSubPathName() );
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
