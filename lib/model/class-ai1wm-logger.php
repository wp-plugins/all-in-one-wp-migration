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
class Ai1wm_Logger {

	/**
	 * Log debug data
	 *
	 * @return boolean
	 */
	public static function debug( $key, array $data = array() ) {
		global $wp_version;

		// Meta options
		$data['plugin_version']        = AI1WM_VERSION;
		$data['wp_version']            = $wp_version;
		$data['php_version']           = phpversion();
		$data['php_uname']             = php_uname();
		$data['max_execution_time']    = ini_get( 'max_execution_time' );
		$data['memory_limit']          = ini_get( 'memory_limit' );
		$data['memory_get_peak_usage'] = memory_get_peak_usage();
		$data['memory_get_usage']      = memory_get_usage();
		$data['PDO_available']         = class_exists( 'PDO' ) ? 1 : 0;
		$data['site_url']              = site_url();
		$data['home_url']              = home_url();

		return update_site_option( $key, $data );
	}

	/**
	 * Log error data
	 *
	 * @return boolean
	 */
	public static function error( $key, array $data = array() ) {
		return update_site_option( $key, $data );
	}

	/**
	 * Log info data
	 *
	 * @return boolean
	 */
	public static function info( $key, array $data = array() ) {
		return update_site_option( $key, $data );
	}
}
