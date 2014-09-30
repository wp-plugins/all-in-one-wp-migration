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

class Ai1wm_Service_Media implements Ai1wm_Service_Interface
{
	protected $options = array();

	public function __construct( array $options = array() ) {
		$this->options = $options;
	}

	/**
	 * Import media
	 *
	 * @return void
	 */
	public function import() {
		$storage = StorageArea::getInstance();

		// Media directory
		$upload_dir     = wp_upload_dir();
		$upload_basedir = $upload_dir['basedir'];
		if ( ! is_dir( $upload_basedir ) ) {
			mkdir( $upload_basedir );
		}

		// Backup media files
		$backup_media_to = $storage->makeDirectory();

		StorageUtility::copy( $upload_basedir, $backup_media_to->getName() );

		// Flush media files
		StorageUtility::flush( $upload_basedir );

		// Import media files
		StorageUtility::copy( $storage->getRootPath() . AI1WM_MEDIA_NAME, $upload_basedir );
	}

	/**
	 * Export media
	 *
	 * @return string
	 */
	public function export() {
		$upload_dir = wp_upload_dir();

		return $upload_dir['basedir'];
	}
}
