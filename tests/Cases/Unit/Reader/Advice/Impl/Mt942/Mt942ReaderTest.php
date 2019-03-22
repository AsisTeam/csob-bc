<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Tests\Cases\Unit\Reader\Advice\Impl\Mt942;

use AsisTeam\CSOBBC\Entity\Advice\Payment\IForeignPayment;
use AsisTeam\CSOBBC\Entity\Advice\Payment\IInlandPayment;
use AsisTeam\CSOBBC\Entity\Advice\Payment\IOtherPayment;
use AsisTeam\CSOBBC\Entity\File;
use AsisTeam\CSOBBC\Reader\Advice\Impl\MT942\Mt942Reader;
use Money\Currency;
use Money\Money;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../../../../../bootstrap.php';

final class Mt942ReaderTest extends TestCase
{

	/** @var Mt942Reader */
	private $reader;

	public function setUp(): void
	{
		$this->reader = new Mt942Reader();
	}

	/**
	 * @dataProvider getFiles
	 */
	public function testReadLines(string $filename): void
	{
		$file = new File(__DIR__ . '/data/' . $filename);
		$lines = $this->reader->readLines($file);

		Assert::count(11, $lines);
		Assert::equal('CEKOCZPPAXXX 00000', $lines[0]);
		$lineWithDiacritics = $lines[8];
		Assert::equal('?25Sorry jako Šitbořice omluv?26a za zpožděnou platbu', $lineWithDiacritics);
	}

	/**
	 * @return string[][]
	 */
	public function getFiles(): array
	{
		return [
			['AV_example2_utf-8.STA'], // utf-8
			['AV_example2_win-1250.STA'], // ascii
		];
	}

	public function testReadExample(): void
	{
		$file = new File(__DIR__ . '/data/AV_example.STA');
		$adv = $this->reader->read($file);

		// header information
		Assert::equal('CEKOCZPPAXXX 00000', $adv->getIdentification());
		Assert::equal('942', $adv->getType());
		Assert::equal('01', $adv->getPriority());
		Assert::equal('MAJITEL ÚČTU', $adv->getAccountOwner());
		Assert::equal('123456789', $adv->getAccountNumber());
		Assert::equal('CZKD0,', $adv->getDebitLimit());

		// transactions
		Assert::count(3, $adv->getTransactions());
		foreach ($adv->getTransactions() as $tr) {
			Assert::equal('2018-02-02', $tr->getDate()->format('Y-m-d'));
			Assert::equal('2018-02-02', $tr->getDateBooked()->format('Y-m-d'));
			Assert::equal('CZK', $tr->getAmount()->getCurrency()->getCode());
			Assert::null($tr->getExpenseDeductionDate());
		}

		// 1st transaction
		$tr = $adv->getTransactions()[0];
		Assert::equal('C', $tr->getBookType());
		Assert::equal('123', $tr->getAmount()->getAmount());
		Assert::equal('FMSC', $tr->getTransactionType());
		Assert::equal(' ', $tr->getClientReference());
		Assert::equal('9836465465487777', $tr->getBankReference());
		Assert::equal('', $tr->getCurrencyConversionDetails());
		Assert::true($tr->getPayment() instanceof IInlandPayment);
		/** @var IInlandPayment $inPay */
		$inPay = $tr->getPayment();
		Assert::equal('ZAUCT.PLATBA', $inPay->getBookItemType());
		Assert::equal('NAZEV PROTISTRANY', $inPay->getCounterPartyName());
		Assert::equal('000019-0000000019/0300', $inPay->getCounterPartyAccountNumber());
		Assert::equal('6666666666', $inPay->getVariableSymbol());
		Assert::equal('8888888888', $inPay->getSpecificSymbol());
		Assert::equal('9999', $inPay->getConstantSymbol());
		Assert::equal('prevod penez CZK text1prevod penez CZK text2(text 3)', $inPay->getMessage());

		// 2nd transaction
		$tr = $adv->getTransactions()[1];
		Assert::equal('D', $tr->getBookType());
		Assert::equal('234', $tr->getAmount()->getAmount());
		Assert::equal('NMSC', $tr->getTransactionType());
		Assert::equal('reference klienta', $tr->getClientReference());
		Assert::equal('565645645 0000', $tr->getBankReference());
		Assert::equal('USD0,11', $tr->getCurrencyConversionDetails());
		Assert::true($tr->getPayment() instanceof IForeignPayment);
		/** @var IForeignPayment $fPay */
		$fPay = $tr->getPayment();
		Assert::equal('ZAHRANICNI PLATBA', $fPay->getBookItemType());
		Assert::equal('PROTISTRANA', $fPay->getCounterparty());
		Assert::equal('PROTISTRANA_NAZEV/ADRESA ADRESAPOKRACOVANI', $fPay->getCounterpartyAddress());
		Assert::equal('CZ00190000000000000000019', $fPay->getCounterPartyIban());
		Assert::equal('CEKOCZPP', $fPay->getCounterpartySwift());
		Assert::equal('prevod USD 0,11 nekam jinamtext transakce druha cast', $fPay->getPurpose());
		Assert::equal(23.0, $fPay->getExchangeRate());
		Assert::equal('2', $fPay->getCounterpartyBankCharge()->getAmount());
		Assert::equal('USD', $fPay->getCounterpartyBankCharge()->getCurrency()->getCode());
		Assert::equal('0', $fPay->getBankCharge()->getAmount());
		Assert::equal('USD', $fPay->getBankCharge()->getCurrency()->getCode());

		// 3rd transaction
		$tr = $adv->getTransactions()[2];
		Assert::equal('D', $tr->getBookType());
		Assert::equal('1', $tr->getAmount()->getAmount());
		Assert::equal('NMSC', $tr->getTransactionType());
		Assert::equal(' ', $tr->getClientReference());
		Assert::equal('56564554444657554', $tr->getBankReference());
		Assert::equal('', $tr->getCurrencyConversionDetails());
		Assert::true($tr->getPayment() instanceof IOtherPayment);
		/** @var IOtherPayment $oPay */
		$oPay = $tr->getPayment();
		Assert::equal('Urok', $oPay->getBookItemType());
		Assert::equal('', $oPay->getCounterPartyName());
		Assert::equal('000000-0000000000/', $oPay->getCounterPartyAccountNumber());
		Assert::equal('', $oPay->getVariableSymbol());
		Assert::equal('', $oPay->getSpecificSymbol());
		Assert::equal('', $oPay->getConstantSymbol());
		Assert::equal('urok duben 2011', $oPay->getMessage());
	}

