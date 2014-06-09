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

class Ai1wm_Message
{
	const MESSAGE_INFO       = 'info';
	const MESSAGE_INFO_CLOSE = 'ai1wm_message_info_close';

	/**
	 * Close message dialog by type
	 *
	 * @param  string $type Type of message
	 * @return array
	 */
	public function close_message( $type ) {
		$errors = array();

		if ( $type == self::MESSAGE_INFO ) {
			update_option( self::MESSAGE_INFO_CLOSE, true );
		} else {
			$errors[] = 'Unregonized message type.';
		}

		return array( 'errors' => $errors );
	}

	/**
	 * Is message dialog closed
	 *
	 * @param  string  $type Type of message
	 * @return boolean
	 */
	public function is_closed( $type  ) {
		if ( $type == self::MESSAGE_INFO ) {
			return get_option( self::MESSAGE_INFO_CLOSE );
		}

		return false;
	}
}
