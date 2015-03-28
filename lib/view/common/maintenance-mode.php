<?php if ( Ai1wm_Maintenance::active() ): ?>
	<div class="ai1wm-message-warning">
		<?php
			_e(
				'Maintenance Mode is <strong>ON</strong>, switch it to ' .
				'<a href="#" id="ai1wm-maintenance-off">OFF</a>',
				AI1WM_PLUGIN_NAME
			);
		?>
	</div>
<?php endif; ?>
