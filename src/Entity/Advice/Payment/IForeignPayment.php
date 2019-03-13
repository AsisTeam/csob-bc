<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Entity\Advice\Payment;

use AsisTeam\CSOBBC\Entity\Advice\IAdvisedPayment;
use Money\Money;

interface IForeignPayment extends IAdvisedPayment
{

	public function getCounterparty(): string;
	public function getCounterpartyAddress(): string;
	public function getCounterpartySwift(): string;
	public function getCounterPartyIban(): string;
	public function getBookItemType(): string;
	public function getExchangeRate(): float;
	public function getPurpose(): string;
	public function getCounterpartyBankCharge(): Money;
	public function getBankCharge(): Money;

}
