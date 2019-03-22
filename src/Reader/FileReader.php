<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader;

use AsisTeam\CSOBBC\Entity\Advice\IAdvice;
use AsisTeam\CSOBBC\Entity\IFile;
use AsisTeam\CSOBBC\Entity\ImportProtocol\IImportProtocol;
use AsisTeam\CSOBBC\Entity\Report\IReport;
use AsisTeam\CSOBBC\Enum\FileTypeEnum;
use AsisTeam\CSOBBC\Exception\Runtime\ReaderException;
use AsisTeam\CSOBBC\Reader\Advice\IAdviceReader;
use AsisTeam\CSOBBC\Reader\Import\IImportProtocolReader;
use AsisTeam\CSOBBC\Reader\Report\IReportReader;

class FileReader
{

	/** @var IReportReader */
	private $reportReader;

	/** @var IAdviceReader */
	private $adviceReader;

	/** @var IImportProtocolReader */
	private $importProtocolReader;

	public function __construct(
		IReportReader $reportReader,
		IAdviceReader $adviceReader,
		IImportProtocolReader $importProtocolReader
	)
	{
		$this->reportReader         = $reportReader;
		$this->adviceReader         = $adviceReader;
		$this->importProtocolReader = $importProtocolReader;
	}

	/**
	 * @return IReport|IAdvice|IImportProtocol
	 */
	public function read(IFile $file)
	{
		switch ($file->getType()) {
			case FileTypeEnum::VYPIS:
				return $this->readReport($file);
			case FileTypeEnum::AVIZO:
				return $this->readAdvice($file);
			case FileTypeEnum::IMPPROT:
				return $this->readImportProtocol($file);
			default:
				throw new ReaderException(sprintf('Reading of "%s" file type is not implemented', $file->getType()));
		}
	}

	public function readReport(IFile $file): IReport
	{
		return $this->reportReader->read($file);
	}

	public function readAdvice(IFile $file): IAdvice
	{
		return $this->adviceReader->read($file);
	}

	public function readImportProtocol(IFile $file): IImportProtocol
	{
		return $this->importProtocolReader->read($file);
	}

}
