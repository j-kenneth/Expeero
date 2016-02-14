<?php
/**
 * Class for generation css file based on the less file and values of the variables those can be passed to the variables file.
 *
 * @author    Themedelight
 * @package   Themedelight/Components
 * @version   1.0.1
 */

class TdLessProcessor extends TdComponent
{
	public $themeBaseDir;

	public $themeBaseUrl;

	/**
	 * Path to the view less file that should be used for css generation.
	 * Path related to the theme location.
	 *
	 * @example
	 * <pre>
	 * /assets/css/style.less
	 * </pre>
	 * @var string
	 */
	public $lessFile = '';

	/**
	 * If compiled css should be saved as file (otherwise will be outputed as inline style).
	 * @var boolean
	 */
	public $saveAs = false;

	public $generateTockenForCssFile = true;

	public $variablesFileMarker = '@variables-file';

	public $variableMetaTag = '@theme_option\:';

	public function generateCss($themeSettings, $plainCss = false) {
		$cssText = '';
		$compiledFromLess = false;

		if ( $this->lessFile ) {
			$compiledFromLess = true;

			$cssText = $this->renderLessFile(
				$this->getThemeFilePath( $this->lessFile ),
				$themeSettings
			);

			if ( ! empty( $themeSettings['custom_css_text'] ) ) {
				$cssText .= "\n" . $themeSettings['custom_css_text'];
			}
		}

		if ( $plainCss ) {
			return $cssText;
		}

		$parts = array();

		if ( $cssText ) {
			$addAsInline = true;
			$cssId = 'style-css';
			if ( $compiledFromLess && $this->saveAs ) {

				$newCssThemeFileName = $this->saveAs;

				// $cssFullFilePath = $this->getThemeFilePath($newCssThemeFileName);
				$newCssFileInfo = $this->generateCssFileInfo( $newCssThemeFileName );

				if ( $newCssFileInfo && file_put_contents( $newCssFileInfo['path'], $cssText ) ) {
					$addAsInline = false;
					// $cacheToken = $this->generateTockenForCssFile ? '?ct=' . time() : '';
					// $styleUrl = $this->getThemeFileUrl($newCssThemeFileName);
					// $parts[] = '<link id="customCss" href="' . esc_url($styleUrl). '" rel="stylesheet" />';
					$parts[$cssId] = array(
						'url' => $newCssFileInfo['url'],
					);
				}
			}

			if ( $addAsInline ) {
				$parts[$cssId] = array(
					'text' => $cssText,
				);
			}
		}

		return $parts;
	}

	protected function getThemeFilePath($localPath) {
		return $this->themeBaseDir . $localPath;
	}

	protected function getThemeFileUrl($localPath) {
		return $this->themeBaseUrl . $localPath;
	}

	protected function generateCssFileInfo($postfix = '') {
		$upload_dir = wp_upload_dir();
		$fileFolderRelativePath = '/' . basename( get_template_directory() ) . '-assets/';
		$fileFolder = $upload_dir['basedir'] . $fileFolderRelativePath;

		if ( ! is_dir( $fileFolder ) ) {
			if ( ! mkdir( $fileFolder, 0755, true ) && WP_DEBUG ) {
				throw new Exception(strtr('Can not create folder {path}.', array(
					'{path}' => $fileFolder,
				)));
			}
		}

		if ( $postfix ) {
			$postfix = preg_replace( '/\.css$/', '', $postfix );
		}
		if ( strlen( $postfix ) < 5 ) {
			$postfix = time() . '-' . rand( 100,999 );
		}
		$fileName = $postfix . '.css';

		if ( is_dir( $fileFolder ) ) {
			return array(
				'path' => $fileFolder . $fileName,
				'url' => $upload_dir['baseurl'] . $fileFolderRelativePath . $fileName,
			);
		}

		return null;
	}

	protected function renderLessFile($fullLessFilePath, array $themeSettings, $compressed = true) {
		$lessVariables = $this->getLessVariables( $fullLessFilePath, $themeSettings );

		$newValuesText = array();

		foreach ( $lessVariables as $option_name => $details ) {
			$txtValue = $details['value'];

			// grouping options related to the font settings
			// to load related fonts later
			$parseRes = null;
			if ( preg_match( '`(\w+)\_font\[(\w+)\]`', $option_name, $parseRes ) ) {
				$fontGroup = $parseRes[1];
				$fontOption = $parseRes[2];
				$fonts[$fontGroup][$fontOption] = $details['value'];
				if ( 'weight' == $fontOption ) {
					$txtValue = str_replace( array( 'normal', 'regular' ), '400', $txtValue );
				} elseif ( 'family' == $fontOption && preg_match( '`\s+`', $txtValue ) ) {
					$txtValue = "'{$txtValue}'";
				}
			}
			if ( $txtValue ) {
				$newValuesText[] = $details['less_name'] . ':' . $txtValue . ';';
			}
		}

		$less = $this->createLessProcessor();
		$less->addImportDir( dirname( $fullLessFilePath ) );
		if ( $compressed ) {
			$less->setFormatter( 'compressed' );
		}

		$lessContent = file_get_contents( $fullLessFilePath );

		// creating tmp file with var values to pass them into less file
		$tmpFile = tempnam( sys_get_temp_dir(), 'less-vars' );
		if ( ! $tmpFile ) {
			$tmpFile = tempnam( ini_get('upload_tmp_dir'), 'less-vars' );
		}
		$tmpBaseName = basename( $tmpFile );
		file_put_contents( $tmpFile, join( "\n",$newValuesText ) );
		$lessContent = preg_replace(
			'`\/\*\s*@variables-file\s*\*\/`',
			"\n@import '{$tmpBaseName}';\n",
			$lessContent
		);

		$less->addImportDir( dirname( $tmpFile ) );

		$compiledCssText = $less->compile( $lessContent );

		unlink( $tmpFile );

		return $compiledCssText;
	}

