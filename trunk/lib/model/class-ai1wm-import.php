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
	protected $options = array();

	public function __construct( array $options = array() ) {
		$this->options = $options;
	}

	/**
	 * Import site
	 *
	 * @return StorageFile
	 */
	public function import() {
		global $wp_version;

		$storage = StorageArea::getInstance();

		// Create import file
		$import_file = $storage->makeFile( $this->options['import']['file'] );

		// Extract archive
		try {
			try {
				$zip = ZipFactory::makeZipArchiver( $import_file->getName(), ! class_exists( 'ZipArchive' ) );
				$zip->extractTo( $storage->getRootPath() );
				$zip->close();
			} catch ( Exception $e ) {
				$zip = ZipFactory::makeZipArchiver( $import_file->getName(), true );
				$zip->extractTo( $storage->getRootPath() );
				$zip->close();
			}
		} catch ( Exception $e ) {
			throw new Ai1wm_Import_Exception(
				_(
					'Site could not be imported!<br />' .
					'Archive file is broken or is not compatible with the plugin! Please verify your archive file.'
				)
			);
		}

		// Verify package
		if ( $this->should_import_package() ) {

			// Enable maintenance mode
			Ai1wm_Maintenance::enable();

			// Database
			if ( $this->should_import_database() ) {
				$service = new Ai1wm_Service_Database( $this->options );
				$service->import();
			}

			// Media
			if ( $this->should_import_media() ) {
				$service = new Ai1wm_Service_Media( $this->options );
				$service->import();
			}

			// Sites (Network mode)
			if ( $this->should_import_sites() ) {
				$service = new Ai1wm_Service_Sites( $this->options );
				$service->import();
			}

			// Themes
			if ( $this->should_import_themes() ) {
				$service = new Ai1wm_Service_Themes( $this->options );
				$service->import();
			}

			// Plugins
			if ( $this->should_import_plugins() ) {
				$service = new Ai1wm_Service_Plugins( $this->options );
				$service->import();
			}

			// Disable maintenance mode
			Ai1wm_Maintenance::disable();

		} else {
			throw new Ai1wm_Import_Exception(
				_(
					'Site could not be imported!<br />' .
					'Archive file is not compatible with the plugin! Please verify your archive file.'
				)
			);
		}

		return $import_file;
	}

	/**
	 * Should import package?
	 *
	 * @return boolean
	 */
	public function should_import_package() {
		global $wp_version;

		// Has package.json file?
		if ( ! is_file( StorageArea::getInstance()->getRootPath() . AI1WM_PACKAGE_NAME ) ) {
			return false;
		}

		// Force import
		if ( isset( $this->options['import']['force'] ) ) {
			return true;
		}

		// Get configuration
		$service = new Ai1wm_Service_Package( $this->options );
		$config  = $service->import();

		// Verify WordPress version
		if ( version_compare( $config['WordPress']['Version'], $wp_version, '<=' ) ) {
			return true;
		} else {
			throw new Ai1wm_Import_Exception(
				sprintf(
					_(
						'You are trying to import data from WordPress v%1$s into WordPress v%2$s, while the process might work,' .
						'we do not recommend this. You should update your WordPress to version %1$s or above and then import the file.' .
						'If you still want to proceed, after making a backup, using the plugin,' .
						'<button type="button" class="ai1wm-button-green-small" id="ai1wm-force-import" data-name="%3$s">CLICK HERE TO CONTINUE</button>'
					),
					$config['WordPress']['Version'],
					$wp_version,
					$this->options['import']['file']
				)
			);
		}
	}

	/**
	 * Should import database?
	 *
	 * @return boolean
	 */
	public function should_import_database() {
		return is_file( StorageArea::getInstance()->getRootPath() . AI1WM_DATABASE_NAME );
	}

	/**
	 * Should import media?
	 *
	 * @return boolean
	 */
	public function should_import_media() {
		return is_dir( StorageArea::getInstance()->getRootPath() . AI1WM_MEDIA_NAME );
	}

	/**
	 * Should import sites?
	 *
	 * @return boolean
	 */
	public function should_import_sites() {
		return is_dir( StorageArea::getInstance()->getRootPath() . AI1WM_SITES_NAME );
	}

	/**
	 * Should import themes?
	 *
	 * @return boolean
	 */
	public function should_import_themes() {
		return is_dir( StorageArea::getInstance()->getRootPath() . AI1WM_THEMES_NAME );
	}

	/**
	 * Should import plugins?
	 *
	 * @return boolean
	 */
	public function should_import_plugins() {
		return is_dir( StorageArea::getInstance()->getRootPath() . AI1WM_PLUGINS_NAME );
	}
}
