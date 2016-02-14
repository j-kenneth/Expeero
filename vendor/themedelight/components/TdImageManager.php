<?php
/**
 * Class allows define theme specific image sizes that will be generated on the 1-st request.
 * Sizes added via add_image_size function - generated all together on the upload image event,
 * but usually image used in some specific context with some specific size. So to prevent this overhead
 * this class has been designed.
 *
 * @author    Themedelight
 * @package   Themedelight/Components
 * @version   2.0.0
 */

class TdImageManager extends TdComponent
{
	/**
	 * List of the theme custom sizes.
	 * Size code used as a key, each element contains size details.
	 * @see addImageSize
	 * @var array
	 */
	protected $sizes = array();

	public function setConfig(array $config) {
		foreach ( $config as $option => $value ) {
			switch ( $option ) {
			case 'sizes':
				foreach ( $value as $_sizeName => $_sizeDetails ) {
					$this->addCustomImageSize(
						$_sizeName,
						isset( $_sizeDetails['width'] ) ? $_sizeDetails['width'] : 0,
						isset( $_sizeDetails['height'] ) ? $_sizeDetails['height'] : 0,
						isset( $_sizeDetails['crop'] ) ? $_sizeDetails['crop'] : false
					);
				}
				break;

			default:
				$this->$option = $value;
				break;
			}
		}
	}

	public function init() {
		if ( parent::init() ) {
			add_action( 'delete_attachment', array( $this, 'removeCustomImageSizes' ) );
			add_filter( 'image_downsize', array( $this, 'filter_imageDownsize' ), 10, 3 );
			return true;
		}
		return false;
	}

	/**
	 * Get image size details.
	 * @param string $size
	 * @return null|array
	 */
	public function getImageSizeDetails( $size ) {
		if ( is_array( $size ) ) {
			if ( ! isset( $size['width'] ) && isset( $size[0] ) ) {
				$size['width'] = $size[0];
			}

			if ( ! isset( $size['height'] ) && isset( $size[1] ) ) {
				$size['height'] = $size[1];
			}

			return $size;
		}

		static $defaultSize = array(
			'thumbnail' => '',
			'medium' => '',
			'large' => '',
		);

		if ( isset( $defaultSize[$size] ) ) {
			if ( '' === $defaultSize[$size] ) {
				$width = get_option( $size . '_size_w' );
				$height = get_option( $size . '_size_h' );
				$crop = get_option( $size . '_crop' );

				if ( $width || $height ) {
					$defaultSize[$size] = array(
						'width' => $width,
						'height' => $height,
						'crop' => $crop,
					);
				} else {
					$defaultSize[$size] = null;
				}
			}
			return $defaultSize[$size];
		}

		// checking is size defined in general sizes list
		global $_wp_additional_image_sizes;
		if ( isset( $_wp_additional_image_sizes[$size] ) ) {
			return $_wp_additional_image_sizes[$size];
		}

		return $this->getCustomImageSizes( $size );
	}

	/**
	 * Adds custom image size.
	 *
	 * @param string  $size
	 * @param number  $width
	 * @param number  $height
	 * @param boolean $crop
	 * @return ThemeImageManager
	 */
	public function addCustomImageSize( $size, $width = 0, $height = 0, $crop = false ) {
		if ( $size && ! has_image_size( $size ) ) {
			$this->sizes[$size] = array(
				'width' => absint( $width ),
				'height' => absint( $height ),
				'crop' => $crop,
			);
		}

		return $this;
	}

	/**
	 * Returns custom image size if size code passed or all defined sizes if code is missed out.
	 *
	 * @param string $size optional
	 * @return array|null
	 */
	public function getCustomImageSizes( $size = '' ) {
		if ( ! $size ) {
			return $this->sizes;
		} else {
			if ( is_array( $size ) ) {
				return $size;
			}

			return isset( $this->sizes[$size] ) ? $this->sizes[$size] : null;
		}
	}

	public function isCustomImageSize($size) {
		return is_string( $size ) && isset( $this->sizes[$size] );
	}

