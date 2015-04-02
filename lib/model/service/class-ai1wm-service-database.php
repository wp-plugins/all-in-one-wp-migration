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
	protected $args       = array();

	protected $storage    = null;

	protected $connection = null;

	public function __construct( array $args = array() ) {
		$this->args = $args;

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

		// Get configuration
		$service = new Ai1wm_Service_Package( $this->args );
		$config  = $service->import();

		$old_values = array();
		$new_values = array();

		// Get Site URL
		if ( isset( $config['SiteURL'] ) && ( $config['SiteURL'] !== site_url() ) ) {
			$old_values[] = $config['SiteURL'];
			$new_values[] = site_url();

			// Get Domain
			$old_domain = parse_url( $config['SiteURL'] );
			$new_domain = parse_url( site_url() );

			// Replace Domain
			$old_values[] = sprintf( '%s://%s', $old_domain['scheme'], $old_domain['host'] );
			$new_values[] = sprintf( '%s://%s', $new_domain['scheme'], $new_domain['host'] );

			// Replace Host
			if ( stripos( site_url(), $old_domain['host'] ) === false && stripos( home_url(), $old_domain['host'] ) === false ) {
				$old_values[] = $old_domain['host'];
				$new_values[] = $new_domain['host'];
			}

			// Replace Path
			$old_values[] = isset( $old_domain['path'] ) && ( $old_domain['path'] !== '/' ) ? trailingslashit( $old_domain['path'] ) : null;
			$new_values[] = isset( $new_domain['path'] ) ? trailingslashit( $new_domain['path'] ) : '/';
		}

		// Get Home URL
		if ( isset( $config['HomeURL'] ) && ( $config['HomeURL'] !== home_url() ) ) {
			$old_values[] = $config['HomeURL'];
			$new_values[] = home_url();
		}

		// Get WordPress Content
		if ( isset( $config['WordPress']['Content'] ) && ( $config['WordPress']['Content'] !== WP_CONTENT_DIR ) ) {
			$old_values[] = $config['WordPress']['Content'];
			$new_values[] = WP_CONTENT_DIR;
		}

		// Get user details
		if ( isset( $config['Import']['User']['Id'] ) && ( $id = $config['Import']['User']['Id'] ) ) {
			$meta = get_userdata( $id );
			$user = array(
				'user_login'           => $meta->user_login,
				'user_pass'            => $meta->user_pass,
				'user_nicename'        => $meta->user_nicename,
				'user_url'             => $meta->user_url,
				'user_email'           => $meta->user_email,
				'display_name'         => $meta->display_name,
				'nickname'             => $meta->nickname,
				'first_name'           => $meta->first_name,
				'last_name'            => $meta->last_name,
				'description'          => $meta->description,
				'rich_editing'         => $meta->rich_editing,
				'user_registered'      => $meta->user_registered,
				'jabber'               => $meta->jabber,
				'aim'                  => $meta->aim,
				'yim'                  => $meta->yim,
				'show_admin_bar_front' => $meta->show_admin_bar_front,
			);
		} else {
			$user = array();
		}

		// Get HTTP user
		$auth_user = get_site_option( AI1WM_AUTH_USER, false, false );

		// Get HTTP password
		$auth_password = get_site_option( AI1WM_AUTH_PASSWORD, false, false );

		// Get secret key
		$secret_key = get_site_option( AI1WM_SECRET_KEY, false, false );

		// Flush database
		$this->connection->flush();

		// Import database
		$this->connection->setOldTablePrefix( AI1WM_TABLE_PREFIX )
						 ->setNewTablePrefix( $wpdb->prefix )
						 ->setOldReplaceValues( $old_values )
						 ->setNewReplaceValues( $new_values )
						 ->import( $this->storage()->database() );

		// Clear WP options cache
		wp_cache_flush();

		// Set new user identity
		if ( isset( $config['Export']['User']['Id'] ) && ( $id = $config['Export']['User']['Id'] ) ) {

			// Update user login and password
			if ( isset( $user['user_login'] ) && isset( $user['user_pass'] ) ) {
				$wpdb->update(
					$wpdb->users,
					array( 'user_login' => $user['user_login'], 'user_pass' => $user['user_pass'] ),
					array( 'ID' => $id ),
					array( '%s', '%s' ),
					array( '%d' )
				);

				// Unset user login
				unset( $user['user_login'] );

				// Unset user password
				unset( $user['user_pass'] );
			}

			// Update user details
			$result = wp_update_user( array( 'ID' => $id ) + $user );

			// Log the error
			if ( is_wp_error( $result ) ) {
				Ai1wm_Log::error( 'Exception while importing user identity: ' . $result->get_error_message() );
			}
		}

		// Set the new HTTP user
		update_site_option( AI1WM_AUTH_USER, $auth_user );

		// Set the new HTTP password
		update_site_option( AI1WM_AUTH_PASSWORD, $auth_password );

		// Set the new secret key value
		update_site_option( AI1WM_SECRET_KEY, $secret_key );
	}

	/**
	 * Export database
	 *
	 * @return string
	 */
	public function export() {
		global $wpdb;

		// Set include tables
		$include_tables = array();
		if ( isset( $this->args['options']['include-tables'] ) ) {
			$include_tables = $this->args['options']['include-tables'];
		}

		// Set exclude tables
		$exclude_tables = array();
		if ( isset( $this->args['options']['exclude-tables' ] ) ) {
			$exclude_tables = $this->args['options']['exclude-tables'];
		}

		$clauses = array();

		// Spam comments
		if ( isset( $this->args['options']['no-spam-comments'] ) ) {
			$clauses[ $wpdb->comments ]    = " WHERE comment_approved != 'spam' ";
			$clauses[ $wpdb->commentmeta ] = sprintf(
				" WHERE comment_id IN ( SELECT comment_ID FROM `%s` WHERE comment_approved != 'spam' ) ",
				$wpdb->comments
			);
		}

		// Post revisions
		if ( isset( $this->args['options']['no-revisions'] ) ) {
			$clauses[ $wpdb->posts ] = " WHERE post_type != 'revision' ";
		}

		// No table data, but leave Admin account
		$no_table_data = isset( $this->args['options']['no-table-data'] );
		if ( $no_table_data ) {
			$clauses                    = array();
			$clauses[ $wpdb->users ]    = ' WHERE id = 1 ';
			$clauses[ $wpdb->usermeta ] = ' WHERE user_id = 1 ';
		}

		// Find and replace
		$old_values = array();
		$new_values = array();
		if ( isset( $this->args['options']['replace'] ) && ( $replace = $this->args['options']['replace'] ) ) {
			for ( $i = 0; $i < count( $replace['old-value'] ); $i++ ) {
				if ( isset( $replace['old-value'][$i] ) && isset( $replace['new-value'][$i] ) ) {
					$old_values[] = $replace['old-value'][$i];
					$new_values[] = $replace['new-value'][$i];
				}
			}
		}

		// Set dump options
		$this->connection->setFileName( $this->storage()->database() )
						 ->setIncludeTables( $include_tables )
						 ->setExcludeTables( $exclude_tables )
						 ->setNoTableData( $no_table_data )
						 ->setOldTablePrefix( $wpdb->prefix )
						 ->setNewTablePrefix( AI1WM_TABLE_PREFIX )
						 ->setOldReplaceValues( $old_values )
						 ->setNewReplaceValues( $new_values )
						 ->setQueryClauses( $clauses )
						 ->setIgnoreTableReplaces( array( $wpdb->postmeta => array() ) );

		// Export database
		$this->connection->export();
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
