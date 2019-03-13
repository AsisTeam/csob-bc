<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Report\Impl\XmlCba\Entity;

use AsisTeam\CSOBBC\Entity\AccountStatement\IAccountStatementMessage;
use AsisTeam\CSOBBC\Entity\AccountStatement\IEntry;
use AsisTeam\CSOBBC\Entity\AccountStatement\IGroupHeader;
use AsisTeam\CSOBBC\Entity\AccountStatement\IStatement;
use Genkgo\Camt\DTO\Message;

class AccountStatement implements IAccountStatementMessage
{

	/** @var Message */
	private $msg;

	public function __construct(Message $msg)
	{
		$this->msg = $msg;
	}

	public function getGroupHeader(): IGroupHeader
	{
		return new GroupHeader($this->msg->getGroupHeader());
	}

	/**
	 * @return IStatement[]
	 */
	public function getStatements(): array
	{
		$sts = [];

		foreach ($this->msg->getRecords() as $rec) {
			$sts[] = new Statement($rec);
		}

		return $sts;
	}

	/**
	 * @return IEntry[]
	 */
	public function getEntries(): array
	{
		$entries = [];
		foreach ($this->getStatements() as $st) {
			$entries = array_merge($entries, $st->getEntries());
		}

		return $entries;
	}

}
