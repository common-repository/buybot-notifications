<?php
/**
	* Plugin Name: GreenReceipt–WhatsApp Commerce
	* Plugin URI: https://www.greenreceipt.in/
	* Description: This is a WooCommerce add-on. By Using this plugin admin can send WhatsApp notifications to the Buyers.
	* Version: 1.0.3
	* Author: Green Receipt
	* Author URI: https://www.greenreceipt.in/
	* Text Domain: https://www.greenreceipt.in/
	* Requires at least: 5.8 or higher
	* Requires PHP: 5.6 or higher
 */
 
if ( ! defined('ABSPATH')) exit;  // if direct access

define("BUYbot_NOTIFICATIONS_TEXT_DOMAIN","BUYbot-NOTIFICATIONS");

$plugin_dir_name = dirname(plugin_basename( __FILE__ )); 

define("BUYBOT_NOTIFICATIONS_GATEEWAY_DIR", WP_PLUGIN_DIR."/".$plugin_dir_name);
define("BUYBOT_NOTIFICATIONS_GATEEWAY_URL", WP_PLUGIN_URL."/".$plugin_dir_name);


global $wc_settings_whatsapp, $whatsappid, $whatsapplabel, $whatsappforwooplnm, $wpdb,$woocommerce,$product;

class BUYbotWANotifications{
	/**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */

	public function __construct(){
		add_action('admin_menu', array($this, 'buybot_notifications_menu'));
		add_action( 'admin_init', array($this, 'buybot_notifications_page_init'));
		add_action( 'admin_enqueue_scripts', array( $this, 'buybot_notifications_enqueue_script'));

	}
	
	public function buybot_notifications_menu()
    {
    	add_menu_page('BUYbot Notification', 'BUYbot Notification', 'manage_options',  'buybot_notifications', array($this, 'buybot_notifications_page'), 'dashicons-email-alt' );
     
	}

	public function buybot_notifications_enqueue_script( ) {
    		wp_enqueue_style( 'buybot_notifications', BUYBOT_NOTIFICATIONS_GATEEWAY_URL. '/assets/css/buybot_style.css');
    		wp_enqueue_script( 'buybot_notifications',BUYBOT_NOTIFICATIONS_GATEEWAY_URL. '/assets/js/buybot_notify.js' );
            wp_enqueue_style( 'buybot_notifications_bootstrap_min', BUYBOT_NOTIFICATIONS_GATEEWAY_URL. '/assets/css/bootstrap.min.css');
            wp_enqueue_script( 'buybot_notifications_twbsPagination',BUYBOT_NOTIFICATIONS_GATEEWAY_URL. '/assets/js/jquery.twbsPagination.min.js' );
	}

	public function buybot_notifications_page_init()
    {        
		
		register_setting(
            'buybot_notifications_option_group', // Option group 
            'buybot_notifications_option_name', // Option name
            array( $this, 'buybot_notifications_sanitize' ) // Sanitize
        );

        add_settings_section(
            'buybot_api_setting', // ID
            'API Credentials', // Title
            '',
            //array( $this, 'print_section_info' ), // Callback
            'buybot_notifications' // Page
        );  

        add_settings_field(
            'api_key', // ID
            'Api Key', // Title 
            array( $this, 'api_key_callback' ), // Callback
            'buybot_notifications', // Page
            'buybot_api_setting' // Section           
        );      

       
        add_settings_section(
            'buybot_order_notification_setting', // ID
            'Order Notification Settings', // Title
            '',
            'buybot_notifications' // Page sanitize_textarea_field()
        );
 
        add_settings_field(
            'buybot_admin_notification',
            'Enable / Disable Admin Notification',
            //'If checked then enable admin whatsapp notification for new order',
            array( $this, 'buybot_admin_notification_callback' ),
            'buybot_notifications',
            "buybot_order_admin_notification_setting"
        );

        add_settings_field(
            'product_review_notification',
            'Enable / Disable Product Review Notification',
            //'If checked then enable admin whatsapp notification for new order',
            array( $this, 'product_review_notification_callback' ),
            'buybot_notifications',
            "buybot_order_admin_notification_setting"
        );

        add_settings_field(
            'adminnumber', 
            'Primary Admin Number. You can specify multiple numbers seperated by comma', 
            array( $this, 'adminnumber_callback' ), 
            'buybot_notifications', 
            'buybot_order_admin_notification_setting'
        );

        add_settings_field(
            'buybot_order_status_notification',
            'Select status to send notification',
            array( $this, 'buybot_order_status_notification_callback' ),
            'buybot_notifications',
            "buybot_order_notification_setting"
        );

    }

