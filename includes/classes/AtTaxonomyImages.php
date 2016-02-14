<?php
/**
 * Component for 'Image' uploading field generaion.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

class AtTaxonomyImages extends TdTaxonomyFieldManager
{
	public $imagePlaceholderUrl = ''; ///assets/td/images/td-taxonomy-images-placeholder.png

	public $buttonUploadImageLabel = 'Upload/Add Image';

	public function init() {
		if ( $this->getStorage()->is_active() && parent::init() ) {
			// Includes script image select from wp media.
			add_action( 'admin_enqueue_scripts', array( $this, 'hookLoadAdminScripts' ) );
			return true;
		}
		return false;
	}

	public function hookLoadAdminScripts( $hook ) {
		if ( 'edit-tags.php' != $hook ) {
			return;
		}

		if ( ! did_action( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
		}
		wp_enqueue_script( 'td-taxonomy-images-select-images', PARENT_URL . '/assets/td/js/TdTaxonomyImagesSelectImages.js', array( 'jquery' ) );
		wp_localize_script( 'td-taxonomy-images-select-images', 'td_image_placeholder', array( $this->getPlacehorderUrl() ) );
	}

	protected function getImageById( $id, array $attr = array() ) {
		$width = isset( $attr['width'] ) ? $attr['width'] : 100;
		$height = isset( $attr['height'] ) ? $attr['height'] : 100;
		$class = isset( $attr['class'] ) ? ' ' . $attr['class'] : '';

		$imageUrl = '';
		if ( isset( $id ) && ! empty( $id ) ) {
			$imageUrl = wp_get_attachment_image_src( $id, array( $width, $height ) );
		}

		$imageUrl = ( ! empty( $imageUrl[0] ) ) ? $imageUrl[0] : $this->getPlacehorderUrl();
		return '<img class="' . esc_attr( 'category-image__image'. $class ) . '" src="' . esc_attr( $imageUrl ) . '" width="' . esc_attr( $width ) . '" heigh="' . esc_attr( $height ) . '">';
	}

	private function getPlacehorderUrl() {
		return $this->imagePlaceholderUrl;
	}

	/**
	 * HTML template for field "Add new taxonomy".
	 */
	public function hookAddFormInsertField() {
		echo '<div class="form-field td-taxonomy-image">' .
			'<label style="cursor:auto;">' . esc_html( $this->getFieldLabel() ) . '</label>' .
			'<input type="text" name="category-image-url" class="td-taxonomy-image__image-url">' .
			'<input type="hidden" name="' . esc_attr( $this->getPostVariableFieldData() ) . '" class="td-taxonomy-image__image-id" value="">' .
			'<input type="button" class="td-taxonomy-image__select button" style="margin:0 2px 0 1px" value="' . esc_html( $this->getButtonUploadImageLabel() ) . '" />' .
			'<input type="button" class="td-taxonomy-image__reset button" value="X" />' .
		'</div>';
	}

	/**
	 * HTML template for field "Edit taxonomy".
	 *
	 * @param object $term
	 */
	public function hookEditFormInsertField( $term ) {
		$taxonomyImageId = $this->getTaxonomyData( $term->term_id );

		echo '<tr class="form-field td-taxonomy-image">' .
			'<th scope="row">' .
				'<label for="theme_taxonomy_image">' . esc_html( $this->getFieldLabel() ) . '</label>' .
			'</th>' .
			'<td>' .
				$this->getImageById( $taxonomyImageId ) .
				'<br>' .
				'<input type="hidden" name="' . esc_attr( $this->getPostVariableFieldData() ) . '" class="td-taxonomy-image__image-id" value="' . esc_attr( $taxonomyImageId ) . '">' .
				'<input type="button" class="td-taxonomy-image__select button" style="margin:0 2px 0 1px" value="' . esc_html( $this->getButtonUploadImageLabel() ) . '" />' .
				'<input type="button" class="td-taxonomy-image__reset button" value="X" />' .
			'</td>' .
		'</tr>';
	}

	/**
	 * HTML template for table column value where show list taxonomy.
	 *
	 * @param stirng $deprecated
	 * @param string $columnId
	 * @param int $termId
	 */
	public function hookAddTableColumnValue( $deprecated, $columnId, $termId ) {
		if ( $this->getTableColumnId() == $columnId ) {
			$taxonomyImageId = $this->getTaxonomyData( $termId );
			print $this->getImageById( $taxonomyImageId, array( 'width' => 50, 'height' => 50 ) );
		}
	}

	/**
	 * Function returns image data by term id.
	 *
	 * @param string $termId
	 * @param string $size
	 * @return array
	 */
	public function getImageData( $termId, $size = 'thumbnail' ) {
		$result = array();

		if ( ! isset( $termId ) || empty( $termId ) ) {
			return $result;
		}

		$attachment_id = $this->getTaxonomyData( $termId );

		if ( $attachment_id ) {
			$result = wp_get_attachment_image_src( $attachment_id, $size );
			$result['attachment_id'] = $attachment_id;
		}

		return $result;
	}

	/**
	 * Function returns image by term id.
	 *
	 * @param string $termId
	 * @param string $size
	 * @param array $attributes
	 * @return string
	 */
	public function getImage( $termId, $size = 'thumbnail', array $attributes = array() ) {
		$result = '';

		if ( ! isset( $termId ) || empty( $termId ) ) {
			return $result;
		}

		$attackmentData = $this->getImageData( $termId, $size );

		if ( ! $attackmentData ) {
			return $result;
		}

		$attributesImage = wp_parse_args( $attributes, array(
			'alt' => empty( $attributes['alt'] ) ? get_post_meta( $attackmentData['attachment_id'], '_wp_attachment_image_alt', true ) : '',
		) );
		$attributesImageHtml = '';
		foreach ( $attributesImage as $attributeName => $attributeVal ) {
			$attributesImageHtml .= ' ' . $attributeName . '="' . esc_attr( $attributeVal ) . '"';
		}

		$result = '<img src="' . esc_url( $attackmentData[0] ) . '"' . $attributesImageHtml . '>';

		return $result;
	}

	public function getButtonUploadImageLabel() {
		return $this->buttonUploadImageLabel;
	}
}
