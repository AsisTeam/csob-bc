<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Tests\Cases\Integration\Client;

use AsisTeam\CSOBBC\Entity\File;
use AsisTeam\CSOBBC\Enum\FileFormatEnum;
use AsisTeam\CSOBBC\Enum\FileStatusEnum;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

class BCHttpClientTest extends AbstractTestClient
{

	public function testDownloadFile(): void
	{
		$urlFromSoap = 'https://testceb-bc.csob.cz/ceb-mock/download?id=3&name=2019-02-05T21:45.pdf&type=VYPIS';
		$data = $this->httpClient->download($urlFromSoap);

		Assert::true(strlen($data) > 0);
		Assert::true($this->isBinary($data));

		// save file to disk if we wish
		// file_put_contents(__DIR__ . '/../../../tmp/test-download.pdf', $data);
	}

	public function testUploadFile(): void
	{
		$file = new File(__DIR__ . '/../../../Cases/Unit/Client/file/example1.txt');
		$file->setStatus(FileStatusEnum::UPLOAD_AVAILABLE);
		$file->setFormat(FileFormatEnum::TXT_ZPS);
		$file->setDownloadUrl('https://testceb-bc.csob.cz/ceb-mock/upload?type=OnlyCorrect&size=70');

		$uplFile = $this->httpClient->upload($file);

		Assert::same($file, $uplFile);
		Assert::true(strlen($file->getUpload()->getFileId()) > 0);
	}

}

(new BCHttpClientTest())->run();