    public function buybot_notifications_sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['api_key'] ) )
            $new_input['api_key'] = sanitize_text_field( $input['api_key'] );

        if( isset( $input['senderid'] ) )
            $new_input['senderid'] = sanitize_text_field( $input['senderid'] );

        if( isset( $input['adminnumber'] ) )
            $new_input['adminnumber'] = sanitize_text_field( $input['adminnumber'] );

        if( isset( $input['buybot_order_canceled'] ) )
            $new_input['buybot_order_canceled'] = sanitize_textarea_field( $input['buybot_order_canceled'] );	

        if( isset( $input['buybot_order_refunded'] ) )
            $new_input['buybot_order_refunded'] = sanitize_textarea_field( $input['buybot_order_refunded'] );

        if( isset( $input['buybot_order_failed'] ) )
            $new_input['buybot_order_failed'] = sanitize_textarea_field( $input['buybot_order_failed'] );
        
        if( isset( $input['buybot_order_hold'] ) )
            $new_input['buybot_order_hold'] = sanitize_textarea_field( $input['buybot_order_hold'] );
        
        if( isset( $input['buybot_order_complete'] ) )
            $new_input['buybot_order_complete'] = sanitize_textarea_field( $input['buybot_order_complete'] );

        if( isset( $input['buybot_order_processing'] ) )
            $new_input['buybot_order_processing'] = sanitize_textarea_field( $input['buybot_order_processing'] );

        if( isset( $input['buybot_order_pending_payment'] ) )
            $new_input['buybot_order_pending_payment'] = sanitize_textarea_field( $input['buybot_order_pending_payment'] );

        if( isset( $input['reg_status'] ) )
            $new_input['reg_status'] = sanitize_text_field( $input['reg_status'] );

        if( isset( $input['profile_update'] ) )
            $new_input['profile_update'] = sanitize_text_field( $input['profile_update'] );
        
        if( isset( $input['forget_pass'] ) )
            $new_input['forget_pass'] = sanitize_text_field( $input['forget_pass'] );

        if( isset( $input['coupon_gen'] ) )
            $new_input['coupon_gen'] = sanitize_textarea_field( $input['coupon_gen'] );
        
        if(isset( $input['buybot_admin_notification']))
        	$new_input['buybot_admin_notification'] = $input['buybot_admin_notification'];
        
        if(isset( $input['product_review_notification']))
        	$new_input['product_review_notification'] = $input['product_review_notification'];
        
		if(isset( $input['buybot_customer_status_login']))
        	$new_input['buybot_customer_status_login'] = $input['buybot_customer_status_login'];

