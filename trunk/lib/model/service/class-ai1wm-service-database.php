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

class Ai1wm_Service_Database implements Ai1wm_Service_Interface
{
	protected $options    = array();

	protected $connection = null;

	public function __construct( array $options = array() ) {
		// Set options
		$this->options = $options;

		// Make connection
		try {
			// Use PDO adapter
			$this->connection = MysqlDumpFactory::makeMysqlDump(
				DB_HOST,
				DB_USER,
				DB_PASSWORD,
				DB_NAME,
				(
					class_exists(
						'PDO'
					) && in_array( 'mysql', PDO::getAvailableDrivers() )
				)
			);
			$this->connection->getConnection();
		} catch ( Exception $e ) {
			// Use mysql adapter
			$this->connection = MysqlDumpFactory::makeMysqlDump(
				DB_HOST,
				DB_USER,
				DB_PASSWORD,
				DB_NAME,
				false
			);
		}
	}

	/**
	 * Import database
	 *
	 * @return string
	 */
	public function import() {
		global $wpdb;

		// Backup database
		$this->export();

		// Flush database
		$this->connection->flush();

		// Get configuration
		$service = new Ai1wm_Service_Package( $this->options );
		$config  = $service->import();

		$old_values = array();
		$new_values = array();

		// Get Site URL
		if ( isset( $config['SiteURL'] ) && ( $config['SiteURL'] != site_url() ) ) {
			$old_values[] = $config['SiteURL'];
			$new_values[] = site_url();
		}

		// Get Home URL
		if ( isset( $config['HomeURL'] ) && ( $config['HomeURL'] != home_url() ) ) {
			$old_values[] = $config['HomeURL'];
			$new_values[] = home_url();

			// Get Domain
			$old_domain = parse_url( $config['HomeURL'] );
			$new_domain = parse_url( home_url() );

			// Replace Domain
			$old_values[] = sprintf( '%s://%s', $old_domain['scheme'], $old_domain['host'] );
			$new_values[] = sprintf( '%s://%s', $new_domain['scheme'], $new_domain['host'] );
		}

		$database_file = StorageArea::getInstance()->makeFile( AI1WM_DATABASE_NAME );

		// Import database
		$this->connection->setOldTablePrefix( AI1WM_TABLE_PREFIX )
						 ->setNewTablePrefix( $wpdb->prefix )
						 ->setOldReplaceValues( $old_values )
						 ->setNewReplaceValues( $new_values )
						 ->import( $database_file->getName() );

		return $database_file->getName();
	}

	/**
	 * Export database
	 *
	 * @return string
	 */
	public function export() {
		global $wpdb;

		$database_file = StorageArea::getInstance()->makeFile();

		// Set include tables
		$include_tables = array();
		if ( isset( $this->options['include-tables'] ) ) {
			$include_tables = $this->options['include-tables'];
		}

		// Set exclude tables
		$exclude_tables = array();
		if ( isset( $this->options['exclude-tables' ] ) ) {
			$exclude_tables = $this->options['exclude-tables'];
		}

		$clauses = array();

		// Spam comments
		if ( isset( $this->options['export-spam-comments'] ) ) {
			$clauses[ $wpdb->comments ]    = " WHERE comment_approved != 'spam' ";
			$clauses[ $wpdb->commentmeta ] = sprintf(
				" WHERE comment_id IN ( SELECT comment_ID FROM `%s` WHERE comment_approved != 'spam' ) ",
				$wpdb->comments
			);
		}

		// Post revisions
		if ( isset( $this->options['export-revisions'] ) ) {
			$clauses[ $wpdb->posts ] = " WHERE post_type != 'revision' ";
		}

		// No table data, but leave Admin account
		$no_table_data = isset( $this->options['no-table-data'] );
		if ( $no_table_data ) {
			$clauses                    = array();
			$clauses[ $wpdb->users ]    = ' WHERE id = 1 ';
			$clauses[ $wpdb->usermeta ] = ' WHERE user_id = 1 ';
		}

		// Find and replace
		$old_values = array();
		$new_values = array();
		if ( isset( $this->options['replace'] ) && ( $replace = $this->options['replace'] ) ) {
			for ( $i = 0; $i < count( $replace['old-value'] ); $i++ ) {
				if ( isset( $replace['old-value'][$i] ) && isset( $replace['new-value'][$i] ) ) {
					$old_values[] = $replace['old-value'][$i];
					$new_values[] = $replace['new-value'][$i];
				}
			}
		}

		// Set dump options
		$this->connection->setFileName( $database_file->getName() )
						 ->setIncludeTables( $include_tables )
						 ->setExcludeTables( $exclude_tables )
						 ->setNoTableData( $no_table_data )
						 ->setOldTablePrefix( $wpdb->prefix )
						 ->setNewTablePrefix( AI1WM_TABLE_PREFIX )
						 ->setOldReplaceValues( $old_values )
						 ->setNewReplaceValues( $new_values )
						 ->setQueryClauses( $clauses );

		// Export database
		$this->connection->export();

		return $database_file->getName();
	}
}
