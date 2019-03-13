<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Advice\Impl\MT942\Entity;

use AsisTeam\CSOBBC\Entity\Advice\IAdvisedPayment;
use AsisTeam\CSOBBC\Entity\Advice\IAdvisedTransaction;
use DateTimeImmutable;
use Money\Money;

final class AdvisedTransaction implements IAdvisedTransaction
{

	/** @var string[] */
	private $lines;

	/** @var DateTimeImmutable */
	private $date;

	/** @var DateTimeImmutable */
	private $dateBooked;

	/** @var string */
	private $bookType = '';

	/** @var Money */
	private $amount;

	/** @var string */
	private $transactionType = '';

	/** @var string */
	private $clientReference = '';

	/** @var string */
	private $bankReference = '';

	/** @var string */
	private $currencyConversionDetails = '';

	/** @var DateTimeImmutable|null */
	private $expenseDeductionDate;

	/** @var IAdvisedPayment */
	private $payment;

	/**
	 * @param string[] $lines
	 */
	public function __construct(array $lines)
	{
		$this->lines = $lines;
	}

	public function getRaw(): string
	{
		return implode("\n", $this->lines);
	}

	public function getDate(): DateTimeImmutable
	{
		return $this->date;
	}

	public function getDateBooked(): DateTimeImmutable
	{
		return $this->dateBooked;
	}

	public function getBookType(): string
	{
		return $this->bookType;
	}

	public function getAmount(): Money
	{
		return $this->amount;
	}

	public function getTransactionType(): string
	{
		return $this->transactionType;
	}

	public function getClientReference(): string
	{
		return $this->clientReference;
	}

	public function getBankReference(): string
	{
		return $this->bankReference;
	}

	public function getCurrencyConversionDetails(): string
	{
		return $this->currencyConversionDetails;
	}

	public function getExpenseDeductionDate(): ?DateTimeImmutable
	{
		return $this->expenseDeductionDate;
	}

	public function setDate(DateTimeImmutable $date): void
	{
		$this->date = $date;
	}

	public function setDateBooked(DateTimeImmutable $dateBooked): void
	{
		$this->dateBooked = $dateBooked;
	}

	public function setBookType(string $bookType): void
	{
		$this->bookType = $bookType;
	}

	public function setAmount(Money $amount): void
	{
		$this->amount = $amount;
	}

	public function setTransactionType(string $transactionType): void
	{
		$this->transactionType = $transactionType;
	}

	public function setClientReference(string $clientReference): void
	{
		$this->clientReference = $clientReference;
	}

	public function setBankReference(string $bankReference): void
	{
		$this->bankReference = $bankReference;
	}

	public function setCurrencyConversionDetails(string $currencyConversionDetails): void
	{
		$this->currencyConversionDetails = $currencyConversionDetails;
	}

	public function setExpenseDeductionDate(?DateTimeImmutable $expenseDeductionDate = null): void
	{
		$this->expenseDeductionDate = $expenseDeductionDate;
	}

	public function getPayment(): IAdvisedPayment
	{
		return $this->payment;
	}

	public function setPayment(IAdvisedPayment $payment): void
	{
		$this->payment = $payment;
	}

}
