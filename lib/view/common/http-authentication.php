<h2><?php _e( 'HTTP Basic Authentication', AI1WM_PLUGIN_NAME ); ?></h2>

<form method="POST" action="">
	<div class="ai1wm-field">
		<label for="ai1wm-username"><?php _e( 'Username', AI1WM_PLUGIN_NAME ); ?></label>
		<input type="text" placeholder="<?php _e( 'Enter Username', AI1WM_PLUGIN_NAME ); ?>" id="ai1wm-username" name="ai1wm-username" class="ai1wm-http-authentication" value="<?php echo $username; ?>" />
	</div>

	<div class="ai1wm-field">
		<label for="ai1wm-password"><?php _e( 'Password', AI1WM_PLUGIN_NAME ); ?></label>
		<input type="password" placeholder="<?php _e( 'Enter Password', AI1WM_PLUGIN_NAME ); ?>" id="ai1wm-password" name="ai1wm-password" class="ai1wm-http-authentication" value="<?php echo $password; ?>" />
	</div>

	<div class="ai1wm-field">
		<button type="submit" class="ai1wm-button-blue">
			<i class="ai1wm-icon-save"></i>
			<?php _e( 'Update', AI1WM_PLUGIN_NAME ); ?>
		</button>
	</div>
</form>