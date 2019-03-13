<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Entity\Report;

use DateTimeImmutable;
use Money\Money;

interface IReportEntry
{

	public function getId(): string;
	public function getDate(): DateTimeImmutable;
	public function getAmount(): Money;
	public function getVariableSymbol(): string;
	public function getSpecificSymbol(): string;
	public function getConstantSymbol(): string;
	public function getAccountNo(): string;
	public function getAccountBank(): string;
	public function getAccountOwner(): string;
	public function getMessage(): string;
	public function getRemark(): string;

	/**
	 * for incoming/outgoing detection
	 */
	public function isIncoming(): bool;
	public function isOutgoing(): bool;

	/**
	 * for payment type detection
	 */
	public function isTypeInland(): bool;
	public function isTypeForeign(): bool;
	public function isTypeSpecial(): bool;

	/**
	 * for foreign payments
	 */
	public function getOriginalAmount(): ?Money;
	public function getRate(): ?string;

}
