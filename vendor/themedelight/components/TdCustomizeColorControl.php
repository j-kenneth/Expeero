<?php
/**
 * Color selection controll for costomization theme panel.
 *
 * @author    Themedelight
 * @package   Themedelight/Components
 * @version   1.0.0
 */

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return;
}

class TdCustomizeColorControl extends WP_Customize_Control
{
	public $type = 'alphacolor';

	// public $palette = '#3FADD7,#555555,#666666, #F5f5f5,#333333,#404040,#2B4267';
	public $palette = true;

	public $default = '#ffffff';

	public $prevent_js_cache = false;

	public function enqueue() {
		// parent::enqueue(); // of the color control
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );

		$scriptId = 'themedelight-themecustomize-color-control';

		wp_enqueue_style(
			$scriptId,
			PARENT_URL . '/assets/td/css/TdCustomizeColorControl.css'
		);

		wp_enqueue_script(
			$scriptId,
			PARENT_URL . '/assets/td/js/TdCustomizeColorControl.js',
			array( 'jquery', 'customize-controls' ),
			$this->prevent_js_cache ? time() : '',
			true
		);
	}

	protected function render() {
		$id = 'customize-control-' . str_replace( '[', '-', str_replace( ']', '', $this->id ) );
		$class = 'customize-control customize-control-' . $this->type;
		ob_start();
		$this->render_content();
		$content = ob_get_clean();
		echo strtr('<li id="{id}" class="{class}">{content}</li>', array(
			'{id}' => esc_attr( $id ),
			'{class}' => esc_attr( $class ),
			'{content}' => $content,
		));
	}

	public function render_content() {
		ob_start();
		$this->link();
		$link_attrib = ob_get_clean();

		echo strtr('<label><span class="customize-control-title">{label}</span>' .
			'<input type="text" data-palette="{pallete}" data-default-color="{default_color}" value="{value}" class="tdcolor-color-control" {link_attrib} />' .
			'</label>',
			array(
				'{label}' => esc_html( $this->label ),
				'{pallete}' => $this->palette,
				'{default_color}' => $this->setting->default ? $this->setting->default : $this->default,
				'{value}' => intval( $this->value() ),
				'{link_attrib}' => $link_attrib
			)
		);
	}
}
