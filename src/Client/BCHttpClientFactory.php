<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Client;

use GuzzleHttp\Client;

final class BCHttpClientFactory
{

	public function create(Options $options): BCHttpClient
	{
		$http = new Client();

		return new BCHttpClient($http, $options);
	}

}
