<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC;

use AsisTeam\CSOBBC\Client\BCClientFacade;
use AsisTeam\CSOBBC\Entity\Advice\IAdvice;
use AsisTeam\CSOBBC\Entity\IFile;
use AsisTeam\CSOBBC\Entity\ImportProtocol\IImportProtocol;
use AsisTeam\CSOBBC\Entity\IPaymentOrder;
use AsisTeam\CSOBBC\Entity\Report\IReport;
use AsisTeam\CSOBBC\Exception\Runtime\ReaderException;
use AsisTeam\CSOBBC\Generator\FileGenerator;
use AsisTeam\CSOBBC\Reader\FileReader;
use AsisTeam\CSOBBC\Request\Filter;
use AsisTeam\CSOBBC\Response\GetDownloadFileListResponse;

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

	public function listFiles(?string $prevQueryDatetime = null, ?Filter $filter = null): GetDownloadFileListResponse
	{
		return $this->api->listFiles($prevQueryDatetime, $filter);
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
	 * @return IAdvice|IReport|IImportProtocol
	 */
	public function downloadAndRead(IFile $file)
	{
		$file->setContent($this->download($file));

		return $this->reader->read($file);
	}


	public function download(IFile $file): string
	{
		if ($file->getDownloadUrl() === null) {
			throw new ReaderException('Unable to download and read file. No "downloadUrl"');
		}

		return $this->api->download($file->getDownloadUrl());
	}

}
