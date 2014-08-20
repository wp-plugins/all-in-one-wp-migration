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

class Ai1wm_Main_Controller
{
	/**
	 * Main Application Controller
	 *
	 * @return Ai1wm_Main_Controller
	 */
	public function __construct() {
		register_activation_hook(
			AI1WM_PLUGIN_NAME .
			DIRECTORY_SEPARATOR .
			AI1WM_PLUGIN_NAME .
			'.php',
			array( $this, 'activation_hook' )
		);
		$this
			->activate_actions()
			->activate_filters();
	}

	/**
	 * Activation hook callback
	 * @return Object Instance of this class
	 */
	public function activation_hook() {
		// Load plugin text domain.
		// $this->load_textdomain();
	}

	/**
	 * Initializes language domain for the plugin
	 * @return Object Instance of this class
	 */
	private function load_textdomain() {
		return $this;
	}

	/**
	 * Register listeners for actions
	 * @return Object Instance of this class
	 */
	private function activate_actions() {
		if ( is_multisite() ) {
			add_action( 'network_admin_menu', array( $this, 'admin_menu' ) );
		} else {
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		}
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'init', array( $this, 'router' ) );
		add_action( 'wp_ajax_leave_feedback', 'Ai1wm_Feedback_Controller::leave_feedback' );
		add_action( 'wp_ajax_report_problem', 'Ai1wm_Report_Controller::report_problem' );
		add_action( 'wp_ajax_import', 'Ai1wm_Import_Controller::import' );
		add_action( 'wp_ajax_close_message', 'Ai1wm_Message_Controller::close_message' );
		add_action( 'wp_ajax_disable_maintenance', 'Ai1wm_Maintenance::disable' );
		add_action( 'get_header', 'Ai1wm_Maintenance::display' );

