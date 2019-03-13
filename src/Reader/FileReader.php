<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader;

use AsisTeam\CSOBBC\Entity\Advice\IAdvice;
use AsisTeam\CSOBBC\Entity\IFile;
use AsisTeam\CSOBBC\Entity\Report\IReport;
use AsisTeam\CSOBBC\Enum\FileTypeEnum;
use AsisTeam\CSOBBC\Exception\Runtime\ReaderException;
use AsisTeam\CSOBBC\Reader\Advice\IAdviceReader;
use AsisTeam\CSOBBC\Reader\Report\IReportReader;

class FileReader
{

	/** @var IReportReader */
	private $reportReader;

	/** @var IAdviceReader */
	private $adviceReader;

	public function __construct(IReportReader $reportReader, IAdviceReader $adviceReader)
	{
		$this->reportReader = $reportReader;
		$this->adviceReader = $adviceReader;
	}

	/**
	 * @return IReport|IAdvice
	 */
	public function read(IFile $file)
	{
		switch ($file->getType()) {
			case FileTypeEnum::VYPIS:
				return $this->readReport($file);
			case FileTypeEnum::AVIZO:
				return $this->readAdvice($file);
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

}
