<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Report\Impl\XmlCba\Entity;

use AsisTeam\CSOBBC\Entity\AccountStatement\IProprietaryReference;
use AsisTeam\CSOBBC\Entity\AccountStatement\IReference;
use Genkgo\Camt\DTO\Reference as CamtReference;

final class Reference implements IReference
{

	/** @var CamtReference */
	private $ref;

	public function __construct(CamtReference $ref)
	{
		$this->ref = $ref;
	}

	public function getMessageId(): ?string
	{
		return $this->ref->getMessageId();
	}

	public function getAccountServiceReference(): ?string
	{
		return $this->ref->getAccountServiceReference();
	}

	public function getPaymentInformationId(): ?string
	{
		return $this->ref->getPaymentInformationId();
	}

	public function getInstructionId(): ?string
	{
		return $this->ref->getInstructionId();
	}

	public function getEndToEndId(): ?string
	{
		return $this->ref->getEndToEndId();
	}

	public function getTransactionId(): ?string
	{
		return $this->ref->getTransactionId();
	}

	public function getMandateId(): ?string
	{
		return $this->ref->getMandateId();
	}

	public function getChequeNumber(): ?string
	{
		return $this->ref->getChequeNumber();
	}

	public function getClearingSystemReference(): ?string
	{
		return $this->ref->getClearingSystemReference();
	}

	public function getAccountOwnerTransactionId(): ?string
	{
		return $this->ref->getAccountOwnerTransactionId();
	}

	public function getAccountServicerTransactionId(): ?string
	{
		return $this->ref->getAccountServicerTransactionId();
	}

	public function getMarketInfrastructureTransactionId(): ?string
	{
		return $this->ref->getMarketInfrastructureTransactionId();
	}

	public function getProcessingId(): ?string
	{
		return $this->ref->getProcessingId();
	}

	/** @return IProprietaryReference[] */
	public function getProprietaries(): array
	{
		$prs = [];

		foreach ($this->ref->getProprietaries() as $proprietaryReference) {
			$prs[] = new ProprietaryReference($proprietaryReference);
		}

		return $prs;
	}

}
