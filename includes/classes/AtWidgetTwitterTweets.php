<?php
/**
 * Tweets rendering widget component.
 *
 * @uses tmhOAuth
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

class AtWidgetTwitterTweets extends WP_Widget
{
	private static $jsonURL = 'https://api.twitter.com/1.1/statuses/user_timeline.json';

	private static $iconTwitter = '<i class="fa fa-twitter"></i>';

	private static $linkReplyTweet = 'https://twitter.com/intent/tweet?in_reply_to=';

	private static $linkReTweet = 'https://twitter.com/intent/retweet?tweet_id=';

	private static $linkFavoriteTweet = 'https://twitter.com/intent/favorite?tweet_id=';

	private $cache_pefix = 'adventuretours_twitterw_';

	private $cache_time = 300; //300 seconds, 5 minutes

	private static $imageSize = array( 'width' => 48, 'height' => 48 );

	public function __construct() {
		parent::__construct(
			'twitter_tweets_adventure_tours',
			'AdventureTours: Twitter',
			array(
				'description' => 'Twitter Widget',
			)
		);
	}

	public function widget( $args, $instance ) {
		extract( $args );

		$output = '';
		if ( empty( $instance['user'] ) || empty( $instance['consumerKey'] ) || empty( $instance['consumerSecret'] ) ) {
			$output .= esc_html__( 'Please complete the twitter settings in the Twitter widget.', 'adventure-tours' );
			// Error, throw exception.
		} else {
			$username = $instance['user'];

			$showTweetTimeTF = $instance['showTweetTimeTF'];
			$includeRepliesTF = $instance['includeRepliesTF'];
			$updateCount = ! empty( $instance['count'] ) && $instance['count'] > 0 ? $instance['count'] : 3;

			$cacheResult = $this->get_cache( 'response' );
			$response = null;
			$freshResponse = false;
			if ( $cacheResult && $cacheResult['user'] == $username ) {
				$response = $cacheResult['response'];
			}

			if ( null === $response ) {
				$response = $this->make_request(
					$username,
					array(
						'consumerKey' => $instance['consumerKey'],
						'consumerSecret' => $instance['consumerSecret'],
					)
				);
				$freshResponse = true;
			}

			$tweetsError = $response && isset( $response->errors ) ? $response->errors['0']->message : false;

			// we will save to cache only valid responses or cache errors if they heppens on production
			if ( $response && $freshResponse && ( ! $tweetsError || ! WP_DEBUG) ) {
				$this->set_cache('response', array(
					'user' => $username,
					'response' => $response,
				));
			}
			if ( $response && ! $tweetsError ) {
				$i = 0;
				foreach ( $response as $tweet ) {
					//skip this iteration of the loop if this is a reply and we are not showing replies
					if ( ! $includeRepliesTF && strlen( $tweet->in_reply_to_screen_name ) ) {
						continue;
					}
					$output .= $this->render_tweet( $tweet, $showTweetTimeTF );
					$i++;

					//exit this loop if we have reached updateCount
					if ( $i >= $updateCount ) {
						break;
					}
				}
			} else {
				$output .= esc_html_e( 'Please verify the settings in the Twitter widget.', 'adventure-tours' );
				if ( $tweetsError && WP_DEBUG ) {
					$output .= '<br />' . $tweetsError;
				}
			}
		}

		printf(
			'%s%s<div class="widget-twitter">%s</div>%s',
			$before_widget,
			! empty( $instance['widgetTitle']) ? $before_title . esc_html( $instance['widgetTitle'] ) . $after_title : '',
			$output,
			$after_widget
		);
	}

	protected function render_tweet($tweet, $showTimeAgo = true) {
		$user = $tweet->user;
		$imgUrl = $user->profile_image_url;//profile_image_url_https
		$nameText = $user->name;
		$sceenName = $user->screen_name;
		$time = ($showTimeAgo) ? '<div class="widget-twitter__item__time">' . esc_html( $this->twitter_time_ltw( $tweet->created_at ) ) . '</div>' : '';

		$tweetsHtml = '<div class="widget-twitter__item">' .
			'<div class="widget-twitter__item__container">' .
				'<div class="widget-twitter__item__container__item widget-twitter__item__container__item--image">' .
					'<img src="' . esc_url( $imgUrl ) . '" alt="" width="' . esc_attr( self::$imageSize['width'] ) . '" height="' . esc_attr( self::$imageSize['height'] ) . '">' .
				'</div>' .
				'<div class="widget-twitter__item__container__item widget-twitter__item__info">' .
					'<a href="' . esc_url( 'https://twitter.com/' . $sceenName ) . '" target="_blank" class="widget-twitter__item__name">' . esc_html( $nameText ) . '</a>' .
					'<div class="widget-twitter__item__login"><a href="' . esc_url( 'https://twitter.com/' . $sceenName ) . '" target="_blank">@' . esc_html( $sceenName ) . '</a></div>' .
				'</div>' .
			'</div>' .
			'<div class="widget-twitter__item__content">' . $this->convert_links( $tweet->text ) . '</div>' .
			$time .
		'</div>';

		return $tweetsHtml;
	}

	protected function convert_links($text) {
		return preg_replace_callback( '`(https?:\/\/|\s\@|\s\#)([^\s]+)`', array( $this, 'convert_text_url_to_link' ), $text );
	}

	public function convert_text_url_to_link($match) {
		$linkText = $fullUrl = ltrim( $match[0] );

		$additionalAttributes = '';
		switch ( $fullUrl[0] ) {
		case '@':
			if ( preg_match( '/^[a-zA-Z0-9_]{1,15}/', $match[2], $matchUserName ) ) {
				$fullUrl = 'https://twitter.com/' . $matchUserName[0];
			} else {
				$fullUrl = 'https://twitter.com/' . $match[2];
			}
			$additionalAttributes = ' style="font-weight:lighter;"';
			break;

		case '#':
			$fullUrl = 'https://twitter.com/search?q=' . $match[2];
			$additionalAttributes = ' style="font-weight:lighter;"';
			break;
		}

		return ( $match[0][0] == ' ' ? ' ' : '' ) . '<a href="' . esc_url( $fullUrl ).'" target="_blank" rel="nofollow"'.$additionalAttributes.'>' . esc_html( $linkText ) . '</a>';
	}

	private function set_cache($name, $value) {
		if ( $this->cache_time > 0 && $this->cache_pefix ) {
			$cache_key = $this->cache_pefix . $name;
			set_transient( $cache_key, $value, $this->cache_time );
		}
	}

	private function get_cache($name) {
		if ( ! $this->cache_pefix ) {
			return false;
		}

		return get_transient( $this->cache_pefix . $name );
	}

	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance = $old_instance;

		$instance['user'] = $new_instance['user'];
		$instance['count'] = $new_instance['count'];
		$instance['widgetTitle'] = $new_instance['widgetTitle'];
		$instance['consumerKey'] = $new_instance['consumerKey'];
		$instance['consumerSecret'] = $new_instance['consumerSecret'];
		$instance['showTweetTimeTF'] = ! empty( $new_instance['showTweetTimeTF'] ) ? '1' : false;
		$instance['includeRepliesTF'] = ! empty( $new_instance['includeRepliesTF'] ) ? '1' : false;

		return $instance;
	}

	public function form( $instance ) {
		// outputs the options form on admin
		$defaults = array(
			'user' => '',
			'consumerKey' => '',
			'consumerSecret' => '',
			'count' => '1',
			'widgetTitle' => esc_html__( 'Latest Tweets','adventure-tours' ),
			'showTweetTimeTF' => false,
			'includeRepliesTF' => false,
		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		echo '<style> .widgets-label{line-height:35px;display:block;} </style>' .
		'<div style="padding-top:15px">' .
			'<label for="' . esc_attr( $this->get_field_id( 'user' ) ) . '" class="widgets-label">' . esc_html__( 'Twitter user', 'adventure-tours' ) .
				'&nbsp;@<input type="text" size="12" id="' . esc_attr( $this->get_field_id( 'user' ) ) . '" name="' . esc_attr( $this->get_field_name( 'user' ) ) . '" value="' . esc_attr( $instance['user'] ) . '" />' .
			'</label>' .

			'<label for="' . esc_attr( $this->get_field_id( 'count' ) ) . '" class="widgets-label">' . esc_html__( 'Show item number', 'adventure-tours' ) .
				'&nbsp;<input type="text" id="' . esc_attr( $this->get_field_id( 'count' ) ) . '" size="2" name="' . esc_attr( $this->get_field_name( 'count' ) ) . '" value="' . esc_attr( $instance['count'] ) . '" />' .
			'</label>' .

			'<label for="' . esc_attr( $this->get_field_id( 'widgetTitle' ) ) . '" class="widgets-label">' . esc_html__( 'Widget title', 'adventure-tours' ) . '</label>' .
				'<input class="widefat" type="text" id="' . esc_attr( $this->get_field_id( 'widgetTitle' ) ) . '" size="16" name="' . esc_attr( $this->get_field_name( 'widgetTitle' ) ) . '" value="' . esc_attr( $instance['widgetTitle'] ) . '" />' .

			'<label for="' . esc_attr( $this->get_field_id( 'consumerKey' ) ) . '" class="widgets-label">' . esc_html__( 'Consumer Key', 'adventure-tours' ) . '</label>' .
				'<input class="widefat" type="text" id="' . esc_attr( $this->get_field_id( 'consumerKey' ) ) . '" name="' . esc_attr( $this->get_field_name( 'consumerKey' ) ) . '" value="' . esc_attr( $instance['consumerKey'] ) . '" />' .

			'<label for="' . esc_attr( $this->get_field_id( 'consumerSecret' ) ) . '" class="widgets-label">' . esc_html__( 'Consumer Secret', 'adventure-tours' ) . '</label>' .
				'<input class="widefat" type="text" id="' . esc_attr( $this->get_field_id( 'consumerSecret' ) ) . '" name="' . esc_attr( $this->get_field_name( 'consumerSecret' ) ) . '" value="' . esc_attr( $instance['consumerSecret'] ) . '" />' .

			'<p>' .
				'<input value="1" type="checkbox" id="' . esc_attr( $this->get_field_id( 'showTweetTimeTF' ) ) . '" name="' . esc_attr( $this->get_field_name( 'showTweetTimeTF' ) ) . '" ' . self::inputChecked( 'checkbox', $instance['showTweetTimeTF'] ) . '>' .
				'<label for="' . esc_attr( $this->get_field_id( 'showTweetTimeTF' ) ) . '">' . esc_html__( 'Show tweet', 'adventure-tours' ) . ' "' . esc_html__( 'time ago', 'adventure-tours' ) . '"' . '</label>' .
			'</p>' .
			'<p>' .
				'<input value="1" type="checkbox" id="' . esc_attr( $this->get_field_id( 'includeRepliesTF' ) ) . '" name="' . esc_attr( $this->get_field_name( 'includeRepliesTF' ) ) . '" ' . self::inputChecked( 'checkbox', $instance['includeRepliesTF'] ) . '>' .
				'<label for="' . esc_attr( $this->get_field_id( 'includeRepliesTF' ) ) . '">' . esc_html__( 'Include replies', 'adventure-tours' ) . '</label>' .
			'</p>' .
		'</div>';
	}

	private function twitter_time_ltw( $time ) {
		//get difference
		$time = time() - strtotime( $time );
		//calculate different time values
		$minute = 60;
		$hour = $minute * 60;
		$day = $hour * 24;
		$week = $day * 7;

		//if less then 3 seconds
		if ( $time < 3 ) {
			return esc_html__( 'right now', 'adventure-tours' );
		}
		//if less then minute
		if ( $time < $minute ) {
			return floor( $time ) . esc_html__( ' seconds ago', 'adventure-tours' );
		}
		//if less then 2 minutes
		if ( $time < $minute * 2 ) {
			return esc_html__( 'about 1 minute ago', 'adventure-tours' );
		}
		//if less then hour
		if ( $time < $hour ) {
			return floor( $time / $minute ) . esc_html__( ' minutes ago', 'adventure-tours' );
		}
		//if less then 2 hours
		if ( $time < $hour * 2 ) {
			return esc_html__( 'about 1 hour ago', 'adventure-tours' );
		}
		//if less then day
		if ( $time < $day ) {
			return floor( $time / $hour ) . esc_html__( ' hours ago', 'adventure-tours' );
		}
		//if more then day, but less then 2 days
		if ( $time > $day && $time < $day * 2 ) {
			return esc_html__( 'yesterday', 'adventure-tours' );
		}
		//if less then year
		if ( $time < $day * 365 ) {
			return floor( $time / $day ) . esc_html__( ' days ago', 'adventure-tours' );
		}

		//else return more than a year
		return esc_html__( 'over a year ago', 'adventure-tours' );
	}

	private function make_request($username, $authDetails, $skipErrors = false ) {
		$tmhOAuth = new tmhOAuth( array(
			'consumer_key' => $authDetails['consumerKey'],
			'consumer_secret' => $authDetails['consumerSecret'],
			'curl_ssl_verifypeer' => false,
		) );

		$tmhOAuth->request(
			'GET',
			self::$jsonURL,
			array(
				'screen_name' => $username,
			)
		);

		$responseText = $tmhOAuth->response['response'];
		$response = $responseText ? json_decode( $responseText ) : null;

		if ( ! $response || ( $skipErrors && isset( $response->errors )) ) {
			return false;
		} else {
			return $response;
		}
	}

	private static function inputChecked( $type, $variable, $val = '' ) {
		$result = '';

		if ( ! empty( $type ) && isset( $variable ) ) {
			switch ( $type ) {
				case 'radio' :
					$result = ( $variable == $val ? 'checked="checked"' : '' );
				break;
				case 'checkbox' :
					$result = ( '1' == $variable ? 'checked="checked"' : '' );
				break;
			}
		}
		return $result;
	}
}
