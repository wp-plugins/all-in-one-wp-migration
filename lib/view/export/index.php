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
?>

<div class="ai1wm-container">
	<div class="ai1wm-row">
		<div class="ai1wm-left">
			<?php include AI1WM_TEMPLATES_PATH . '/common/maintenance-mode.php'; ?>

			<div class="ai1wm-holder">
				<?php foreach ( apply_filters( 'ai1wm-messages', array() ) as $type => $message ): ?>
					<div class="ai1wm-message ai1wm-<?php echo $type; ?>-message">
						<?php echo $message; ?>
					</div>
				<?php endforeach; ?>

				<h1><i class="ai1wm-icon-export"></i> <?php _e( 'Export Site', AI1WM_PLUGIN_NAME ); ?></h1>

				<?php include AI1WM_TEMPLATES_PATH . '/common/report-problem.php'; ?>

				<form action="" method="post" id="ai1wm-export-form" class="ai1wm-clear">

					<?php foreach ( $messages as $key => $text ): ?>
						<div class="ai1wm-message ai1wm-info-message">
							<a href="#" class="ai1wm-message-close-button" data-key="<?php echo $key; ?>">
								<i class="ai1wm-icon-close"></i>
							</a>
							<?php echo $text; ?>
						</div>
					<?php endforeach; ?>

					<?php include AI1WM_TEMPLATES_PATH . '/export/find-replace.php'; ?>

					<?php do_action( 'ai1wm_export_left_options' ); ?>

					<?php include AI1WM_TEMPLATES_PATH . '/export/advanced-settings.php'; ?>

					<?php include AI1WM_TEMPLATES_PATH . '/export/export-buttons.php'; ?>

					<?php do_action( 'ai1wm_export_left_end' ); ?>
				</form>
			</div>
		</div>
		<div class="ai1wm-right">
			<div class="ai1wm-sidebar">
				<div class="ai1wm-segment">

					<?php if ( ! AI1WM_DEBUG ) : ?>
						<?php include AI1WM_TEMPLATES_PATH . '/common/share-buttons.php'; ?>
					<?php endif; ?>

					<h2><?php _e( 'Leave Feedback', AI1WM_PLUGIN_NAME ); ?></h2>

					<?php include AI1WM_TEMPLATES_PATH . '/common/leave-feedback.php'; ?>

				</div>
			</div>
		</div>
	</div>
</div>
