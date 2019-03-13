<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Report\Impl\XmlCba\Entity;

use AsisTeam\CSOBBC\Entity\AccountStatement\IGroupHeader;
use AsisTeam\CSOBBC\Entity\AccountStatement\IPagination;
use AsisTeam\CSOBBC\Entity\AccountStatement\IRecipient;
use DateTimeImmutable;
use Genkgo\Camt\DTO\GroupHeader as CamtGroupHeader;

final class GroupHeader implements IGroupHeader
{

	/** @var CamtGroupHeader */
	private $hdr;

	public function __construct(CamtGroupHeader $hdr)
	{
		$this->hdr = $hdr;
	}

	public function getMessageId(): string
	{
		return $this->hdr->getMessageId();
	}

	public function getCreatedOn(): DateTimeImmutable
	{
		return $this->hdr->getCreatedOn();
	}

	public function getFrequency(): ?string
	{
		return $this->hdr->getAdditionalInformation();
	}

	public function getMessageRecipient(): IRecipient
	{
		return $this->hdr->getMessageRecipient() ? new Recipient($this->hdr->getMessageRecipient()) : null;
	}

	public function getPagination(): IPagination
	{
		return new Pagination($this->hdr->getPagination());
	}

}
