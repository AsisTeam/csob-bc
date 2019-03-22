<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Tests\Cases\Integration\Client;

use AsisTeam\CSOBBC\Client\BCClientFacade;
use AsisTeam\CSOBBC\Entity\File;
use AsisTeam\CSOBBC\Entity\Upload;
use AsisTeam\CSOBBC\Enum\FileFormatEnum;
use AsisTeam\CSOBBC\Enum\UploadModeEnum;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

class BCFacadeClientTest extends AbstractTestClient
{

	/** @var BCClientFacade */
	private $facade;

	public function setUp(): void
	{
		parent::setUp();

		$this->facade = new BCClientFacade($this->soapClient, $this->httpClient);
	}

	public function testListFilesDownloadAndUpload(): void
	{
		$resp = $this->facade->listFiles();
		Assert::true(count($resp->getFiles()) > 0);

		foreach ($resp->getFiles() as $listed) {
			if ($listed->getDownloadUrl() !== null) {
				echo 'Downloading -> ' . $listed->getDownloadUrl() . PHP_EOL;
				$contents = $this->facade->download($listed->getDownloadUrl());
				Assert::true($this->isBinary($contents));
			}
		}

		$file1 = new File(__DIR__ . '/../../Unit/Client/file/example1.txt');
		$file1->setFormat(FileFormatEnum::TXT_ZPS);
		$file1->setUploadMode(UploadModeEnum::INCLUDE_INCORRECT);

		$file2 = new File(__DIR__ . '/../../Unit/Client/file/example2.txt', 'CustomName.txt');
		$file2->setFormat(FileFormatEnum::TXT_ZPS);
		$file2->setUploadMode(UploadModeEnum::ONLY_CORRECT);

		$this->facade->upload([$file1, $file2]);

		Assert::true($file1->getUpload() instanceof Upload);
		Assert::true($file2->getUpload() instanceof Upload);
	}

}

(new BCFacadeClientTest())->run();
