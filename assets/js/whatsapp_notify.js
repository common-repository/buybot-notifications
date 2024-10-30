/*jQuery(document).ready(function(){
    jQuery('.shortcode').live('click', function(event) {        
         jQuery('.ul-table').toggle('show');
    });

    var sms_notifications = "sms_order_";
    /*order status*/
    /*var sms_order_complete = jQuery("#"+ sms_notifications +"complete");
    var sms_order_hold = jQuery("#"+ sms_notifications +"hold");
    var sms_order_processing = jQuery("#"+ sms_notifications +"processing");
    var sms_order_pending_payment = jQuery("#"+ sms_notifications +"pending_payment");
    var sms_order_canceled = jQuery("#"+ sms_notifications +"canceled");
    var sms_order_refunded = jQuery("#"+ sms_notifications +"refunded");
    var sms_order_failed = jQuery("#"+ sms_notifications +"failed");
    var coupon_gen = jQuery("#coupon_gen");
	/*order status*/

	//order complete
	/*var sms_order_complete_label = 'sms_order_complete_manual_message_sms_text_cntr';
	sms_order_complete.after('<span class="smscounter " id="'+ sms_order_complete_label +'"></span>');
	
	SMSNotificationsTextLimit( sms_order_complete, sms_order_complete_label );
	sms_order_complete.keyup(function() { 
		SMSNotificationsTextLimit( sms_order_complete, sms_order_complete_label );
	});

	// order hold
	var sms_order_hold_label = 'sms_order_hold_manual_message_sms_text_cntr';
	sms_order_hold.after('<span class="smscounter ppp" id="'+ sms_order_hold_label +'"></span>');
	
	SMSNotificationsTextLimit( sms_order_hold, sms_order_hold_label );
	sms_order_hold.keyup(function() { 
		SMSNotificationsTextLimit( sms_order_hold, sms_order_hold_label );
	});

	//order processing
	var sms_order_processing_label = 'sms_order_processing_manual_message_sms_text_cntr';
	sms_order_processing.after('<span class="smscounter" id="'+ sms_order_processing_label +'"></span>');
	
	SMSNotificationsTextLimit( sms_order_processing, sms_order_processing_label );
	sms_order_processing.keyup(function() { 
		SMSNotificationsTextLimit( sms_order_processing, sms_order_processing_label );
	});

	// order pending payment
	var sms_order_pending_payment_label = 'sms_order_pending_payment_manual_message_sms_text_cntr';
	sms_order_pending_payment.after('<span class="smscounter" id="'+ sms_order_pending_payment_label +'"></span>');
	
	SMSNotificationsTextLimit( sms_order_pending_payment, sms_order_pending_payment_label );
	sms_order_pending_payment.keyup(function() { 
		SMSNotificationsTextLimit( sms_order_pending_payment, sms_order_pending_payment_label );
	});

	//order canceled
	var sms_order_canceled_label = 'sms_order_canceled_manual_message_sms_text_cntr';
	sms_order_canceled.after('<span class="smscounter" id="'+ sms_order_canceled_label +'"></span>');
	
	SMSNotificationsTextLimit( sms_order_canceled, sms_order_canceled_label );
	sms_order_canceled.keyup(function() { 
		SMSNotificationsTextLimit( sms_order_canceled, sms_order_canceled_label );
	});

	//order refunded
	var sms_order_refunded_label = 'sms_order_refunded_manual_message_sms_text_cntr';
	sms_order_refunded.after('<span class="smscounter" id="'+ sms_order_refunded_label +'"></span>');
	
	SMSNotificationsTextLimit( sms_order_refunded, sms_order_refunded_label );
	sms_order_refunded.keyup(function() { 
		SMSNotificationsTextLimit( sms_order_refunded, sms_order_refunded_label );
	});

	//order failed
	var sms_order_failed_label = 'sms_order_failed_manual_message_sms_text_cntr';
	sms_order_failed.after('<span class="smscounter" id="'+ sms_order_failed_label +'"></span>');
	
	SMSNotificationsTextLimit( sms_order_failed, sms_order_failed_label );
	sms_order_failed.keyup(function() { 
		SMSNotificationsTextLimit( sms_order_failed, sms_order_failed_label );
	});
	
	var coupon_gen_label = 'coupon_gen_manual_message_sms_text_cntr';
	coupon_gen.after('<span class="smscounter" id="'+ coupon_gen_label +'"></span>');
	
	SMSNotificationsTextLimit( coupon_gen, coupon_gen_label );
	coupon_gen.keyup(function() { 
		SMSNotificationsTextLimit( coupon_gen, coupon_gen_label );
	});

    function SMSNotificationsTextLimit(limitField, limitCount)
	{
		var limitNum = 160;
		if(limitField && document.getElementById(limitCount))
		{
			if (limitField.val().length > limitNum) 
			{
				limitField.val(limitField.val().substring(0, limitNum));
			}
			else
			{
				document.getElementById(limitCount).innerHTML = limitNum - limitField.val().length;
			}
		}
	}
});*/