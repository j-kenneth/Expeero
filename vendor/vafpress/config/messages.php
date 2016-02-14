<?php

return array(

	////////////////////////////////////////
	// Localized JS Message Configuration //
	////////////////////////////////////////

	/**
	 * Validation Messages
	 */
	'validation' => array(
		'alphabet'     => esc_html__('Value needs to be Alphabet', 'adventure-tours'),
		'alphanumeric' => esc_html__('Value needs to be Alphanumeric', 'adventure-tours'),
		'numeric'      => esc_html__('Value needs to be Numeric', 'adventure-tours'),
		'email'        => esc_html__('Value needs to be Valid Email', 'adventure-tours'),
		'url'          => esc_html__('Value needs to be Valid URL', 'adventure-tours'),
		'maxlength'    => esc_html__('Length needs to be less than {0} characters', 'adventure-tours'),
		'minlength'    => esc_html__('Length needs to be more than {0} characters', 'adventure-tours'),
		'maxselected'  => esc_html__('Select no more than {0} items', 'adventure-tours'),
		'minselected'  => esc_html__('Select at least {0} items', 'adventure-tours'),
		'required'     => esc_html__('This is required', 'adventure-tours'),
	),

	/**
	 * Import / Export Messages
	 */
	'util' => array(
		'import_success'    => esc_html__('Import succeed, option page will be refreshed..', 'adventure-tours'),
		'import_failed'     => esc_html__('Import failed', 'adventure-tours'),
		'export_success'    => esc_html__('Export succeed, copy the JSON formatted options', 'adventure-tours'),
		'export_failed'     => esc_html__('Export failed', 'adventure-tours'),
		'restore_success'   => esc_html__('Restoration succeed, option page will be refreshed..', 'adventure-tours'),
		'restore_nochanges' => esc_html__('Options identical to default', 'adventure-tours'),
		'restore_failed'    => esc_html__('Restoration failed', 'adventure-tours'),
	),

	/**
	 * Control Fields String
	 */
	'control' => array(
		// select2 select box
		'select2_placeholder' => esc_html__('Select option(s)', 'adventure-tours'),
		// fontawesome chooser
		'fac_placeholder'     => esc_html__('Select an Icon', 'adventure-tours'),
	),

);

/**
 * EOF
 */