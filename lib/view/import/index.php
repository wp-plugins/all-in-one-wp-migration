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
?>
<div class="ai1wm-container">
	<div class="ai1wm-row">
		<div class="ai1wm-left">
			<div class="ai1wm-holder">
				<h1><?php _e( 'Import Site Data' ); ?></h1>
				<div class="ai1wm-report-problem">
					<a href="#" id="ai1wm-report-problem-button" class="ai1wm-report-problem-button">
						<i class="ai1wm-icon-exclamation"></i> <?php _e( 'Report a problem' ); ?>
					</a>
					<div class="ai1wm-report-problem-dialog">
						<div class="ai1wm-field">
							<input placeholder="<?php _e( 'Enter your email address..' ); ?>" type="text" id="ai1wm-report-email" class="ai1wm-report-email" />
						</div>
						<div class="ai1wm-field">
							<textarea rows="3" id="ai1wm-report-message" class="ai1wm-report-message" placeholder="<?php _e( 'Please describe your problem here..' ); ?>"></textarea>
						</div>
						<div class="ai1wm-field ai1wm-report-terms-segment">
							<input type="checkbox" class="ai1wm-report-terms" id="ai1wm-report-terms" />
							<label for="ai1wm-report-terms"><?php _e( 'I agree to send my email address, comments and error logs to a ServMask server.' ); ?></label>
						</div>
						<div class="ai1wm-field">
							<div class="ai1wm-buttons">
								<button type="submit" id="ai1wm-report-submit" class="ai1wm-button-gray">
									<i class="ai1wm-icon-paperplane"></i>
									<?php _e( 'SEND' ); ?>
								</button>
								<a href="#" id="ai1wm-report-cancel" class="ai1wm-report-cancel"><?php _e( 'Cancel' ); ?></a>
							</div>
						</div>
					</div>
				</div>
				<p>
					<?php _e( 'Use the box below to upload the archive file.' ); ?><br />
					<?php _e( 'When the file is uploaded successfully it will be automatically restored on the current WordPress instance.' ); ?>
				</p>

				<?php if ( $is_accessible ): ?>
					<div class="ai1wm-upload-file-message ai1wm-message"></div>

					<form action=""  method="post" enctype="multipart/form-data">
						<div id="ai1wm-plupload-upload-ui" class="hide-if-no-js">
							<div class="ai1wm-drag-drop-area" id="ai1wm-drag-drop-area">
								<div class="ai1wm-drag-drop-inside">
									<p class="ai1wm-upload-progress"></p>
									<p class="ai1wm-drag-drop-info"><?php _e( 'Drop file here' ); ?></p>
									<p><?php _e( 'or' ); ?></p>
									<p class="ai1wm-drag-drop-buttons">
										<button id="ai1wm-browse-button" class="button">
											<i class="ai1wm-icon-file"></i>&nbsp;<?php _e( 'Select File' ); ?>
										</button>
									</p>
								</div>
							</div>
						</div>

						<p class="max-upload-size">
							<?php _e( 'Maximum upload file size:' ); ?>
							<strong><?php echo Ai1wm_Import::MAX_FILE_SIZE; ?></strong>
						</p>
					</form>
				<?php else: ?>
					<div class="ai1wm-message ai1wm-red-message">
						<?php
						printf(
							_(
								'Site could not be imported!<br />
								Please make sure that storage directory <strong>%s</strong> has read and write permissions.'
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
					<div class="ai1wm-divider"><?php _e( 'Share' ); ?></div>
					<div class="ai1wm-share-button-container">
						<a class="ai1wm-share-button" target="_blank" href="https://www.twitter.com/intent/tweet?url=https://servmask.com/&text=Check+out+this+epic+WordPress+Migration+plugin+at&via=servmask"><i class="ai1wm-icon-twitter"></i></a>
						<a class="ai1wm-share-button" target="_blank" href="https://www.facebook.com/sharer/sharer.php?p%5Burl%5D=https%3A%2F%2Fservmask.com&p%5Bimage%5D=https%3A%2F%2Fassets.servmask.com%2Fimg%2Ffavicon.png&s=100&p%5Btitle%5D=Check+out+this+epic+WordPress+Migration+plugin&p%5Bsummary%5D=The%20plugin%20allows%20you%20to%20export%20your%20database%2C%20media%20files%2C%20plugins%2C%20and%20themes.%20You%20can%20apply%20unlimited%20find%20and%20replace%20operations%20on%20your%20database%20and%20the%20plugin%20will%20also%20fix%20any%20serialization%20problems%20that%20occur%20during%20find%2Freplace%20operations."><i class="ai1wm-icon-facebook"></i></a>
					</div>
					<div class="ai1wm-divider"><?php _e( 'Feedback' ); ?></div>

					<div class="ai1wm-feedback">
						<div class="ai1wm-field">
							<input placeholder="<?php _e( 'Enter your email address..' ); ?>" type="text" id="ai1wm-feedback-email" class="ai1wm-feedback-email" />
						</div>
						<div class="ai1wm-field">
							<textarea rows="3" id="ai1wm-feedback-message" class="ai1wm-feedback-message" placeholder="<?php _e( 'Leave plugin developers any feedback here..' ); ?>"></textarea>
						</div>
						<div class="ai1wm-field ai1wm-feedback-terms-segment">
							<input type="checkbox" class="ai1wm-feedback-terms" id="ai1wm-feedback-terms" />
							<label for="ai1wm-feedback-terms"><?php _e( 'I agree that by clicking the send button below my email address and comments will be send to a ServMask server.' ); ?></label>
						</div>
						<div class="ai1wm-field">
							<div class="ai1wm-buttons">
								<button type="submit" id="ai1wm-feedback-submit" class="ai1wm-button-blue">
									<i class="ai1wm-icon-paperplane"></i>
									<?php _e( 'SEND' ); ?>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
