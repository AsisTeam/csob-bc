<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Report\Impl\XmlCba\Entity;

use AsisTeam\CSOBBC\Entity\AccountStatement\IAddress;
use Genkgo\Camt\DTO\Address as CamtAddress;

class Address implements IAddress
{

	/** @var CamtAddress */
	private $rec;

	public function __construct(CamtAddress $rec)
	{
		$this->rec = $rec;
	}

	/**
	 * @return string[]
	 */
	public function getAddressLines(): array
	{
		return $this->rec->getAddressLines();
	}

	public function getStreetName(): ?string
	{
		return $this->rec->getStreetName();
	}

	public function getBuildingNumber(): ?string
	{
		return $this->rec->getBuildingNumber();
	}

	public function getTownName(): ?string
	{
		return $this->rec->getTownName();
	}

	public function getPostCode(): ?string
	{
		return $this->rec->getPostCode();
	}

	public function getCountry(): ?string
	{
		return $this->rec->getCountry();
	}

}
