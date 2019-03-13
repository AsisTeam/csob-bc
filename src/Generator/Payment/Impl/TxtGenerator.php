<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Generator\Payment\Impl;

use AsisTeam\CSOBBC\Entity\File;
use AsisTeam\CSOBBC\Entity\ForeignPayment;
use AsisTeam\CSOBBC\Entity\IFile;
use AsisTeam\CSOBBC\Entity\InlandPayment;
use AsisTeam\CSOBBC\Entity\IPaymentOrder;
use AsisTeam\CSOBBC\Enum\FileFormatEnum;
use AsisTeam\CSOBBC\Enum\UploadModeEnum;
use AsisTeam\CSOBBC\Exception\Runtime\GeneratorException;
use AsisTeam\CSOBBC\Generator\Payment\IPaymentFileGenerator;
use DateTimeImmutable;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;

final class TxtGenerator implements IPaymentFileGenerator
{

	private const SEPARATOR = '|';

	/** @var string */
	private $tmpDir;

	/** @var bool */
	private $keepTmp;

	/** @var DecimalMoneyFormatter */
	private $moneyFormatter;

	public function __construct(string $tmpDir, bool $keepTmp = false)
	{
		$this->tmpDir = $tmpDir;
		$this->keepTmp = $keepTmp;
		$this->moneyFormatter = new DecimalMoneyFormatter(new ISOCurrencies());
	}

	/**
	 * @param IPaymentOrder[] $payments
	 */
	public function generate(array $payments, string $type): IFile
	{
		if ($type === IPaymentFileGenerator::TYPE_FOREIGN) {
			/** @var ForeignPayment[] $payments */
			$content = $this->generateForeignContent($payments);
			$file = $this->createFile($content);
			$file->setFormat(FileFormatEnum::TXT_ZPS);
		} else {
			$content = $this->generateInlandContent($payments);
			$file = $this->createFile($content);
			$file->setFormat(FileFormatEnum::TXT_TPS);
		}

		$file->setSeparator(self::SEPARATOR);
		$file->setUploadMode(UploadModeEnum::ONLY_CORRECT);

		if (!$this->keepTmp && $file->getLocation() !== null) {
			@unlink($file->getLocation());
		}

		return $file;
	}

	/**
	 * @param ForeignPayment[] $payments
	 */
	public function generateForeign(array $payments): IFile
	{
		$content = '';
		foreach ($payments as $payment) {
			$content .= $this->generateForeignLine($payment) . PHP_EOL;
		}

		$file = $this->createFile($content);
		$file->setFormat(FileFormatEnum::TXT_TPS);

		return $file;
	}

	private function createFile(string $content): File
	{
		$file = sprintf(
			'%s/payments-batch-%s-%s',
			$this->tmpDir,
			(new DateTimeImmutable())->format('YmdHis'),
			substr(md5($content), 0, 6)
		);

		$bytes = file_put_contents($file, iconv('UTF-8', 'Windows-1250', $content));
		if ($bytes === false) {
			throw new GeneratorException(sprintf('TXTGenerator unable to create tmp file "%s"', $file));
		}

		return new File($file);
	}

	/**
	 * @param InlandPayment[] $payments
	 */
	private function generateInlandContent(array $payments): string
	{
		$content = '';
		foreach ($payments as $payment) {
			$content .= $this->generateInlandLine($payment) . PHP_EOL;
		}

		return $content;
	}

	private function generateInlandLine(InlandPayment $p): string
	{
		$rcvrMsg = str_replace(self::SEPARATOR, ' ', (string) $p->getRecipientMessage());
		$origMsg = str_replace(self::SEPARATOR, ' ', (string) $p->getOriginatorMessage());
		$conName = str_replace(self::SEPARATOR, ' ', (string) $p->getCounterpartyName());

		$fields = [
			$p->getType(),
			$p->getOriginatorAccountNumber(),
			$p->getDueDate()->format('Ymd'),
			$this->moneyFormatter->format($p->getAmount()),
			$p->getCounterpartyAccountNumber(),
			$p->getCounterpartyBankCode(),
			$p->getConstantSymbol() ?? '',
			$p->getVariableSymbol(),
			$p->getVariableSymbolCashing(),
			$p->getSpecificSymbol(),
			$p->getSpecificSymbolCashing(),
			(string) substr($rcvrMsg, 0, 35),
			(string) substr($rcvrMsg, 35, 35),
			(string) substr($rcvrMsg, 70, 35),
			(string) substr($rcvrMsg, 105, 35),
			(string) substr($origMsg, 0, 35),
			(string) substr($conName, 0, 35),
		];

		return implode(self::SEPARATOR, $fields);
	}

	/**
	 * @param ForeignPayment[] $payments
	 */
	private function generateForeignContent(array $payments): string
	{
		$content = '';
		foreach ($payments as $payment) {
			$content .= $this->generateForeignLine($payment) . PHP_EOL;
		}

		return $content;
	}

	private function generateForeignLine(ForeignPayment $p): string
	{
		$conBank = str_replace(self::SEPARATOR, ' ', (string) $p->getCounterpartyBankIdentification());
		$conAddr = str_replace(self::SEPARATOR, ' ', (string) $p->getCounterpartyNameAndAddress());
		$purpose = str_replace(self::SEPARATOR, ' ', (string) $p->getPurpose());
		$instructions = str_replace(self::SEPARATOR, ' ', (string) $p->getBankInstructions());

		$fields = [
			$p->getOriginatorAccountNumber(),
			0,
			0,
			$p->getOriginatorReference(),
			$p->getDueDate()->format('Ymd'),
			$this->moneyFormatter->format($p->getAmount()),
			'',
			$p->getCurrency(),
			$p->getCounterpartyIban(),
			'',
			'',
			$p->getCounterpartySwift(),
			(string) substr($conBank, 0, 35),
			(string) substr($conBank, 35, 35),
			(string) substr($conBank, 70, 35),
			(string) substr($conBank, 105, 35),
			$p->getCounterpartyCountry(),
			(string) substr($conAddr, 0, 35),
			(string) substr($conAddr, 35, 35),
			(string) substr($conAddr, 70, 35),
			(string) substr($conAddr, 105, 35),
			'',
			'',
			$p->getCharge(),
			(string) substr($purpose, 0, 35),
			(string) substr($purpose, 35, 35),
			(string) substr($purpose, 70, 35),
			(string) substr($purpose, 105, 35),
			(string) substr($instructions, 0, 35),
			(string) substr($instructions, 35, 35),
			(string) substr($instructions, 70, 35),
			(string) substr($instructions, 105, 35),
			'',
			'',
		];

		return implode(self::SEPARATOR, $fields);
	}

}
