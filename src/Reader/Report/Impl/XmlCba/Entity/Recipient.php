<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Report\Impl\XmlCba\Entity;

use AsisTeam\CSOBBC\Entity\AccountStatement\IAddress;
use AsisTeam\CSOBBC\Entity\AccountStatement\IRecipient;
use Genkgo\Camt\DTO\Recipient as CamtRecipient;

class Recipient implements IRecipient
{

	/** @var CamtRecipient */
	private $rec;

	public function __construct(CamtRecipient $rec)
	{
		$this->rec = $rec;
	}

	public function getIdentification(): ?string
	{
		return $this->rec->getIdentification();
	}

	public function getName(): ?string
	{
		return $this->rec->getName();
	}

	public function getPhoneNumber(): ?string
	{
		return $this->rec->getContactDetails() ? $this->rec->getContactDetails()->getPhoneNumber() : null;
	}

	public function getMobileNumber(): ?string
	{
		return $this->rec->getContactDetails() ? $this->rec->getContactDetails()->getMobileNumber() : null;
	}

	public function getEmailAddress(): ?string
	{
		return $this->rec->getContactDetails() ? $this->rec->getContactDetails()->getEmailAddress() : null;
	}

	public function getAddress(): IAddress
	{
		return $this->rec->getAddress() ? new Address($this->rec->getAddress()) : null;
	}

}
