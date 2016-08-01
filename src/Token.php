<?php

namespace AwesomeMotive\Eloqua;

use League\OAuth2\Client\Token\AccessToken;

class Token extends AccessToken {

	/**
	 * @return string
	 */
	public function get_accessToken() {

		return $this->accessToken;

	}

	/**
	 * @return string
	 */
	public function get_refreshToken() {

		return $this->refreshToken;

	}

	/**
	 * @return int
	 */
	public function get_expires() {

		return $this->expires;

	}

}