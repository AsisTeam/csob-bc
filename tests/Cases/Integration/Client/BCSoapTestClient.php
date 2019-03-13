<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Tests\Cases\Integration\Client;

use AsisTeam\CSOBBC\Entity\File;
use AsisTeam\CSOBBC\Enum\FileFormatEnum;
use AsisTeam\CSOBBC\Enum\FileStatusEnum;
use AsisTeam\CSOBBC\Enum\FileTypeEnum;
use AsisTeam\CSOBBC\Enum\UploadModeEnum;
use AsisTeam\CSOBBC\Request\Filter;
use AsisTeam\CSOBBC\Response\GetDownloadFileListResponse;
use DateTimeImmutable;
use Nette\Utils\Validators;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

class BCSoapTestClient extends AbstractTestClient
{

	/** @var DateTimeImmutable */
	private $now;

	public function setUp(): void
	{
		parent::setUp();

		$this->now = new DateTimeImmutable();
	}

	public function testGetFiles(): void
	{
		$resp = $this->soapClient->getFiles();
		$this->assertNonEmptyGetDownloadFileList($resp);
	}

	public function testGetFilesWithSinceParam(): void
	{
		$since = (new DateTimeImmutable(''))->modify('-5 days');
		$this->assertNonEmptyGetDownloadFileList($this->soapClient->getFiles($since));
	}

	public function testGetFilesWithFilter(): void
	{
		$filter = new Filter();
		$filter->setCreatedBefore(new DateTimeImmutable('2019-02-05 10:55:00'));
		$filter->setFileName('test.pdf');
		$filter->setFileTypes([FileTypeEnum::IMPPROT, FileTypeEnum::VYPIS]);

		$this->assertNonEmptyGetDownloadFileList($this->soapClient->getFiles(null, $filter));
	}

	public function testStartUpload(): void
	{
		$file1 = new File(__DIR__ . '/../../Unit/Client/file/example1.txt');
		$file1->setFormat(FileFormatEnum::TXT_ZPS);
		$file1->setUploadMode(UploadModeEnum::INCLUDE_INCORRECT);

		$file2 = new File(__DIR__ . '/../../Unit/Client/file/example2.txt', 'CustomName.txt');
		$file2->setFormat(FileFormatEnum::TXT_ZPS);
		$file2->setUploadMode(UploadModeEnum::ONLY_CORRECT);

		$resp = $this->soapClient->startUpload([$file1, $file2]);

		Assert::true(strlen($resp->getTicketId()) > 0);
		Assert::count(2, $resp->getFiles());

		Assert::equal('example1.txt', $resp->getFiles()[0]->getFileName());
		Assert::equal(FileStatusEnum::UPLOAD_AVAILABLE, $resp->getFiles()[0]->getStatus());
		Assert::notEqual(null, $resp->getFiles()[0]->getUploadUrl());
		Assert::true(Validators::isUri($resp->getFiles()[0]->getUploadUrl()));

		Assert::equal('CustomName.txt', $resp->getFiles()[1]->getFileName());
		Assert::equal(FileStatusEnum::UPLOAD_AVAILABLE, $resp->getFiles()[1]->getStatus());
		Assert::notEqual(null, $resp->getFiles()[1]->getUploadUrl());
		Assert::true(Validators::isUri($resp->getFiles()[1]->getUploadUrl()));
	}

	private function assertNonEmptyGetDownloadFileList(GetDownloadFileListResponse $resp): void
	{
		Assert::true($resp->getDate()->getTimestamp() >= $this->now->getTimestamp());
		Assert::true(strlen($resp->getTicketId()) > 0);
		Assert::true(count($resp->getFiles()) > 0);
	}

}

(new BCSoapTestClient())->run();
