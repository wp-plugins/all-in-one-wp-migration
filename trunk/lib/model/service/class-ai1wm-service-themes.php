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

class Ai1wm_Service_Themes implements Ai1wm_Service_Interface
{
	protected $options = array();

	public function __construct( array $options = array() ) {
		$this->options = $options;
	}

	/**
	 * Import themes
	 *
	 * @return void
	 */
	public function import() {
		$storage = StorageArea::getInstance();

		// Themes directory
		$themes_dir = get_theme_root();
		if ( ! is_dir( $themes_dir ) ) {
			mkdir( $themes_dir );
		}

		// Backup themes files
		$backup_themes_to = $storage->makeDirectory();

		StorageUtility::copy( $themes_dir, $backup_themes_to->getName() );

		// Flush themes files
		StorageUtility::flush( $themes_dir );

		// Import themes files
		StorageUtility::copy( $storage->getRootPath() . AI1WM_THEMES_NAME, $themes_dir );
	}

	/**
	 * Export themes
	 *
	 * @return string
	 */
	public function export() {
		return get_theme_root();
	}
}
