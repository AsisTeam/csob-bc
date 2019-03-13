<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Report\Impl\XmlCba\Entity;

use AsisTeam\CSOBBC\Entity\AccountStatement\IAddress;
use AsisTeam\CSOBBC\Entity\AccountStatement\IRelatedParty;
use Genkgo\Camt\DTO\RelatedParty as CamtRelatedParty;

class RelatedParty implements IRelatedParty
{

	/** @var CamtRelatedParty */
	private $party;

	public function __construct(CamtRelatedParty $party)
	{
		$this->party = $party;
	}

	public function getAccount(): ?string
	{
		return $this->party->getAccount()->getIdentification();
	}

	public function getName(): string
	{
		return (string) $this->party->getRelatedPartyType()->getName();
	}

	public function getAddress(): ?IAddress
	{
		return $this->party->getRelatedPartyType()->getAddress() ?
			new Address($this->party->getRelatedPartyType()->getAddress()) :
			null;
	}

}
