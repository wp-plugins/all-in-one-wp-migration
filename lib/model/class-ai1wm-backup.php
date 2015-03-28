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
class Ai1wm_Backup {

	/**
	 * Get all backup files
	 *
	 * @return array
	 */
	public function get_files() {
		$backups  = array();

		try {
			$iterator = new RegexIterator(
				new DirectoryIterator( AI1WM_BACKUPS_PATH ),
				'/^(.+)-(\d+)-(\d+)-(\d+)\.wpress$/',
				RegexIterator::GET_MATCH
			);

			foreach ( $iterator as $item ) {
				try {
					$backup = new Ai1wm_File;
					$backup->setFile( $item[0] );
					$backup->setName( $item[1] );
					$backup->setSize( $iterator->getSize() );
					$backup->setCreatedAt( strtotime( "{$item[2]} {$item[3]}" ) );

					// Add backup file
					$backups[] = $backup;
				} catch ( Exception $e ) {
					// Log the error
					Ai1wm_Log::error( 'Exception while listing backup file: ' . $e->getMessage() );
				}
			}
		} catch ( Exception $e ) {
			$backups = array();
		}

		// Sort backups by most recent first
		usort( $backups, array( $this, 'compare' ) );

		return $backups;
	}

	/**
	 * Delete file
	 *
	 * @param  string  $file File name
	 * @return boolean
	 */
	public function delete_file( $file ) {
		if ( empty( $file ) ) {
			throw new Ai1wm_Backup_Exception( __( 'File name is not specified.', AI1WM_PLUGIN_NAME ) );
		} else if ( ! unlink( AI1WM_BACKUPS_PATH . DIRECTORY_SEPARATOR . $file ) ) {
			throw new Ai1wm_Backup_Exception(
				sprintf(
					__( 'Unable to delete <strong>"%s"</strong> file.', AI1WM_PLUGIN_NAME ),
					AI1WM_BACKUPS_PATH . DIRECTORY_SEPARATOR . $file
				)
			);
		}

		return true;
	}

	/**
	 * Get free disk space
	 *
	 * @return integer
	 */
	public function get_free_space() {
		return @disk_free_space( AI1WM_BACKUPS_PATH );
	}

	/**
	 * Get total disk space
	 *
	 * @return integer
	 */
	public function get_total_space() {
		return @disk_total_space( AI1WM_BACKUPS_PATH );
	}

	/**
	 * Compare backup files by created at
	 *
	 * @param  Ai1wm_File $a File object
	 * @param  Ai1wm_File $b File object
	 * @return integer
	 */
	public function compare( $a, $b ) {
		if ( $a->getCreatedAt() === $b->getCreatedAt() ) {
			return 0;
		}

		return ( $a->getCreatedAt() > $b->getCreatedAt() ) ? - 1 : 1;
	}
}
