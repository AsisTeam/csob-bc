<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Report\Impl\XmlCsob;

use AsisTeam\CSOBBC\Entity\IFile;
use AsisTeam\CSOBBC\Entity\Report\IReport;
use AsisTeam\CSOBBC\Exception\Runtime\ReaderException;
use AsisTeam\CSOBBC\Reader\Report\Impl\XmlCsob\Entity\Report;
use AsisTeam\CSOBBC\Reader\Report\IReportReader;
use Money\Currency;
use Money\Money;
use SimpleXMLElement;
use Throwable;

final class XmlCsobReader implements IReportReader
{

	public function read(IFile $file): IReport
	{
		try {
			libxml_use_internal_errors(true);
			$xml = new SimpleXMLElement($file->getContent());
			return Report::fromXml($xml);
		} catch (Throwable $e) {
			throw new ReaderException(sprintf('Unable to parse Csob xml report. Error: %s', $e));
		}
	}

	public static function createMoney(string $amount, string $currency): Money
	{
		$amount = str_replace(',', '', $amount);
		$amount = str_replace('+', '', $amount);
		$amount = str_replace('-', '', $amount);
		$amount = \ltrim($amount, '0');

		return new Money($amount, new Currency($currency));
	}

}
