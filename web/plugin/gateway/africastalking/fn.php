<?php
defined('_SECURE_') or die('Forbidden');

// hook_sendsms
// called by main sms sender
// return true for success delivery
// $smsc		: smsc
// $sms_sender	: sender mobile number
// $sms_footer	: sender sms footer or sms sender ID
// $sms_to		: destination sms number
// $sms_msg		: sms message tobe delivered
// $uid			: sender User ID
// $gpid		: group phonebook id (optional)
// $smslog_id	: sms ID
// $sms_type	: send flash message when the value is "flash"
// $unicode		: send unicode character (16 bit)

function africastalking_hook_sendsms($smsc, $sms_sender,$sms_footer,$sms_to,$sms_msg,$uid='',$gpid=0,$smslog_id=0,$sms_type='text',$unicode=0) {
	// global $tmpl_param;   // global all variables needed, eg: varibles from config.php
	global $plugin_config;
	$ok = false;	

	_log("enter smsc:" . $smsc . " smslog_id:" . $smslog_id . " uid:" . $uid . " to:" . $sms_to, 3, "africastalking_hook_sendsms");

	// override plugin gateway configuration by smsc configuration
	$plugin_config = gateway_apply_smsc_config($smsc, $plugin_config);	
	
		$sms_sender = stripslashes($sms_sender);
		if ($plugin_config['africastalking']['module_sender']) {
			$sms_sender = $plugin_config['africastalking']['module_sender'];
		}
		
		$sms_footer = stripslashes($sms_footer);
		$sms_msg = stripslashes($sms_msg);
		
		if ($sms_footer) {
			$sms_msg = $sms_msg . $sms_footer;
		}

			$username   = $plugin_config['africastalking']['api_key'];
			$apikey     = $plugin_config['africastalking']['api_secret'];
			$recipients = $sms_to;
			$message    = $sms_msg;
			$from = $plugin_config['africastalking']['module_sender'];
			$options = array("enqueue" => 1);
			$bulkSMSMode = 1;

		$gateway    = new AfricasTalkingGateway($username, $apikey);
		try 
		{ 
		   $results = $gateway->sendMessage($recipients, $message, $from, $options, $bulkSMSMode);
		            
		  foreach($results as $result) {
		    // status is either "Success" or "error message"
		    echo " Number: " .$result->number;
		    echo " Status: " .$result->status;
		    echo " MessageId: " .$result->messageId;
		    echo " Cost: "   .$result->cost."\n";

		    _log("sendsms url:[" . $plugin_config['africastalking']['url'] . "] callback:[" . $plugin_config['africastalking']['callback_url'], "] smsc:[" . $smsc . "]", 3, "africastalking_hook_sendsms");

			if ($result->status) {
					$c_status = $result->status;
					$c_message_id = $result->messageId;	
					
					//log and send to db
					_log("sent smslog_id:" . $smslog_id . " message_id:" . $c_message_id . " status:" . $c_status . " smsc:[" . $smsc . "]", 2, "africastalking_hook_sendsms");				

					$db_query = "
						INSERT INTO " . _DB_PREF_ . "_gatewayAfricastalking (local_smslog_id,remote_smslog_id,status,error_text)
						VALUES ('$smslog_id','$c_message_id','$c_status','NULL')";
					$id = @dba_insert_id($db_query);

					if ($id && ($c_status == 'sent')) {
						$ok = true;
						$p_status = 0;
					} else {
						$p_status = 2;
					}
					dlr($smslog_id, $uid, $p_status);	
		    }else{
					// even when the response is not what we expected we still print it out for debug purposes
					$result = str_replace("\n", " ", $result);
					$result = str_replace("\r", " ", $result);
					_log("failed smslog_id:" . $smslog_id . " resp:" . $result . " smsc:[" . $smsc . "]", 2, "africastalking_hook_sendsms");
			}
		}
		catch ( AfricasTalkingGatewayException $e )
		{
		  echo "Encountered an error while sending: ".$e->getMessage();
		}
	// return true or false
	if (!$ok) {
		$p_status = 2;
		dlr($smslog_id, $uid, $p_status);
	}
	
	_log("sendsms end", 3, "africastalking_hook_sendsms");

	// return $ok;	
	return $ok;
}

// hook_getsmsinbox
// called by incoming sms processor
// no returns needed
function africastalking_hook_call($requests) {
	// please note that we must globalize these 2 variables
	global $core_config, $plugin_config;
	$called_from_hook_call = true;
	$access = $requests['access'];
	if ($access == 'callback') {
		$fn = $core_config['apps_path']['plug'] . '/gateway/africastalking/callback.php';
		_log("start load:" . $fn, 2, "africastalking call");
		include $fn;
		_log("end load callback", 2, "africastalking call");
	}
}