<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Report\Impl\XmlCsob\Entity;

use AsisTeam\CSOBBC\Entity\Report\IReport;
use AsisTeam\CSOBBC\Entity\Report\IReportEntry;
use AsisTeam\CSOBBC\Exception\Runtime\ReaderException;
use AsisTeam\CSOBBC\Reader\Report\Impl\XmlCsob\XmlCsobReader;
use DateTimeImmutable;
use Money\Money;
use SimpleXMLElement;

final class Report implements IReport
{

	// positive / negative balance indicator
	private const INDICATOR_D = 'D';

	/** @var string */
	private $serialNo = '';

	/** @var string */
	private $accountNo = '';

	/** @var string */
	private $accountOwner = '';

	/** @var DateTimeImmutable */
	private $dateStart;

	/** @var DateTimeImmutable */
	private $dateEnd;

	/** @var Money */
	private $amountStart;

	/** @var string */
	private $amountStartIndicator;

	/** @var Money */
	private $amountEnd;

	/** @var string */
	private $amountEndIndicator;

	/** @var string */
	private $frequency = '';

	/** @var IReportEntry[] */
	private $entries = [];

	public static function fromXml(SimpleXMLElement $xml): self
	{
		if (strlen((string) $xml->STA_VER) === 0) {
			throw new ReaderException('Missing document version element.');
		}

		if ($xml->FINSTA03->count() > 1) {
			throw new ReaderException('Too many FINSTA03 elements.');
		}

		$report               = new self();
		$report->serialNo     = (string) $xml->FINSTA03->S28_CISLO_VYPISU;
		$report->accountNo    = (string) $xml->FINSTA03->S25_CISLO_UCTU;
		$report->accountOwner = (string) $xml->FINSTA03->SHORTNAME;
		$report->frequency    = (string) $xml->FINSTA03->FREKVENCE . ' - ' . (string) $xml->FINSTA03->FREKV_TXT;

		if (strlen((string) $xml->FINSTA03->S60_DATUM) > 0) {
			$report->dateStart = new DateTimeImmutable((string) $xml->FINSTA03->S60_DATUM);
		}

		if (strlen((string) $xml->FINSTA03->S62_DATUM) === 0) {
			throw new ReaderException('Missing S62_DATUM field');
		} else {
			$report->dateEnd = new DateTimeImmutable((string) $xml->FINSTA03->S62_DATUM);
		}

		$currency            = (string) $xml->FINSTA03->S60_MENA;
		$report->amountStart = XmlCsobReader::createMoney((string) $xml->FINSTA03->S60_CASTKA, $currency);
		$report->amountStartIndicator = (string) $xml->FINSTA03->S60_CD_INDIK;
		$report->amountEnd = XmlCsobReader::createMoney((string) $xml->FINSTA03->S62_CASTKA, $currency);
		$report->amountEndIndicator = (string) $xml->FINSTA03->S62_CD_INDIK;

		foreach ($xml->FINSTA03->FINSTA05 as $item) {
			$report->addEntry(ReportEntry::fromXml($item));
		}

		return $report;
	}

	public function getSerialNo(): string
	{
		return $this->serialNo;
	}

	public function getAccountNo(): string
	{
		return $this->accountNo;
	}

	public function getAccountOwner(): string
	{
		return $this->accountOwner;
	}

	public function getFrequency(): string
	{
		return $this->frequency;
	}

	public function addEntry(ReportEntry $entry): void
	{
		$this->entries[] = $entry;
	}

	/** @return IReportEntry[] */
	public function getEntries(): array
	{
		return $this->entries;
	}

	public function getDateStart(): ?DateTimeImmutable
	{
		return $this->dateStart;
	}

	public function getAmountStart(): Money
	{
		return $this->amountStart;
	}

	public function isAmountStartNegative(): bool
	{
		return $this->amountStartIndicator === self::INDICATOR_D;
	}

	public function getDateEnd(): DateTimeImmutable
	{
		return $this->dateEnd;
	}

	public function getAmountEnd(): Money
	{
		return $this->amountEnd;
	}

	public function isAmountEndNegative(): bool
	{
		return $this->amountEndIndicator === self::INDICATOR_D;
	}

}
