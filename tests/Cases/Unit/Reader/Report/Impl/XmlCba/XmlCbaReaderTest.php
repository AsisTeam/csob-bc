<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Tests\Cases\Unit\Reader\Report\Impl\XmlCba;

use AsisTeam\CSOBBC\Entity\File;
use AsisTeam\CSOBBC\Reader\Report\Impl\XmlCba\XmlCbaReader;
use Money\Currency;
use Money\Money;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../../../../../bootstrap.php';

final class XmlCbaReaderTest extends TestCase
{

	/** @var XmlCbaReader */
	private $reader;

	public function setUp(): void
	{
		$this->reader = new XmlCbaReader();
	}

	public function testRead(): void
	{
		$file = new File(__DIR__ . '/data/xml_cba_cz_vzorovy_vypis_2016_v4.xml');
		$msg = $this->reader->read($file);

		// check group header
		$hdr = $msg->getGroupHeader();
		Assert::equal('camt.053-2011-03-31-001', $hdr->getMessageId());
		Assert::equal('2011-03-31', $hdr->getCreatedOn()->format('Y-m-d'));
		Assert::equal('Petr Novotny', $hdr->getMessageRecipient()->getName());
		Assert::equal('Praha', $hdr->getMessageRecipient()->getAddress()->getTownName());
		Assert::equal('Dobrovolniku', $hdr->getMessageRecipient()->getAddress()->getStreetName());
		Assert::null($hdr->getMessageRecipient()->getEmailAddress());
		Assert::null($hdr->getMessageRecipient()->getMobileNumber());
		Assert::equal('1', $hdr->getPagination()->getPageNumber());
		Assert::true($hdr->getPagination()->isLastPage());
		Assert::equal('Mesicni', $hdr->getFrequency());

		// check statement
		Assert::count(1, $msg->getStatements());
		$stmt = $msg->getStatements()[0];

		Assert::equal('CZ5203000000192000145399-2012-03-31', $stmt->getId());
		Assert::equal('1', $stmt->getElectronicSequenceNumber());
		Assert::equal('1', $stmt->getLegalSequenceNumber());
		Assert::equal('2011-03-31 17:30:47', $stmt->getCreatedOn()->format('Y-m-d H:i:s'));
		Assert::equal('2011-03-01 06:00:00', $stmt->getFromDate()->format('Y-m-d H:i:s'));
		Assert::equal('2011-03-31 17:30:47', $stmt->getToDate()->format('Y-m-d H:i:s'));
		Assert::equal('CZ5203000000192000145399', $stmt->getAccount());

		// check entries
		Assert::count(10, $stmt->getEntries());
		Assert::count(10, $msg->getEntries());
		$entry = $stmt->getEntries()[0];
		Assert::equal('13587', $entry->getReference());
		Assert::true($entry->getAmount()->equals(new Money('100000', new Currency('CZK'))));
		Assert::false($entry->getReversalIndicator());
		Assert::equal(0, $entry->getIndex());
		Assert::null($entry->getBatchPaymentId());
		Assert::null($entry->getTotalChargesAndTaxAmount());
		Assert::equal('', $entry->getAdditionalInfo());
		Assert::null($entry->getAccountServicerReference());
		Assert::equal('', $entry->getDomainBankTransactionCode());
		Assert::equal('10000107000', $entry->getProprietaryBankTransactionCode());
		Assert::equal('2011-03-01', $entry->getBookingDate()->format('Y-m-d'));
		Assert::equal('2011-03-01', $entry->getValueDate()->format('Y-m-d'));
		Assert::equal([], $entry->getChargeRecords());
		Assert::null($entry->getTotalChargesAndTaxAmount());

		// Transaction details
		Assert::count(1, $entry->getTransactionDetails());
		$trx = $entry->getTransactionDetails()[0];
		Assert::null($trx->getAmount());

		Assert::count(1, $trx->getReferences());
		Assert::equal('123487', $trx->getReferences()[0]->getMessageId());
		Assert::equal('10000107000', $trx->getProprietaryBankTransactionCode());
		Assert::equal('CBA', $trx->getProprietaryBankTransactionIssuer());

		Assert::count(1, $trx->getRelatedParties());
		Assert::equal('0080109999999999', $trx->getRelatedParties()[0]->getAccount());
		Assert::equal('Jan Novak', $trx->getRelatedParties()[0]->getName());
		Assert::null($trx->getRelatedParties()[0]->getAddress());

		Assert::count(1, $trx->getRelatedAgents());
		Assert::equal('KOMBCZPP', $trx->getRelatedAgents()[0]->getBic());
		Assert::equal('', $trx->getRelatedAgents()[0]->getName());

		Assert::equal(
			'DOC. NM1910400032142015 Spotrebni material 0000022294 Tesneni polanka s.r.o.',
			$trx->getRemittanceMessage()
		);
		Assert::equal('', $trx->getRemittanceCreditorReference());
	}

}

(new XmlCbaReaderTest())->run();
