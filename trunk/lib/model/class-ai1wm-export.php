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

class Ai1wm_Export
{
	protected $options = array();

	public function __construct( array $options = array() ) {
		$this->options = $options;
	}

	/**
	 * Export site
	 *
	 * @return StorageFile
	 */
	public function export() {
		$storage = StorageArea::getInstance();

		// Enable maintenance mode
		Ai1wm_Maintenance::enable();

		// Create export file
		$export_file = $storage->makeFile();

		// Make archive file
		try {
			$zip = ZipFactory::makeZipArchiver( $export_file->getName(), ! class_exists( 'ZipArchive' ), true );
		} catch ( Exception $e ) {
			$zip = ZipFactory::makeZipArchiver( $export_file->getName(), true, true );
		}

		// Package
		if ( $this->should_export_package() ) {
			$service = new Ai1wm_Service_Package( $this->options );
			$zip->addFromString( AI1WM_PACKAGE_NAME, $service->export() );
		}

		// Database
		if ( $this->should_export_database() ) {
			$service = new Ai1wm_Service_Database( $this->options );

			// Add database to archive
			$zip->addFile( $service->export(), AI1WM_DATABASE_NAME );
		}

		// Media
		if ( $this->should_export_media() ) {
			$service = new Ai1wm_Service_Media( $this->options );

			// Add media to archive
			$zip->addDir( $service->export(), AI1WM_MEDIA_NAME );

			// Sites (Network mode)
			$service = new Ai1wm_Service_Sites( $this->options );
			if ( ( $sites = $service->export() ) ) {
				// Add sites to archive
				$zip->addDir( $sites, AI1WM_SITES_NAME );
			}
		}

		// Themes
		if ( $this->should_export_themes() ) {
			$service = new Ai1wm_Service_Themes( $this->options );

			// Add themes to archive
			$zip->addDir( $service->export(), AI1WM_THEMES_NAME );
		}

		// Plugins
		if ( $this->should_export_plugins() ) {
			$service = new Ai1wm_Service_Plugins( $this->options );

			// Add plugins to archive
			if ( ( $plugins = $service->get_installed_plugins() ) ) {
				$zip->addDir( $service->export(), AI1WM_PLUGINS_NAME, $plugins );
			}
		}

		// Disable maintenance mode
		Ai1wm_Maintenance::disable();

		return $export_file;
	}

	/**
	 * Should export package?
	 *
	 * @return boolean
	 */
	public function should_export_package() {
		return true;
	}

	/**
	 * Should export database?
	 *
	 * @return boolean
	 */
	public function should_export_database() {
		return ! isset( $this->options['export-database'] );
	}

	/**
	 * Should export media?
	 *
	 * @return boolean
	 */
	public function should_export_media() {
		return ! isset( $this->options['export-media'] );
	}

	/**
	 * Should export themes?
	 *
	 * @return boolean
	 */
	public function should_export_themes() {
		return ! isset( $this->options['export-themes'] );
	}

	/**
	 * Should export plugins?
	 *
	 * @return boolean
	 */
	public function should_export_plugins() {
		return ! isset( $this->options['export-plugins'] );
	}
}
