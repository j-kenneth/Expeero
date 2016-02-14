<?php
/**
 * Class that implements 'proxy' for retriving share counters for social sharing buttons.
 *
 * @author    Themedelight
 * @package   Themedelight/Components
 * @version   1.0.0
 */

class TdSharrreActions extends TdComponent
{
	/**
	 * Ajax action name via that counters can be retived.
	 *
	 * @var string
	 */
	public $ajax_action_name = 'sharrre_curl';

	public function init() {
		if ( ! parent::init() ) {
			return false;
		}

		if ( $this->ajax_action_name ) {
			add_action( 'wp_ajax_' . $this->ajax_action_name, array( $this, 'ajax_action' ) );
			add_action( 'wp_ajax_nopriv_' . $this->ajax_action_name, array( $this, 'ajax_action' ) );
		}

		return true;
	}

	/**
	 * Ajax action processing function.
	 *
	 * @return void
	 */
	public function ajax_action() {
		header( 'content-type: application/json' );

		$urlParam = isset( $_GET['url'] ) ? $_GET['url'] : '';
		$json = array(
			'url' => $urlParam,
			'count' => 0,
		);

		if ( filter_var( $urlParam, FILTER_VALIDATE_URL ) ) {
			$url = urlencode( $urlParam );
			$type = isset( $_GET['type'] ) ? urlencode( $_GET['type'] ) : '';
			if ( $type == 'googlePlus' ) {  //source http://www.helmutgranda.com/2011/11/01/get-a-url-google-count-via-php/
				$content = $this->do_request( 'https://plusone.google.com/u/0/_/+1/fastbutton?url='.$url.'&count=true' );
				if ( $content ) {
					$dom = new DOMDocument;
					$dom->preserveWhiteSpace = false;
					@$dom->loadHTML( $content );
					$domxpath = new DOMXPath( $dom );
					$newDom = new DOMDocument;
					$newDom->formatOutput = true;

					$filtered = $domxpath->query( "//div[@id='aggregateCount']" );
					if ( isset( $filtered->item( 0 )->nodeValue ) ) {
						$json['count'] = str_replace( '>', '', $filtered->item( 0 )->nodeValue );
					}
				}
			} elseif ( $type == 'stumbleupon' ) {
				$content = $this->do_request( "http://www.stumbleupon.com/services/1.01/badge.getinfo?url=$url" );
				if ( $content ) {
					$result = json_decode( $content );
					if ( isset( $result->result->views ) ) {
						$json['count'] = $result->result->views;
					}
				}
			}
		}

		echo wp_json_encode( $json );
		exit();
	}

	/**
	 * Returns content for the passed url.
	 *
	 * @param  string $encUrl url that should be loaded.
	 * @return string
	 */
	protected function do_request($encUrl) {
		$response = wp_remote_get($encUrl, array(
			'redirection' => 3,
			'timeout' => 10,
			'user-agent' => 'sharrre',
			'sslverify' => false,
		));
		if ( ! empty( $response['body'] ) ) {
			return $response['body'];
		}
		return '';
	}
}
