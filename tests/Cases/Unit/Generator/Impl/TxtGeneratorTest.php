<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Tests\Cases\Unit\Generator\Impl;

use AsisTeam\CSOBBC\Entity\ForeignPayment;
use AsisTeam\CSOBBC\Entity\InlandPayment;
use AsisTeam\CSOBBC\Enum\FileFormatEnum;
use AsisTeam\CSOBBC\Enum\ForeignPaymentCharge;
use AsisTeam\CSOBBC\Enum\PaymentOrderType;
use AsisTeam\CSOBBC\Enum\UploadModeEnum;
use AsisTeam\CSOBBC\Generator\Payment\Impl\TxtGenerator;
use AsisTeam\CSOBBC\Generator\Payment\IPaymentFileGenerator;
use DateTimeImmutable;
use Money\Currency;
use Money\Money;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../../../bootstrap.php';

final class TxtGeneratorTest extends TestCase
{

	/** @var TxtGenerator */
	private $generator;

	public function setUp(): void
	{
		$this->generator = new TxtGenerator(__DIR__ . '/../../../../tmp');
	}

	public function testGenerateInland(): void
	{
		$file = $this->generator->generate($this->prepareInlandPayments(), IPaymentFileGenerator::TYPE_INLAND);

		Assert::true(strlen($file->getFileName()) > 0);
		Assert::equal(FileFormatEnum::TXT_TPS, $file->getFormat());
		Assert::equal('|', $file->getSeparator());
		Assert::equal(UploadModeEnum::ONLY_CORRECT, $file->getUploadMode());

		$expected = file_get_contents(__DIR__ . '/data/txt_inland.txt');
		Assert::equal($expected, $file->getContent());
		Assert::equal(strlen($expected), $file->getSize());
		Assert::equal(md5($expected), $file->getHash());
	}

	public function testGenerateForeign(): void
	{
		$file = $this->generator->generate($this->prepareForeignPayments(), IPaymentFileGenerator::TYPE_FOREIGN);

		Assert::true(strlen($file->getFileName()) > 0);
		Assert::equal(FileFormatEnum::TXT_ZPS, $file->getFormat());
		Assert::equal('|', $file->getSeparator());
		Assert::equal(UploadModeEnum::ONLY_CORRECT, $file->getUploadMode());

		$expected = file_get_contents(__DIR__ . '/data/txt_foreign.txt');
		Assert::equal($expected, $file->getContent());
		Assert::equal(strlen($expected), $file->getSize());
		Assert::equal(md5($expected), $file->getHash());
	}

	/**
	 * @return InlandPayment[]
	 */
	private function prepareInlandPayments(): array
	{
		$p1 = new InlandPayment(
			PaymentOrderType::WITH_PRIORITY,
			'130450683',
			new Money('12345', new Currency('CZK')),
			'43-14680210',
			'0100',
			new DateTimeImmutable('2019-02-13')
		);

		$p2 = new InlandPayment(
			PaymentOrderType::COMMON,
			'130450683',
			new Money('50000', new Currency('CZK')),
			'8524262',
			'0710',
			new DateTimeImmutable('2019-02-14'),
			'7605122514',
			'0308'
		);
		$p2->setRecipientMessage('Here you have your money back bro.');

		$p3 = new InlandPayment(
			PaymentOrderType::CASHING,
			'130450683',
			new Money(12345, new Currency('CZK')),
			'43-14680210',
			'0100',
			new DateTimeImmutable('2019-02-13')
		);
		$p3->setVariableSymbolCashing('1234567890');
		$p3->setSpecificSymbolCashing('555');
		$p3->setOriginatorMessage('inkaso');
		$p3->setCounterpartyName('Platce inkasa s.ro.');

		return [$p1, $p2, $p3];
	}

	/**
	 * @return ForeignPayment[]
	 */
	private function prepareForeignPayments(): array
	{
		$p1 = new ForeignPayment(
			'CZ6508000000192000145399',
			new Money(50035, new Currency('EUR')),
			'DE89370400440532013000',
			'CTBAAU2S',
			'DE',
			'Prijemce platby s.r.o, Norimberk Strasse 123, Munchen',
			ForeignPaymentCharge::CHARGE_OUR,
			'I pay for thw ordered goods',
			new DateTimeImmutable('2019-02-13')
		);

		return [$p1];
	}

}

(new TxtGeneratorTest())->run();
