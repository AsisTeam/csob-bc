<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Entity;

use DateTimeImmutable;
use Money\Money;

interface IAccountStatementEntry
{

	public function getReference(): string;
	public function getBankTransactionCodeProprietary(): string;
	public function getBankTransactionCodeDomain(): string;

	public function getAmount(): Money;

	public function getBookingDate(): DateTimeImmutable;
	public function getValueDate(): DateTimeImmutable;

	public function getReversalIndicator(): bool;
	public function getTotalChargesAndTaxAmount(): Money;

}
