<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Response;

abstract class AbstractResponse
{

	/** @var string */
	protected $ticketId;

	public function getTicketId(): string
	{
		return $this->ticketId;
	}

}
