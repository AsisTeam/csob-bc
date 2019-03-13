<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Entity\Advice\Payment;

use AsisTeam\CSOBBC\Entity\Advice\IAdvisedPayment;

interface IOtherPayment extends IAdvisedPayment
{

	public function getCounterPartyName(): string;

	public function getCounterPartyAccountNumber(): string;

	public function getBookItemType(): string;

	public function getVariableSymbol(): string;

	public function getSpecificSymbol(): string;

	public function getConstantSymbol(): string;

	public function getMessage(): string;

}
