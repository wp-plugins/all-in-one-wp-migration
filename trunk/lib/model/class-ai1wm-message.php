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
	protected $messages = array();

	public function __construct() {
		$this->messages = array(
			'SiteURLDepricated' => _(
				'Since version 1.8.0, Site URL is deprecated.' .
				'Upon import, the plugin auto-detects Site URL and makes necessary changes to the database.'
			),
		);

		// Prepare messages
		$msgs = array();
		$keys = get_option( AI1WM_MESSAGES );
		foreach ( array_keys( $this->messages ) as $key ) {
			if ( ! isset( $keys[$key] ) ) {
				$msgs[$key] = true;
			}
		}

		// Update messages
		if ( $msgs ) {
			update_option( AI1WM_MESSAGES, $msgs );
		}
	}

	/**
	 * Get list of all active messages
	 *
	 * @return array
	 */
	public function get_messages() {
		$msgs = array();
		$keys = get_option( AI1WM_MESSAGES );
		foreach ( $keys as $key => $active ) {
			if ( isset( $this->messages[$key] ) && $active ) {
				$msgs[$key] = $this->messages[$key];
			}
		}

		return $msgs;
	}

	/**
	 * Close message by key
	 *
	 * @param  string  $key Message key
	 * @return array
	 */
	public function close_message( $key ) {
		$errors = array();

		$keys = get_option( AI1WM_MESSAGES );
		if ( isset( $keys[$key] ) ) {
			// Deactivate message from the list
			$keys[$key] = false;

			// Update keys
			if ( ! update_option( AI1WM_MESSAGES, $keys ) ) {
				$errors[] = 'Something went wrong! Please try again later.';
			}
		} else {
			$errors[] = 'Message key does not exist in the list.';
		}

		return array( 'errors' => $errors );
	}
}
