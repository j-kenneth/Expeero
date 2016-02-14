<?php
/**
 * Font selection controll for costomization theme panel.
 *
 * @author    Themedelight
 * @package   Themedelight/Components
 * @version   1.0.0
 */

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return;
}

class TdCustomizeFontControl extends WP_Customize_Control
{
	public $type = 'themedelight_font';

	public $font_set_filter = 'themedelight_customize_font_set';

	public $font_set = array(
		/*'font1' => array(),
		'font2' => array(
			'style' => array( 'normal', 'italic', ),
			'weight' => array( '400', '700' )
		),
		'font3' => array(
			'weight' => array('300','400')
		),*/
	);

	public $prevent_js_cache = false;

	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );

		if ( $this->font_set_filter ) {
			$this->font_set = apply_filters( $this->font_set_filter, $this->font_set, $this->id );
		}
	}

	public function enqueue() {
		// $scriptId = "themedelight-themecustomize-font-control{$this->id}";
		$scriptId = 'themedelight-themecustomize-font-control';

		wp_enqueue_script(
			$scriptId,
			PARENT_URL . '/assets/td/js/TdCustomizeFontControl.js',
			array( 'jquery', 'customize-controls' ),
			$this->prevent_js_cache ? time() : '',
			true
		);

		wp_localize_script($scriptId, '_TdCustomizeFontControl' . $this->id, array(
			'font_set' => $this->font_set,
		));
	}

	public function render_content() {
		$curFamily = $this->get_sub_value( 'family' );
		$fontList = $this->get_font_family_list();
		$styleList = $this->get_style_list( $curFamily );
		$weightList = $this->get_weight_list( $curFamily );
?>
		<?php if ( ! empty( $this->label ) ) : ?>
			<span class="customize-control-title"><?php print esc_html( $this->label ); ?></span>
		<?php endif;
if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php print $this->description; ?></span>
		<?php endif; ?>

		<select <?php print $this->get_sub_link( 'family' ); ?>>
			<?php $this->render_options( $fontList, $curFamily ); ?>
		</select>

		<div style="margin:8px 0;">
			<span style="width:60px;display:inline-block">Style:</span><select <?php print $this->get_sub_link( 'style' ); ?>>
				<?php $this->render_options( $styleList, $this->get_sub_value( 'style', 'normal' ) ); ?>
			</select>
		</div>

		<div>
			<span style="width:60px;display:inline-block">Weight:</span><select <?php print $this->get_sub_link( 'weight' ); ?>>
				<?php $this->render_options( $weightList, $this->get_sub_value( 'weight', '400' ) ); ?>
			</select>
		</div>
<?php
	}

	protected function get_font_family_list() {
		$result = array();

		$list = $this->font_set ? array_keys( $this->font_set ) : array();

		foreach ( $list as $name ) {
			$result[$name] = $name;
		}

		return $result;
	}

	protected function get_style_list($family) {
		$list = array( 'normal' );

		$fontConfig = isset( $this->font_set[$family] ) ? $this->font_set[$family] : array();

		if ( ! empty( $fontConfig['style'] ) ) {
			$list = $fontConfig['style'];
		}

		$result = array();
		foreach ( $list as $weight ) {
			$result[$weight] = $weight;
		}

		return $result;
	}

	protected function get_weight_list($family) {
		$list = array( 'normal' );

		$fontConfig = isset( $this->font_set[$family] ) ? $this->font_set[$family] : array();

		if ( ! empty( $fontConfig['weight'] ) ) {
			$list = $fontConfig['weight'];
		}

		$result = array();
		foreach ( $list as $weight ) {
			$result[$weight] = $weight;
		}

		return $result;
	}

	protected function get_sub_link($key, $setting = 'default') {
		// $link = $this->get_link($setting);
		// $result = substr($link, 0, -1) . "[$key]\" data-subkey=\"{$key}\"";
		return "data-subkey=\"{$key}\"";
	}

	protected function get_sub_value($key, $default = '') {
		$value = $this->value();
		return $value && isset( $value[$key] ) ? $value[$key] : $default;
	}

	protected function render_options(array $options, $curvalue) {
		foreach ( $options as $value => $label ) {
			echo '<option value="' . esc_attr( $value ) . '"' . selected( $curvalue, $value, false ) . '>' . $label . '</option>';
		}
	}
}
