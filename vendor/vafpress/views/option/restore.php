<div class="vp-field">
	<div class="label">
		<label>
			<?php esc_html_e('Restore Default Options', 'adventure-tours') ?>
		</label>
		<div class="description">
			<p><?php esc_html_e('Restore options to initial default values.', 'adventure-tours') ?></p>
		</div>
	</div>
	<div class="field">
		<div class="input">
			<div class="buttons">
				<input class="vp-js-restore vp-button button button-primary" type="button" value="<?php esc_attr_e('Restore Default', 'adventure-tours') ?>" />
				<p><?php 
					//esc_html_e('** Please make sure you have already make a backup data of your current settings. Once you click this button, your current settings will be gone.', 'adventure-tours');
					esc_html_e('** Please make sure that you have a backup copy of your current settings. Once you click this button, your current settings will be gone.', 'adventure-tours'); ?></p>
				<span style="margin-left: 10px;">
					<span class="vp-field-loader vp-js-loader" style="display: none;"><img src="<?php VP_Util_Res::img_out('ajax-loader.gif', ''); ?>" style="vertical-align: middle;"></span>
					<span class="vp-js-status" style="display: none;"></span>
				</span>
			</div>
		</div>
	</div>
</div>