<?php
/**
 * Helper for form rendering/fields rendering, js related init fuctions running.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.3.2
 */

class AtFormRendererHelper extends TdComponent
{
	public $field_config = array();

	/**
	 * Assoc of field values.
	 *
	 * @var array
	 */
	public $field_vals = array();

	/**
	 * Assoc of fild errors.
	 *
	 * @var array
	 */
	public $field_errors = array();

	public $row_template = '<div class="form-block__item form-block__field-width-icon form-block__field--{field_key}">{input_html}{icon_html}</div>';

	/**
	 * Renders fields configurated via $field_config option.
	 *
	 * @return string
	 */
	public function render() {
		return $this->render_fields_set( array_keys( $this->field_config ) );
	}

	/**
	 * Renders specefied set of fields from $fields_config option.
	 *
	 * @var array     $field_keys
	 * @return string
	 */
	public function render_fields_set( array $field_keys ) {
		$fields = array();
		foreach ( $field_keys as $field_key ) {
			$field_config = isset( $this->field_config[ $field_key ] ) ? $this->field_config[ $field_key ] : array();
			$fields[ $field_key ] = $this->render_field_row( $field_key, $field_config );
		}

		return join( PHP_EOL, $this->order_fields_set( $fields ) );
	}

	/**
	 * Renders specefied field row.
	 *
	 * @param  string $field_key
	 * @param  assoc  $field_config
	 * @return string
	 */
	public function render_field_row( $field_key, array $field_config ) {
		return strtr( $this->row_template, array(
			'{field_key}' => esc_attr( $field_key ),
			'{label}' => isset( $field_config['label'] ) ? esc_html( $field_config['label'] ) : '',
			'{input_html}' => $this->render_input( $field_key, $field_config ),
			'{icon_html}' => ! empty( $field_config['icon_class'] ) ? sprintf( '<i class="%s"></i>', $field_config['icon_class'] ) : '',
		) );
	}

	/**
	 * Renders field input element.
	 *
	 * @param  string $field_key
	 * @param  array  $field_config
	 * @return string
	 */
	public function render_input( $field_key, array $field_config ) {
		$type = isset( $field_config['type'] ) ? $field_config['type'] : 'text';

		$value = '';
		if ( ! isset( $this->field_vals[ $field_key ] ) ) {
			if ( isset( $field_config['default'] ) ) {
				$value = $field_config['default'];
			}
		} else {
			$value = $this->field_vals[ $field_key ];
		}

		$name = isset( $field_config['name'] ) ? $field_config['name'] : $field_key;
		$errors = isset( $this->field_errors[ $field_key ] ) ? $this->field_errors[ $field_key ] : array();

		$attributes = !empty( $field_config['attributes'] ) ? $field_config['attributes'] : array();

		$attributes['name'] = $name;

		$transparent_attributes = array('id','class');
		foreach ( $transparent_attributes as $att_name ) {
			if ( !empty( $field_config[ $att_name ] ) ) {
				$attributes[ $att_name ] = $field_config[ $att_name ];
			}
		}

		if ( $errors ) {
			$attributes['title'] = join( '<br>', $errors );
		}

		$result = '';
		switch ( $type ) {
		case 'select':
			$options_html = '';
			if ( ! empty( $field_config['options'] ) ) {
				$options_html = $this->render_options( $field_config['options'], $value );
			}
			$result = sprintf( '<select %s>%s</select>', $this->render_field_attributes( $attributes, $field_key, $field_config ), $options_html );
			break;

		case 'variation_select':
			$args = $field_config;
			if ( !isset( $args['selected'] ) ) {
				$args['selected'] = $value ? $value : ( isset( $args['default'] ) ? $args['default'] : '' );
			}
			if ( !isset( $args['show_option_none'] ) && !empty( $field_config['label'] ) ) {
				$args['show_option_none'] = $field_config['label'];
			}
			ob_start();
			wc_dropdown_variation_attribute_options( $args );
			$result = ob_get_clean();
			break;

		case 'textarea':
			if ( ! empty( $field_config['placeholder'] ) ) {
				$attributes['placeholder'] = $field_config['placeholder'];
			}

			$textarea_attributes = $attributes;
			unset( $textarea_attributes['type'] );

			$result = sprintf( '<textarea %s>%s</textarea>', $this->render_field_attributes( $textarea_attributes, $field_key, $field_config ), $value );
			break;

		case 'text':
		case 'hidden':
		case 'number':
		default:
			$attributes['value'] = $value;
			if ( in_array( $type, array( 'hidden', 'number', 'date', 'text' ) ) ) {
				$attributes['type'] = $type;
			} else {
				$attributes['type'] = 'text';
			}

			if ( ! empty( $field_config['placeholder'] ) ) {
				$attributes['placeholder'] = $field_config['placeholder'];
			}

			$result = sprintf( '<input %s>', $this->render_field_attributes( $attributes, $field_key, $field_config ) );

			/*if ( 'date' == $name ) {
				$options_html = '';
				if ( ! empty( $field_config['options'] ) ) {
					$options_html = $this->render_options( $field_config['options'], $value );
				}
				$result = sprintf( '<select %s>%s</select>', $this->render_field_attributes( $attributes, $field_key, $field_config ), $options_html );
			}*/

			break;
		}

		return $result;
	}

	/**
	 * Renders form field value and title attributes.
	 * Value for the 'value' attribute taken from 'field_vals' set,
	 * the value for the 'title' attribute - from field errors.
	 *
	 * @param  array   $attributes
	 * @param  string  $field_key
	 * @param  assoc   $field_config
	 * @return string
	 */
	public function render_field_attributes( array $attributes, $field_key, $field_config ) {
		if ( ! $attributes ) {
			return '';
		}

		$parts = array();
		foreach ($attributes as $key => $value) {
			$parts[] = sprintf('%s="%s"', $key, esc_attr( $value ) );
		}
		return join(' ', $parts);
	}

	/**
	 * Allows change fields order during fields set rendering.
	 * @param  assoc  $fields
	 * @return assoc
	 */
	public function order_fields_set( array $fields ) {
		return apply_filters( 'adventure_tours_form_renderer_order_fields_set', $fields, $this );
	}

	/**
	 * Renders html for of options elements for select input.
	 *
	 * @param  assoc  $list
	 * @param  string $selected
	 * @return string
	 */
	protected function render_options( $list, $selected ) {
		if ( ! $list ) {
			return '';
		}
		$parts = array();
		foreach ( $list as $val => $text ) {
			$parts[] = sprintf( '<option value="%s"%s>%s</option>',
				esc_attr( $val ),
				$selected && $selected == $val ? ' selected="selected"' : '',
				esc_html( $text )
			);
		}
		return join( PHP_EOL, $parts );
	}

	public function init_js_errors( $items_selector ) {
		if ( ! $items_selector ) {
			return;
		}

		TdJsClientScript::addScript('initValidationBookTour', <<<SCRIPT
			Theme.FormValidationHelper
				.initTooltip('{$items_selector}')
				.addClass('form-validation-item')
				.tooltip('show');
SCRIPT
		);
	}
}
