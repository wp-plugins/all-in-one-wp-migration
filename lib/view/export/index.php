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
				<h1><?php _e( 'Export Site Data' ); ?></h1>
				<p>
				<?php _e( 'Use the form below to replace any data from the current database.' ); ?><br />
				<?php _e( 'In the first field, enter a string that you want to search for, in the second field enter another string to replace the first string with.' ); ?>
				</p>
				<form action="" method="post" id="ai1wm-export-form">

					<div class="ai1wm-replace-row">
						<div class="ai1wm-field-inline">
							<input type="text" value="<?php echo get_bloginfo( 'url' ); ?>" placeholder="<?php _e( 'Current Site URL' ); ?>" name="options[replace][old-value][]" id="old-value-1" />
						</div>

						<div class="ai1wm-field-inline">
							<input type="text" value="" placeholder="<?php _e( 'New Website URL (ex. https://servmask.com)' ); ?>" name="options[replace][new-value][]" id="new-value-1" />
						</div>
						<div class="ai1wm-clear"></div>
					</div>

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
					<button class="ai1wm-button-gray" id="add-new-replace-button"><i class="ai1wm-icon-plus"></i>&nbsp;ADD MORE</button>

					<div class="ai1wm-divider"><?php _e( 'Options' ); ?></div>

					<div class="ai1wm-field">
						<div class="ai1wm-checkbox">
							<input type="checkbox" id="export-spam-comments" name="options[export-spam-comments]" />
							<label for="export-spam-comments"><?php _e( 'Do not export spam comments' ); ?></label>
						</div>
					</div>

					<div class="ai1wm-field">
						<div class="ai1wm-checkbox">
							<input type="checkbox" id="export-revisions" name="options[export-revisions]" />
							<label for="export-revisions"><?php _e( 'Do not export post revisions' ); ?></label>
						</div>
					</div>

					<div class="ai1wm-accordion">
						<div class="ai1wm-title">
							<i class="ai1wm-icon-arrow-right"></i>
							<?php _e( 'Advanced settings' ); ?>
						</div>
						<div class="ai1wm-clear"></div>
						<div class="ai1wm-content">
							<div class="ai1wm-field">
								<div class="ai1wm-checkbox">
									<input type="checkbox" id="export-media" name="options[export-media]" />
									<label for="export-media"><?php _e( 'Do not export media library (files)' ); ?></label>
								</div>
							</div>

							<div class="ai1wm-field">
								<div class="ai1wm-checkbox">
									<input type="checkbox" id="export-themes" name="options[export-themes]" />
									<label for="export-themes"><?php _e( 'Do not export themes (files)' ); ?></label>
								</div>
							</div>

							<div class="ai1wm-field">
								<div class="ai1wm-checkbox">
									<input type="checkbox" id="export-plugins" name="options[export-plugins]" />
									<label for="export-plugins"><?php _e( 'Do not export plugins (files)' ); ?></label>
								</div>
								<?php foreach ( $list_plugins as $key => $plugin ): ?>
									<input type="hidden" name="options[include-plugins][<?php _e( $key ); ?>]" value="<?php _e( $plugin['Name'] ); ?>" />
								<?php endforeach; ?>
							</div>

							<div class="ai1wm-field">
								<div class="ai1wm-checkbox">
									<input type="checkbox" id="export-database" name="options[export-database]" />
									<label for="export-database"><?php _e( 'Do not export database (sql)' ); ?></label>
								</div>
							</div>

							<div class="ai1wm-field">
								<div class="ai1wm-checkbox">
									<input type="checkbox" id="no-table-data" name="options[no-table-data]" />
									<label for="no-table-data"><?php _e( 'Do not export table data' ); ?></label>
								</div>
							</div>
						</div>
					</div>

					<div class="ai1wm-field">
						<div class="ai1wm-buttons">
							<button type="submit" name="options[action]" value="export" class="ai1wm-button-green">
								<i class="ai1wm-icon-arrow-down"></i>
								<?php _e( 'EXPORT PACKAGE' ); ?>
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
		<div class="ai1wm-right">
			<div class="ai1wm-sidebar">
				<div class="ai1wm-segment">
					<div class="ai1wm-divider"><?php _e( 'Help' ); ?>&nbsp;<i class="ai1wm-icon-help"></i></div>

					<p>
						<?php _e( 'You can easily export database, media, themes and plugins by single click.' ); ?><br />
						<?php _e( 'For more advanced usage the plugin provides highly customizable interface where you can select what type of data to be exported and what options to be applied.' ); ?>
					</p>

					<p><?php _e( 'Quick steps' ); ?></p>
					<ol>
						<li><?php _e( 'Change Website URL parameter and any other related to your work.' ); ?></li>
						<li><?php _e( 'Select desired export options and advanced settings.' ); ?></li>
						<li><?php _e( 'Press "Export Package" button and the archive file will pop up in the browser.' ); ?></li>
						<li><?php _e( 'Now the file is ready and you can import it in your WordPress environments.' ); ?></li>
					</ol>

					<p>
						<?php _e( 'For any comments or suggestions please use the feedback form below.' ); ?><br />
						<?php _e( 'Thanks for using our product.' ); ?>
					</p>

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