		// Add a links to plugin list page
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );

		return $this;
	}

	/**
	 * Add a links to plugin list page
	 * @return void
	 */
	public 	function plugin_row_meta( $links, $file ) {
		if ( $file == AI1WM_PLUGIN_BASENAME ) {
			$links[] = sprintf( __( '<a href="%s" target="_blank">Get Support</a>', AI1WM_PLUGIN_NAME ), 'https://servmask.com/#contactModal' );
		}

		return $links;
	}

	/**
	 * Register listeners for filters
	 * @return Object Instance of this class
	 */
	private function activate_filters() {
		return $this;
	}

	/**
	 * Register plugin menus
	 */
	public function admin_menu() {
		// top level WP Migration menu
		add_menu_page(
			'Site Migration',
			'Site Migration',
			'export',
			'site-migration-export',
			'Ai1wm_Export_Controller::index',
			'',
			76
		);

		// sublevel Import menu
		$export_page_hook_suffix = add_submenu_page(
			'site-migration-export',
			'Export',
			'Export',
			'export',
			'site-migration-export',
			'Ai1wm_Export_Controller::index'
		);
		add_action(
			'admin_print_scripts-' . $export_page_hook_suffix,
			array( $this, 'register_export_scripts_and_styles' )
		);

		// sublevel Import menu
		$import_page_hook_suffix = add_submenu_page(
			'site-migration-export',
			'Import',
			'Import',
			'import',
			'site-migration-import',
			'Ai1wm_Import_Controller::index'
		);
		add_action(
			'admin_print_scripts-' . $import_page_hook_suffix,
			array( $this, 'register_import_scripts_and_styles' )
		);
	}

	/**
	 * Outputs menu icon between head tags
	 */
	public function admin_head() {
		global $wp_version;
		$url = AI1WM_URL . '/lib/view/assets';
		?>
		<style type="text/css" media="all">
			@font-face {
				font-family: 'servmask';
				src:url('<?php echo esc_url( $url ); ?>/font/servmask.eot?v=<?php echo AI1WM_VERSION; ?>');
				src:url('<?php echo esc_url( $url ); ?>/font/servmask.eot?v=<?php echo AI1WM_VERSION; ?>#iefix') format('embedded-opentype'),
					url('<?php echo esc_url( $url ); ?>/font/servmask.woff?v=<?php echo AI1WM_VERSION; ?>') format('woff'),
					url('<?php echo esc_url( $url ); ?>/font/servmask.ttf?v=<?php echo AI1WM_VERSION; ?>') format('truetype'),
					url('<?php echo esc_url( $url ); ?>/font/servmask.svg?v=<?php echo AI1WM_VERSION; ?>#servmask') format('svg');
				font-weight: normal;
				font-style: normal;
			}
			<?php if ( version_compare( $wp_version, '3.8', '<' ) ) : ?>
				.toplevel_page_site-migration-export > div.wp-menu-image {
					background: none !important;
				}
				.toplevel_page_site-migration-export > div.wp-menu-image:before {
					line-height: 27px !important;
					content: '\e603' !important;
					font-family: 'servmask' !important;
					speak: none !important;
					font-style: normal !important;
					font-weight: normal !important;
					font-variant: normal !important;
					text-transform: none !important;
					margin-left: 7px;
					/* Better Font Rendering =========== */
					-webkit-font-smoothing: antialiased !important;
					-moz-osx-font-smoothing: grayscale !important;
				}
			<?php else : ?>
			.toplevel_page_site-migration-export > div.wp-menu-image:before {
				content: '\e603' !important;
				font-family: 'servmask' !important;
				speak: none !important;
				font-style: normal !important;
				font-weight: normal !important;
				font-variant: normal !important;
				text-transform: none !important;
				line-height: 1 !important;
				/* Better Font Rendering =========== */
				-webkit-font-smoothing: antialiased !important;
				-moz-osx-font-smoothing: grayscale !important;
			}
			<?php endif; ?>
		</style>
		<?php
	}

	/**
	 * Register scripts and styles for Export Controller
	 */
	public function register_export_scripts_and_styles() {
		wp_enqueue_script(
			'ai1wm-js-export',
			Ai1wm_Template::asset_link( 'javascript/export.min.js' ),
			array( 'plupload-all', 'jquery' )
		);
		wp_enqueue_style(
			'ai1wm-css-export',
			Ai1wm_Template::asset_link( 'css/export.min.css' )
		);
		$feedback_init = array(
			'ajax' => array(
				'url' => admin_url( 'admin-ajax.php' ) . '?action=leave_feedback',
			),
		);
		wp_localize_script( 'ai1wm-js-export', 'ai1wm_feedback', $feedback_init );
		$report_init = array(
			'ajax' => array(
				'url' => admin_url( 'admin-ajax.php' ) . '?action=report_problem',
			),
		);
		wp_localize_script( 'ai1wm-js-export', 'ai1wm_report', $report_init );
		$message_init = array(
			'ajax' => array(
				'url' => admin_url( 'admin-ajax.php' ) . '?action=close_message',
			),
		);
		wp_localize_script( 'ai1wm-js-export', 'ai1wm_message', $message_init );
		$maintenance_init = array(
			'ajax' => array(
				'url' => admin_url( 'admin-ajax.php' ) . '?action=disable_maintenance',
			),
		);
		wp_localize_script( 'ai1wm-js-export', 'ai1wm_maintenance', $maintenance_init );
	}

	/**
	 * Register scripts and styles for Import Controller
	 */
	public function register_import_scripts_and_styles() {
		wp_enqueue_script(
			'ai1wm-js-import',
			Ai1wm_Template::asset_link( 'javascript/import.min.js' ),
			array( 'plupload-all', 'jquery' )
		);
		wp_enqueue_style(
			'ai1wm-css-import',
			Ai1wm_Template::asset_link( 'css/import.min.css' )
		);
		$plupload_init = array(
			'runtimes'            => 'html5,silverlight,flash,html4',
			'browse_button'       => 'ai1wm-browse-button',
			'container'           => 'ai1wm-plupload-upload-ui',
			'drop_element'        => 'ai1wm-drag-drop-area',
			'file_data_name'      => 'upload-file',
			'max_file_size'       => apply_filters( 'ai1wm_max_file_size', AI1WM_MAX_FILE_SIZE ),
			'chunk_size'          => apply_filters( 'ai1wm_max_chunk_size', AI1WM_MAX_CHUNK_SIZE ),
			'max_retries'         => apply_filters( 'ai1wm_max_chunk_retries', AI1WM_MAX_CHUNK_RETRIES ),
			'url'                 => admin_url( 'admin-ajax.php' ),
			'flash_swf_url'       => includes_url( 'js/plupload/plupload.flash.swf' ),
			'silverlight_xap_url' => includes_url( 'js/plupload/plupload.silverlight.xap' ),
			'multiple_queues'     => false,
			'urlstream_upload'    => true,
			'unique_names'        => true,
			'multipart'           => true,
			'multipart_params'    => array(
				'action' => 'import',
			),
			'filters'             => array(
				array(
					'title'      => __( 'Allowed Files' ),
					'extensions' => 'zip',
				),
			),
		);
		wp_localize_script( 'ai1wm-js-import', 'ai1wm_uploader', $plupload_init );
		$feedback_init = array(
			'ajax' => array(
				'url' => admin_url( 'admin-ajax.php' ) . '?action=leave_feedback',
			),
		);
		wp_localize_script( 'ai1wm-js-import', 'ai1wm_feedback', $feedback_init );
		$report_init = array(
			'ajax' => array(
				'url' => admin_url( 'admin-ajax.php' ) . '?action=report_problem',
			),
		);
		wp_localize_script( 'ai1wm-js-import', 'ai1wm_report', $report_init );
		$maintenance_init = array(
			'ajax' => array(
				'url' => admin_url( 'admin-ajax.php' ) . '?action=disable_maintenance',
			),
		);
		wp_localize_script( 'ai1wm-js-import', 'ai1wm_maintenance', $maintenance_init );
		$import_init = array(
			'ajax' => array(
				'url' => admin_url( 'admin-ajax.php' ) . '?action=import',
			),
		);
		wp_localize_script( 'ai1wm-js-import', 'ai1wm_import', $import_init );
	}

	/**
	 * Register initial router
	 */
	public function router() {
		if ( isset( $_POST['options']['action'] ) && ( $action = $_POST['options']['action'] ) ) {
			switch ( $action ) {
				case 'export':
					Ai1wm_Export_Controller::export();
					break;

				case 'staging':
					Ai1wm_Staging_Controller::deploy();
					break;

				case 'production':
					Ai1wm_Production_Controller::deploy();
					break;
			}
		}
	}
}