if(isset( $input['buybot_customer_status_register']))
        	$new_input['buybot_customer_status_register'] = $input['buybot_customer_status_register'];


		if(isset( $input['buybot_order_status_pending_payment']))
        	$new_input['buybot_order_status_pending_payment'] = $input['buybot_order_status_pending_payment'];

        if(isset( $input['buybot_order_status_processing']))
        	$new_input['buybot_order_status_processing'] = $input['buybot_order_status_processing'];

        if(isset( $input['buybot_order_status_on-hold']))
        	$new_input['buybot_order_status_on-hold'] = $input['buybot_order_status_on-hold'];

        if(isset( $input['buybot_order_status_completed']))
        	$new_input['buybot_order_status_completed'] = $input['buybot_order_status_completed'];

        if(isset( $input['buybot_order_status_cancelled']))
        	$new_input['buybot_order_status_cancelled'] = $input['buybot_order_status_cancelled'];

        if(isset( $input['buybot_order_status_refunded']))
        	$new_input['buybot_order_status_refunded'] = $input['buybot_order_status_refunded'];

        if(isset( $input['buybot_order_status_failed']))
        	$new_input['buybot_order_status_failed'] = $input['buybot_order_status_failed'];

        return $new_input;
    }

    public function adminnumber_callback()
    { 
        printf(
            '<input type="text" id="adminnumber" name="buybot_notifications_option_name[adminnumber]" size="50" value="%s" />',
            isset( $this->options['adminnumber'] ) ? esc_attr( $this->options['adminnumber']) : ''
        );

    }

    public function buybot_admin_notification_callback(){
    	printf(
        '<input id="%1$s" name="buybot_notifications_option_name[buybot_admin_notification]" type="checkbox" %2$s /> ','buybot_admin_notification',checked( isset( $this->options['buybot_admin_notification'] ), true, false ));
    }
    //
    public function product_review_notification_callback(){
    	printf(
        '<input id="%1$s" name="buybot_notifications_option_name[product_review_notification]" type="checkbox" %2$s /> ','product_review_notification',checked( isset( $this->options['product_review_notification'] ), true, false ));
    }

    public function buybot_order_status_notification_callback(){
		printf(
        '<input id="%1$s" name="buybot_notifications_option_name[buybot_order_status_pending_payment]" type="checkbox" %2$s /> ','buybot_order_status_pending_payment',checked( isset( $this->options['buybot_order_status_pending_payment'] ), true, false ));
        printf('<label>Pending payment</label> <br/>');
        
        printf(
        '<input id="%1$s" name="buybot_notifications_option_name[buybot_order_status_processing]" type="checkbox" %2$s /> ','buybot_order_status_processing',checked( isset( $this->options['buybot_order_status_processing'] ), true, false ));
        printf('<label>Processing</label><br/>');

        printf(
        '<input id="%1$s" name="buybot_notifications_option_name[buybot_order_status_on-hold]" type="checkbox" %2$s /> ','buybot_order_status_on-hold',checked( isset( $this->options['buybot_order_status_on-hold'] ), true, false ));
        printf('<label>On hold</label><br/>');

        printf(
        '<input id="%1$s" name="buybot_notifications_option_name[buybot_order_status_completed]" type="checkbox" %2$s /> ','buybot_order_status_completed',checked( isset( $this->options['buybot_order_status_completed'] ), true, false ));
        printf('<label>Completed</label><br/>');

        printf(
        '<input id="%1$s" name="buybot_notifications_option_name[buybot_order_status_cancelled]" type="checkbox" %2$s /> ','buybot_order_status_cancelled',checked( isset( $this->options['buybot_order_status_cancelled'] ), true, false ));
        printf('<label>Cancelled</label><br/>');

        printf(
        '<input id="%1$s" name="buybot_notifications_option_name[buybot_order_status_refunded]" type="checkbox" %2$s /> ','buybot_order_status_refunded',checked( isset( $this->options['buybot_order_status_refunded'] ), true, false ));
        printf('<label>Refunded</label><br/>');

        printf(
        '<input id="%1$s" name="buybot_notifications_option_name[buybot_order_status_failed]" type="checkbox" %2$s /> ',
        'buybot_order_status_failed',checked( isset( $this->options['buybot_order_status_failed'] ), true, false ));
        printf('<label>Failed</label><br/>');

       
        printf(
            ' <input type="checkbox" name="cart_checkbox" value="cart_example_value" checked="checked" disabled="disabled" />',
            ); 
        printf('<label>Abandoned cart</label><br/>');

    }

    public function api_key_callback()
    {
        printf(
            '<input type="text" id="api_key" name="buybot_notifications_option_name[api_key]" size="50" value="%s" />',
            isset( $this->options['api_key'] ) ? esc_attr( $this->options['api_key']) : ''
        );
        printf('<span class="apikeys"><a href="https://www.bynow.co.in/WooComerceRegistration" target="_blank">Click Here</a> to create/manage your API Keys</span>');

    }
	
	public function buybot_notifications_page()
  	{
  		$reputewhatsappid = 'buybot_notifications';
  		$this->options = get_option( 'buybot_notifications_option_name' );
  		?>
  	<div class="wrap">
  		<h2>Green Receipt - WhatsApp Commerce</h2>
  		<form method="post" action="options.php">
  			<?php
  				settings_fields( 'buybot_notifications_option_group' );
                do_settings_sections( 'buybot_notifications' );
                submit_button();
  			?>
  		</form>
  	</div>
		<?php
  	}


    public function notice_result( $result, $message ) {

        if ( empty( $result ) ) {
            return;
        }

        if ( $result == 'error' ) {
            return '<div class="updated settings-error notice error is-dismissible"><p><strong>' . $message . '</strong></p><button class="notice-dismiss" type="button"><span class="screen-reader-text">' . __( 'Close', 'buybot-notifications' ) . '</span></button></div>';
        }

        if ( $result == 'update' ) {
            return '<div class="updated settings-update notice is-dismissible"><p><strong>' . $message . '</strong></p><button class="notice-dismiss" type="button"><span class="screen-reader-text">' . __( 'Close', 'buybot-notifications' ) . '</span></button></div>';
        }
    }


}
if( is_admin() )
$my_settings_page =  new BUYbotWANotifications();

require_once( BUYBOT_NOTIFICATIONS_GATEEWAY_DIR.'/core/order.class.php' );
require_once( BUYBOT_NOTIFICATIONS_GATEEWAY_DIR.'/core/buybot.class.php' );

function buybot_notifications_install(){
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
    $table_prefix = $wpdb->prefix;
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );	
	
	buybot_get_token();
}
register_activation_hook( __FILE__, 'buybot_notifications_install' ); 

/****Delete Plugin Remove Tables *****/
function buybot_notifications_uninstall()
{
	global $wpdb;
	delete_option("buybot_notifications_db_version");
}
register_uninstall_hook( __FILE__, 'buybot_notifications_uninstall' );

function buybot_get_token(){
	
	$url = "https://api.greenreceipt.in/webhook/wordpress/AddMerchant";
	$storename= get_bloginfo( 'name' );
	$storeurl= network_site_url( '/' );
	
	$body = json_encode(array("storename"=>$storename,"storeurl"=>$storeurl));
    $args = array(
        'method'      => 'POST',
        'timeout'     => 45,
        'sslverify'   => false,
        'headers'     => array(
            'Content-Type'  => 'application/json',
        ),
        'body'        => $body,
    );
    $request = wp_remote_post( $url, $args );
    if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
        error_log( print_r( $request, true ) );
    }
    $response = wp_remote_retrieve_body( $request );
	$options = get_option( 'buybot_notifications_option_name' );
	$values = array("api_key"=>str_replace("\"", "",$response),"buybot_order_status_pending_payment"=>"on","buybot_order_status_processing"=>"on","buybot_order_status_on-hold"=>"on","buybot_order_status_completed"=>"on","buybot_order_status_cancelled"=>"on","buybot_order_status_failed"=>"on");
	update_option( 'buybot_notifications_option_name',$values );
	
	
}