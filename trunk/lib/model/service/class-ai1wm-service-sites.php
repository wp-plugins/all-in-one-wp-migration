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

class Ai1wm_Service_Sites implements Ai1wm_Service_Interface
{
	protected $options = array();

	public function __construct( array $options = array() ) {
		$this->options = $options;
	}

	/**
	 * Import sites (Network mode)
	 *
	 * @return void
	 */
	public function import() {
		global $wp_version;

		$storage = StorageArea::getInstance();

		if ( version_compare( $wp_version, '3.5', '<' ) ) {
			// Blogs.dir directory
			$blogs_dir = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . AI1WM_BLOGS_NAME;
			if ( ! is_dir( $blogs_dir ) ) {
				mkdir( $blogs_dir );
			}

			// Backup blogs.dir files
			$backup_blogs_to = $storage->makeDirectory();

			StorageUtility::copy( $blogs_dir, $backup_blogs_to->getName() );

			// Flush blogs.dir files
			StorageUtility::flush( $blogs_dir );

			// Import blogs.dir files
			StorageUtility::copy( $storage->getRootPath() . AI1WM_SITES_NAME, $blogs_dir );
		} else {
			// Media directory
			$upload_dir     = wp_upload_dir();
			$upload_basedir = $upload_dir['basedir'];
			if ( ! is_dir( $upload_basedir ) ) {
				mkdir( $upload_basedir );
			}

			// Sites directory
			$sites_dir = $upload_basedir . DIRECTORY_SEPARATOR . AI1WM_SITES_NAME;
			if ( ! is_dir( $sites_dir ) ) {
				mkdir( $sites_dir );
			}

			// Backup sites files
			$backup_sites_to = $storage->makeDirectory();

			StorageUtility::copy( $sites_dir, $backup_sites_to->getName() );

			// Flush sites files
			StorageUtility::flush( $sites_dir );

			// Import sites files
			StorageUtility::copy( $storage->getRootPath() . AI1WM_SITES_NAME, $sites_dir );
		}
	}

	/**
	 * Export sites (Network mode)
	 *
	 * @return string
	 */
	public function export() {
		$blogs_dir = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . AI1WM_BLOGS_NAME;
		if ( is_dir( $blogs_dir ) ) {
			return $blogs_dir;
		}
	}
}
