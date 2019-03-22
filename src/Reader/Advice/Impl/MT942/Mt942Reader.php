<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Advice\Impl\MT942;

use AsisTeam\CSOBBC\Entity\Advice\IAdvice;
use AsisTeam\CSOBBC\Entity\Advice\IAdvisedTransaction;
use AsisTeam\CSOBBC\Entity\IFile;
use AsisTeam\CSOBBC\Exception\Runtime\ReaderException;
use AsisTeam\CSOBBC\Reader\Advice\IAdviceReader;
use AsisTeam\CSOBBC\Reader\Advice\Impl\MT942\Entity\Advice;
use AsisTeam\CSOBBC\Reader\Advice\Impl\MT942\Entity\AdvisedTransaction;
use AsisTeam\CSOBBC\Reader\Advice\Impl\MT942\Entity\Payment\AdvisedForeignPayment;
use AsisTeam\CSOBBC\Reader\Advice\Impl\MT942\Entity\Payment\AdvisedInlandPayment;
use AsisTeam\CSOBBC\Reader\Advice\Impl\MT942\Entity\Payment\AdvisedOtherPayment;
use DateTime;
use DateTimeImmutable;
use Money\Currency;
use Money\Money;

final class Mt942Reader implements IAdviceReader
{

	private const ACCOUNT_OWNER_PREFIX          = ':20:';
	private const ACCOUNT_NUMBER_PREFIX         = ':25:';
	private const ACCOUNT_DEBIT_LIMIT           = ':34F:';
	private const TRANSACTION_BASIC_PREFIX      = ':61:';
	private const TRANSACTION_DETAIL_PREFIX     = ':86:';
	private const TRANSACTION_ADDITIONAL_PREFIX = '/OCMT/';

	public function read(IFile $file): IAdvice
	{
		$advice = new Advice();
		$lines = $this->readLines($file);

		$this->parseHeader($advice, $lines);
		$clusters = $this->clusterTransactionLines($lines);
		foreach ($clusters as $cluster) {
			$advice->addTransaction($this->parseTransaction($cluster));
		}

		return $advice;
	}

	/**
	 * @return string[]
	 */
	public function readLines(IFile $file): array
	{
		// CSOB sends files in CP1250 encoding, convert it before reading
		$content = iconv('CP1250', 'utf-8', $file->getContent());
		if ($content === false) {
			return [];
		}

		$lines = preg_split("/\\r\\n|\\r|\\n/", $content);

		if ($lines === false) {
			throw new ReaderException('Could not read lines');
		}

		// avoid empty lines
		$cleanLines = [];
		foreach ($lines as $key => $line) {
			if ($line !== '') {
				$cleanLines[] = $line;
			}
		}

		return $cleanLines;
	}

	/**
	 * @param string[] $lines
	 */
	private function parseHeader(Advice $advice, array $lines): void
	{
		$advice->setIdentification($lines[0]);

		$typePrior = explode(' ', $lines[1]);
		$advice->setType($typePrior[0] ?? '');
		$advice->setPriority($typePrior[1] ?? '');

		$advice->setAccountOwner(LineFinder::get($lines, self::ACCOUNT_OWNER_PREFIX));
		$advice->setAccountNumber(LineFinder::get($lines, self::ACCOUNT_NUMBER_PREFIX));
		$advice->setDebitLimit(LineFinder::get($lines, self::ACCOUNT_DEBIT_LIMIT));
	}

	/**
	 * @param string[] $lines
	 * @return string[][]
	 */
	private function clusterTransactionLines(array $lines): array
	{
		$clustered = [];
		$curr      = -1;

		// check if it's Transaction basic line and add to the cluster until next tran. basic line occurs
		foreach ($lines as $key => $line) {
			if (substr($line, 0, strlen(self::TRANSACTION_BASIC_PREFIX)) === self::TRANSACTION_BASIC_PREFIX) {
				$curr++;
				$clustered[$curr] = [];
			}

			if ($curr < 0) {
				continue;
			}

			$clustered[$curr][] = $line;
		}

		return $clustered;
	}

