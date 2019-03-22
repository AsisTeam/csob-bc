<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Tests\Cases\Integration;

use AsisTeam\CSOBBC\CEB;
use AsisTeam\CSOBBC\CEBFactory;
use AsisTeam\CSOBBC\Client\Options;
use Tester\Environment;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';


final class CEBPlaygroundlTest extends TestCase
{

	/** @var CEB */
	private $ceb;

	public function setUp(): void
	{
		Environment::skip('This test should be run manually. It is prepared for you cto test usage of CEB. Just fill valid options details and play with CEB api.');

		$tmp = __DIR__ . '/../../../tmp/';
		$cert = __DIR__ . '/../../../cert/bccert.pem';
		$passphrase = '';
		$contract = '';
		$guid = '';

		$options = new Options($cert, $passphrase, $contract, $guid);

		$this->ceb = (new CEBFactory($options, $tmp))->create();
	}

	public function testSomething(): void
	{
		$files = $this->ceb->listFiles();
		$this->ceb->downloadAndRead($files->getFiles()[count($files->getFiles()) - 1]);
	}

}

(new CEBPlaygroundlTest())->run();
