<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Entity\Advice;

use DateTimeImmutable;
use Money\Money;

interface IAdvisedTransaction
{

	public function getDate(): DateTimeImmutable;
	public function getDateBooked(): DateTimeImmutable;
	public function getBookType(): string;
	public function getAmount(): Money;
	public function getTransactionType(): string;
	public function getClientReference(): string;
	public function getBankReference(): string;
	public function getCurrencyConversionDetails(): string;
	public function getExpenseDeductionDate(): ?DateTimeImmutable;

	public function getPayment(): IAdvisedPayment;

}
