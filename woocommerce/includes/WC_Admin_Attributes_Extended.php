<?php
/**
 * Class for implementing icons selector into product attribute management section.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

class WC_Admin_Attributes_Extended extends TdComponent
{
	public $parent_page_slug = 'edit.php?post_type=product';

	public $original_page_slug = 'product_attributes';

	public $extended_page_slug = 'product_attributes_extended';

	public $field_name = 'attribute_icon';

	public $storage;

	public function hook() {
		if ( ! $this->field_name ) {
			return;
		}

		remove_submenu_page( $this->parent_page_slug, $this->original_page_slug );
		add_submenu_page( $this->parent_page_slug, esc_html__( 'Attributes', 'adventure-tours' ), esc_html__( 'Attributes', 'adventure-tours' ), 'manage_product_terms', $this->extended_page_slug, array( $this, 'render_page' ) );

		add_action( 'woocommerce_attribute_added', array( $this, 'action_attribute_added' ), 20, 2 );
		add_action( 'woocommerce_attribute_updated', array( $this, 'action_attribute_updated' ), 20, 3 );
	}

	public function action_attribute_added($attribute_id, $attribute) {
		$new_value = $this->read_icon_value();
		if ( false !== $new_value ) {
			$this->getStorage()->setData( $attribute_id, $new_value );
		}
	}

	public function action_attribute_updated($attribute_id, $attribute, $old_attribute_name) {
		$new_value = $this->read_icon_value();
		if ( false !== $new_value ) {
			$this->getStorage()->setData( $attribute_id, $new_value );
		}
	}

	protected function read_icon_value() {
		$field_name = $this->field_name;
		return $field_name && isset( $_POST[$field_name] ) ? wc_clean( stripslashes( $_POST[$field_name] ) ) : false;
	}

	public function render_page() {
		ob_start();
		WC_Admin_Attributes::output();
		$html = ob_get_clean();

		// Replacement to fix item edition links.
		if ( $this->original_page_slug != $this->extended_page_slug ) {
			$html = str_replace( 'page=' . $this->original_page_slug, 'page=' . $this->extended_page_slug, $html );
		}

		print $this->add_own_field( $html );
		$this->load_assets();
	}

	protected function load_assets() {
		// Assets loading, should be improved.
		wp_enqueue_style( 'td-taxonomy-fiels-icons-select2', PARENT_URL . '/assets/td/css/TdTaxonomyIconsSelect2.min.css' );
		wp_enqueue_script( 'td-taxonomy-fiels-icons-select2', PARENT_URL . '/assets/td/js/TdTaxonomyIconsSelect2.full.min.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'td-taxonomy-fiels-icons-chooser', PARENT_URL . '/assets/td/js/TdTaxonomyIconsChooser.js', array( 'jquery', 'td-taxonomy-fiels-icons-select2' ), '', true );
		wp_localize_script( 'td-taxonomy-fiels-icons-chooser', 'td_icon_placeholder', esc_html__( 'None', 'adventure-tours' ) );
	}

	public function get_options_list() {
		$result = array( 
			'none' => esc_html__( 'None', 'adventure-tours' )
		);

		$icons_list = adventure_tours_di( 'icons_manager' )->get_list();
		foreach ( $icons_list as $icon ) {
			$result[$icon['value']] = $icon['label'];
		}

		return $result;
	}

	protected function add_own_field( $html ) {
		$itemId = ! empty( $_GET['edit'] ) ? $_GET['edit'] : null;

		// Inserting additional field in item management html.
		// Based on mode (add/edit) selecting element that should be replaced with field html.
		$placementFlagText = $itemId ? '</table>' : '<p class="submit">';
		return str_replace(
			$placementFlagText,
			$this->make_field_html( $itemId ) . $placementFlagText,
			$html
		);
	}

	protected function make_field_html( $itemId = null ) {
		$result = '';
		$options = $this->get_options_list();
		if ( ! $options ) {
			return $result;
		}

		$curValue = $itemId ? $this->getStorage()->getData($itemId) : null;
		$optionItems = array();
		foreach ( $options as $val => $title ) {
			$optionItems[] = sprintf( '<option value="%s"%s>%s</option>',
				esc_attr( $val ),
				$curValue == $val ? ' selected="selected"' : '',
				esc_html( $title )
			);
		}

		$fieldName = $this->field_name;
		$args = array(
			'{label}' => esc_html__( 'Icon', 'adventure-tours' ),
			'{fieldName}' => esc_attr( $fieldName ),
			'{selectHtml}' => '<select class="td-js-font-icons" name="'. esc_attr( $fieldName ) .'">' . join( '', $optionItems ). '</select>',
			'{description}' => esc_html__( 'Determines what icon will be used for this attribute.', 'adventure-tours' ),
		);

		if ( $itemId ) {
			$result = strtr('
				<tr class="form-field form-required">
					<th scope="row" valign="top">
						<label for="{fieldName}">{label}</label>
					</th>
					<td>
						{selectHtml}
						<p class="description">{description}</p>
					</td>
				</tr>',
				$args
			);
		} else {
			$result = strtr('
				<div class="form-field">
					<label for="{fieldName}">{label}</label>
					{selectHtml}
					<p class="description">{description}</p>
				</div>',
				$args
			);
		}
		return $result;
	}

	public function getStorage() {
		if ( ! $this->storage ) {
			throw new Exception( 'Storage should be defined.' );
		}
		return $this->storage;
	}
}
