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
 */
?>
<div class="ai1wm-container">
	<div class="ai1wm-row">
		<div class="ai1wm-left">
			<div class="ai1wm-holder">
				<h1><?php _e( 'Import Site Data' ); ?></h1>

				<p>
					<?php _e( 'Use the box below to upload the archive file.' ); ?><br />
					<?php _e( 'When the file is uploaded successfully it will be autimatically restored on the current WordPress instanace.' ); ?>
				</p>

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
						<strong><?php _e( wp_max_upload_size() / 1024 / 1024 ); ?> <?php _e( 'MB' ); ?></strong>
					</p>
				</form>
			</div>
		</div>
		<div class="ai1wm-right">
			<div class="ai1wm-sidebar">
				<div class="ai1wm-segment">
					<div class="ai1wm-divider"><?php _e( 'Feedback' ); ?></div>

					<div id="ai1wm-feedback">
						<div class="ai1wm-field">
							<input placeholder="<?php _e( 'Enter your email address..' ); ?>" type="text" id="ai1wm-feedback-email" class="ai1wm-feedback-email" name="" value="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>" />
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
