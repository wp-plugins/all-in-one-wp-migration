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
class Ai1wm_Service_Package implements Ai1wm_Service_Interface {

	protected $args    = array();

	protected $storage = null;

	public function __construct( array $args = array() ) {
		$this->args = $args;
	}

	/**
	 * Import package configuration
	 *
	 * @return array
	 */
	public function import() {
		global $wp_version;

		$config = array();

		// Get package file
		$package = file_get_contents( $this->storage()->package() );
		if ( false === $package ) {
			throw new Ai1wm_Import_Exception( 'Unable to read package.json file' );
		}

		// Get config data
		if ( ( $config = json_decode( $package, true ) ) ) {

			// Add plugin version
			if ( ! isset( $config['Plugin']['Version'] ) ) {
				$config['Plugin']['Version'] = AI1WM_VERSION;
			}

			// Add WordPress version
			if ( ! isset( $config['WordPress']['Version'] ) ) {
				$config['WordPress']['Version'] = $wp_version;
			}

			// Add WordPress content
			if ( ! isset( $config['WordPress']['Content'] ) ) {
				$config['WordPress']['Content'] = WP_CONTENT_DIR;
			}

			// Add user identity
			if ( ! isset( $config['Import']['User'] ) && ! empty( $config['Export']['User'] ) ) {
				$config['Import']['User'] = array( 'Id' => get_current_user_id() );

				// Save package file
				$package = file_put_contents( $this->storage()->package(), json_encode( $config ) );
				if ( false === $package ) {
					throw new Ai1wm_Import_Exception( 'Unable to write package.json file' );
				}
			}
		} else {
			throw new Ai1wm_Import_Exception( 'Unable to parse package.json file' );
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

		// Get options
		$options = wp_load_alloptions();

		// Get Site URL
		$site_url = site_url();
		if ( isset( $options['siteurl'] ) ) {
			$site_url = rtrim( $options['siteurl'], '/' );
		}

		// Get Home URL
		$home_url = home_url();
		if ( isset( $options['home'] ) ) {
			$home_url = rtrim( $options['home'], '/' );
		}

		// Default configuration
		$config = array(
			'SiteURL'   => $site_url,
			'HomeURL'   => $home_url,
			'Plugin'    => array( 'Version' => AI1WM_VERSION ),
			'WordPress' => array( 'Version' => $wp_version, 'Content' => WP_CONTENT_DIR ),
		);

		// Get user identity
		if ( apply_filters( 'ai1wm_keep_user_identity_on_export', false ) ) {
			$config['Export']['User'] = array( 'Id' => get_current_user_id() );
		}

		// Save package file
		$package = file_put_contents( $this->storage()->package(), json_encode( $config ) );
		if ( false === $package ) {
			throw new Ai1wm_Import_Exception( 'Unable to write package.json file' );
		}
	}

	/*
	 * Get storage object
	 *
	 * @return Ai1wm_Storage
	 */
	protected function storage() {
		if ( $this->storage === null ) {
			$this->storage = new Ai1wm_Storage( $this->args );
		}

		return $this->storage;
	}
}
