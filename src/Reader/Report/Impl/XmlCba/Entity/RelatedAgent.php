<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Report\Impl\XmlCba\Entity;

use AsisTeam\CSOBBC\Entity\AccountStatement\IRelatedAgent;
use Genkgo\Camt\DTO\RelatedAgent as CamtRelatedAgent;

class RelatedAgent implements IRelatedAgent
{

	/** @var CamtRelatedAgent */
	private $agent;

	public function __construct(CamtRelatedAgent $agent)
	{
		$this->agent = $agent;
	}

	public function getName(): string
	{
		return (string) $this->agent->getRelatedAgentType()->getName();
	}

	public function getBic(): string
	{
		return (string) $this->agent->getRelatedAgentType()->getBIC();
	}

}
