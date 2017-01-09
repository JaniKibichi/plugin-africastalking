<?php

/**
 * This file is part of playSMS.
 *
 * playSMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * playSMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with playSMS. If not, see <http://www.gnu.org/licenses/>.
 */
defined('_SECURE_') or die('Forbidden');

if (!auth_isadmin()) {
	auth_block();
}

include $core_config['apps_path']['plug'] . "/gateway/africastalking/config.php";

$callback_url = $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/plugin/gateway/africastalking/callback.php";
$callback_url = str_replace("//", "/", $callback_url);
$callback_url = "http://" . $callback_url;

switch (_OP_) {
	case "manage" :
		$tpl = array(
			'name' => 'africastalking',
			'vars' => array(
				'DIALOG_DISPLAY' => _dialog(),
				'Manage africastalking' => _('Manage africastalking'),
				'Gateway name' => _('Gateway name'),
				'Africastalking URL' => _('Africastalking URL'),
				'API key' => _('API key'),
				'API secret' => _('API secret'),
				'Module sender ID' => _('Module sender ID'),
				'Module timezone' => _('Module timezone'),
				'Save' => _('Save'),
				'Notes' => _('Notes'),
				'HINT_JSON_FORMAT' => _hint(_('Use JSON format URL')),
				'HINT_FILL_SECRET' => _hint(_('Fill to change the API secret')),
				'HINT_GLOBAL_SENDER' => _hint(_('Max. 16 numeric or 11 alphanumeric char. empty to disable')),
				'HINT_TIMEZONE' => _hint(_('Eg: +0700 for Jakarta/Bangkok timezone')),
				'CALLBACK_URL_IS' => _('Your callback URL is'),
				'CALLBACK_URL_ACCESSIBLE' => _('Your callback URL should be accessible from Africastalking'),
				'AFRICASTALKING_PUSH_DLR' => _('Africastalking will push DLR and incoming SMS to your callback URL'),
				'AFRICASTALKING_IS_BULK' => _('Africastalking is a bulk SMS provider'),
				'AFRICASTALKING_FREE_CREDIT' => _('free credits are available for testing purposes'),
				'BUTTON_BACK' => _back('index.php?app=main&inc=core_gateway&op=gateway_list'),
				'status_active' => $status_active,
				'africastalking_param_url' => $plugin_config['africastalking']['url'],
				'africastalking_param_api_key' => $plugin_config['africastalking']['api_key'],
				'africastalking_param_module_sender' => $plugin_config['africastalking']['module_sender'],
				'africastalking_param_datetime_timezone' => $plugin_config['africastalking']['datetime_timezone'],
				'callback_url' => $callback_url 
			) 
		);
		_p(tpl_apply($tpl));
		break;
	case "manage_save" :
		$up_url = $_POST['up_url'];
		$up_api_key = $_POST['up_api_key'];
		$up_api_secret = $_POST['up_api_secret'];
		$up_module_sender = $_POST['up_module_sender'];
		$up_global_timezone = $_POST['up_global_timezone'];
		$_SESSION['dialog']['info'][] = _('No changes have been made');
		if ($up_url && $up_api_key) {
			if ($up_api_secret) {
				$api_secret_change = "cfg_api_secret='$up_api_secret',";
			}
			$db_query = "
				UPDATE " . _DB_PREF_ . "_gatewayAfricastalking_config
				SET c_timestamp='" . time() . "',
				cfg_url='$up_url',
				cfg_api_key='$up_api_key',
				" . $api_secret_change . "
				cfg_module_sender='$up_module_sender',
				cfg_datetime_timezone='$up_global_timezone'";
			if (@dba_affected_rows($db_query)) {
				$_SESSION['dialog']['info'][] = _('Gateway module configurations has been saved');
			}
		}
		header("Location: " . _u('index.php?app=main&inc=gateway_africastalking&op=manage'));
		exit();
		break;
}
