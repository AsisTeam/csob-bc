<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Report\Impl\XmlCba;

use AsisTeam\CSOBBC\Entity\AccountStatement\IAccountStatementMessage;
use AsisTeam\CSOBBC\Entity\IFile;
use AsisTeam\CSOBBC\Exception\Runtime\ReaderException;
use AsisTeam\CSOBBC\Reader\Report\IAccountStatementReader;
use AsisTeam\CSOBBC\Reader\Report\Impl\XmlCba\Entity\AccountStatement;
use Genkgo\Camt\Config;
use Genkgo\Camt\DTO\Message;
use Genkgo\Camt\Exception\ReaderException as CamtReaderException;
use Genkgo\Camt\Reader;

final class XmlCbaReader implements IAccountStatementReader
{

	/** @var Reader */
	private $camtReader;

	public function __construct()
	{
		$this->camtReader = new Reader(Config::getDefault());
	}

	public function read(IFile $file): IAccountStatementMessage
	{
		try {
			$message = $this->camtReader->readString($file->getContent());
		} catch (CamtReaderException $e) {
			throw new ReaderException($e->getMessage(), $e->getCode(), $e);
		}

		if (!($message instanceof Message)) {
			throw new ReaderException(sprintf('Expecting reader Message object but "%s" given', get_class($message)));
		}

		return new AccountStatement($message);
	}

}
