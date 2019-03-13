<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Report\Impl\XmlCba\Entity;

use AsisTeam\CSOBBC\Entity\AccountStatement\IProprietaryReference;
use Genkgo\Camt\DTO\ProprietaryReference as CamtProprietaryReference;

class ProprietaryReference implements IProprietaryReference
{

	/** @var CamtProprietaryReference */
	private $pRef;

	public function __construct(CamtProprietaryReference $pRef)
	{
		$this->pRef = $pRef;
	}

	public function getType(): string
	{
		return $this->pRef->getType();
	}

	public function getReference(): string
	{
		return $this->pRef->getReference();
	}

}