	/**
	 * Returns list of variables that can be set for the passed less file.
	 * Each one has following keys:
	 * 	- 'less_name'   string - name of the less variable (contains leading @)
	 * 	- 'option_name' string - name of the theme option related with variable
	 * 	- 'value'       string - that should be sent to the less file
	 * 	- 'rawValue'    mixed  - optional, will be defined if corresponding option in theme options is not a string
	 * 	- 'default'     string - default value that currently set in the less file
	 *
	 * @param  string $fullLessFilePath full path to the less file
	 * @param  array  $themeSettings    values of the theme options
	 * @return array
	 */
	public function getLessVariables($fullLessFilePath, array $themeSettings) {
		$result = array();

		$variablesFile = $this->getVariablesFileFromLessFile( $fullLessFilePath );
		$parsedOptions = $this->parseAvailabeOptionsFromVariablesFile( $variablesFile );

		if ( $parsedOptions ) {
			foreach ( $parsedOptions as $_optionName => $details ) {

				$optionName = null;
				$optionSubKey = null;
				$parseResults = null;

				if ( preg_match( '/(\w+)\[(\w+)\]/', $_optionName, $parseResults ) ) {
					$optionName = $parseResults[1];
					$optionSubKey = $parseResults[2];
				} else {
					$optionName = $_optionName;
				}

				$rawValue = isset( $themeSettings[$optionName] ) ? $themeSettings[$optionName] : null;
				if ( $optionSubKey && $rawValue ) {
					$rawValue = isset( $rawValue[$optionSubKey] ) ? $rawValue[$optionSubKey] : null;
				}

				$textValue = $this->convertThemeOptionValueForLess( $rawValue, $_optionName );

				$details['value'] = $textValue;
				if ( $textValue != $rawValue ) {
					$details['rawValue'] = $rawValue;
				}

				$result[$_optionName] = $details;
			}
		}
		return $result;
	}

	/**
	 * Filtering function used to convert some option value before rendering it to the less variable.
	 *
	 * @param  mixed  $rawValue     value of the $option_name from the theme settings.
	 * @param  string $option_name  name of the option.
	 * @return mixed
	 */
	public function convertThemeOptionValueForLess($rawValue, $option_name) {
		if ( $rawValue ) {
			if ( is_array( $rawValue ) ) { // && preg_match('/_font_size$/', $option_name)
				return join( '', $rawValue );
			}
		}
		return $rawValue;
	}

	/**
	 * Parses variables file to find all definition of options.
	 * Relation between less variable and option should be defined via \/\*@theme_option:OPTION_NAME \*\/ meta comment.
	 *
	 * @param  string $variablesFile path to the less variables file
	 * @return assoc
	 */
	protected function parseAvailabeOptionsFromVariablesFile($variablesFile) {
		$content = file_get_contents( $variablesFile );
		$result = array();

		$parseRes = null;
		$metaTag = $this->variableMetaTag;
		if ( preg_match_all( '`\@(\S+)\:\s*([^;]+);[\ ]*\/\*\s*'.$metaTag.'\s*(\S+)\s*\*\/`', $content, $parseRes ) ) {
			foreach ( $parseRes[0] as $index => $fullText ) {
				$option_name = $parseRes[3][$index];
				$result[$option_name] = array(
					'less_name' => '@' . $parseRes[1][$index],
					'option_name' => $option_name,
					'default' => $parseRes[2][$index],
				);
			}
		}
		return $result;
	}

	/**
	 * Searches for the variables file inside the passed less file.
	 * Variables file should be marked in the following way:
	 * <pre>
	 * @import 'variables.less';\/\* @variables-file \*\/
	 * </pre>
	 *
	 * @param  string $lessFilePath path to the less file
	 * @return string               path to the less file that defines all variables required for less
	 */
	protected function getVariablesFileFromLessFile($lessFilePath) {
		$metaComment = $this->variablesFileMarker ? $this->variablesFileMarker : '@variables-file';

		$result = null;

		$parseRes = null;
		if ( preg_match( '`@import\s*(.*);\s*\/\*\s*'.$metaComment.'\s*\*\/`', file_get_contents( $lessFilePath ), $parseRes ) ) {
			$relatedPath = $parseRes[1];
			$result = dirname( $lessFilePath ) . '/' . trim( $relatedPath, '\"\'' );
		};

		if ( ! $result ) {
			throw new Exception( 'Variables file has not been found.' );
		} elseif ( ! file_exists( $result ) ) {
			throw new Exception(strtr('File {filePath} does not exist.', array(
				'{filePath}' => $result,
			)));
		}

		return $result;
	}

	/**
	 * Creates instance of the less processor used for the less file compilation.
	 *
	 * @return lessc
	 */
	protected function createLessProcessor() {
		$lessProcessor = new lessc;
		TdLesscPlugin::getInstance()->inject( $lessProcessor );
		return $lessProcessor;
	}
}
