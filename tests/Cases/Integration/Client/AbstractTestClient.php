<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Tests\Cases\Integration\Client;

use AsisTeam\CSOBBC\Client\BCHttpClient;
use AsisTeam\CSOBBC\Client\BCHttpClientFactory;
use AsisTeam\CSOBBC\Client\BCSoapClient;
use AsisTeam\CSOBBC\Client\BCSoapClientFactory;
use AsisTeam\CSOBBC\Client\Options;
use Tester\Environment;
use Tester\TestCase;

abstract class AbstractTestClient extends TestCase
{

	private const TEST_ENV = true;

	/** @var BCSoapClient */
	protected $soapClient;

	/** @var BCHttpClient */
	protected $httpClient;

	public function setUp(): void
	{
		Environment::skip('This test should be run manually. Some assertions may not be currently valid.');

		// Note: set your own cert path, passphrase and contractNo
		$cert = __DIR__ . '/../../../../cert/bccert.pem';
		$passphrase = 'heslo';
		$contract = 'contract';
		$guid = '123e4567-e89b-12d3-a456-426655440000';

		$options = new Options($cert, $passphrase, $contract, $guid, self::TEST_ENV);

		$this->soapClient = (new BCSoapClientFactory())->create($options);
		$this->httpClient = (new BCHttpClientFactory())->create($options);
	}

	protected function isBinary(string $data): bool
	{
		return preg_match('~[^\x20-\x7E\t\r\n]~', $data) > 0;
	}

}
