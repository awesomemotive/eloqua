<?php

namespace AwesomeMotive\Eloqua\Service;

use AwesomeMotive\Eloqua\Eloqua;

abstract class AbstractService {

	/**
	 * @var GetEloqua
	 */
	protected $client;

	public function __construct( Eloqua $client ) {

		$this->client = $client;

	}

}