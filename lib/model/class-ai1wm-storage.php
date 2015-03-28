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
class Ai1wm_Storage {

	protected $storage = null;

	protected $archive = null;

	public function __construct( array $args = array() ) {
		// Set storage
		if ( isset( $args['storage'] ) ) {
			$this->storage = basename( $args['storage'] );
		}

		// Set archive
		if ( isset( $args['archive'] ) ) {
			$this->archive = basename( $args['archive'] );
		}
	}

	/**
	 * Get storage path
	 *
	 * @return string
	 */
	public function path() {
		if ( ! isset( $this->storage ) ) {
			throw new Ai1wm_Storage_Exception( 'Storage name is not configured.' );
		}

		// Make storage directory
		$path = AI1WM_STORAGE_PATH . DIRECTORY_SEPARATOR . $this->storage;
		if ( ! is_dir( $path ) ) {
			mkdir( $path );
		}

		return $path;
	}

	/**
	 * Get archive path
	 *
	 * @return string
	 */
	public function archive() {
		if ( ! isset( $this->archive ) ) {
			throw new Ai1wm_Storage_Exception( 'Archive name is not configured.' );
		}

		// Use backup if needed for restore purposes
		if ( file_exists( $this->backup() ) ) {
			return $this->backup();
		}

		return $this->path() . DIRECTORY_SEPARATOR . $this->archive;
	}

	/**
	 * Get backup path
	 *
	 * @return string
	 */
	public function backup() {
		if ( ! isset( $this->archive ) ) {
			throw new Ai1wm_Storage_Exception( 'Archive name is not configured.' );
		}

		return AI1WM_BACKUPS_PATH . DIRECTORY_SEPARATOR . $this->archive;
	}

	/**
	 * Get package file path
	 *
	 * @return string
	 */
	public function package() {
		return $this->path() . DIRECTORY_SEPARATOR . AI1WM_PACKAGE_NAME;
	}

	/**
	 * Get file map path
	 *
	 * @return string
	 */
	public function filemap() {
		return $this->path() . DIRECTORY_SEPARATOR . AI1WM_FILEMAP_NAME;
	}

	/**
	 * Get database file path
	 *
	 * @return string
	 */
	public function database() {
		return $this->path() . DIRECTORY_SEPARATOR . AI1WM_DATABASE_NAME;
	}

	/**
	 * Get status file path
	 *
	 * @return string
	 */
	public function status() {
		return $this->path() . DIRECTORY_SEPARATOR . AI1WM_STATUS_NAME;
	}

	/**
	 * Clean storage path
	 *
	 * @return void
	 */
	public function clean() {
		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($this->path()),
			RecursiveIteratorIterator::CHILD_FIRST
		);

		foreach ($iterator as $item) {
            // Skip dots
            if ($iterator->isDot()) {
                continue;
            }

			if ($item->isFile()) {
				unlink($item->getPathname());
			} else {
				rmdir($item->getPathname());
			}
		}

		// Remove storage path
		rmdir($this->path());
	}
}
