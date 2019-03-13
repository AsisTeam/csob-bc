<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Report\Impl\XmlCba\Entity;

use AsisTeam\CSOBBC\Entity\AccountStatement\ICharge;
use AsisTeam\CSOBBC\Entity\AccountStatement\IReference;
use AsisTeam\CSOBBC\Entity\AccountStatement\IRelatedAgent;
use AsisTeam\CSOBBC\Entity\AccountStatement\IRelatedParty;
use AsisTeam\CSOBBC\Entity\AccountStatement\ITransactionDetail;
use DateTimeImmutable;
use Genkgo\Camt\DTO\EntryTransactionDetail;
use Money\Money;

class TransactionDetail implements ITransactionDetail
{

	/** @var EntryTransactionDetail */
	private $rec;

	public function __construct(EntryTransactionDetail $rec)
	{
		$this->rec = $rec;
	}

	public function getAmount(): ?Money
	{
		return $this->rec->getAmount() !== null ? $this->rec->getAmount()->getAmount() : null;
	}

	/**
	 * @return IReference[]
	 */
	public function getReferences(): array
	{
		$refs = [];
		foreach ($this->rec->getReferences() as $camtRef) {
			$refs[] = new Reference($camtRef);
		}

		return $refs;
	}

	/**
	 * @return IRelatedParty[]
	 */
	public function getRelatedParties(): array
	{
		$parties = [];
		foreach ($this->rec->getRelatedParties() as $camtParty) {
			$parties[] = new RelatedParty($camtParty);
		}

		return $parties;
	}

	/**
	 * @return IRelatedAgent[]
	 */
	public function getRelatedAgents(): array
	{
		$agents = [];
		foreach ($this->rec->getRelatedAgents() as $a) {
			$agents[] = new RelatedAgent($a);
		}

		return $agents;
	}

	public function getRemittanceCreditorReference(): string
	{
		return $this->rec->getRemittanceInformation()->getCreditorReferenceInformation() ?
			$this->rec->getRemittanceInformation()->getCreditorReferenceInformation()->getRef() :
			'';
	}

	public function getRemittanceMessage(): string
	{
		return $this->rec->getRemittanceInformation()->getMessage();
	}

	public function getAcceptanceDateTime(): DateTimeImmutable
	{
		return $this->rec->getRelatedDates()->getAcceptanceDateTime();
	}

	/**
	 * @return ICharge[]
	 */
	public function getCharges(): array
	{
		$ch = [];

		foreach ($this->rec->getCharges()->getRecords() as $chRec) {
			$ch[] = new Charge($chRec);
		}

		return $ch;
	}

	public function getTotalChargesAndTaxAmount(): Money
	{
		return $this->rec->getCharges()->getTotalChargesAndTaxAmount();
	}

	public function getReturnAdditionalInformation(): string
	{
		return $this->rec->getReturnInformation()->getAdditionalInformation();
	}

	public function getReturnCode(): string
	{
		return $this->rec->getReturnInformation()->getCode();
	}

	public function getProprietaryBankTransactionCode(): string
	{
		return $this->rec->getBankTransactionCode()->getProprietary()->getCode();
	}

	public function getProprietaryBankTransactionIssuer(): string
	{
		return $this->rec->getBankTransactionCode()->getProprietary()->getIssuer();
	}

	public function getDomainBankTransactionCode(): string
	{
		return $this->rec->getBankTransactionCode()->getDomain()->getCode();
	}

	public function getDomainBankTransactionFamily(): string
	{
		return $this->rec->getBankTransactionCode()->getDomain()->getFamily()->getCode();
	}

}
