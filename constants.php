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

$local = array(
	'ИЛИЕВ™',
	'dev.servmask.com',
	'Borislav-MacBook-Pro.local',
);

if ( function_exists( 'gethostname' ) && in_array( gethostname(), $local ) ) {
	define( 'AI1WM_DEBUG', true );
} else {
	define( 'AI1WM_DEBUG', false );
}

// ==================
// = Plugin Version =
// ==================
define( 'AI1WM_VERSION', '3.3' );

// ===============
// = Plugin Name =
// ===============
define( 'AI1WM_PLUGIN_NAME', 'all-in-one-wp-migration' );

// =================
// = Directory Index =
// =================
define( 'AI1WM_DIRECTORY_INDEX', 'index.php' );

// ================
// = Storage Path =
// ================
define( 'AI1WM_STORAGE_PATH', AI1WM_PATH . DIRECTORY_SEPARATOR . 'storage' );

// ==================
// = Error Log Path =
// ==================
define( 'AI1WM_LOG_FILE', AI1WM_STORAGE_PATH . DIRECTORY_SEPARATOR . 'error.log' );

// ===============
// = Status Path =
// ===============
define( 'AI1WM_STATUS_FILE', AI1WM_STORAGE_PATH . DIRECTORY_SEPARATOR . 'status.log' );

// ================
// = Backups Path =
// ================
define( 'AI1WM_BACKUPS_PATH', AI1WM_PATH . DIRECTORY_SEPARATOR . 'backups' );

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

// ================
// = Service Path =
// ================
define( 'AI1WM_SERVICE_PATH', AI1WM_MODEL_PATH . DIRECTORY_SEPARATOR . 'service' );

// =============
// = View Path =
// =============
define( 'AI1WM_TEMPLATES_PATH', AI1WM_LIB_PATH . DIRECTORY_SEPARATOR . 'view' );

// ===================
// = Set Bandar Path =
// ===================
define( 'BANDAR_TEMPLATES_PATH', AI1WM_TEMPLATES_PATH );

// ==================
// = Exception Path =
// ==================
define( 'AI1WM_EXCEPTION_PATH', AI1WM_LIB_PATH . DIRECTORY_SEPARATOR . 'exception' );

// ===============
// = Vendor Path =
// ===============
define( 'AI1WM_VENDOR_PATH', AI1WM_LIB_PATH . DIRECTORY_SEPARATOR . 'vendor' );

// =========================
// = ServMask Feedback Url =
// =========================
define( 'AI1WM_FEEDBACK_URL', 'https://servmask.com/ai1wm/feedback/create' );

// =======================
// = ServMask Report Url =
// =======================
define( 'AI1WM_REPORT_URL', 'https://servmask.com/ai1wm/report/create' );

// ==============================
// = ServMask Archive Tools Url =
// ==============================
define( 'AI1WM_ARCHIVE_TOOLS_URL', 'https://servmask.com/archive/tools' );

// =========================
// = ServMask Table Prefix =
// =========================
define( 'AI1WM_TABLE_PREFIX', 'SERVMASK_PREFIX_' );

// =========================
// = Archive Database Name =
// =========================
define( 'AI1WM_DATABASE_NAME', 'database.sql' );

// ========================
// = Archive Package Name =
// ========================
define( 'AI1WM_PACKAGE_NAME', 'package.json' );

// ========================
// = Archive Status Name  =
// ========================
define( 'AI1WM_STATUS_NAME', 'status.log' );

// ========================
// = Archive FileMap Name =
// ========================
define( 'AI1WM_FILEMAP_NAME', 'filemap.list' );

// ======================
// = Export Options Key =
// ======================
define( 'AI1WM_EXPORT_OPTIONS', 'ai1wm_export_options' );

// =====================
// = Error Handler Key =
// =====================
define( 'AI1WM_ERROR_HANDLER', 'ai1wm_error_handler' );

// =========================
// = Exception Handler Key =
// =========================
define( 'AI1WM_EXCEPTION_HANDLER', 'ai1wm_exception_handler' );

// ========================
// = Maintenance Mode Key =
// ========================
define( 'AI1WM_MAINTENANCE_MODE', 'ai1wm_maintenance_mode' );

// ==============
// = Secret Key =
// ==============
define( 'AI1WM_SECRET_KEY', 'ai1wm_secret_key' );

// =============
// = Auth User =
// =============
define( 'AI1WM_AUTH_USER', 'ai1wm_auth_user' );

// =================
// = Auth Password =
// =================
define( 'AI1WM_AUTH_PASSWORD', 'ai1wm_auth_password' );

// ================
// = Messages Key =
// ================
define( 'AI1WM_MESSAGES', 'ai1wm_messages' );

// =================
// = Support Email =
// =================
define( 'AI1WM_SUPPORT_EMAIL', 'support@servmask.com' );

// =================
// = Max File Size =
// =================
define( 'AI1WM_MAX_FILE_SIZE', 536870912 );

// ==================
// = Max Chunk Size =
// ==================
define( 'AI1WM_MAX_CHUNK_SIZE', 5242880 );

// =====================
// = Max Chunk Retries =
// =====================
define( 'AI1WM_MAX_CHUNK_RETRIES', 10 );

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