	/**
	 * @param string[] $lines
	 */
	private function parseTransaction(array $lines): IAdvisedTransaction
	{
		$tr = new AdvisedTransaction($lines);

		$basic = LineFinder::find($lines, self::TRANSACTION_BASIC_PREFIX);
		if ($basic === null) {
			throw new ReaderException('Cannot find basic line of advice transaction');
		}

		$this->populateTransactionBasicLine($basic, $tr);

		$add = LineFinder::find($lines, self::TRANSACTION_ADDITIONAL_PREFIX);
		if ($add !== null) {
			$this->populateTransactionAdditionalLine($add, $tr);
		}

		$detail = LineFinder::find($lines, self::TRANSACTION_DETAIL_PREFIX);
		if ($detail === null) {
			throw new ReaderException('Cannot find detail line of advice transaction');
		}
		$this->populatePayment($lines, $tr);

		return $tr;
	}

	private function populateTransactionBasicLine(string $basic, AdvisedTransaction $tr): IAdvisedTransaction
	{
		$date = substr($basic, 0, 6);
		$year = '20' . substr($date, 0, 2);

		$tr->setDate(
			$this->createImDate((int) $year, (int) substr($date, 2, 2), (int) substr($date, 4, 2))
		);

		$dateB = substr($basic, 6, 4);
		$tr->setDateBooked(
			$this->createImDate((int) $year, (int) substr($dateB, 0, 2), (int) substr($dateB, 2, 2))
		);

		if (substr($basic, 10, 1) === 'R') {
			$bType = substr($basic, 10, 2);
			$rest  = substr($basic, 12);
		} else {
			$bType = substr($basic, 10, 1);
			$rest  = substr($basic, 11);
		}
		$tr->setBookType($bType);

		$rest   = explode(',', $rest);
		$amount = $rest[0] . substr($rest[1], 0, 2);
		$tr->setAmount(new Money((int) $amount, new Currency('CZK')));

		$trType = substr($rest[1], 2, 4);
		$tr->setTransactionType($trType);

		$rest  = substr($rest[1], 6);
		$refId = explode('//', $rest);
		$tr->setClientReference($refId[0]);
		$tr->setBankReference($refId[1]);

		return $tr;
	}

	private function populateTransactionAdditionalLine(string $add, AdvisedTransaction $tr): AdvisedTransaction
	{
		$tr->setCurrencyConversionDetails(substr($add, 0, 29));

		$expDate = substr($add, 29, 6);
		if ($expDate === false) {
			return $tr;
		}

		$year = '20' . substr($expDate, 0, 2);
		$tr->setExpenseDeductionDate(
			$this->createImDate((int) $year, (int) substr($expDate, 2, 2), (int) substr($expDate, 4, 2))
		);

		return $tr;
	}

	/**
	 * @param string[] $lines
	 */
	private function populatePayment(array $lines, AdvisedTransaction $tr): AdvisedTransaction
	{
		// create single string containing all lines
		$single = $this->createDetailSingleLine($lines);
		$type = substr($single, 4, 3);
		$single = substr($single, 7);

		// create separate lines for each payment info
		$payLines = explode('?', $single);
		$payLines = array_map(function ($val) {
			return '?' . $val;
		}, $payLines);

		switch ($type) {
			case '111':
				$tr->setPayment(AdvisedInlandPayment::createFromLines($payLines));
				break;
			case '030':
				$tr->setPayment(AdvisedForeignPayment::createFromLines($payLines));
				break;
			case '040':
				$tr->setPayment(AdvisedOtherPayment::createFromLines($payLines));
				break;
			default:
				throw new ReaderException(sprintf('Unknown payment type "%s"', $type));
		}

		return $tr;
	}

	/**
	 * @param string[] $lines
	 */
	private function createDetailSingleLine(array $lines): string
	{
		$detailStarted = false;
		$rest          = '';

		foreach ($lines as $line) {
			if (substr($line, 0, strlen(self::TRANSACTION_DETAIL_PREFIX)) === self::TRANSACTION_DETAIL_PREFIX) {
				$detailStarted = true;
			}

			if ($detailStarted) {
				$rest .= $line;
			}
		}

		return $rest;
	}

	private function createImDate(int $year, int $month, int $day): DateTimeImmutable
	{
		$d = new DateTime();
		$d->setDate($year, $month, $day);

		return new DateTimeImmutable($d->format('Y-m-d'));
	}

}
