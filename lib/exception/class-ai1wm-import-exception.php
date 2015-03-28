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
class Ai1wm_Import_Exception extends Exception {

	/**
	 * Exception title
	 * @type string
	 */
	protected $title;

	/**
	 * Extend constructor to accept message and title
	 *
	 * We want to be able to set the title of our modal and default Exception class
	 * doesn't have at title property so we have to extend the constructor to accept one
	 *
	 * @param string $message Exception message
	 * @param string $title   Exception title
	 */
	public function __construct( $message = '', $title = '' ) {
		// initialize our parent
		parent::__construct( $message );

		$this->title = $title;
	}

	/**
	 * Get exception title
	 *
	 * We check if the title is set and return it if it is,
	 * if title is not set (empty string), we return a default string
	 * Site could not be imported, which should be a catch all phrase
	 *
	 * @return string
	 */
	public function get_title() {
		if ( empty( $this->title ) ) {
			// no title is set, return default title
			return __( 'Site could not be imported', AI1WM_PLUGIN_NAME );
		} else {
			return $this->title;
		}
	}

	/**
	 * Get exception message
	 *
	 * We are creating our own WordPress code-style compatible function to
	 * return the value of getMessage method of our parent, Exception, class.
	 * This is only necessary so that we have a common way to name our methods
	 *
	 * @return string
	 */
	public function get_message() {
		return $this->getMessage();
	}
}
