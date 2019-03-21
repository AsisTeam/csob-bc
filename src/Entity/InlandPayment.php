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

	public function setType(string $type): self
	{
		if (!PaymentOrderType::isValid($type)) {
			throw new LogicalException(sprintf('Invalid inland payment type "%s"', $type));
		}

		$this->type = $type;

		return $this;
	}

	public function getOriginatorAccountNumber(): string
	{
		return $this->originatorAccountNumber;
	}

	public function setOriginatorAccountNumber(string $originatorAccountNumber): self
	{
		$this->originatorAccountNumber = trim($originatorAccountNumber);

		return $this;
	}

	public function getDueDate(): DateTimeImmutable
	{
		return $this->dueDate;
	}

	public function setDueDate(DateTimeImmutable $dueDate): self
	{
		$this->dueDate = $dueDate;

		return $this;
	}

	public function getAmount(): Money
	{
		return $this->amount;
	}

	public function setAmount(Money $amount): self
	{
		if (!$amount->isPositive()) {
			throw new LogicalException('Payment amount must be positive number');
		}

		if ($amount->getCurrency()->getCode() !== 'CZK') {
			throw new LogicalException('Only amounts with CZK currency are allowed for inland payments.');
		}

		$this->amount = $amount;

		return $this;
	}

	public function getCounterpartyAccountNumber(): string
	{
		return $this->counterpartyAccountNumber;
	}

	public function setCounterpartyAccountNumber(string $counterpartyAccountNumber): self
	{
		$this->counterpartyAccountNumber = trim($counterpartyAccountNumber);

		return $this;
	}

	public function getCounterpartyBankCode(): string
	{
		return $this->counterpartyBankCode;
	}

	public function setCounterpartyBankCode(string $counterpartyBankCode): self
	{
		if (strlen($counterpartyBankCode) !== 4) {
			throw new InvalidArgumentException('Counterparty bank code must consist of 4 digits');
		}

		$this->counterpartyBankCode = $counterpartyBankCode;

		return $this;
	}

	public function getCounterpartyName(): ?string
	{
		return $this->counterpartyName;
	}

	public function setCounterpartyName(?string $counterpartyName): self
	{
		$this->counterpartyName = $counterpartyName;

		return $this;
	}

	public function getConstantSymbol(): ?string
	{
		return $this->constantSymbol;
	}

	public function setConstantSymbol(?string $constantSymbol): self
	{
		if ($constantSymbol !== null && strlen($constantSymbol) !== 4) {
			throw new InvalidArgumentException('Constant symbol must consist of 4 digits');
		}

		$this->constantSymbol = $constantSymbol;

		return $this;
	}

	public function getVariableSymbol(): ?string
	{
		return $this->variableSymbol;
	}

	public function setVariableSymbol(?string $variableSymbol): self
	{
		if ($variableSymbol !== null && strlen($variableSymbol) > 10) {
			throw new InvalidArgumentException('Variable symbol may contain maximally 10 digits');
		}

		$this->variableSymbol = $variableSymbol;

		return $this;
	}

	public function getVariableSymbolCashing(): ?string
	{
		return $this->variableSymbolCashing;
	}

	public function setVariableSymbolCashing(?string $variableSymbolCashing): self
	{
		if ($variableSymbolCashing !== null && strlen($variableSymbolCashing) > 10) {
			throw new InvalidArgumentException('Cashing variable symbol may contain maximally 10 digits');
		}

		$this->variableSymbolCashing = $variableSymbolCashing;

		return $this;
	}

	public function getSpecificSymbol(): ?string
	{
		return $this->specificSymbol;
	}

	public function setSpecificSymbol(?string $specificSymbol): self
	{
		$this->specificSymbol = $specificSymbol;

		return $this;
	}

	public function getSpecificSymbolCashing(): ?string
	{
		return $this->specificSymbolCashing;
	}

	public function setSpecificSymbolCashing(?string $specificSymbolCashing): self
	{
		$this->specificSymbolCashing = $specificSymbolCashing;

		return $this;
	}

	public function getRecipientMessage(): ?string
	{
		return $this->recipientMessage;
	}

	public function setRecipientMessage(?string $recipientMessage): self
	{
		$this->recipientMessage = $recipientMessage;

		return $this;
	}

	public function getOriginatorMessage(): ?string
	{
		return $this->originatorMessage;
	}

	public function setOriginatorMessage(?string $originatorMessage): self
	{
		$this->originatorMessage = $originatorMessage;

		return $this;
	}

}
