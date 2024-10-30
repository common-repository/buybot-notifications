<?php 

if ( ! defined('ABSPATH')) exit; 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(ABSPATH . 'wp-load.php');
require_once(ABSPATH . '/wp-includes/pluggable.php');
require_once(ABSPATH . '/wp-content/plugins/woocommerce/includes/wc-cart-functions.php');
require_once (BUYBOT_NOTIFICATIONS_GATEEWAY_DIR.'/core/buybot.class.php');

// Include WooCommerce core files
if ( ! function_exists( 'WC' ) ) {
    include_once( ABSPATH . 'wp-content/plugins/woocommerce/woocommerce.php' );
}

global $wpdb,$woocommerce,$product;

if ( in_array( 'woocommerce/woocommerce.php', 
    apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {



//--------------

add_action( 'wp_head', 'get_store_details' );

function get_store_details() {

	$options = get_option( 'buybot_notifications_option_name' );

	// Get the store name
	$store_name = get_bloginfo('name');
  
	// Get the store URL
	$store_url = get_bloginfo('url');
  
	// Get the store owner name
	$store_owner_name = get_option('woocommerce_store_owner');
  
	// Get the store owner email
	$store_owner_email = get_option('woocommerce_email_from_address');
  
	// Get the customer query number if present
	$customer_query_number = get_option('woocommerce_support_phone');

	// Get the store address
	$store_address = get_option('woocommerce_store_address');

	// Get the store address line 2
	$store_address_2 = get_option('woocommerce_store_address_2');

	// Get the store city
	$store_city = get_option('woocommerce_store_city');

	// Get the store postcode
	$store_postcode = get_option('woocommerce_store_postcode');

	// Get the store country
	$store_country = get_option('woocommerce_store_country');

	// Get the store state
	$store_state = get_option('woocommerce_store_state');

	// Get the store phone number
	$store_phone_number = get_option('woocommerce_support_phone');

	  // Create an array with the information
	  $store_details = array(
        'access_key' => $options['api_key'],
        'store_name' => $store_name,
        'store_url' => $store_url,
        'store_owner_name' => $store_owner_name,
        'store_owner_email' => $store_owner_email,
        'customer_query_number' => $customer_query_number,
		'store_address' => $store_address,
        'store_address_2' => $store_address_2,
        'store_city' => $store_city,
        'store_postcode' => $store_postcode,
        'store_country' => $store_country,
        'store_state' => $store_state,
        'store_phone_number' => $store_phone_number,
    );

	  // Convert the array to JSON format
	  $store_details_json = json_encode($store_details);

	  // Write the JSON data to a file
	  file_put_contents(ABSPATH . "storedetails.txt", $store_details_json . "\n", FILE_APPEND);

	  
}

  

//--------------

// Schedule the generate_product_details() function to run every 24 hours
add_action( 'wp', 'schedule_product_details' );
function schedule_product_details() {
    if ( ! wp_next_scheduled( 'generate_product_details_event' ) ) {
        wp_schedule_event( time(), 'daily', 'generate_product_details_event' );
    }
}

//Fetch All Product Details from website

//add_action( 'woocommerce_loaded', 'generate_product_details' );

// Hook the generate_product_details() function to the scheduled event
add_action( 'generate_product_details_event', 'generate_product_details' );

function generate_product_details() {

	$options = get_option( 'buybot_notifications_option_name' );

	 // Get abandoned cart details
	//  $cart_items = get_abandoned_cart_details();

	//  file_put_contents( ABSPATH . 'Abandoned2.txt', json_encode( $cart_items ) );

    $args = array( 'post_type' => 'product', 'posts_per_page' => -1 );
    $products = get_posts( $args );
    // echo '<pre>$products:-'; print_r( $products ); echo '</pre>';
  //  $product_details = array();
	$product_details = array(
		'access_key' => $options['api_key'],
		'products' => array()
	);

    foreach ( $products as $product ) {
        $product_id = $product->ID;
        $product_name = $product->post_title;
        $product_price = get_post_meta( $product_id, '_price', true );
        $product_quantity = get_post_meta( $product_id, '_stock', true );
        $product_stock_status = get_post_meta( $product_id, '_stock_status', true );
        $product_sku = get_post_meta( $product_id, '_sku', true );
        $product_short_description = $product->post_excerpt;
        $product_long_description = $product->post_content;
		$product_weight = get_post_meta( $product_id, '_weight', true );
		$product_length = get_post_meta( $product_id, '_length', true );
		$product_width = get_post_meta( $product_id, '_width', true );
		$product_height = get_post_meta( $product_id, '_height', true );
		// $product_categories = wp_get_post_terms( $product_id, 'product_cat' );
		// $product_colors = wp_get_post_terms( $product_id, 'pa_color' );
       // $product_image_src = wp_get_attachment_url( get_post_thumbnail_id( $product_id ) );
	   $product_images = array();
        $attachments = get_posts(
            array(
                'post_type' => 'attachment',
                'posts_per_page' => -1,
                'post_parent' => $product_id,
                'exclude' => get_post_thumbnail_id( $product_id )
            )
        );
        foreach ( $attachments as $attachment ) {
            $product_images[] = wp_get_attachment_url( $attachment->ID );
        }

        $product_details['products'][] = array(
			//'access_key' => $options['api_key'],
            'product_id' => $product_id,
            'product_name' => $product_name,
            'product_price' => $product_price,
            'product_quantity' => $product_quantity,
            'product_stock_status' => $product_stock_status,
            'product_sku' => $product_sku,
            'product_short_description' => $product_short_description,
            'product_long_description' => $product_long_description,
           // 'product_image_src' => $product_image_src,
			'product_images' => $product_images,
			'product_length' => $product_length,
			'product_height' => $product_height,
			'product_width'  => $product_width,
			'product_weight'  => $product_weight,
			// 'product_categories' => $product_categories,
     	    // 'product_colors' => $product_colors,
        );
    }
	$request_args = array(
		'method' => 'POST',
		'body' => json_encode( $product_details ),
		'headers' => array(
			'Content-Type' => 'application/json',
		),
	);

	$response = wp_remote_post( 'https://api.greenreceipt.in/webhook/wordpress/ItemList', $request_args );

	if ( is_wp_error( $response ) ) {
		$error_message = $response->get_error_message();
		error_log( "Error sending catalog details: $error_message" );
	} else {
		$response_code = wp_remote_retrieve_response_code( $response );
		if ( $response_code === 200 ) {
			$response_body = wp_remote_retrieve_body( $response );
			file_put_contents( ABSPATH . 'APIResponse.txt', $response_body );
			file_put_contents( ABSPATH . 'APIValue.txt', json_encode( $product_details['products'] ) );
			error_log( 'Catalog details sent successfully.' );
		} else {
			error_log( 'Error sending catalog details: ' . wp_remote_retrieve_response_message( $response ) );
		}
	}

    //file_put_contents( ABSPATH . 'ProductDetails.txt', json_encode( $product_details ) );
	file_put_contents( ABSPATH . 'ProductDetails.txt', json_encode( $product_details ) . "\n", FILE_APPEND );

}




//--------------------------------------------------------------------------------------------------

	
add_action("woocommerce_order_status_changed", "buybot_notifications_order_status",  20, 1);

	
	function buybot_notifications_order_status($order_id){
        
		 static $called = false;
		if (isset($called) && $called) {
			return false;
		}
		
		$called = true;
		global $woocommerce;
		$order = new WC_Order ($order_id);
        
		$options = get_option( 'buybot_notifications_option_name' );

		$buybot = new buybot(false, false, $options['api_key']);

		
		// Get all order items and product details
		$order_items = $order->get_items();
		$item_details = array();
		$product_details = array();

		foreach ($order_items as $item) {
			$product_id = $item->get_product_id();
			$product = $item->get_product();
			$item_data = $item->get_data();
			$item_meta_data = $item->get_meta_data();
			
			// Get item details
			$item_details[] = array(
				'product_id' => $product_id,
				'name' => $item_data['name'],
				'quantity' => $item_data['quantity'],
				'total' => wc_format_decimal($item_data['total'], 2),
				'subtotal' => wc_format_decimal($item_data['subtotal'], 2),
				'tax' => wc_format_decimal($item_data['subtotal_tax'], 2),
				
				'meta_data' => $item_meta_data,
				'sku' => $product->get_sku(),
				'description' => $product->get_description(),
				'url' => get_permalink($product_id),
				'image' => wp_get_attachment_url($product->get_image_id()),
				
				'attributes' => $product->get_attributes(),
				'product_type' => $product->get_type(),
			);
		
			// Get product details
			$product_details[] = array(
				'product_id' => $product_id,
				'name' => $product->get_name(),
				'sku' => $product->get_sku(),
				'regular_price' => wc_format_decimal($product->get_regular_price(), 2),
				'sale_price' => wc_format_decimal($product->get_sale_price(), 2),
				'price' => wc_format_decimal($product->get_price(), 2),
				'weight' => wc_format_decimal($product->get_weight(), 2),
				'dimensions' => array(
					'length' => wc_format_decimal($product->get_length(), 2),
					'width' => wc_format_decimal($product->get_width(), 2),
					'height' => wc_format_decimal($product->get_height(), 2),
				),
				'categories' => $product->get_category_ids(),
				'tags' => $product->get_tag_ids(),
				'description' => $product->get_description(),
   				'short_description' => $product->get_short_description(),
   				'url' => get_permalink($product_id),
   				'image' => wp_get_attachment_url($product->get_image_id()),
   				'gallery_images' => $product->get_gallery_image_ids(),
  		 		
  	 			'type' => $product->get_type(),
    		    'is_on_sale' => $product->is_on_sale(),
   				'average_rating' => $product->get_average_rating(),
    			'review_count' => $product->get_review_count(),
			);
		}
		
		

		 $order_data = array(
					'access_key' => $options['api_key'],
					'order_id' => $order->get_id(),
                    'order_number' => $order->get_order_number(),
                    'order_date' => date('Y-m-d H:i:s', strtotime(get_post($order->get_id())->post_date)),
                    'status' => $order->get_status(),
                    'shipping_total' => $order->get_total_shipping(),
                    'shipping_tax_total' => wc_format_decimal($order->get_shipping_tax(), 2),
                    'fee_total' => '',
                    'fee_tax_total' => '',
                    'tax_total' => wc_format_decimal($order->get_total_tax(), 2),
                    'cart_discount' => (defined('WC_VERSION') && (WC_VERSION >= 2.3)) ? wc_format_decimal($order->get_total_discount(), 2) : wc_format_decimal($order->get_cart_discount(), 2),
                    'order_discount' => (defined('WC_VERSION') && (WC_VERSION >= 2.3)) ? wc_format_decimal($order->get_total_discount(), 2) : wc_format_decimal($order->get_order_discount(), 2),
                    'discount_total' => wc_format_decimal($order->get_total_discount(), 2),
                    'order_total' => wc_format_decimal($order->get_total(), 2),
                    'order_currency' => $order->get_currency(),
                    'payment_method' => $order->get_payment_method(),
                    'shipping_method' => $order->get_shipping_method(),
                    'customer_id' => $order->get_user_id(),
                    'customer_user' => $order->get_user_id(),
                    'customer_email' => ($a = get_userdata($order->get_user_id() )) ? $a->user_email : '',
                    'billing_first_name' => $order->get_billing_first_name(),
                    'billing_last_name' => $order->get_billing_last_name(),
                    'billing_company' => $order->get_billing_company(),
                    'billing_email' => $order->get_billing_email(),
                    'billing_phone' => $order->get_billing_phone(),
                    'billing_address_1' => $order->get_billing_address_1(),
                    'billing_address_2' => $order->get_billing_address_2(),
                    'billing_postcode' => $order->get_billing_postcode(),
                    'billing_city' => $order->get_billing_city(),
                    'billing_state' => $order->get_billing_state(),
                    'billing_country' => $order->get_billing_country(),
                    'shipping_first_name' => $order->get_shipping_first_name(),
                    'shipping_last_name' => $order->get_shipping_last_name(),
                    'shipping_company' => $order->get_shipping_company(),
                    'shipping_address_1' => $order->get_shipping_address_1(),
                    'shipping_address_2' => $order->get_shipping_address_2(),
                    'shipping_postcode' => $order->get_shipping_postcode(),
                    'shipping_city' => $order->get_shipping_city(),
                    'shipping_state' => $order->get_shipping_state(),
                    'shipping_country' => $order->get_shipping_country(),
                    'customer_note' => $order->get_customer_note(),
                    'download_permissions' => $order->is_download_permitted() ? $order->is_download_permitted() : 0,
					'date_time' => date('Y-m-d H:i:s'),
					'items' => $item_details,
					'products' => $product_details,
					'delivery_dt' => '',
					'delivery_person' =>'',
					'delivery_time' => '',
            );

				// error_log(print_r($order_data, true));
				//file_put_contents(ABSPATH. "Order_Details.txt", $order_data);

				// echo "<pre>".print_r($order_data,true)."</pre>"; //Faiyaz
		
		$telephoneNumber = $order->billing_phone;
		$headerkey = md5($options['api_key']);
		 if($order->status === 'pending' && $options['buybot_order_status_pending_payment']=='on') {
			$recipients = $telephoneNumber;
			$order_data['event_name'] = 'order_pending';			
			$body=json_encode($order_data);
			$result = $buybot->sendWhatsapp($body);
			file_put_contents(ABSPATH. "OrderresponseResult.txt", $body);
		}

		if($order->status === 'failed' && $options['buybot_order_status_failed']=='on' ) {
			$recipients = $telephoneNumber; 
			$order_data['event_name'] = 'order_failed';
			$body=json_encode($order_data);
			$result = $buybot->sendWhatsapp($body);	
			file_put_contents(ABSPATH. "OrderresponseResult.txt", $body);		
		}

		if($order->status === 'refunded' && $options['buybot_order_status_refunded']=='on' ) {
			$recipients = $telephoneNumber; 
			$order_data['event_name'] = 'order_refund';
			$body=json_encode($order_data);
			$result = $buybot->sendWhatsapp($body);
			file_put_contents(ABSPATH. "OrderresponseResult.txt", $body);
		}

		if($order->status === 'completed' && $options['buybot_order_status_completed']=='on' ) {
			$recipients = $telephoneNumber; 
			$order_data['event_name'] = 'order_completed';
			$body=json_encode($order_data);
			$result = $buybot->sendWhatsapp($body);
			file_put_contents(ABSPATH. "OrderresponseResult.txt", $body);
		}

		if($order->status === 'cancelled' && $options['buybot_order_status_cancelled']=='on') {
			$recipients = $telephoneNumber; 
			$order_data['event_name'] = 'order_cancelled';
			$body=json_encode($order_data);
			$result = $buybot->sendWhatsapp($body);
			file_put_contents(ABSPATH. "OrderresponseResult.txt", $body);
		}
		if($order->status === 'processing' && $options['buybot_order_status_processing']=='on'){
			$recipients = $telephoneNumber; 
			$order_data['event_name'] = 'order_processing';
			$body=json_encode($order_data);
			$result = $buybot->sendWhatsapp($body);		
			file_put_contents(ABSPATH. "OrderresponseResult.txt", $body);
		}		
		if($order->status === 'on-hold' && $options['buybot_order_status_on-hold']=='on'){
			$recipients = $telephoneNumber; 
			$order_data['event_name'] = 'order_hold';
			$body=json_encode($order_data);
			// echo "<pre>".print_r($body,true)."</pre>";
			$result = $buybot->sendWhatsapp($body);
			file_put_contents(ABSPATH. "OrderresponseResult.txt", $body);
		}
		
	}	

	//Cart timestamp

	add_action( 'woocommerce_add_to_cart', 'set_cart_created_timestamp' );
	function set_cart_created_timestamp() {
		if ( ! WC()->session->get( 'cart_created_timestamp' ) ) {
			WC()->session->set( 'cart_created_timestamp', time() );
		}
	}

		//Abandoned Cart
		add_action( 'woocommerce_cart_actions', 'capture_abandoned_cart_details' );
		add_action( 'woocommerce_before_checkout_form', 'capture_abandoned_cart_details' );

	function capture_abandoned_cart_details() {

		$options = get_option( 'buybot_notifications_option_name' );

		$buybot = new buybot(false, false, $options['api_key']);

		//Check if the user has completed the checkout process
		if ( is_checkout() ) {
		 	//User is on the checkout page, no need to capture abandoned cart details
			return;
		}

		// Get the cart items
		$cart_items = WC()->cart->get_cart();

		// Get the timestamp of the cart(added)
		$cart_timestamp = WC()->session->get( 'cart_created_timestamp' );

		// Convert the timestamp to human-readable format
		$cart_timestamp_formatted = date( 'Y-m-d H:i:s', $cart_timestamp );

		// Get the access key
		$access_key = $options['api_key'];

		$log_data = array(
		'access_key' => $access_key,
		'cart_created_timestamp' => $cart_timestamp_formatted,
		'cart_items' => array(),
	);
		

		// Loop through each cart item to get its details
		foreach ( $cart_items as $cart_item_key => $cart_item ) {
			$product_name = $cart_item['data']->get_name();
			$item_url = $cart_item['data']->get_permalink();
			$item_quantity = $cart_item['quantity'];
			$item_price = $cart_item['data']->get_price();
			$item_images = array();
			// Get all image sources for the item
			$product_images = $cart_item['data']->get_gallery_image_ids();
			if ( $product_images ) {
				foreach ( $product_images as $image_id ) {
					$image_src = wp_get_attachment_image_src( $image_id, 'full' )[0];
					array_push( $item_images, $image_src );
				}
			} else {
				$image_id = $cart_item['data']->get_image_id();
				$image_src = wp_get_attachment_image_src( $image_id, 'full' )[0];
				array_push( $item_images, $image_src );
			}

			// Log the abandoned cart item details to a file or database table
			$email = WC()->customer->get_billing_email();
			$name = WC()->customer->get_billing_first_name() . ' ' . WC()->customer->get_billing_last_name();
			$phone = WC()->customer->get_billing_phone();
			$log_data['cart_items'][] = array(
				//'access_key' => $options['api_key'],
				//'cart_created_timestamp' => $cart_timestamp_formatted,
				'email' => $email,
				'name' => $name,
				'phone' => $phone,
				'product_name' => $product_name,
				'item_url' => $item_url,
				'item_images' => $item_images,
				'item_quantity' => $item_quantity,
				'item_price' => $item_price,
				//'order_key' => $order_key, // Add the order key to the log data
			);
			
	}
	$request_args = array(
		'method' => 'POST',
		'body' => json_encode( $log_data ),
		'headers' => array(
			'Content-Type' => 'application/json',
		),
	);

	$response = wp_remote_post( 'https://api.greenreceipt.in/webhook/wordpress/CartItem', $request_args );

	if ( is_wp_error( $response ) ) {
		$error_message = $response->get_error_message();
		error_log( "Error sending abandoned cart details: $error_message" );
	} else {
		$response_code = wp_remote_retrieve_response_code( $response );
		if ( $response_code === 200 ) {
			error_log( 'Abandoned cart details sent successfully.' );
		} else {
			error_log( 'Error sending abandoned cart details: ' . wp_remote_retrieve_response_message( $response ) );
		}
	}
}

}
		
	



?>