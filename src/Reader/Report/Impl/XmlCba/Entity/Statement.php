<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Report\Impl\XmlCba\Entity;

use AsisTeam\CSOBBC\Entity\AccountStatement\IEntry;
use AsisTeam\CSOBBC\Entity\AccountStatement\IPagination;
use AsisTeam\CSOBBC\Entity\AccountStatement\IStatement;
use DateTimeImmutable;
use Genkgo\Camt\DTO\Record;

class Statement implements IStatement
{

	/** @var Record */
	private $rec;

	public function __construct(Record $rec)
	{
		$this->rec = $rec;
	}

	public function getId(): string
	{
		return $this->rec->getId();
	}

	public function getCreatedOn(): DateTimeImmutable
	{
		return $this->rec->getCreatedOn();
	}

	public function getAccount(): string
	{
		return $this->rec->getAccount()->getIdentification();
	}

	public function getPagination(): IPagination
	{
		return new Pagination($this->rec->getPagination());
	}

	public function getElectronicSequenceNumber(): string
	{
		return $this->rec->getElectronicSequenceNumber();
	}

	public function getLegalSequenceNumber(): string
	{
		return $this->rec->getLegalSequenceNumber();
	}

	public function getCopyDuplicateIndicator(): ?string
	{
		return $this->rec->getCopyDuplicateIndicator();
	}

	public function getAdditionalInformation(): ?string
	{
		return $this->rec->getAdditionalInformation();
	}

	public function getFromDate(): ?DateTimeImmutable
	{
		return $this->rec->getFromDate();
	}

	public function getToDate(): ?DateTimeImmutable
	{
		return $this->rec->getToDate();
	}

	/**
	 * @return IEntry[]
	 */
	public function getEntries(): array
	{
		$entries = [];
		foreach ($this->rec->getEntries() as $e) {
			$entries[] = new Entry($e);
		}

		return $entries;
	}

}