	/**
	 * Removes generated custom image sizes.
	 * @param int $post_id
	 * @return void
	 */
	public function removeCustomImageSizes( $post_id ) {
		$file_dir = get_attached_file( $post_id );
		if ( empty( $file_dir ) ) {
			return;
		}

		$imageSizes = $this->getCustomImageSizes();

		if ( ! $imageSizes ) {
			return;
		}

		$file_dir_info = pathinfo( $file_dir );
		$file_base_dir = isset( $file_dir_info['dirname'] ) ? $file_dir_info['dirname'] : '';
		$file_name = isset( $file_dir_info['filename'] ) ? $file_dir_info['filename'] : '';
		$file_ext = isset( $file_dir_info['extension'] ) ? $file_dir_info['extension'] : '';

		foreach ( $imageSizes as $size ) {
			$crop = $size['crop'];
			$width = $size['width'];
			$height = $size['height'];

			if ( false == $crop ) {
				// get image size after cropping
				list( $orig_w, $orig_h ) = getimagesize( $file_dir );
				$dims = image_resize_dimensions( $orig_w, $orig_h, $width, $height, $crop );
				$width = $dims[4];
				$height = $dims[5];
			}

			$file = $file_base_dir . '/' . $file_name . '-' . $width . 'x' . $height . '.' . $file_ext;

			if ( ! file_exists( $file ) ) {
				continue;
			}

			if ( unlink( $file ) ) {
				// files remove
			} else {
				// files not remove
			}
		}
	}

	public function getPlaceholdImage( $width, $height, $text = '', $asImageElement = false, array $attributes = array() ) {
		if ( empty( $width ) && empty( $height ) ) {
			return '';
		}

		$url = 'http://placehold.it/' . $width . 'x' . $height . ( $text ? '&text=' . urlencode( $text ) : '');

		if ( $asImageElement ) {
			$attributesText = '';
			if ( $attributes ) {
				foreach ( $attributes as $name => $attributeValue ) {
					$attributesText .= ' ' . $name . '="' . esc_attr( $attributeValue ) . '"';
				}
			}
			return '<img src="' . $url . '" alt="image of ' . ($width . 'x' . $height) . '"' . $attributesText . '>';
		} else {
			return $url;
		}
	}

	public function getAttachmentIdByUrl( $url, $check_host = true ) {
		if ( $check_host ) {
			// checking that image belongs to the our host
			$current_host = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
			$file_host = str_ireplace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );
			if ( $current_host != $file_host ) {
				return null;
			}
		}

		// split the $url into two parts with the wp-content directory as the separator
		$parsed_url  = explode( parse_url( WP_CONTENT_URL, PHP_URL_PATH ), $url );

		if ( empty( $parsed_url[1] ) ) {
			return null;
		}

		// searching in the DB for any attachment GUID with a partial path match
		global $wpdb;
		$attachment = $wpdb->get_col( $wpdb->prepare(
			"SELECT ID FROM `{$wpdb->posts}` " .
			'WHERE `post_type`=%s AND `guid` LIKE %s;',
			'attachment',
			'%' . $wpdb->esc_like( $parsed_url[1] )
		) );

		// Returns null if no attachment is found
		return isset( $attachment[0] ) ? $attachment[0] : null;
	}

	public function filter_imageDownsize($false, $id, $size) {
		if ( ! $this->isCustomImageSize( $size ) ) {
			return null;
		}

		$curMeta = wp_get_attachment_metadata( $id );

		$img_url = wp_get_attachment_url( $id );
		if ( isset( $curMeta['sizes'][$size] ) ) {
			$curSize = $curMeta['sizes'][$size];
			return array(
				str_replace( wp_basename( $img_url ), $curSize['file'], $img_url ),
				$curSize['width'],
				$curSize['height'],
				true,
			);
		}

		// via editor
		$editor = wp_get_image_editor( get_attached_file( $id ) );
		if ( is_wp_error( $editor ) ) {
			return null;
		}

		$newSizes = array();
		$newSizes[$size] = $this->getCustomImageSizes( $size );

		$newSizes = $editor->multi_resize( $newSizes );
		if ( $newSizes ) {
			$createdSize = $newSizes[$size];
			$curMeta['sizes'][$size] = $createdSize;
			wp_update_attachment_metadata( $id, $curMeta );

			return array(
				str_replace( wp_basename( $img_url ), $createdSize['file'], $img_url ),
				$createdSize['width'],
				$createdSize['height'],
				true,
			);
		}

		return null;
	}
}
