<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Report\Impl\XmlCba\Entity;

use AsisTeam\CSOBBC\Entity\AccountStatement\ICharge;
use Genkgo\Camt\DTO\ChargesRecord;
use Money\Money;

class Charge implements ICharge
{

	/** @var ChargesRecord */
	private $rec;

	public function __construct(ChargesRecord $rec)
	{
		$this->rec = $rec;
	}

	public function getAmount(): Money
	{
		return $this->rec->getAmount();
	}

	public function getChargesIncludedIndicator(): bool
	{
		return (bool) $this->rec->getChargesIncluded­Indicator();
	}

	public function getIdentification(): ?string
	{
		return $this->rec->getIdentification()->getIdentification();
	}

}
