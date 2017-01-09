<?php
defined('_SECURE_') or die('Forbidden');

if (!class_exists('Africastalking')) {
	include $core_config['apps_path']['plug'] . '/gateway/africastalking/lib/Africastalking.php';
}

if (!class_exists('Playsms_Africastalking')) {
	include $core_config['apps_path']['plug'] . '/gateway/africastalking/lib/Playsms_Africastalking.php';
}

$db_query = "SELECT * FROM " . _DB_PREF_ . "_gatewayAfricastalking_config";
$db_result = dba_query($db_query);

if ($db_row = dba_fetch_array($db_result)) {
	$plugin_config['africastalking']['name'] = 'africastalking';
	$plugin_config['africastalking']['url'] = ($db_row['cfg_url'] ? $db_row['cfg_url'] : 'https://api.africastalking.com/version1/messaging');
	$plugin_config['africastalking']['api_key'] = $db_row['cfg_api_key'];
	$plugin_config['africastalking']['api_secret'] = $db_row['cfg_api_secret'];
	$plugin_config['africastalking']['module_sender'] = $db_row['cfg_module_sender'];
	$plugin_config['africastalking']['datetime_timezone'] = $db_row['cfg_datetime_timezone'];
}

// smsc configuration
$plugin_config['africastalking']['_smsc_config_'] = array(
	'api_key' => _('API key'),
	'api_secret' => _('API secret'),
	'module_sender' => _('Module sender ID'),
	'datetime_timezone' => _('Module timezone') 
);