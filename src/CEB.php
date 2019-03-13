<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC;

use AsisTeam\CSOBBC\Client\BCClientFacade;
use AsisTeam\CSOBBC\Entity\Advice\IAdvice;
use AsisTeam\CSOBBC\Entity\IFile;
use AsisTeam\CSOBBC\Entity\IPaymentOrder;
use AsisTeam\CSOBBC\Entity\Report\IReport;
use AsisTeam\CSOBBC\Exception\Runtime\ReaderException;
use AsisTeam\CSOBBC\Generator\FileGenerator;
use AsisTeam\CSOBBC\Reader\FileReader;
use AsisTeam\CSOBBC\Request\Filter;
use AsisTeam\CSOBBC\Response\GetDownloadFileListResponse;
use DateTimeImmutable;

final class CEB
{

	/** @var BCClientFacade */
	private $api;

	/** @var FileReader */
	private $reader;

	/** @var FileGenerator */
	private $generator;

	public function __construct(
		BCClientFacade $api,
		FileReader $reader,
		FileGenerator $generator
	)
	{
		$this->api = $api;
		$this->reader = $reader;
		$this->generator = $generator;
	}

	/**
	 * @param IPaymentOrder[] $payments
	 */
	public function generatePaymentFile(array $payments): IFile
	{
		return $this->generator->generatePaymentFile($payments);
	}

	/**
	 * @param IFile[] $files
	 */
	public function upload(array $files): void
	{
		$this->api->upload($files);
	}

	public function listFiles(?DateTimeImmutable $since = null, ?Filter $filter = null): GetDownloadFileListResponse
	{
		return $this->api->listFiles($since, $filter);
	}

	/**
	 * @param IPaymentOrder[] $payments
	 */
	public function pay(array $payments): void
	{
		$file = $this->generator->generatePaymentFile($payments);
		$this->api->upload([$file]);
	}

	/**
	 * @return IAdvice|IReport
	 */
	public function downloadAndRead(IFile $file)
	{
		if ($file->getDownloadUrl() === null) {
			throw new ReaderException('Unable to download and read file. No "downloadUrl"');
		}

		$content = $this->api->download($file->getDownloadUrl());
		$file->setContent($content);

		return $this->reader->read($file);
	}

}
