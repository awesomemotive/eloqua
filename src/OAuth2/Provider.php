<?php

namespace AwesomeMotive\Eloqua\OAuth2;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;

class Provider extends AbstractProvider {

	public $domain = 'https://login.eloqua.com';

	public $scopes = ['full'];

	/**
	 * Get the URL that this provider uses to begin authorization.
	 *
	 * @return string
	 */
	public function urlAuthorize() {

		return $this->domain . '/auth/oauth2/authorize';

	}

	/**
	 * Get the URL that this provider uses to request an access token.
	 *
	 * @return string
	 */
	public function urlAccessToken() {

		return $this->domain . '/auth/oauth2/token';

	}

}