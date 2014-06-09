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
			<div class="ai1wm-holder">
				<h1><?php _e( 'Export Site Data' ); ?></h1>
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
					<?php _e( 'Use the form below to replace any data from the current database.' ); ?><br />
					<?php _e( 'In the first field, enter a string that you want to search for, in the second field enter another string to replace the first string with.' ); ?>
				</p>

				<form action="" method="post" id="ai1wm-export-form">

					<?php if ( ! $is_closed ): ?>
						<div class="ai1wm-replace-row">
							<div class="ai1wm-message ai1wm-blue-message">
								<a href="#" id="ai1wm-message-close-button" class="ai1wm-close-button" data-type="info"><i class="ai1wm-icon-close"></i></a>
								<?php _e( 'Since version 1.8.0, Site URL is deprecated. Upon import, the plugin auto-detects Site URL and makes necessary changes to the database.' ); ?>
							</div>
						</div>
					<?php endif; ?>

					<div class="ai1wm-replace-row">
						<div class="ai1wm-field-inline">
							<input type="text" value="" placeholder="<?php _e( 'Find' ); ?>" name="options[replace][old-value][]" id="old-value-1" />
						</div>

						<div class="ai1wm-field-inline">
							<input type="text" value="" placeholder="<?php _e( 'Replace with' ); ?>" name="options[replace][new-value][]" id="new-value-1" />
						</div>
						<div class="ai1wm-clear"></div>
					</div>
					<div class="ai1wm-clear"></div>
					<button class="ai1wm-button-gray" id="add-new-replace-button"><i class="ai1wm-icon-plus"></i><?php _e( 'ADD MORE' ); ?></button>

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
								<div class="ai1wm-checkbox-v2">
									<input type="checkbox" id="export-spam-comments" name="options[export-spam-comments]" />
									<label for="export-spam-comments"><?php _e( 'Do <strong>not</strong> export spam comments' ); ?></label>
								</div>
							</div>

							<div class="ai1wm-field">
								<div class="ai1wm-checkbox-v2">
									<input type="checkbox" id="export-revisions" name="options[export-revisions]" />
									<label for="export-revisions"><?php _e( 'Do <strong>not</strong> export post revisions' ); ?></label>
								</div>
							</div>
							<div class="ai1wm-field">
								<div class="ai1wm-checkbox-v2">
									<input type="checkbox" id="export-media" name="options[export-media]" />
									<label for="export-media"><?php _e( 'Do <strong>not</strong> export media library (files)' ); ?></label>
								</div>
							</div>

							<div class="ai1wm-field">
								<div class="ai1wm-checkbox-v2">
									<input type="checkbox" id="export-themes" name="options[export-themes]" />
									<label for="export-themes"><?php _e( 'Do <strong>not</strong> export themes (files)' ); ?></label>
								</div>
							</div>

							<div class="ai1wm-field">
								<div class="ai1wm-checkbox-v2">
									<input type="checkbox" id="export-plugins" name="options[export-plugins]" />
									<label for="export-plugins"><?php _e( 'Do <strong>not</strong> export plugins (files)' ); ?></label>
								</div>
								<?php foreach ( $list_plugins as $key => $plugin ): ?>
									<input type="hidden" name="options[include-plugins][<?php _e( $key ); ?>]" value="<?php _e( $plugin['Name'] ); ?>" />
								<?php endforeach; ?>
							</div>

							<div class="ai1wm-field">
								<div class="ai1wm-checkbox-v2">
									<input type="checkbox" id="export-database" name="options[export-database]" />
									<label for="export-database"><?php _e( 'Do <strong>not</strong> export database (sql)' ); ?></label>
								</div>
							</div>

							<div class="ai1wm-field">
								<div class="ai1wm-checkbox-v2">
									<input type="checkbox" id="no-table-data" name="options[no-table-data]" />
									<label for="no-table-data"><?php _e( 'Do <strong>not</strong> export table data' ); ?></label>
								</div>
							</div>
						</div>
					</div>

					<div class="ai1wm-field">
						<?php if ( $is_accessible ): ?>
							<div class="ai1wm-checkbox-v2 ai1wm-export-stats">
								<input type="checkbox" id="ai1wm-export-stats" />
								<label for="ai1wm-export-stats">
									<?php _e( 'Send usage data to ServMask Inc. This allows us to improve your experience with the plugin.' ); ?>
									<a href="https://www.iubenda.com/privacy-policy/946881" target="_blank">Privacy Policy</a>
								</label>
							</div>
							<div class="ai1wm-buttons">
								<input type="hidden" name="options[action]" value="export" />
								<button
									type="submit"
									id="ai1wm-export-button"
									class="ai1wm-button-green">
									<i class="ai1wm-icon-arrow-down"></i>
									<?php _e( 'EXPORT PACKAGE' ); ?>
								</button>
							</div>
						<?php else: ?>
							<div class="ai1wm-message ai1wm-red-message">
								<?php
								printf(
									_(
										'Site could not be exported!<br />
										Please make sure that storage directory <strong>%s</strong> has read and write permissions.'
									),
									AI1WM_STORAGE_PATH
								);
								?>
							</div>
						<?php endif; ?>
					</div>
				</form>
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
					<div class="ai1wm-divider"><?php _e( 'Help' ); ?>&nbsp;<i class="ai1wm-icon-help"></i></div>

					<p>
						<?php _e( 'This screen allows you to export database, media files, themes and plugins as one zipped file.' ); ?><br />
						<?php _e( 'You can then use the import functionality provided by this plugin to import the zipped file onto any other WordPress site that you have administrator access to.' ); ?>
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
							<?php _e( 'Press "Export Package" button and the site archive file will pop up in your browser.' ); ?>
						</li>
						<li>
							<i class="ai1wm-icon-arrow-right"></i>
							<?php _e( 'Once the file is successfully downloaded on your computer you can import it in all of your WordPress sites.' ); ?>
						</li>
					</ul>

					<p>
						<?php _e( 'For any comments or suggestions please use the feedback form below.' ); ?><br />
						<?php _e( 'Thanks for using our product.' ); ?>
					</p>

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
