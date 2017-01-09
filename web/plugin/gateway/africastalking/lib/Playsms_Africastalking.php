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
class Playsms_Africastalking extends AfricasTalkingGateway {

	public function __construct($_apiKey = '', $_username = '', $baseUrl = '') {
		if ($_apiKey = trim($_apiKey)) {
			$this->setAPIKey($_apiKey);
		}

		if ($_username = trim($_username)) {
			$this->setUsername($_username);
		}		
		
		$this->setBaseUrl($baseUrl);
	}

	public function setAPIKey($_apiKey) {
		if ($_apiKey = trim($_apiKey)) {
			$this->_apiKey = $_apiKey;
		} else {
			throw new Exception("APIKey must be defined", 1);
		}
	}

	public function setUsername($_username) {
		if ($_username = trim($_username)) {
			$this->_username = $_username;
		} else {
			throw new Exception("Username must be defined", 1);
		}
	}

	public function setBaseUrl($baseUrl) {
		if ($baseUrl = trim($baseUrl)) {
			$this->baseUrl = $baseUrl;
		} else {
			$this->baseUrl = 'https://api.africastalking.com/version1/messaging';
		}
	}
}
