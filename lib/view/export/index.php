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
				<h1><i class="ai1wm-icon-export"></i> <?php _e( 'Export Site' ); ?></h1>
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

				<form action="" method="post" id="ai1wm-export-form" class="ai1wm-clear">

					<?php foreach ( $messages as $key => $text ): ?>
						<div class="ai1wm-message ai1wm-info-message">
							<a href="#" class="ai1wm-message-close-button" data-key="<?php echo $key; ?>"><i class="ai1wm-icon-close"></i></a>
							<?php echo $text; ?>
						</div>
					<?php endforeach; ?>

					<ul id="ai1wm-queries">
						<li class="ai1wm-collapsable ai1wm-expanded">
							<p>
								<span>
									<strong><?php _e( 'Find' ); ?></strong>
									<small class="ai1wm-query-find-text ai1wm-tooltip" title="Search the database for this text"><?php _e( '&lt;text&gt;' ); ?></small>
									<strong><?php _e( 'Replace with' ); ?></strong>
									<small class="ai1wm-query-replace-text ai1wm-tooltip" title="Replace the database with this text"><?php _e( '&lt;another-text&gt;' ); ?></small>
									<strong><?php _e( 'in the database' ); ?></strong>
								</span>
								<span class="ai1wm-collapse-arrow ai1wm-icon-chevron-right"></span>
							</p>
							<div>
								<input class="ai1wm-query-find-input" type="text" placeholder="<?php _e( 'Find' ); ?>" name="options[replace][old-value][]" />
								<input class="ai1wm-query-replace-input" type="text" placeholder="<?php _e( 'Replace with' ); ?>" name="options[replace][new-value][]" />
							</div>
						</li>
					</ul>

					<button class="ai1wm-button-gray" id="ai1wm-add-new-replace-button">
						<i class="ai1wm-icon-plus2"></i><?php _e( 'Add another' ); ?>
					</button>

					<div class="ai1wm-accordion">
						<div class="ai1wm-title">
							<h4>
								<i class="ai1wm-icon-arrow-right"></i>
								<?php _e( 'Advanced options' ); ?>
								<small><?php _e( ' (click to expand)' ); ?></small>
							</h4>
						</div>
						<div class="ai1wm-clear"></div>
						<div class="ai1wm-content">
							<div class="ai1wm-field">
								<input type="checkbox" id="export-spam-comments" name="options[export-spam-comments]" />
								<label for="export-spam-comments"><?php _e( 'Do <strong>not</strong> export spam comments' ); ?></label>
							</div>

							<div class="ai1wm-field">
								<input type="checkbox" id="export-revisions" name="options[export-revisions]" />
								<label for="export-revisions"><?php _e( 'Do <strong>not</strong> export post revisions' ); ?></label>
							</div>
							<div class="ai1wm-field">
								<input type="checkbox" id="export-media" name="options[export-media]" />
								<label for="export-media"><?php _e( 'Do <strong>not</strong> export media library (files)' ); ?></label>
							</div>

							<div class="ai1wm-field">
								<input type="checkbox" id="export-themes" name="options[export-themes]" />
								<label for="export-themes"><?php _e( 'Do <strong>not</strong> export themes (files)' ); ?></label>
							</div>

							<div class="ai1wm-field">
								<input type="checkbox" id="export-plugins" name="options[export-plugins]" />
								<label for="export-plugins"><?php _e( 'Do <strong>not</strong> export plugins (files)' ); ?></label>
							</div>

							<div class="ai1wm-field">
								<input type="checkbox" id="export-database" name="options[export-database]" />
								<label for="export-database"><?php _e( 'Do <strong>not</strong> export database (sql)' ); ?></label>
							</div>

							<div class="ai1wm-field">
								<input type="checkbox" id="no-table-data" name="options[no-table-data]" />
								<label for="no-table-data"><?php _e( 'Do <strong>not</strong> export table data' ); ?></label>
							</div>
						</div>
					</div>

					<div class="ai1wm-field">
						<?php if ( $is_accessible ): ?>
							<div class="ai1wm-export-stats">
								<input type="checkbox" id="ai1wm-export-stats" />
								<label for="ai1wm-export-stats">
									<?php _e( 'Send usage data to ServMask Inc. This allows us to improve your experience with the plugin.' ); ?>
									<a class="ai1wm-no-underline" href="https://www.iubenda.com/privacy-policy/946881" target="_blank">Privacy Policy</a>
								</label>
							</div>
							<div class="ai1wm-buttons">
								<input type="hidden" name="options[action]" value="export" />
								<button type="button" id="ai1wm-export-button" class="ai1wm-button-green">
									<i class="ai1wm-icon-arrow-down"></i> <?php _e( 'Export' ); ?>
								</button>
							</div>
						<?php else: ?>
							<div class="ai1wm-message ai1wm-red-message">
								<?php
								printf(
									_(
										'<h3>Site could not be exported!</h3>' .
										'<p>Please make sure that storage directory <strong>%s</strong> has read and write permissions.</p>'
									),
									AI1WM_STORAGE_PATH
								);
								?>
							</div>
						<?php endif; ?>
					</div>

					<div id="ai1wm-export-modal" class="ai1wm-modal ai1wm-not-visible">
						<div class="ai1wm-modal-content-middle">
							<div class="ai1wm-modal-left">
								<div
									class="fb-like"
									data-href="https://www.facebook.com/servmaskproduct"
									data-layout="box_count"
									data-action="recommend"
									data-show-faces="true"
									data-share="false"
								></div>
							</div>
							<div class="ai1wm-modal-right">
								<a href="https://wordpress.org/support/view/plugin-reviews/all-in-one-wp-migration?filter=5#postform" class="ai1wm-modal-social-button" target="_blank">
									<i class="ai1wm-icon-wordpress2"></i><br /> <?php _e( 'Write a review' ); ?>
								</a>
							</div>
						</div>

						<div class="ai1wm-modal-action">
							<p>
								<span class="ai1wm-loader"></span>
							</p>
							<p>
								<span><?php _e( 'Your download will begin shortly ...' ); ?></span>
							</p>
						</div>
					</div>
				</form>
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
					<h2><?php _e( 'Help Center' ); ?></h2>

					<p>
						<?php _e( 'This screen allows you to export database, media files, themes and plugins as one zipped file.' ); ?><br />
						<?php _e( 'You can then use the import functionality provided by this plugin to import the zipped file onto any other WordPress sites that you have administrator access to.' ); ?>
					</p>

					<p>
						<strong><?php _e( 'Quick hints' ); ?></strong>
					</p>

					<ul>
						<li>
							<i class="ai1wm-icon-arrow-right"></i>
							<?php _e( 'In the advanced settings section you can configure more precisely the way of exporting.' ); ?>
						</li>
						<li>
							<i class="ai1wm-icon-arrow-right"></i>
							<?php _e( 'Press "Export" button and the site archive file will pop up in your browser.' ); ?>
						</li>
						<li>
							<i class="ai1wm-icon-arrow-right"></i>
							<?php _e( 'Once the file is successfully downloaded on your computer, you can import it to any of your WordPress sites.' ); ?>
						</li>
					</ul>

					<p>
						<?php _e( 'Thank you for using our product.' ); ?>
					</p>

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
