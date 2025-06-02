<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Report\Impl\XmlCsob\Entity;

use AsisTeam\CSOBBC\Entity\Report\IReport;
use AsisTeam\CSOBBC\Entity\Report\IReportEntry;
use AsisTeam\CSOBBC\Reader\Report\Impl\XmlCsob\XmlCsobReader;
use DateTimeImmutable;
use Money\Currency;
use Money\Money;
use SimpleXMLElement;

final class ReportEntry implements IReportEntry
{

	// inland / foreign / special
	private const INL = 'INL';
	private const FOO = 'FOO';
	private const FOI = 'FOI';
	private const SPE = 'SPE';

	// incoming / outgoing payment indicator
	private const IDICATOR_C = 'C';
	private const IDICATOR_D = 'D';
	private const IDICATOR_CR = 'CR';
	private const IDICATOR_DR = 'DR';

	/** @var string */
	private $id;

	/** @var string */
	private $type;

	/** @var DateTimeImmutable */
	private $date;

	/** @var string */
	private $indicator;

	/** @var Money */
	private $amount;

	/** @var string */
	private $variableSymbol;

	/** @var string */
	private $specificSymbol;

	/** @var string */
	private $constantSymbol;

	/** @var string */
	private $accountNo;

	/** @var string */
	private $accountBank;

	/** @var string */
	private $accountOwner;

	/** @var string */
	private $message;

	/** @var string */
	private $remark;

	/** @var Money|null */
	private $originalAmount;

	/** @var string|null */
	private $rate;

	public static function fromXml(SimpleXMLElement $el, IReport $report): self
	{
		$entry = new self();

		$entry->id             = (string) $el->S28_POR_CISLO . '/' . $report->getSerialNo() . '/' . $report->getDateEnd()->format('Y');
		$entry->type           = (string) $el->DOM_ZAHR;
		$entry->indicator      = (string) $el->S61_CD_INDIK;
		$entry->accountOwner   = (string) $el->PART_ACC_ID;
		$entry->accountNo      = (string) $el->PART_ACCNO;
		$entry->accountBank    = (string) $el->PART_BANK_ID;
		$entry->date           = new DateTimeImmutable((string) $el->S61_DATUM);
		$entry->amount         = XmlCsobReader::createMoney((string) $el->S61_CASTKA, (string) $el->S61_MENA);
		$entry->variableSymbol = (string) $el->S86_VARSYMOUR;
		$entry->specificSymbol = (string) $el->S86_SPECSYMOUR;
		$entry->constantSymbol = (string) $el->S86_KONSTSYM;
		$entry->message        = (string) $el->PART_ID1_1 . (string) $el->PART_ID1_2
			. (string) $el->PART_ID2_1 . (string) $el->PART_ID2_2
			. (string) $el->PART_MSG_1 . (string) $el->PART_MSG_2;
		$entry->remark = (string) $el->REMARK;

		if (strlen((string) $el->RATE) > 0) {
			$entry->rate = (string) $el->RATE;
		}

		if (strlen((string) $el->ORIG_AMOUNT) > 0 && strlen((string) $el->ORIG_CURR) > 0) {
			$entry->originalAmount = new Money(
				str_replace(',', '', (string) $el->ORIG_AMOUNT),
				new Currency((string) $el->ORIG_CURR)
			);
		}

		return $entry;
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function getDate(): DateTimeImmutable
	{
		return $this->date;
	}

	public function getAmount(): Money
	{
		return $this->amount;
	}

	public function getVariableSymbol(): string
	{
		return $this->variableSymbol;
	}

	public function getSpecificSymbol(): string
	{
		return $this->specificSymbol;
	}

	public function getConstantSymbol(): string
	{
		return $this->constantSymbol;
	}

	public function getAccountNo(): string
	{
		return $this->accountNo;
	}

	public function getAccountBank(): string
	{
		return $this->accountBank;
	}

	public function getAccountOwner(): string
	{
		return $this->accountOwner;
	}

	public function getMessage(): string
	{
		return $this->message;
	}

	public function getRemark(): string
	{
		return $this->remark;
	}

	public function isTypeInland(): bool
	{
		return $this->type === self::INL;
	}

	public function isTypeForeign(): bool
	{
		return $this->type === self::FOO || $this->type === self::FOI;
	}

	public function isTypeSpecial(): bool
	{
		return $this->type === self::SPE;
	}

	public function getOriginalAmount(): ?Money
	{
		return $this->originalAmount;
	}

	public function getRate(): ?string
	{
		return $this->rate;
	}

	public function isIncoming(): bool
	{
		return $this->indicator === self::IDICATOR_C || $this->indicator === self::IDICATOR_DR;
	}

	public function isOutgoing(): bool
	{
		return $this->indicator === self::IDICATOR_D || $this->indicator === self::IDICATOR_CR;
	}

}
