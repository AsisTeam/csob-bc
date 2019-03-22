<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Entity\ImportProtocol;

use Money\Money;

interface IImportProtocolTransaction
{

	public function getAmount(): Money;
	public function getCounterpartyAccount(): string;
	public function getCounterpartyBank(): string;
	public function getSenderAccount(): string;

	public function isOk(): bool;
	public function getStatus(): string;
	public function getError(): string;

}
