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

// include all the files that you want to load in here
require_once AI1WM_VENDOR_PATH .
			DIRECTORY_SEPARATOR .
			'mysqldump-factory' .
			DIRECTORY_SEPARATOR .
			'mysqldump-factory' .
			DIRECTORY_SEPARATOR .
			'lib' .
			DIRECTORY_SEPARATOR .
			'MysqlDumpFactory.php';

require_once AI1WM_VENDOR_PATH .
			DIRECTORY_SEPARATOR .
			'bandar' .
			DIRECTORY_SEPARATOR .
			'bandar' .
			DIRECTORY_SEPARATOR .
			'lib' .
			DIRECTORY_SEPARATOR .
			'Bandar.php';

require_once AI1WM_VENDOR_PATH .
			DIRECTORY_SEPARATOR .
			'zip-factory' .
			DIRECTORY_SEPARATOR .
			'zip-factory' .
			DIRECTORY_SEPARATOR .
			'lib' .
			DIRECTORY_SEPARATOR .
			'ZipFactory.php';

require_once AI1WM_VENDOR_PATH .
			DIRECTORY_SEPARATOR .
			'storage-factory' .
			DIRECTORY_SEPARATOR .
			'storage-factory' .
			DIRECTORY_SEPARATOR .
			'lib' .
			DIRECTORY_SEPARATOR .
			'StorageArea.php';

require_once AI1WM_CONTROLLER_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wm-main-controller.php';

require_once AI1WM_CONTROLLER_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wm-import-controller.php';

require_once AI1WM_CONTROLLER_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wm-export-controller.php';

require_once AI1WM_CONTROLLER_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wm-feedback-controller.php';

require_once AI1WM_CONTROLLER_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wm-report-controller.php';

require_once AI1WM_MODEL_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wm-template.php';

require_once AI1WM_MODEL_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wm-export.php';

require_once AI1WM_MODEL_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wm-import.php';

require_once AI1WM_MODEL_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wm-file.php';

require_once AI1WM_MODEL_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wm-error.php';

require_once AI1WM_MODEL_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wm-feedback.php';

require_once AI1WM_MODEL_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wm-report.php';

