<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Tests\Cases\Unit\Client;

use AsisTeam\CSOBBC\Client\BCSoapClient;
use AsisTeam\CSOBBC\Entity\File;
use AsisTeam\CSOBBC\Enum\FileFormatEnum;
use AsisTeam\CSOBBC\Enum\FileStatusEnum;
use AsisTeam\CSOBBC\Enum\UploadModeEnum;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../../bootstrap.php';

class BCSoapClientTest extends TestCase
{

	public function testGetDownloadFileList(): void
	{
		$soapMock = SoapMockHelper::createSoapMock('getDownloadFileList_v2', 'getDownloadFileList.xml');
		$client = new BCSoapClient($soapMock, '123', 'guid');

		$resp = $client->getFiles();

		Assert::equal('2019-02-05T21:57:32.855', $resp->getQueryTimestamp());
		Assert::equal('CBAPIceb-int19020521573282700183', $resp->getTicketId());
		Assert::true(count($resp->getFiles()) > 0);

		$file = $resp->getFiles()[0];
		Assert::equal('2019-02-05T21:45.pdf', $file->getFileName());
		Assert::equal('VYPIS', $file->getType());
		Assert::equal('2019-02-05', $file->getCreated()->format('Y-m-d'));
		Assert::equal(89579, $file->getSize());
		Assert::equal('D', $file->getStatus());
		Assert::equal(
			'https://testceb-bc.csob.cz/ceb-mock/download?id=3&name=2019-02-05T21:45.pdf&type=VYPIS',
			$file->getDownloadUrl()
		);
	}

	public function testStartUploadFileList(): void
	{
		$soapMock = SoapMockHelper::createSoapMock('StartUploadFileList_v1', 'startUploadFileList.xml');
		$client = new BCSoapClient($soapMock, '123', 'guid');

		$file1 = new File(__DIR__ . '/../../Unit/Client/file/example1.txt');
		$file1->setFormat(FileFormatEnum::TXT_ZPS);
		$file1->setUploadMode(UploadModeEnum::INCLUDE_INCORRECT);

		$file2 = new File(__DIR__ . '/../../Unit/Client/file/example2.txt', 'CustomName.txt');
		$file2->setFormat(FileFormatEnum::TXT_ZPS);
		$file2->setUploadMode(UploadModeEnum::ONLY_CORRECT);

		$resp = $client->startUpload([$file1, $file2]);

		Assert::equal('CBAPIceb-int19020617260621200253', $resp->getTicketId());

		Assert::equal('example1.txt', $resp->getFiles()[0]->getFileName());
		Assert::equal(FileStatusEnum::UPLOAD_AVAILABLE, $resp->getFiles()[0]->getStatus());
		Assert::equal('dbbc21ea6bf44805b244ebccf5c49a42', $resp->getFiles()[0]->getHash());
		Assert::equal(
			'https://testceb-bc.csob.cz/ceb-mock/upload?type=IncludeIncorrect&size=5000',
			$resp->getFiles()[0]->getUploadUrl()
		);

		Assert::equal('CustomName.txt', $resp->getFiles()[1]->getFileName());
		Assert::equal(FileStatusEnum::UPLOAD_AVAILABLE, $resp->getFiles()[1]->getStatus());
		Assert::equal('04fa99859f283286f1e33c6bc6434c16', $resp->getFiles()[1]->getHash());
		Assert::equal(
			'https://testceb-bc.csob.cz/ceb-mock/upload?type=IncludeIncorrect&size=5000',
			$resp->getFiles()[1]->getUploadUrl()
		);
	}

	public function testFileCreation(): void
	{
		$file = new File(__DIR__ . '/file/example1.txt');
		$file->setFormat(FileFormatEnum::TXT_ZPS);
		$file->setUploadMode(UploadModeEnum::INCLUDE_INCORRECT);
		Assert::equal('example1.txt', $file->getFileName());
		Assert::equal(31, $file->getSize());

		$file = new File(__DIR__ . '/file/example1.txt', 'CustomName.txt');
		Assert::equal('CustomName.txt', $file->getFileName());
		Assert::equal(31, $file->getSize());

		// Check that file has proper content, hash and size
		Assert::equal('some content of the first file' . PHP_EOL, $file->getContent());
		Assert::equal($file->getHash(), md5($file->getContent()));
		Assert::equal($file->getSize(), strlen($file->getContent()));
	}

}

(new BCSoapClientTest())->run();
