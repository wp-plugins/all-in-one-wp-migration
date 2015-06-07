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
class Ai1wm_Export_File extends Ai1wm_Export_Abstract {

	public function export() {
		// Set progress
		Ai1wm_Status::set( array( 'message' => __( 'Renaming exported file...', AI1WM_PLUGIN_NAME ) ) );

		// Close achive file
		$archive = new Ai1wm_Compressor( $this->storage()->archive() );

		// Append EOF block
		$archive->close( true );

		// Rename archive file
		if ( rename( $this->storage()->archive(), $this->storage()->backup() ) ) {

			// Set progress
			Ai1wm_Status::set(
				array(
					'type'    => 'download',
					'message' => sprintf(
						__(
							'<a href="%s/%s" class="ai1wm-button-green ai1wm-emphasize">' .
							'<span>Download %s</span>' .
							'<em>Size: %s</em>' .
							'</a>',
							AI1WM_PLUGIN_NAME
						),
						AI1WM_BACKUPS_URL,
						basename( $this->storage()->backup() ),
						parse_url( home_url(), PHP_URL_HOST ),
						size_format( filesize( $this->storage()->backup() ) )
					)
				),
				$this->storage()->status() // status.log file
			);
		}
	}
}
