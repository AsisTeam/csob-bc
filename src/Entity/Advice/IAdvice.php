<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Entity\Advice;

interface IAdvice
{

	public function getIdentification(): string;
	public function getType(): string;
	public function getPriority(): string;
	public function getAccountOwner(): string;
	public function getAccountNumber(): string;
	public function getDebitLimit(): string;

	/**
	 * @return IAdvisedTransaction[]
	 */
	public function getTransactions(): array;

}
