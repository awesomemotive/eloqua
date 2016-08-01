<?php

namespace AwesomeMotive\Eloqua;

use AwesomeMotive\Eloqua\Exception\ServiceNotFoundException;
use AwesomeMotive\Eloqua\Exception\ValidationExceptionHandler;
use AwesomeMotive\Eloqua\OAuth2\Provider;
use AwesomeMotive\Eloqua\Service\CampaignService;
use AwesomeMotive\Eloqua\Service\SubscriberService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use League\OAuth2\Client\Token\AccessToken;

class Eloqua {

	protected $baseUrl = 'https://api.getEloqua.com/v2/';

	/**
	 * @var string
	 */
	protected $clientId;

	/**
	 * @var string
	 */
	protected $clientSecret;

	/**
	 * @var string
	 */
	protected $redirectUri;

	/**
	 * @var OAuth2\Provider
	 */
	protected $authProvider;

	/**
	 * @var \GuzzleHttp\Client
	 */
	protected $httpClient;

	/**
	 * @var Token
	 */
	protected $token;

	/**
	 * @var array
	 */
	protected $apis = array();

	public function __construct( $config = array() ) {

		if ( isset( $config['clientId'] ) ) {
			$this->clientId = $config['clientId'];
		}

		if ( isset( $config['clientSecret'] ) ) {
			$this->clientSecret = $config['clientSecret'];
		}

		if ( isset( $config['redirectUri'] ) ) {
			$this->redirectUri = $config['redirectUri'];
		}

	}

	/**
	 * @return string
	 */
	public function get_clientId() {
		return $this->clientId;

	}

	/**
	 * @param string $clientId
	 */
	public function set_clientId( $clientId ) {
		$this->clientId = $clientId;

	}

	/**
	 * @return string
	 */
	public function get_clientSecret() {
		return $this->clientSecret;

	}

	/**
	 * @param string $clientSecret
	 */
	public function set_clientSecret( $clientSecret ) {
		$this->clientSecret = $clientSecret;

	}

	/**
	 * @return string
	 */
	public function get_redirectUri() {
		return $this->redirectUri;

	}

	/**
	 * @param string $redirectUri
	 */
	public function set_redirectUri( $redirectUri ) {
		$this->redirectUri = $redirectUri;

	}

	/**
	 * @return OAuth2\Provider
	 */
	public function get_authProvider() {

		if ( ! $this->authProvider ) {
			$config             = array(
				'clientId'     => $this->clientId,
				'clientSecret' => $this->clientSecret,
				'redirectUri'  => $this->redirectUri,
			);
			$this->authProvider = new Provider( $config );
		}

		return $this->authProvider;

	}

	/**
	 * @return \GuzzleHttp\Client
	 */
	public function get_httpClient() {
		if ( ! $this->httpClient ) {
			$this->httpClient = new Client( array(
				'base_uri' => $this->baseUrl,
				'headers'  => array(
					'Authorization' => 'Bearer ' . $this->token->get_accessToken(),
				),
			) );
		}

		return $this->httpClient;

	}

	/**
	 * @param Token $token
	 */
	public function set_token( Token $token ) {

		$this->token = $token;

	}

	/**
	 * @return SubscriberService
	 */
	public function subscribers() {

		return $this->get_api( 'SubscriberService' );

	}

	/**
	 * @return CampaignService
	 */
	public function campaigns() {

		return $this->get_api( 'CampaignService' );

	}

	public function get_api( $class ) {
		$fq_class = '\\AwesomeMotive\\Eloqua\\Service\\' . $class;

		if ( ! class_exists( $fq_class ) ) {
			throw new ServiceNotFoundException( 'Service: ' . $class . ' could not be found' );
		}

		if ( ! array_key_exists( $fq_class, $this->apis ) ) {
			$this->apis[ $fq_class ] = new $fq_class( $this );
		}

		return $this->apis[ $fq_class ];
	}

	public function request( $path = '', $method = 'get', $data = array() ) {

		$path = $this->accountId . '/' . $path;

		$options = array();

		switch ( $method ) {
			case 'get' :
				if ( ! empty( $data ) ) {
					$query = array();
					foreach ( $data as $key => $value ) {
						$query[ $key ] = $value;
					}
					$options['query'] = $query;
				}
				break;
			case 'post' :
				if ( ! empty( $data ) ) {
					$json = array();
					foreach ( $data as $key => $value ) {
						$json[ $key ] = $value;
					}
					$options['json'] = $json;
				}
				break;
		}

		try {
			/** @var \GuzzleHttp\Psr7\Response $response **/
			$response = $this->get_httpClient()->{$method}( $path, $options );
			return json_decode( $response->getBody() );
		} catch ( RequestException $e ) {
			if ( $e->hasResponse() && $e->getResponse()->getStatusCode() === 422 ) {
				$body = $e->getResponse()->getBody()->getContents();
				$handler = new ValidationExceptionHandler( $body );
				$handler->handle();
			}
		} catch ( \Exception $e ) {
			return $e->getMessage();
		}

		return false;

	}


}