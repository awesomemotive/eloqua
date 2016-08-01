<?php

use \Codeception\Util\Stub;
use \AspectMock\Test as test;

class DripTest extends \Codeception\TestCase\Test {
	/**
	 * @var \UnitTester
	 */
	protected $tester;

	protected $accountId = '1234567890';

	protected $config = array(
		'clientId'     => '0987654321',
		'clientSecret' => '000000000000000000',
		'redirectUri'  => 'https://google.com',
	);

	/**
	 * @var AwesomeMotive\Drip\Drip
	 */
	protected $drip;

	protected function _before() {

		$this->drip = new \AwesomeMotive\Drip\Drip( $this->accountId, $this->config );

	}

	public function testDripConstructor() {

		$this->assertSame( $this->accountId, $this->drip->get_accountId() );
		$this->assertSame( $this->config['clientId'], $this->drip->get_clientId() );
		$this->assertSame( $this->config['clientSecret'], $this->drip->get_clientSecret() );
		$this->assertSame( $this->config['redirectUri'], $this->drip->get_redirectUri() );

	}

	public function testSetters() {

		$this->drip->set_accountId( 'abcd' );
		$this->assertSame( 'abcd', $this->drip->get_accountId() );

		$this->drip->set_clientId( 'dcba' );
		$this->assertSame( 'dcba', $this->drip->get_clientId() );

		$this->drip->set_clientSecret( '1234' );
		$this->assertSame( '1234', $this->drip->get_clientSecret() );

		$this->drip->set_redirectUri( 'https://facebook.com' );
		$this->assertSame( 'https://facebook.com', $this->drip->get_redirectUri() );

	}

	public function testGetOAuthProvider() {

		$provider = $this->drip->get_authProvider();

		$this->assertInstanceOf( "\\AwesomeMotive\\Drip\\OAuth2\\Provider", $provider );

	}

	public function testGetHttpClient() {

		/** @var \AwesomeMotive\Drip\Token $token */
		$token = Stub::make( "\\AwesomeMotive\\Drip\\Token", [ 'get_accessToken' => '1234' ] );
		$this->drip->set_token( $token );

		$client = $this->drip->get_httpClient();

		$this->assertInstanceOf( "\\GuzzleHttp\\Client", $client );

	}

	public function testFailsWhenRequestingNonexistentService() {

		$this->setExpectedException(
			"\\AwesomeMotive\\Drip\\Exception\\ServiceNotFoundException",
			"Service: NonexistentService could not be found"
		);
		$service = $this->drip->get_api( 'NonexistentService' );

	}

	public function testCanGetSubscriberService() {

		$subscribers = $this->drip->subscribers();

		$this->assertInstanceOf( "\\AwesomeMotive\\Drip\\Service\\AbstractService", $subscribers );
		$this->assertInstanceOf( "\\AwesomeMotive\\Drip\\Service\\SubscriberService", $subscribers );

	}

	public function testCanGetCampaignService() {

		$campaigns = $this->drip->campaigns();

		$this->assertInstanceOf( "\\AwesomeMotive\\Drip\\Service\\AbstractService", $campaigns );
		$this->assertInstanceOf( "\\AwesomeMotive\\Drip\\Service\\CampaignService", $campaigns );

	}

//	public function testCanPerformRawRequest() {
//
//		$testResponse = new \GuzzleHttp\Psr7\Response( 200, [ ], "{'success': true }" );
//		$mock = new \GuzzleHttp\Handler\MockHandler([
//			$testResponse
//		]);
//
//		$handler = \GuzzleHttp\HandlerStack::create( $mock );
//
//		$client = test::double( new \GuzzleHttp\Client( ['handler' => $handler] ), ['get' => $testResponse] );
//
//		/** @var \AwesomeMotive\Drip\Token $token */
//		$token = Stub::make( "\\AwesomeMotive\\Drip\\Token", [ 'get_accessToken' => '1234' ] );
//		$this->drip->set_token( $token );
//
//		$response = $this->drip->request( 'test' );
//		var_dump($response);
//		$this->assertNotEmpty($response);
//
//	}
}