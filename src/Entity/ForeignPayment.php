<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Entity;

use AsisTeam\CSOBBC\Enum\ForeignPaymentCharge;
use AsisTeam\CSOBBC\Exception\Logical\InvalidArgumentException;
use AsisTeam\CSOBBC\Exception\LogicalException;
use DateTimeImmutable;
use Money\Money;

final class ForeignPayment implements IPaymentOrder
{

	/** @var string */
	private $originatorAccountNumber; // may be in ABO or IBAN format

	/** @var string */
	private $originatorReference;

	/** @var DateTimeImmutable */
	private $dueDate;

	/** @var Money */
	private $amount;

	/** @var string */
	private $counterpartyIBAN;

	/** @var string */
	private $counterpartySWIFT;

	/** @var string */
	private $counterpartyBankIdentification;

	/** @var string */
	private $counterpartyCountry;

	/** @var string */
	private $counterpartyNameAndAddress;

	/** @var string */
	private $charge;

	/** @var string */
	private $purpose;

	/** @var string */
	private $bankInstructions;

	public function __construct(
		string $originatorAccountNumber,
		Money $amount,
		string $counterpartyIBAN,
		string $counterpartySWIFT,
		string $counterpartyCountry,
		string $counterpartyNameAndAddress,
		string $charge,
		string $purpose,
		?DateTimeImmutable $dueDate = null,
		string $counterpartyBankIdentification = '',
		string $originatorReference = '',
		string $bankInstructions = ''
	)
	{
		$this->setOriginatorAccountNumber($originatorAccountNumber);
		$this->setAmount($amount);
		$this->setCounterpartyIban($counterpartyIBAN);
		$this->setCounterpartySwift($counterpartySWIFT);
		$this->setCharge($charge);
		$this->setPurpose($purpose);
		$this->setDueDate($dueDate ?? new DateTimeImmutable());
		$this->setCounterpartyCountry($counterpartyCountry);
		$this->setCounterpartyNameAndAddress($counterpartyNameAndAddress);
		$this->setCounterpartyBankIdentification($counterpartyBankIdentification);
		$this->setOriginatorReference($originatorReference);
		$this->setBankInstructions($bankInstructions);
	}

	public function getOriginatorAccountNumber(): string
	{
		return $this->originatorAccountNumber;
	}

	public function setOriginatorAccountNumber(string $originatorAccountNumber): void
	{
		if (strlen($originatorAccountNumber) > 24) {
			throw new InvalidArgumentException('Originator bank account must not contain more then 24 digits');
		}

		$this->originatorAccountNumber = trim($originatorAccountNumber);
	}

	public function getOriginatorReference(): string
	{
		return $this->originatorReference;
	}

	public function setOriginatorReference(string $originatorReference): void
	{
		$this->originatorReference = substr($originatorReference, 0, 16);
	}

	public function getDueDate(): DateTimeImmutable
	{
		return $this->dueDate;
	}

	public function setDueDate(DateTimeImmutable $dueDate): void
	{
		$this->dueDate = $dueDate;
	}

	public function getAmount(): Money
	{
		return $this->amount;
	}

	public function setAmount(Money $amount): void
	{
		if (!$amount->isPositive()) {
			throw new LogicalException('Payment amount must be positive number');
		}

		$this->amount = $amount;
	}

	public function getCurrency(): string
	{
		return $this->amount->getCurrency()->getCode();
	}

	public function getCounterpartyIban(): string
	{
		return $this->counterpartyIBAN;
	}

	public function setCounterpartyIban(string $counterpartyIBAN): void
	{
		$this->counterpartyIBAN = trim($counterpartyIBAN);
	}

	public function getCounterpartySwift(): string
	{
		return $this->counterpartySWIFT;
	}

	public function setCounterpartySwift(string $counterpartySWIFT): void
	{
		$this->counterpartySWIFT = trim($counterpartySWIFT);
	}

	public function getCounterpartyNameAndAddress(): string
	{
		return $this->counterpartyNameAndAddress;
	}

	public function setCounterpartyNameAndAddress(string $counterpartyNameAndAddress): void
	{
		$this->counterpartyNameAndAddress = $counterpartyNameAndAddress;
	}

	public function getCharge(): string
	{
		return $this->charge;
	}

	public function setCharge(string $charge): void
	{
		if (!ForeignPaymentCharge::isValid($charge)) {
			throw new InvalidArgumentException(sprintf('Given charge type "%s" is invalid', $charge));
		}

		$this->charge = $charge;
	}

	public function getPurpose(): string
	{
		return $this->purpose;
	}

	public function setPurpose(string $purpose): void
	{
		if (strlen($purpose) < 3) {
			throw new InvalidArgumentException('Purpose of the payment must contain at least 3 characters');
		}

		$this->purpose = $purpose;
	}

	public function getCounterpartyBankIdentification(): string
	{
		return $this->counterpartyBankIdentification;
	}

	public function setCounterpartyBankIdentification(string $counterpartyBankIdentification): void
	{
		$this->counterpartyBankIdentification = $counterpartyBankIdentification;
	}

	public function getBankInstructions(): string
	{
		return $this->bankInstructions;
	}

	public function setBankInstructions(string $bankInstructions): void
	{
		$this->bankInstructions = $bankInstructions;
	}

	public function getCounterpartyCountry(): string
	{
		return $this->counterpartyCountry;
	}

	public function setCounterpartyCountry(string $iso): void
	{
		if (strlen($iso) !== 2) {
			throw new InvalidArgumentException('Counterparty country must be valid ISO country code.');
		}

		$this->counterpartyCountry = $iso;
	}

}
