<?php

/*
Plugin Name: CB Instagram API Plugin
Plugin URI: https://github.com/corey-benson/cb-instagramed
Description: A plugin to pull the lastest images from a users Instagram feed. Use the shortcode [instagramed] to pull in the feed and the parameters image_size and api_count to determine display [instagramed image_size="" api_count=""].
Author: Corey Benson
Version: 1.0
Author URI: https://github.com/corey-benson/
*/

define('CB-INST-API_PATH', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );
define('CB-INST-API_NAME', "Instagram");
define("CB-INST-API_VERSION", "1.0");
define("CB-INST-API_SLUG", 'cb_instagram');


class CB_Instagramed {

	// User credentials
	private $user_id = '#######';
	private $access_token = '###################';

	public function __construct() {

		add_shortcode( 'instagramed', array($this, 'render_shortcode'));
		add_action( 'http_request_args', 'no_ssl_http_request_args', 10, 2 );

	}

	public function no_ssl_http_request_args( $args, $url ) {

		// Fix SSL request error
		$args['sslverify'] = false;
		return $args;

	}

	public function render_shortcode($atts) {

		// Get attibutes and set defaults
		extract(shortcode_atts(array(
			'image_size' => 'thumbnail' ,
			'api_count' => '16'
		), $atts ));


		// Define resource output
		$inst_str = "";
		$url = "https://api.instagram.com/v1/users/" . $this->user_id . "/media/recent?access_token=" . $this->access_token . "&count=" . $api_count;

		// Get the remote data
		$response = wp_remote_get( $url ); 

		if ( is_wp_error( $response ) ) {

			// Handle errors
			$error_str = $response->get_error_message();

			// Constrct HTML to display on error 
			$inst_str .= '<div class="images">';
			$inst_str .= '</div>';

		} else {

			// Process response
			$response = json_decode( $response['body'] );
			$data_array = array();
			$index = 0;

			// Get the response data
			foreach( $response->data as $obj ) {

				$data_array[ $index ][ 'href' ] = $obj->link;
				$data_array[ $index ][ 'username' ] = $obj->user->username;
				$data_array[ $index ][ 'image' ] = $obj->images->$image_size->url;

				$index++;

			}


			// Construct HTML to display images
			$inst_str .= '<div class="images">';
			$inst_str .= '</div>';

		}

		return $inst_str;

	}


}

$cb_instagram = new CB_Instagramed();

?>