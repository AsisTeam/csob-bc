<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Client;

use SoapClient;

final class BCSoapClientFactory
{

	private const WSDL = __DIR__ . '/../../cebbc-wsdl/CEBBCWS.wsdl';
	private const API_TEST = 'https://testceb-bc.csob.cz/cebbc/api';
	private const API_PROD = 'https://ceb-bc.csob.cz/cebbc/api';

	public function create(Options $options): BCSoapClient
	{
		$soap = new SoapClient(
			self::WSDL,
			[
				'location'     => $options->isTest() ? self::API_TEST : self::API_PROD,
				'soap_version' => SOAP_1_1,
				'encoding'     => 'UTF-8',
				'trace'        => true,
				'exceptions'   => true,
				'local_cert'   => $options->getCertPath(),
				'passphrase'   => $options->getCertPassphrase(),
			]
		);

		return new BCSoapClient($soap, $options->getContractNo(), $options->getClientAppGuid());
	}

}
