<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Advice\Impl\MT942\Entity\Payment;

use AsisTeam\CSOBBC\Entity\Advice\Payment\IForeignPayment;
use AsisTeam\CSOBBC\Reader\Advice\Impl\MT942\LineFinder;
use Money\Currency;
use Money\Money;

final class AdvisedForeignPayment implements IForeignPayment
{

	private const EX_RATE             = '?00Kurs:';
	private const COUNTERPARTY        = '?20';
	private const BOOK_TYPE           = '?21';
	private const PURPOSE_1           = '?22';
	private const PURPOSE_2           = '?23';
	private const PURPOSE_3           = '?24';
	private const PURPOSE_4           = '?25';
	private const PURPOSE_5           = '?26';
	private const COUNTERPARTY_CHARGE = '?27POPL.ZAHR:';
	private const SWIFT               = '?30';
	private const IBAN                = '?31';
	private const ADDRESS_1           = '?32';
	private const ADDRESS_2           = '?33';
	private const LOCAL_CHARGE        = '//CHGS/';

	/** @var float */
	private $exRate = 1;

	/** @var string */
	private $counterparty = '';

	/** @var string */
	private $counterpartyAddress = '';

	/** @var string */
	private $swift = '';

	/** @var string */
	private $iban = '';

	/** @var string */
	private $bookType = '';

	/** @var string */
	private $purpose = '';

	/** @var Money */
	private $counterpartyCharge;

	/** @var Money */
	private $localCharge;

	public function __construct(
		string $iban,
		string $swift,
		string $counterparty
	)
	{
		$this->iban = $iban;
		$this->swift = $swift;
		$this->counterparty = $counterparty;

		$this->counterpartyCharge = new Money(0, new Currency('CZK'));
		$this->localCharge = new Money(0, new Currency('CZK'));
	}

	/**
	 * @param string[] $lines
	 */
	public static function createFromLines(array $lines): self
	{
		$p = new self(
			LineFinder::get($lines, self::IBAN),
			LineFinder::get($lines, self::SWIFT),
			LineFinder::get($lines, self::COUNTERPARTY)
		);

		$p->bookType = LineFinder::get($lines, self::BOOK_TYPE);
		$p->exRate = floatval(LineFinder::get($lines, self::EX_RATE));
		$p->purpose = implode(
			'',
			[
				LineFinder::get($lines, self::PURPOSE_1),
				LineFinder::get($lines, self::PURPOSE_2),
				LineFinder::get($lines, self::PURPOSE_3),
				LineFinder::get($lines, self::PURPOSE_4),
				LineFinder::get($lines, self::PURPOSE_5),
			]
		);

		// local charge is present on the line with address
		$addr2 = LineFinder::get($lines, self::ADDRESS_2);
		$lCh = explode(self::LOCAL_CHARGE, $addr2);
		if (isset($lCh[1])) {
			$amnt = str_replace(',', '', substr($lCh[1], 3));
			$p->localCharge = new Money((int) $amnt, new Currency(substr($lCh[1], 0, 3)));
		}

		$p->counterpartyAddress = implode(
			' ',
			[
				LineFinder::get($lines, self::ADDRESS_1),
				$lCh[0] ?? '',
			]
		);

		$cpCh = LineFinder::find($lines, self::COUNTERPARTY_CHARGE);
		if ($cpCh !== null) {
			$amnt = str_replace(',', '', substr($cpCh, 3));
			$p->counterpartyCharge = new Money((int) $amnt, new Currency(substr($cpCh, 0, 3)));
		}

		return $p;
	}

	public function toString(): string
	{
		return implode(
			', ',
			[
				$this->bookType,
				$this->counterparty,
				$this->swift,
				$this->iban,
				$this->purpose,
			]
		);
	}

	public function getExchangeRate(): float
	{
		return $this->exRate;
	}

	public function getCounterparty(): string
	{
		return $this->counterparty;
	}

	public function getCounterpartySwift(): string
	{
		return $this->swift;
	}

	public function getCounterPartyIban(): string
	{
		return $this->iban;
	}

	public function getBookItemType(): string
	{
		return $this->bookType;
	}

	public function getPurpose(): string
	{
		return $this->purpose;
	}

	public function getCounterpartyBankCharge(): Money
	{
		return $this->counterpartyCharge;
	}

	public function getBankCharge(): Money
	{
		return $this->localCharge;
	}

	public function getCounterpartyAddress(): string
	{
		return $this->counterpartyAddress;
	}

}
