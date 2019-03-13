<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Tests\Cases\Unit\Reader\Report\Impl\XmlCsob;

use AsisTeam\CSOBBC\Entity\File;
use AsisTeam\CSOBBC\Reader\Report\Impl\XmlCsob\XmlCsobReader;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../../../../../bootstrap.php';

final class XmlCsobReaderTest extends TestCase
{

	/** @var XmlCsobReader */
	private $reader;

	public function setUp(): void
	{
		$this->reader = new XmlCsobReader();
	}

	public function testRead(): void
	{
		$file = new File(__DIR__ . '/data/226047602_20190220_5_DCZB.xml');
		$report = $this->reader->read($file);

		Assert::equal('5', $report->getSerialNo());
		Assert::equal('226047602/0300', $report->getAccountNo());
		Assert::equal('AsisTeam s.r.o.', $report->getAccountOwner());
		Assert::equal('D - denní', $report->getFrequency());
		Assert::equal('2019-02-20', $report->getDateStart()->format('Y-m-d'));
		Assert::equal('2019-02-20', $report->getDateEnd()->format('Y-m-d'));
		Assert::equal('71270497', $report->getAmountStart()->getAmount());
		Assert::equal('CZK', $report->getAmountStart()->getCurrency()->getCode());
		Assert::equal('45516838', $report->getAmountEnd()->getAmount());
		Assert::equal('CZK', $report->getAmountEnd()->getCurrency()->getCode());

		Assert::count(3, $report->getEntries());

		// check 1st payment
		$pmnt = $report->getEntries()[0];
		Assert::true($pmnt->isIncoming());
		Assert::true($pmnt->isTypeInland());
		Assert::equal('SBAX468232', $pmnt->getId());
		Assert::equal('187983987', $pmnt->getAccountNo());
		Assert::equal('0300', $pmnt->getAccountBank());
		Assert::equal('DOBRY PLATCE', $pmnt->getAccountOwner());
		Assert::equal('půjčka', $pmnt->getMessage());
		Assert::equal('816500', $pmnt->getAmount()->getAmount());
		Assert::equal('CZK', $pmnt->getAmount()->getCurrency()->getCode());

		// check 2nd payment
		$pmnt = $report->getEntries()[1];
		Assert::true($pmnt->isOutgoing());
		Assert::true($pmnt->isTypeInland());
		Assert::equal('IBAW6E6883', $pmnt->getId());
		Assert::equal('12345678', $pmnt->getAccountNo());
		Assert::equal('0300', $pmnt->getAccountBank());
		Assert::equal('stavební bytové družstvo', $pmnt->getAccountOwner());
		Assert::equal('Zálohy - stavební bytové družstvo', $pmnt->getMessage());
		Assert::equal('172100', $pmnt->getAmount()->getAmount());
		Assert::equal('CZK', $pmnt->getAmount()->getCurrency()->getCode());

		// check 2nd payment
		$pmnt = $report->getEntries()[2];
		Assert::true($pmnt->isOutgoing());
		Assert::true($pmnt->isTypeInland());
		Assert::equal('CB000AISRY', $pmnt->getId());
		Assert::equal('284812111', $pmnt->getAccountNo());
		Assert::equal('0300', $pmnt->getAccountBank());
		Assert::equal('1021316', $pmnt->getVariableSymbol());
		Assert::equal('JUDr. Jmeno Prijmeni', $pmnt->getAccountOwner());
		Assert::equal('vyplata JUDR', $pmnt->getMessage());
		Assert::equal('4302700', $pmnt->getAmount()->getAmount());
		Assert::equal('CZK', $pmnt->getAmount()->getCurrency()->getCode());
	}

}

(new XmlCsobReaderTest())->run();
