<?php
/**
 * Copyright (C) 2013 ServMask LLC
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
		add_action( 'wp_ajax_upload_file', 'Ai1wm_Import_Controller::upload_file' );

		// Enable or disable maintenance mode
		if ( get_option( Ai1wm_Import::MAINTENANCE_MODE ) ) {
			add_action( 'get_header', array( $this, 'activate_maintenance_mode' ) );
		}

		return $this;
	}

	/**
	 * Register listeners for filters
	 * @return Object Instance of this class
	 */
	private function activate_filters() {
		return $this;
	}

	/**
	 * Enable or disable Wordpress maintenance mode
	 * @return void
	 */
	public function activate_maintenance_mode() {
		$title = _( 'Maintenance Mode' );
		$body  = sprintf(
			'<h1>%s</h1><p>%s<br /><strong>%s%s</strong></p>',
			_( 'Website Under Maintenance' ),
			_( 'Hi, our Website is currently undergoing scheduled maintenance' ),
			_( 'Please check back very soon.' ),
			_( 'Sorry for the inconvenience!' )
		);

		wp_die( $body, $title );
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
		$_wp_version = $wp_version;
		if ( strlen( $_wp_version ) === '3' ) {
			$_wp_version += '.0';
		} else if ( strlen( $_wp_version ) === '1' ) {
			$_wp_version += '0.0';
		}
		$url = AI1WM_URL . '/lib/view/assets';
		?>
		<style type="text/css" media="all">
			@font-face {
				font-family: 'servmask';
				src:url('<?php echo esc_url( $url ); ?>/font/servmask.eot');
				src:url('<?php echo esc_url( $url ); ?>/font/servmask.eot?#iefix') format('embedded-opentype'),
					url('<?php echo esc_url( $url ); ?>/font/servmask.woff') format('woff'),
					url('<?php echo esc_url( $url ); ?>/font/servmask.ttf') format('truetype'),
					url('<?php echo esc_url( $url ); ?>/font/servmask.svg#servmask') format('svg');
				font-weight: normal;
				font-style: normal;
			}
			<?php if ( version_compare( $_wp_version, '3.8', '<' ) ) : ?>
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
			'file_data_name'      => 'input_file',
			'multiple_queues'     => false,
			'max_file_size'       => Ai1wm_Import::MAX_FILE_SIZE,
			'chunk_size'          => Ai1wm_Import::MAX_CHUNK_SIZE,
			'max_retries'         => Ai1wm_Import::MAX_CHUNK_RETRIES,
			'url'                 => admin_url( 'admin-ajax.php' ),
			'flash_swf_url'       => includes_url(
				'js/plupload/plupload.flash.swf'
			),
			'silverlight_xap_url' => includes_url(
				'js/plupload/plupload.silverlight.xap'
			),
			'filters'             => array(
				array(
					'title'      => __( 'Allowed Files' ),
					'extensions' => 'zip',
				),
			),
			'multipart'           => true,
			'urlstream_upload'    => true,
			'multipart_params'    => array(
				'action' => 'upload_file',
				'name'   => uniqid() . '.part',
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
