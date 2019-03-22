<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Import\Impl\XmlCsob;

use AsisTeam\CSOBBC\Entity\IFile;
use AsisTeam\CSOBBC\Entity\ImportProtocol\IImportProtocol;
use AsisTeam\CSOBBC\Exception\Runtime\ReaderException;
use AsisTeam\CSOBBC\Reader\Import\IImportProtocolReader;
use AsisTeam\CSOBBC\Reader\Import\Impl\XmlCsob\Entity\CsobImportProtocol;
use DOMDocument;
use SimpleXMLElement;
use Throwable;

final class ImprotReader implements IImportProtocolReader
{

	private const SCHEMA = __DIR__ . '/improt-schema.xsd';

	public function read(IFile $file): IImportProtocol
	{
		$xml = new DOMDocument();
		$xml->loadXML($file->getContent());

		if (!$xml->schemaValidate(self::SCHEMA)) {
			throw new ReaderException('Given IMPROT file is not valid against CSOB XSD.');
		}

		try {
			libxml_use_internal_errors(true);
			$xml = new SimpleXMLElement($file->getContent());

			return CsobImportProtocol::fromXml($xml);
		} catch (Throwable $e) {
			throw new ReaderException(sprintf('Unable to parse Csob xml import protocol. Error: %s', $e));
		}
	}

}
