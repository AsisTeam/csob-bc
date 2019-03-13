<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Tests\Cases\Integration;

use AsisTeam\CSOBBC\CEB;
use AsisTeam\CSOBBC\Client\BCClientFacade;
use AsisTeam\CSOBBC\Client\BCHttpClient;
use AsisTeam\CSOBBC\Client\BCSoapClient;
use AsisTeam\CSOBBC\Entity\Advice\IAdvice;
use AsisTeam\CSOBBC\Entity\Report\IReport;
use AsisTeam\CSOBBC\Generator\FileGenerator;
use AsisTeam\CSOBBC\Reader\Advice\Impl\MT942\Mt942Reader;
use AsisTeam\CSOBBC\Reader\FileReader;
use AsisTeam\CSOBBC\Reader\Report\Impl\XmlCsob\XmlCsobReader;
use AsisTeam\CSOBBC\Tests\Cases\Unit\Client\SoapMockHelper;
use Mockery;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';


final class CEBTest extends TestCase
{

	public function testListDownloadAndReadFiles(): void
	{
		$soapMock   = SoapMockHelper::createSoapMock('getDownloadFileList_v2', 'getDownloadFileListAvizoVypis.xml');
		$soapClient = new BCSoapClient($soapMock, '123', 'guid');

		$httpClient = Mockery::mock(BCHttpClient::class);
		$httpClient->shouldReceive('download')->andReturn(
			file_get_contents(__DIR__ . '/../Unit/Reader/Report/Impl/XmlCsob/data/226047602_20190220_5_DCZB.xml'),
			file_get_contents(__DIR__ . '/../Unit/Reader/Advice/Impl/Mt942/data/AV_example.STA')
		);

		$facade    = new BCClientFacade($soapClient, $httpClient);
		$reader    = new FileReader(new XmlCsobReader(), new Mt942Reader());
		$generator = Mockery::mock(FileGenerator::class);

		$ceb  = new CEB($facade, $reader, $generator);
		$list = $ceb->listFiles();

		Assert::count(2, $list->getFiles());

		// first one is VYPIS type
		$as = $ceb->downloadAndRead($list->getFiles()[0]);
		Assert::true($as instanceof IReport);
		Assert::count(3, $as->getEntries());

		// second one is AVIZO type
		$adv = $ceb->downloadAndRead($list->getFiles()[1]);
		Assert::true($adv instanceof IAdvice);
		Assert::count(3, $adv->getTransactions());
	}

}

(new CEBTest())->run();
