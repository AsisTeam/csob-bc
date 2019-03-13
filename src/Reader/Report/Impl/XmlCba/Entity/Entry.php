<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Report\Impl\XmlCba\Entity;

use AsisTeam\CSOBBC\Entity\AccountStatement\ICharge;
use AsisTeam\CSOBBC\Entity\AccountStatement\IEntry;
use AsisTeam\CSOBBC\Entity\AccountStatement\ITransactionDetail;
use DateTimeImmutable;
use Genkgo\Camt\DTO\Entry as CamtEntry;
use Money\Money;

class Entry implements IEntry
{

	/** @var CamtEntry */
	private $e;

	public function __construct(CamtEntry $e)
	{
		$this->e = $e;
	}

	public function getAmount(): Money
	{
		return $this->e->getAmount();
	}

	public function getBookingDate(): DateTimeImmutable
	{
		return $this->e->getBookingDate();
	}

	public function getValueDate(): DateTimeImmutable
	{
		return $this->e->getValueDate();
	}

	public function getReversalIndicator(): bool
	{
		return $this->e->getReversalIndicator();
	}

	public function getReference(): string
	{
		return $this->e->getReference();
	}

	public function getAccountServicerReference(): ?string
	{
		return $this->e->getAccountServicerReference();
	}

	public function getIndex(): int
	{
		return $this->e->getIndex();
	}

	public function getBatchPaymentId(): ?string
	{
		return $this->e->getBatchPaymentId();
	}

	public function getAdditionalInfo(): string
	{
		return $this->e->getAdditionalInfo();
	}

	public function getProprietaryBankTransactionCode(): string
	{
		return $this->e->getBankTransactionCode()->getProprietary() ?
			$this->e->getBankTransactionCode()->getProprietary()->getCode() :
			'';
	}

	public function getDomainBankTransactionCode(): string
	{
		return $this->e->getBankTransactionCode()->getDomain() ?
			$this->e->getBankTransactionCode()->getDomain()->getCode() :
			'';
	}

	/**
	 * @return ITransactionDetail[]
	 */
	public function getTransactionDetails(): array
	{
		$tds = [];

		foreach ($this->e->getTransactionDetails() as $detail) {
			$tds[] = new TransactionDetail($detail);
		}

		return $tds;
	}

	/**
	 * @return ICharge[]
	 */
	public function getChargeRecords(): array
	{
		$recs = [];
		if ($this->e->getCharges() === null) {
			return [];
		}

		foreach ($this->e->getCharges()->getRecords() as $chRec) {
			$recs[] = new Charge($chRec);
		}

		return $recs;
	}

	public function getTotalChargesAndTaxAmount(): ?Money
	{
		return $this->e->getCharges() ? $this->e->getCharges()->getTotalChargesAndTaxAmount() : null;
	}

}
