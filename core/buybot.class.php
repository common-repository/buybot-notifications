<?php
if ( ! defined('ABSPATH')) exit; 

global $wpdb,$woocommerce,$product;

class buybot {

	
	private $username;
	private $hash;
	private $apiKey;

	private $errorReporting = false;

	public $errors = array();
	public $warnings = array();

	public $lastRequest = array();
	function __construct($username, $hash, $apiKey = false)
	{
		$this->username = $username;
		$this->hash = $hash;
		if ($apiKey) {
			$this->apiKey = $apiKey;
		}

	}


public static function sendWhatsapp($params)
	{
		$url = "https://api.greenreceipt.in/webhook/wordpress/SendMessage";
		$body = $params;
    $args = array(
        'method'      => 'POST',
        'timeout'     => 45,
        'sslverify'   => false,
        'headers'     => array(
            //'Authorization' => $headerkey,
            'Content-Type'  => 'application/json',
        ),
        'body'        => $body,
    );
    $request = wp_remote_post( $url, $args );
    if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
        error_log( print_r( $request, true ) );
    }

    $response = wp_remote_retrieve_body( $request );

	}

	//new funtion added for catalog details
	// public static function sendWhatsappmethod($params,$headerkey)
	// {
	// 	$url = "https://api.greenreceipt.in/webhook/woocommerce/ItemList";
	// 	$body = $params;
    // $args = array(
    //     'method'      => 'POST',
    //     'timeout'     => 45,
    //     'sslverify'   => false,
    //     'headers'     => array(
    //         'Authorization' => $headerkey,
    //         'Content-Type'  => 'application/json',
    //     ),
    //     'body'        => $body,
    // );
    // $request = wp_remote_post( $url, $args );
    // if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
    //     error_log( print_r( $request, true ) );
    // }

    // $response = wp_remote_retrieve_body( $request );

	// }


	
}
	

?>
