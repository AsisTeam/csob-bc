<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Tests\Cases\Unit\Reader\Import\Impl\XmlCsob;

use AsisTeam\CSOBBC\Entity\File;
use AsisTeam\CSOBBC\Reader\Import\Impl\XmlCsob\ImprotReader;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../../../../../bootstrap.php';

final class ImprotReaderTest extends TestCase
{

	/** @var ImprotReader */
	private $reader;

	public function setUp(): void
	{
		$this->reader = new ImprotReader();
	}

	public function testReadAllOk(): void
	{
		$file   = new File(__DIR__ . '/data/example_all_ok.xml');
		$improt = $this->reader->read($file);

		Assert::equal('20190322093137', $improt->getId());
		Assert::equal('2019-03-22', $improt->getCreated()->format('Y-m-d'));
		Assert::equal('1476568', $improt->getCebImportId());
		Assert::equal('12345', $improt->getTotalAmount()->getAmount());
		Assert::equal('ACCP', $improt->getStatus());
		Assert::true($improt->isOk());

		Assert::count(1, $improt->getAllTransactions());
		Assert::count(0, $improt->getInvalidTransactions());

		$tr = $improt->getAllTransactions()[0];
		Assert::true($tr->isOk());
		Assert::equal('ACCP', $tr->getStatus());
		Assert::equal('12345', $tr->getAmount()->getAmount());
		Assert::equal('CZK', $tr->getAmount()->getCurrency()->getCode());
		Assert::equal('', $tr->getError());
		Assert::equal('226047602', $tr->getSenderAccount());
		Assert::equal('430014680297', $tr->getCounterpartyAccount());
		Assert::equal('0100', $tr->getCounterpartyBank());
	}

	public function testReadWithError(): void
	{
		$file   = new File(__DIR__ . '/data/example_one_rejected.xml');
		$improt = $this->reader->read($file);

		Assert::false($improt->isOk());
		Assert::count(2, $improt->getAllTransactions());
		Assert::count(1, $improt->getInvalidTransactions());

		$tr = $improt->getInvalidTransactions()[0];
		Assert::false($tr->isOk());
		Assert::equal('RJCT', $tr->getStatus());
		Assert::equal('065525 Účet klienta nebyl nalezen v evidenci účtů.', $tr->getError());
		Assert::equal('12345', $tr->getAmount()->getAmount());
		Assert::equal('CZK', $tr->getAmount()->getCurrency()->getCode());
		Assert::equal('', $tr->getSenderAccount());
		Assert::equal('430014680297', $tr->getCounterpartyAccount());
		Assert::equal('0100', $tr->getCounterpartyBank());
	}

}

(new ImprotReaderTest())->run();
