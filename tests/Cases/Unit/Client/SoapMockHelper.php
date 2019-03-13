<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Tests\Cases\Unit\Client;

use Mockery;
use Mockery\MockInterface;
use SoapClient;

final class SoapMockHelper
{

	/**
	 * @return SoapClient|MockInterface
	 */
	public static function createSoapMock(string $method, string $file)
	{
		$xml = simplexml_load_file(sprintf('%s/data/%s', __DIR__, $file));
		$std = json_decode(json_encode($xml));

		return Mockery::mock(SoapClient::class)->shouldReceive($method)->andReturn($std)->getMock();
	}

}
