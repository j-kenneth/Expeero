<?php
/**
 * Font size selection controll for costomization theme panel.
 *
 * @author    Themedelight
 * @package   Themedelight/Components
 * @version   1.0.0
 */

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return;
}

class TdCustomizeFontSizeControl extends WP_Customize_Control
{
	public $type = 'themedelight_font_size';

	public $as_subfield = false;

	public $unit_list = array(
		'px' => 'px',
		'em' => 'em',
		'%' => '%',
	);

	public $prevent_js_cache = false;

	public function render_content() {
?>
		<?php if ( ! empty( $this->label ) ) : ?>
			<?php if ( ! $this->description && $this->as_subfield ) { ?>
				<span style="width:56px;display:inline-block"><?php print esc_html( $this->label ); ?>:</span>
			<?php } else { ?>
				<span class="customize-control-title"><?php print esc_html( $this->label ); ?></span>
			<?php } ?>
		<?php endif;
		if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php print $this->description; ?></span>
		<?php endif; ?>

		<input style="width:50px" <?php print $this->get_sub_link( 'size' ); ?> value="<?php print $this->get_sub_value( 'size', 16 ); ?>" />
		<select style="min-width:0;width:60px" <?php print $this->get_sub_link( 'unit' ); ?>>
			<?php $this->render_options( $this->unit_list, $this->get_sub_value( 'unit', 'px' ) ); ?>
		</select>
<?php
	}

	protected function get_sub_link($key, $setting = 'default') {
		return "data-subkey=\"{$key}\"";
	}

	protected function get_sub_value($key, $default = '') {
		$value = $this->value();
		return $value && isset( $value[$key] ) ? $value[$key] : $default;
	}

	protected function render_options(array $options, $curvalue) {
		foreach ( $options as $value => $label ) {
			echo '<option value="' . esc_attr( $value ) . '"' . selected( $curvalue, $value, false ) . '>' . $label . '</option>'; }
	}

	public function enqueue() {
		wp_enqueue_script(
			'themedelight-themecustomize-font-size-control',
			PARENT_URL . '/assets/td/js/TdCustomizeFontSizeControl.js',
			array( 'jquery', 'customize-controls' ),
			$this->prevent_js_cache ? time() : '',
			true
		);
	}
}
