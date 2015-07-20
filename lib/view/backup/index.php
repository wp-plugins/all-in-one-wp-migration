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
				<h1><i class="ai1wm-icon-export"></i> <?php _e( 'Backups', AI1WM_PLUGIN_NAME ); ?></h1>

				<?php include AI1WM_TEMPLATES_PATH . '/common/report-problem.php'; ?>

				<?php if ( is_readable( AI1WM_BACKUPS_PATH ) && is_writable( AI1WM_BACKUPS_PATH ) ): ?>
					<div class="ai1wm-clear">
						<?php if ( $total_space ): ?>
							<p id="ai1wm-backup-size">
								<?php _e( 'Available disk space', AI1WM_PLUGIN_NAME ); ?>
								<strong><?php echo size_format( $free_space, 2 ); ?></strong>
							</p>

							<?php $progress = ceil( ( $free_space / $total_space ) * 100 ); ?>

							<div id="ai1wm-backup-progress">
								<div id="ai1wm-backup-progress-bar" style="width: <?php echo $progress; ?>%;">
									<?php echo $progress; ?>%
								</div>
							</div>
						<?php endif; ?>
					</div>

					<table class="ai1wm-backups <?php echo empty( $backups ) ? 'ai1wm-hide' : null; ?>">
						<thead>
							<tr>
								<th class="ai1wm-column-name"><?php _e( 'Name', AI1WM_PLUGIN_NAME ); ?></th>
								<th class="ai1wm-column-date"><?php _e( 'Date', AI1WM_PLUGIN_NAME ); ?></th>
								<th class="ai1wm-column-size"><?php _e( 'Size', AI1WM_PLUGIN_NAME ); ?></th>
								<th class="ai1wm-column-actions"></th>
							</tr>
						</thead>

						<tbody>
							<?php foreach ( $backups as $backup ): ?>
							<tr>
								<td class="ai1wm-column-name">
									<i class="ai1wm-icon-file-zip"></i>
									<?php echo $backup['filename']; ?>
								</td>
								<td class="ai1wm-column-date">
									<?php echo human_time_diff( $backup['mtime'] ); ?> <?php _e( 'ago', AI1WM_PLUGIN_NAME ); ?>
								</td>
								<td class="ai1wm-column-size">
									<?php echo size_format( $backup['size'], 2 ); ?>
								</td>
								<td class="ai1wm-column-actions ai1wm-backup-actions">
									<a href="<?php echo AI1WM_BACKUPS_URL . '/' . $backup['filename']; ?>" class="ai1wm-button-green ai1wm-button-alone ai1wm-backup-download">
										<i class="ai1wm-icon-arrow-down ai1wm-icon-alone"></i>
										<span><?php _e( 'Download', AI1WM_PLUGIN_NAME ); ?></span>
									</a>
									<a href="<?php echo network_admin_url( 'admin.php?page=site-migration-import&restore-file=' . $backup['filename'] ); ?>" class="ai1wm-button-gray ai1wm-button-alone ai1wm-backup-restore">
										<i class="ai1wm-icon-cloud-upload ai1wm-icon-alone"></i>
										<span><?php _e( 'Restore', AI1WM_PLUGIN_NAME ); ?></span>
									</a>
									<a href="#" data-delete-file="<?php echo $backup['filename']; ?>" class="ai1wm-button-red ai1wm-button-alone ai1wm-backup-delete">
										<i class="ai1wm-icon-close ai1wm-icon-alone"></i>
										<span><?php _e( 'Delete', AI1WM_PLUGIN_NAME ); ?></span>
									</a>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>

					<div class="ai1wm-backups-empty <?php echo empty( $backups ) ? null : 'ai1wm-hide'; ?>">
						<p><?php _e( 'There are no backups available at this time, why not create a new one?', AI1WM_PLUGIN_NAME ); ?></p>
						<p>
							<a href="<?php echo network_admin_url( 'admin.php?page=site-migration-export' ); ?>" class="ai1wm-button-green">
								<i class="ai1wm-icon-export"></i>
								<?php _e( 'Create backup', AI1WM_PLUGIN_NAME ); ?>
							</a>
						</p>
					</div>
				<?php else: ?>
					<br />
					<br />
					<div class="ai1wm-clear ai1wm-message ai1wm-red-message">
						<?php
						printf(
							__(
								'<h3>Site could not create backups!</h3>' .
								'<p>Please make sure that storage directory <strong>%s</strong> has read and write permissions.</p>',
								AI1WM_PLUGIN_NAME
							),
							AI1WM_STORAGE_PATH
						);
						?>
					</div>
				<?php endif; ?>
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

					<?php if ( isset( $_SERVER['AUTH_TYPE'] ) ) : ?>
						<?php include AI1WM_TEMPLATES_PATH . '/common/http-authentication.php'; ?>
					<?php endif; ?>

				</div>
			</div>
		</div>
	</div>
</div>
