<?php
error_reporting(0);

if (!$called_from_hook_call) {
	chdir("../../../");
	
	// ignore CSRF
	$core_config['init']['ignore_csrf'] = TRUE;
	
	include "init.php";
	include $core_config['apps_path']['libs'] . "/function.php";
	chdir("plugin/gateway/africastalking/");
	$requests = $_REQUEST;
}

$cb_from = $_REQUEST['from'];
$cb_to = $_REQUEST['to'];
$cb_timestamp = $_REQUEST['date'];
$cb_text = $_REQUEST['text'];
$cb_apimsgid = $_REQUEST['id'];
$cb_status = $_REQUEST['status'];
$cb_smsc = (trim($_REQUEST['smsc']) ? trim($_REQUEST['smsc']) : 'africastalking');

if ($cb_timestamp && $cb_from && $cb_text) {
	$cb_datetime = date($datetime_format, $cb_timestamp);
	$sms_datetime = trim($cb_datetime);
	$sms_sender = trim($cb_from);
	$message = trim(htmlspecialchars_decode(urldecode($cb_text)));
	$sms_receiver = trim($cb_to);
	
	_log("sender:" . $sms_sender . " receiver:" . $sms_receiver . " dt:" . $sms_datetime . " msg:[" . $message . "]", 3, "africastalking incoming");
	
	// collected:
	// $sms_datetime, $sms_sender, $message, $sms_receiver
	$sms_sender = addslashes($sms_sender);
	$message = addslashes($message);
	recvsms($sms_datetime, $sms_sender, $message, $sms_receiver, $cb_smsc);
}

if ($cb_status && $cb_apimsgid) {
	$db_query = "
		SELECT " . _DB_PREF_ . "_tblSMSOutgoing.smslog_id AS smslog_id," . _DB_PREF_ . "_tblSMSOutgoing.uid AS uid
		FROM " . _DB_PREF_ . "_tblSMSOutgoing," . _DB_PREF_ . "_gatewayAfricastalking_apidata
		WHERE
			" . _DB_PREF_ . "_tblSMSOutgoing.smslog_id=" . _DB_PREF_ . "_gatewayAfricastalking_apidata.smslog_id AND
			" . _DB_PREF_ . "_gatewayAfricastalking_apidata.apimsgid='$cb_apimsgid'";
	$db_result = dba_query($db_query);
	$db_row = dba_fetch_array($db_result);
	$uid = $db_row['uid'];
	$smslog_id = $db_row['smslog_id'];
	if ($uid && $smslog_id) {
		$c_sms_status = 0;
		switch ($cb_status) {
			case "Submitted" :
			case "Buffered" :
				$c_sms_status = 0;
				break; // pending
			case "Sent" :
				$c_sms_status = 1;
				break; // sent
			case "Rejected" :
			case "Failed" :
				$c_sms_status = 2;
				break; // failed
			case "Success" :
				$c_sms_status = 3;
				break; // delivered
		}
		$c_sms_credit = ceil($cb_charge);
		// pending
		$p_status = 0;
		if ($c_sms_status) {
			$p_status = $c_sms_status;
		}
		dlr($smslog_id, $uid, $p_status);
	}
}

