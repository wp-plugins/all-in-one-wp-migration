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
<div id="fb-root"></div>
<script>
	(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=597242117012725&version=v2.0";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
</script>

<div class="ai1wm-container">
	<div class="ai1wm-row">
		<div class="ai1wm-left">
			<?php if ( Ai1wm_Maintenance::active() ): ?>
				<div class="ai1wm-update-nag">
					<?php echo _e( 'Maintenance Mode is <strong>ON</strong>, switch it to <a href="#" id="ai1wm-maintenance-off">OFF</a>' ); ?>
				</div>
			<?php endif; ?>

			<div class="ai1wm-holder">
				<h1><i class="ai1wm-icon-publish"></i> <?php _e( 'Import Site' ); ?></h1>
				<div class="ai1wm-report-problem">
					<button id="ai1wm-report-problem-button" class="ai1wm-button-red">
						<i class="ai1wm-icon-notification"></i> <?php _e( 'Report issue' ); ?>
					</button>

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
								<button type="submit" id="ai1wm-report-submit" class="ai1wm-button-blue">
									<i class="ai1wm-icon-paperplane"></i>
									<?php _e( 'Send' ); ?>
								</button>
								<a href="#" id="ai1wm-report-cancel" class="ai1wm-report-cancel"><?php _e( 'Cancel' ); ?></a>
							</div>
						</div>
					</div>
				</div>

				<p class="ai1wm-clear">
					<?php _e( 'Use the box below to upload the archive file.' ); ?><br />
					<?php _e( 'When the file is successfully uploaded, it will be automatically restored on the current WordPress instance.' ); ?>
				</p>

				<?php if ( $is_accessible ): ?>
					<div class="ai1wm-import-messages"></div>

					<div class="ai1wm-upload-form">
						<form action=""  method="post" enctype="multipart/form-data">
							<div class="hide-if-no-js" id="ai1wm-plupload-upload-ui">
								<div class="ai1wm-drag-drop-area" id="ai1wm-drag-drop-area">
									<div id="ai1wm-upload-init">
										<p>
											<i class="ai1wm-icon-cloud-upload"></i><br />
											<?php _e( 'Drag & Drop to upload' ); ?>
										</p>
										<button id="ai1wm-browse-button" class="ai1wm-button-gray">
											<?php _e( 'or, SELECT A FILE' ); ?>
										</button>
									</div>
								</div>
							</div>
						</form>

						<div id="ai1wm-upload-in-progress">
							<div id="ai1wm-upload-progress">
								<div id="ai1wm-upload-progress-bar"></div>
							</div>
							<p id="ai1wm-upload-text">
								<?php _e( 'Uploading' ); ?>
								<strong id="ai1wm-upload-file-name"></strong>
							</p>
							<p id="ai1wm-install-text">
								<?php _e( 'Installing' ); ?>
								<strong id="ai1wm-install-file-name"></strong>
							</p>
							<p id="ai1wm-complete-text">
								<?php _e( 'Installation completed. Follow the instructions listed above.' ); ?>
							</p>
							<button id="ai1wm-upload-cancel" class="ai1wm-button-red">
								<?php _e( 'Cancel' ); ?>
							</button>
						</div>
					</div>

					<p>
						<?php _e( 'Maximum upload file size:' ); ?>
						<?php if ( $max_file_size ): ?>
							<span class="ai1wm-max-upload-size"><?php echo $max_file_size; ?></span>
							<span class="ai1wm-unlimited-import">
								<a href="https://servmask.com/get-unlimited" class="ai1wm-label">
									<i class="ai1wm-icon-notification"></i>
									<?php _e( 'Get unlimited' ); ?>
								</a>
							</span>
						<?php else: ?>
							<span class="ai1wm-max-upload-size"><?php _e( 'Unlimited' ); ?></span>
						<?php endif; ?>
					</p>
				<?php else: ?>
					<div class="ai1wm-message ai1wm-red-message">
						<?php
						printf(
							_(
								'Site could not be imported!<br />' .
								'Please make sure that storage directory <strong>%s</strong> has read and write permissions.'
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
					<div class="ai1wm-share-button-container">
						<span>
							<a
								href="https://twitter.com/share"
								class="twitter-share-button"
								data-url="https://servmask.com"
								data-text="Check this epic WordPress Migration plugin"
								data-via="servmask"
								data-related="servmask"
								data-hashtags="servmask"
							>
								<?php _e( 'Tweet' ); ?>
							</a>
							<script>
								!function (d,s,id) {
									var js,
										fjs = d.getElementsByTagName(s)[0],
										p   = /^http:/.test(d.location) ? 'http' : 'https';

									if (!d.getElementById(id)) {
										js = d.createElement(s);
										js.id = id;
										js.src = p+'://platform.twitter.com/widgets.js';
										fjs.parentNode.insertBefore(js, fjs);
									}
								}(document, 'script', 'twitter-wjs');
							</script>
						</span>
						<span>
							<div
								class="fb-like ai1wm-top-negative-four"
								data-href="https://www.facebook.com/servmaskproduct"
								data-layout="button_count"
								data-action="recommend"
								data-show-faces="true"
								data-share="false"
							></div>
						</span>
					</div>

					<h2><?php _e( 'Leave Feedback' ); ?></h2>

					<div class="ai1wm-feedback">
						<ul class="ai1wm-feedback-types">
							<li>
								<input type="radio" class="ai1wm-flat-radio-button ai1wm-feedback-type" id="ai1wm-feedback-type-1" name="ai1wm-feedback-type" value="review" />
								<a id="ai1wm-feedback-type-link-1" href="https://wordpress.org/support/view/plugin-reviews/all-in-one-wp-migration?rate=5#postform" target="_blank">
									<i></i>
									<span><?php _e( 'I would like to review this plugin' ); ?></span>
								</a>
							</li>
							<li>
								<input type="radio" class="ai1wm-flat-radio-button ai1wm-feedback-type" id="ai1wm-feedback-type-2" name="ai1wm-feedback-type" value="suggestions" />
								<label for="ai1wm-feedback-type-2">
									<i></i>
									<span><?php _e( 'I have ideas to improve this plugin' ); ?></span>
								</label>
							</li>
							<li>
								<input type="radio" class="ai1wm-flat-radio-button ai1wm-feedback-type" id="ai1wm-feedback-type-3" name="ai1wm-feedback-type" value="help-needed" />
								<label for="ai1wm-feedback-type-3">
									<i></i>
									<span><?php _e( 'I need help with this plugin' ); ?></span>
								</label>
							</li>
						</ul>

						<div class="ai1wm-feedback-form">
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
										<?php _e( 'Send' ); ?>
									</button>
									<a class="ai1wm-feedback-cancel" id="ai1wm-feedback-cancel" href="#"><?php _e( 'Cancel' ); ?></a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
