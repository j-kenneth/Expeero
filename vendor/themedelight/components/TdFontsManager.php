<?php
/**
 * Component for generation web fonts defenition rules/links.
 *
 * @author    Themedelight
 * @package   Themedelight/Components
 * @version   1.1.1
 */

class TdFontsManager extends TdComponent
{
	/**
	 * Font families config. Font family should be used as a key.
	 * Each element may include following keys:
	 *     'style'   optional set of allowed styles, array('normal') is a default value
	 *     'weight'  optional set of allowed weights, array('400') is a default value
	 *     'files'   optional set of font files.
	 * @var array
	 */
	public $font_set = array();

	/**
	 * Convert set of font settings for css that should be included to the document to connect defined font.
	 * Each element of $fonts shold
	 * @param  array $fonts each element should has following keys:
	 *                      'family' - required
	 *                      'style'  - optional, "normal" is default value
	 *                      'weight' - optional, "400" is default value
	 * @return array  each element will include
	 */
	public function generateDefinitions(array $fonts) {
		$googleApiElements = array();
		$inlineDefinitions = array();

		foreach ( $fonts as $key => $fontSettings ) {
			$family = ! empty( $fontSettings['family'] ) ? $fontSettings['family'] : '';
			if ( ! $family ) {
				continue;
			}
			$fontConfig = $this->getConfigByFamily( $family );
			$weight = ! empty( $fontSettings['weight'] ) ? $fontSettings['weight'] : '';
			$style = ! empty( $fontSettings['style'] ) ? $fontSettings['style'] : '';

			// if font definition has not key 'files' - it is google web font
			$isGoogle = empty( $fontConfig['files'] );

			$google_style_definition = $this->getUnifiedFontWeight( $weight ) . $style;
			if ( $isGoogle ) {
				$googleApiElements[$family][$google_style_definition] = $google_style_definition;
			} else {
				$inlineDefinitions[$family . $google_style_definition] = $this->renderFontFamilyDefinition( $fontSettings, $fontConfig );
			}
		}

		$result = array();
		if ( $googleApiElements ) {
			$gApiFamilies = array();
			foreach ( $googleApiElements as $family => $definitions ) {
				$paramText = str_replace( ' ', '+', $family );

				if ( $definitions ) {
					$paramText .= ':' . join( ',', $definitions );
				}

				$gApiFamilies[] = $paramText;
			}

			$result['google-fonts'] = array(
				'url' => '//fonts.googleapis.com/css?family=' . join( '|', $gApiFamilies ),
			);
		}

		if ( $inlineDefinitions ) {
			$result['inline-fonts'] = array(
				'text' => join( "\n\n", $inlineDefinitions ),
			);
		}

		return $result;
	}

	public function getConfigByFamily($family) {
		if ( $family && isset( $this->font_set[$family] ) ) {
			return $this->font_set[$family];
		}
		return array();
	}

	/**
	 * Generates @font-face css rules based on the values passed in settings and configuration in $famlityConfig.
	 *
	 * @param  assoc  $settings
	 * @param  assoc  $familyConfig
	 * @return string
	 */
	protected function renderFontFamilyDefinition( $settings, $familyConfig ) {
		$files_set = !empty( $familyConfig['files'] ) ? $familyConfig['files'] : array();

		$weight = !empty( $settings['weight'] ) ? $settings['weight'] : 'regular';
		$style = !empty( $settings['style'] ) ? $settings['style'] : 'normal';
		$unified_weight = $this->getUnifiedFontWeight( $weight );
		$is_default_weight = '400' == $unified_weight;

		$render_files = array();

		if ( is_array( $files_set ) ) {
			// $is_flat = count( $files_set ) === count( $files_set, COUNT_RECURSIVE );
			// if ( $is_flat ) $render_files = $files_set;
			$possible_keys = array(
				$weight . '_' . $style,
				$unified_weight . '_' . $style,
			);
			if ( 'normal' == $style ) {
				$possible_keys[] = $weight;
				$possible_keys[] = $unified_weight;

				if ( $is_default_weight ) {
					$possible_keys[] = '';
				}
			}
			if ( $is_default_weight ) {
				$possible_keys[] = $style;
			}

			foreach ( $possible_keys as $possible_key ) {
				if ( isset( $files_set[$possible_key] ) ) {
					$render_files = $files_set[$possible_key];
					break;
				}
			}
		}

		return $this->renderFontFaceDefinition( $settings['family'], $unified_weight, $style, $render_files );
	}

	/**
	 * Generates font-face definition.
	 *
	 * @param  string       $family font family name.
	 * @param  string       $weight font weight.
	 * @param  string       $style  font style.
	 * @param  array|string $files  set of ulrs related with combination of weight & style or plain css text that defines specific font family
	 * @return string
	 */
	protected function renderFontFaceDefinition( $family, $weight, $style, $files ) {
//echo "\n\n"; var_export($files); echo "\n\n";
		if ( is_string( $files ) ) {
			if ( strpos( $files, '@font-face' ) !== false ) {
				return $files;
			} else {
				$files = (array) $files;
			}
		}

		$definition_lines = array();

		if ( $files ) {
			// otf => 'opentype'
			$known_formats = array('woff2','woff','truetype','svg');
			foreach ($files as $_format => $_url ) {
				$detected_format = '';
				if ( $_format && in_array( $_format, $known_formats ) ) {
					$detected_format = $_format;
				} else {
					$_matches = null;
					if ( preg_match( '`\.(' . join('|', $known_formats) . ')`', $_url, $_matches ) ) {
						$detected_format = $_matches[1];
					}
				}
				if ( $detected_format ) {
					$definition_lines[] = sprintf('url("%s") format("%s")', $_url, $detected_format );
				} else {
					$definition_lines[] = sprintf('url("%s")', $_url );
				}
			}
		}

		if ( $definition_lines ) {
			return sprintf('@font-face {'.
					'font-family:"%s";' .
					'font-style:%s;' .
					'font-weight:%s;' .
					'src:%s;' .
				'}', 
				$family,
				$style,
				$weight,
				join( ",\n", $definition_lines )
			);
		} else {
			return '';
		}
	}

	protected function getUnifiedFontWeight( $weight ) {
		return str_replace( array( 'normal','regular' ), '400', $weight );
	}
}
