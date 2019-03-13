<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Advice\Impl\MT942\Entity\Payment;

use AsisTeam\CSOBBC\Entity\Advice\Payment\IInlandPayment;
use AsisTeam\CSOBBC\Reader\Advice\Impl\MT942\LineFinder;

final class AdvisedInlandPayment implements IInlandPayment
{

	private const COUNTERPARTY         = '?00';
	private const BOOK_TYPE            = '?20';
	private const COUNTERPARTY_ACCOUNT = '?21';
	private const VARIABLE_SYMBOL      = '?22VS:';
	private const SPECIFIC_SYMBOL      = '?23SS:';
	private const CONSTANT_SYMBOL      = '?24KS:';
	private const MESSAGE_1            = '?25';
	private const MESSAGE_2            = '?26';
	private const MESSAGE_3            = '?27';
	private const MESSAGE_4            = '?28';

	/** @var string */
	private $counterPartyName;

	/** @var string */
	private $counterPartyAccountNumber;

	/** @var string */
	private $bookItemType;

	/** @var string */
	private $variableSymbol;

	/** @var string */
	private $specificSymbol;

	/** @var string */
	private $constantSymbol;

	/** @var string */
	private $message;

	public function __construct(
		string $counterPartyName,
		string $counterPartyAccountNumber,
		string $bookItemType,
		string $variableSymbol = '',
		string $specificSymbol = '',
		string $constantSymbol = '',
		string $message = ''
	)
	{
		$this->counterPartyName          = $counterPartyName;
		$this->counterPartyAccountNumber = $counterPartyAccountNumber;
		$this->bookItemType              = $bookItemType;
		$this->variableSymbol            = $variableSymbol;
		$this->specificSymbol            = $specificSymbol;
		$this->constantSymbol            = $constantSymbol;
		$this->message                   = $message;
	}

	/**
	 * @param string[] $lines
	 */
	public static function createFromLines(array $lines): self
	{
		return new self(
			LineFinder::get($lines, self::COUNTERPARTY),
			LineFinder::get($lines, self::COUNTERPARTY_ACCOUNT),
			LineFinder::get($lines, self::BOOK_TYPE),
			LineFinder::get($lines, self::VARIABLE_SYMBOL),
			LineFinder::get($lines, self::SPECIFIC_SYMBOL),
			LineFinder::get($lines, self::CONSTANT_SYMBOL),
			implode(
				'',
				[
					LineFinder::get($lines, self::MESSAGE_1),
					LineFinder::get($lines, self::MESSAGE_2),
					LineFinder::get($lines, self::MESSAGE_3),
					LineFinder::get($lines, self::MESSAGE_4),
				]
			)
		);
	}

	public function toString(): string
	{
		return implode(
			', ',
			[
				$this->counterPartyName,
				$this->counterPartyAccountNumber,
				$this->bookItemType,
				$this->variableSymbol,
				$this->specificSymbol,
				$this->constantSymbol,
				$this->message,
			]
		);
	}

	public function getCounterPartyName(): string
	{
		return $this->counterPartyName;
	}

	public function getCounterPartyAccountNumber(): string
	{
		return $this->counterPartyAccountNumber;
	}

	public function getBookItemType(): string
	{
		return $this->bookItemType;
	}

	public function getVariableSymbol(): string
	{
		return $this->variableSymbol;
	}

	public function getSpecificSymbol(): string
	{
		return $this->specificSymbol;
	}

	public function getConstantSymbol(): string
	{
		return $this->constantSymbol;
	}

	public function getMessage(): string
	{
		return $this->message;
	}

}
