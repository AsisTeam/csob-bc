<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Report\Impl\XmlCba\Entity;

use AsisTeam\CSOBBC\Entity\AccountStatement\IPagination;
use Genkgo\Camt\DTO\Pagination as CamtPagination;

class Pagination implements IPagination
{

	/** @var CamtPagination */
	private $pag;

	public function __construct(CamtPagination $pag)
	{
		$this->pag = $pag;
	}

	public function getPageNumber(): string
	{
		return $this->pag->getPageNumber();
	}

	public function isLastPage(): bool
	{
		return $this->pag->isLastPage();
	}

}
