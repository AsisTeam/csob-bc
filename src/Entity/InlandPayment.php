<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Entity;

use AsisTeam\CSOBBC\Enum\PaymentOrderType;
use AsisTeam\CSOBBC\Exception\Logical\InvalidArgumentException;
use AsisTeam\CSOBBC\Exception\LogicalException;
use DateTimeImmutable;
use Money\Money;

final class InlandPayment implements IPaymentOrder
{

	/** @var string */
	private $type;

	/** @var string */
	private $originatorAccountNumber;

	/** @var DateTimeImmutable */
	private $dueDate;

	/** @var Money */
	private $amount;

	/** @var string */
	private $counterpartyAccountNumber;

	/** @var string */
	private $counterpartyBankCode;

	/** @var string|null */
	private $constantSymbol;

	/** @var string|null */
	private $variableSymbol;

	/** @var string|null */
	private $variableSymbolCashing;

	/** @var string|null */
	private $specificSymbol;

	/** @var string|null */
	private $specificSymbolCashing;

	/** @var string|null */
	private $recipientMessage;

	/** @var string|null */
	private $originatorMessage;

	/** @var string|null */
	private $counterpartyName;

	public function __construct(
		string $type,
		string $originatorAccountNumber,
		Money $amount,
		string $counterpartyAccountNumber,
		string $counterpartyBankCode,
		?DateTimeImmutable $dueDate = null,
		?string $variableSymbol = null,
		?string $constantSymbol = null
	)
	{
		$this->setType($type);
		$this->setAmount($amount);
		$this->setOriginatorAccountNumber($originatorAccountNumber);
		$this->setDueDate($dueDate ?? new DateTimeImmutable());
		$this->setCounterpartyAccountNumber($counterpartyAccountNumber);
		$this->setCounterpartyBankCode($counterpartyBankCode);
		$this->setConstantSymbol($constantSymbol);
		$this->setVariableSymbol($variableSymbol);
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function setType(string $type): void
	{
		if (!PaymentOrderType::isValid($type)) {
			throw new LogicalException(sprintf('Invalid inland payment type "%s"', $type));
		}

		$this->type = $type;
	}

	public function getOriginatorAccountNumber(): string
	{
		return $this->originatorAccountNumber;
	}

	public function setOriginatorAccountNumber(string $originatorAccountNumber): void
	{
		$this->originatorAccountNumber = trim($originatorAccountNumber);
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

		if ($amount->getCurrency()->getCode() !== 'CZK') {
			throw new LogicalException('Only amounts with CZK currency are allowed for inland payments.');
		}

		$this->amount = $amount;
	}

	public function getCounterpartyAccountNumber(): string
	{
		return $this->counterpartyAccountNumber;
	}

	public function setCounterpartyAccountNumber(string $counterpartyAccountNumber): void
	{
		$this->counterpartyAccountNumber = trim($counterpartyAccountNumber);
	}

	public function getCounterpartyBankCode(): string
	{
		return $this->counterpartyBankCode;
	}

	public function setCounterpartyBankCode(string $counterpartyBankCode): void
	{
		if (strlen($counterpartyBankCode) !== 4) {
			throw new InvalidArgumentException('Counterparty bank code must consist of 4 digits');
		}

		$this->counterpartyBankCode = $counterpartyBankCode;
	}

	public function getCounterpartyName(): ?string
	{
		return $this->counterpartyName;
	}

	public function setCounterpartyName(?string $counterpartyName): void
	{
		$this->counterpartyName = $counterpartyName;
	}

	public function getConstantSymbol(): ?string
	{
		return $this->constantSymbol;
	}

	public function setConstantSymbol(?string $constantSymbol): void
	{
		if ($constantSymbol !== null && strlen($constantSymbol) !== 4) {
			throw new InvalidArgumentException('Constant symbol must consist of 4 digits');
		}

		$this->constantSymbol = $constantSymbol;
	}

	public function getVariableSymbol(): ?string
	{
		return $this->variableSymbol;
	}

	public function setVariableSymbol(?string $variableSymbol): void
	{
		if ($variableSymbol !== null && strlen($variableSymbol) > 10) {
			throw new InvalidArgumentException('Variable symbol may contain maximally 10 digits');
		}

		$this->variableSymbol = $variableSymbol;
	}

	public function getVariableSymbolCashing(): ?string
	{
		return $this->variableSymbolCashing;
	}

	public function setVariableSymbolCashing(?string $variableSymbolCashing): void
	{
		if ($variableSymbolCashing !== null && strlen($variableSymbolCashing) > 10) {
			throw new InvalidArgumentException('Cashing variable symbol may contain maximally 10 digits');
		}

		$this->variableSymbolCashing = $variableSymbolCashing;
	}

	public function getSpecificSymbol(): ?string
	{
		return $this->specificSymbol;
	}

	public function setSpecificSymbol(?string $specificSymbol): void
	{
		$this->specificSymbol = $specificSymbol;
	}

	public function getSpecificSymbolCashing(): ?string
	{
		return $this->specificSymbolCashing;
	}

	public function setSpecificSymbolCashing(?string $specificSymbolCashing): void
	{
		$this->specificSymbolCashing = $specificSymbolCashing;
	}

	public function getRecipientMessage(): ?string
	{
		return $this->recipientMessage;
	}

	public function setRecipientMessage(?string $recipientMessage): void
	{
		$this->recipientMessage = $recipientMessage;
	}

	public function getOriginatorMessage(): ?string
	{
		return $this->originatorMessage;
	}

	public function setOriginatorMessage(?string $originatorMessage): void
	{
		$this->originatorMessage = $originatorMessage;
	}

}