	public function testReadRealSingle(): void
	{
		$file = new File(__DIR__ . '/data/AV_226047602_20190225_0016.STA');
		$adv = $this->reader->read($file);

		// header information
		Assert::equal('CEKOCZPPAXXX 00000', $adv->getIdentification());
		Assert::equal('942', $adv->getType());
		Assert::equal('01', $adv->getPriority());
		Assert::equal('ASISTEAM S.R.O.', $adv->getAccountOwner());
		Assert::equal('0300/226047602', $adv->getAccountNumber());
		Assert::equal('CZKD0,', $adv->getDebitLimit());

		// transaction information
		Assert::count(1, $adv->getTransactions());
		Assert::true($adv->getTransactions()[0]->getPayment() instanceof IInlandPayment);
		/** @var IInlandPayment $inPay */
		$inPay = $adv->getTransactions()[0]->getPayment();
		Assert::equal('ZAUCT.PLATBA', $inPay->getBookItemType());
		Assert::equal('ZIKMUND RYSAVY', $inPay->getCounterPartyName());
		Assert::equal('000000-0181582348/0300', $inPay->getCounterPartyAccountNumber());
		Assert::equal('0225805852', $inPay->getVariableSymbol());
		Assert::equal('', $inPay->getSpecificSymbol());
		Assert::equal('', $inPay->getConstantSymbol());
		Assert::equal('', $inPay->getMessage());
	}

	public function testRealMultiple(): void
	{
		$file = new File(__DIR__ . '/data/AV_226047602_20190226_0002.STA');
		$adv = $this->reader->read($file);

		// header information
		Assert::equal('CEKOCZPPAXXX 00000', $adv->getIdentification());
		Assert::equal('942', $adv->getType());
		Assert::equal('01', $adv->getPriority());
		Assert::equal('ASISTEAM S.R.O.', $adv->getAccountOwner());
		Assert::equal('0300/226047602', $adv->getAccountNumber());
		Assert::equal('CZKD0,', $adv->getDebitLimit());

		// transaction information / check most useful pieces of information
		Assert::count(4, $adv->getTransactions());
		$curr = new Currency('CZK');

		$tr1 = $adv->getTransactions()[0];
		Assert::true($tr1->getAmount()->equals(new Money('592600', $curr)));
		Assert::equal('2019-02-26', $tr1->getDateBooked()->format('Y-m-d'));
		Assert::equal('0201602040', $tr1->getPayment()->getVariableSymbol());
		Assert::equal('000000-3015924567/0800', $tr1->getPayment()->getCounterpartyAccountNumber());
		Assert::equal('Nevrla Marketa', $tr1->getPayment()->getCounterpartyName());

		$tr2 = $adv->getTransactions()[1];
		Assert::true($tr2->getAmount()->equals(new Money('500900', $curr)));
		Assert::equal('2019-02-26', $tr2->getDateBooked()->format('Y-m-d'));
		Assert::equal('0201802076', $tr2->getPayment()->getVariableSymbol());
		Assert::equal('000000-1814929876/0800', $tr2->getPayment()->getCounterpartyAccountNumber());

		$tr3 = $adv->getTransactions()[2];
		Assert::true($tr3->getAmount()->equals(new Money('630000', $curr)));
		Assert::equal('2019-02-26', $tr3->getDateBooked()->format('Y-m-d'));
		Assert::equal('0201802071', $tr3->getPayment()->getVariableSymbol());
		Assert::equal('000000-1111232323/3030', $tr3->getPayment()->getCounterpartyAccountNumber());

		$tr4 = $adv->getTransactions()[3];
		Assert::true($tr4->getAmount()->equals(new Money('1600000', $curr)));
		Assert::equal('2019-02-26', $tr4->getDateBooked()->format('Y-m-d'));
		Assert::equal('0201702037', $tr4->getPayment()->getVariableSymbol());
		Assert::equal('000000-1819202122/0800', $tr4->getPayment()->getCounterpartyAccountNumber());
	}

}

(new Mt942ReaderTest())->run();
