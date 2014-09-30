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

class Ai1wm_Service_Package implements Ai1wm_Service_Interface
{
	protected $options = array();

	public function __construct( array $options = array() ) {
		$this->options = $options;
	}

	/**
	 * Import package configuration
	 *
	 * @return array
	 */
	public function import() {
		global $wp_version;

		// Get config file
		$data = file_get_contents( StorageArea::getInstance()->getRootPath() . AI1WM_PACKAGE_NAME );

		// Parse config file
		$config = json_decode( $data, true );

		// Add plugin version
		if ( ! isset( $config['Plugin']['Version'] ) ) {
			$config['Plugin']['Version'] = AI1WM_VERSION;
		}

		// Add wordpress version
		if ( ! isset( $config['WordPress']['Version'] ) ) {
			$config['WordPress']['Version'] = $wp_version;
		}

		return $config;
	}

	/**
	 * Export package configuration
	 *
	 * @return string
	 */
	public function export() {
		global $wp_version;

		$config = array(
			'SiteURL' => site_url(),
			'HomeURL' => home_url(),
			'Plugin' => array( 'Version' => AI1WM_VERSION ),
			'WordPress' => array( 'Version' => $wp_version ),
		);

		return json_encode( $config );
	}
}
