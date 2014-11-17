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

// ==================
// = Plugin Version =
// ==================
define( 'AI1WM_VERSION', '2.0.4' );

// ===============
// = Plugin Name =
// ===============
define( 'AI1WM_PLUGIN_NAME', 'all-in-one-wp-migration' );

// ===============
// = Storage Index =
// ===============
define( 'AI1WM_STORAGE_INDEX', 'index.php' );

// ===============
// = Storage Path =
// ===============
define( 'AI1WM_STORAGE_PATH', AI1WM_PATH . DIRECTORY_SEPARATOR . 'storage' );

// ============
// = Lib Path =
// ============
define( 'AI1WM_LIB_PATH', AI1WM_PATH . DIRECTORY_SEPARATOR . 'lib' );

// ===================
// = Controller Path =
// ===================
define( 'AI1WM_CONTROLLER_PATH', AI1WM_LIB_PATH . DIRECTORY_SEPARATOR . 'controller' );

// ==============
// = Model Path =
// ==============
define( 'AI1WM_MODEL_PATH', AI1WM_LIB_PATH . DIRECTORY_SEPARATOR . 'model' );

// ==============
// = Service Path =
// ==============
define( 'AI1WM_SERVICE_PATH', AI1WM_MODEL_PATH . DIRECTORY_SEPARATOR . 'service' );

// =============
// = View Path =
// =============
define( 'BANDAR_TEMPLATES_PATH', AI1WM_LIB_PATH . DIRECTORY_SEPARATOR . 'view' );

// ==================
// = Exception Path =
// ==================
define( 'AI1WM_EXCEPTION_PATH', AI1WM_LIB_PATH . DIRECTORY_SEPARATOR . 'exception' );

// ===============
// = Vendor Path =
// ===============
define( 'AI1WM_VENDOR_PATH', AI1WM_LIB_PATH . DIRECTORY_SEPARATOR . 'vendor' );

// ==============
// = ServMask Feedback Url =
// ==============
define( 'AI1WM_FEEDBACK_URL', 'https://servmask.com/ai1wm/feedback/create' );

// ==============
// = ServMask Report Url =
// ==============
define( 'AI1WM_REPORT_URL', 'https://servmask.com/ai1wm/report/create' );

// ==============
// = ServMask Table Prefix =
// ==============
define( 'AI1WM_TABLE_PREFIX', 'SERVMASK_PREFIX_' );

// ==============
// = Archive Database Name =
// ==============
define( 'AI1WM_DATABASE_NAME', 'database.sql' );

// ==============
// = Archive Media Name =
// ==============
define( 'AI1WM_MEDIA_NAME', 'media' );

// ==============
// = Archive Sites Name =
// ==============
define( 'AI1WM_SITES_NAME', 'sites' );

// ==============
// = Archive Blogs Name =
// ==============
define( 'AI1WM_BLOGS_NAME', 'blogs.dir' );

// ==============
// = Archive Themes Name =
// ==============
define( 'AI1WM_THEMES_NAME', 'themes' );

// ==============
// = Archive Plugins Name =
// ==============
define( 'AI1WM_PLUGINS_NAME', 'plugins' );

// ==============
// = Archive Package Name =
// ==============
define( 'AI1WM_PACKAGE_NAME', 'package.json' );

// ==============
// = Export Options Key =
// ==============
define( 'AI1WM_EXPORT_OPTIONS', 'ai1wm_export_options' );

// ==============
// = Error Handler Key =
// ==============
define( 'AI1WM_ERROR_HANDLER', 'ai1wm_error_handler' );

// ==============
// = Exception Handler Key =
// ==============
define( 'AI1WM_EXCEPTION_HANDLER', 'ai1wm_exception_handler' );

// ==============
// = Maintenance Mode Key =
// ==============
define( 'AI1WM_MAINTENANCE_MODE', 'ai1wm_maintenance_mode' );

// ==============
// = Messages Key =
// ==============
define( 'AI1WM_MESSAGES', 'ai1wm_messages' );

// ==============
// = Max File Size =
// ==============
define( 'AI1WM_MAX_FILE_SIZE', '512MB' );

// ==============
// = Max Chunk Size =
// ==============
define( 'AI1WM_MAX_CHUNK_SIZE', '500KB' );

// ==============
// = Max Chunk Retries =
// ==============
define( 'AI1WM_MAX_CHUNK_RETRIES', '100' );

// ===========================
// = WP_CONTENT_DIR Constant =
// ===========================
if ( ! defined( 'WP_CONTENT_DIR' ) ) {
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
}

// ==========================
// = WP_PLUGIN_DIR Constant =
// ==========================
if ( ! defined( 'WP_PLUGIN_DIR' ) ) {
	define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
}

