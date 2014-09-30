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

class Ai1wm_Maintenance
{
	/**
	 * Enable WordPress maintenance mode
	 *
	 * @return boolean
	 */
	public static function enable() {
		return update_option( AI1WM_MAINTENANCE_MODE, true );
	}

	/**
	 * Disable WordPress maintenance mode
	 *
	 * @return boolean
	 */
	public static function disable() {
		update_option( AI1WM_MAINTENANCE_MODE, false );
	}

	public static function active() {
		return get_option( AI1WM_MAINTENANCE_MODE );
	}

	/**
	 * Display Wordpress maintenance mode
	 *
	 * @return void
	 */
	public static function display() {
		if ( self::active() ) {
			$title = _( 'Maintenance Mode' );
			$body  = sprintf(
				'<h1>%s</h1><p>%s<br /><strong>%s%s</strong></p>',
				_( 'Website Under Maintenance' ),
				_( 'Hi, our Website is currently undergoing scheduled maintenance' ),
				_( 'Please check back very soon. ' ),
				_( 'Sorry for the inconvenience!' )
			);

			wp_die( $body, $title );
		}
	}
}
