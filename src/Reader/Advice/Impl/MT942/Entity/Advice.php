<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Advice\Impl\MT942\Entity;

use AsisTeam\CSOBBC\Entity\Advice\IAdvice;
use AsisTeam\CSOBBC\Entity\Advice\IAdvisedTransaction;

final class Advice implements IAdvice
{

	/** @var string */
	private $identification;

	/** @var string */
	private $type;

	/** @var string */
	private $priority;

	/** @var string */
	private $accountOwner;

	/** @var string */
	private $accountNumber;

	/** @var string */
	private $debitLimit;

	/** @var IAdvisedTransaction[] */
	private $transactions = [];

	public function getIdentification(): string
	{
		return $this->identification;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function getPriority(): string
	{
		return $this->priority;
	}

	public function getAccountOwner(): string
	{
		return $this->accountOwner;
	}

	public function getAccountNumber(): string
	{
		return $this->accountNumber;
	}

	public function getDebitLimit(): string
	{
		return $this->debitLimit;
	}

	/**
	 * @return IAdvisedTransaction[]
	 */
	public function getTransactions(): array
	{
		return $this->transactions;
	}

	public function setIdentification(string $identification): void
	{
		$this->identification = $identification;
	}

	public function setType(string $type): void
	{
		$this->type = $type;
	}

	public function setPriority(string $priority): void
	{
		$this->priority = $priority;
	}

	public function setAccountOwner(string $accountOwner): void
	{
		$this->accountOwner = $accountOwner;
	}

	public function setAccountNumber(string $accountNumber): void
	{
		$this->accountNumber = $accountNumber;
	}

	public function setDebitLimit(string $debitLimit): void
	{
		$this->debitLimit = $debitLimit;
	}

	public function addTransaction(IAdvisedTransaction $transaction): void
	{
		$this->transactions[] = $transaction;
	}

}
