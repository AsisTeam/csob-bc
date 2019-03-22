<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Entity\ImportProtocol;

use DateTimeImmutable;
use Money\Money;

interface IImportProtocol
{

	public function isOk(): bool;

	public function getId(): string;
	public function getCreated(): DateTimeImmutable;
	public function getCebImportId(): string;
	public function getTotalAmount(): Money;
	public function getStatus(): string;
	public function getError(): string;

	/**
	 * @return IImportProtocolTransaction[]
	 */
	public function getAllTransactions(): array;

	/**
	 * @return IImportProtocolTransaction[]
	 */
	public function getInvalidTransactions(): array;

}
