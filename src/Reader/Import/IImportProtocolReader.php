<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Import;

use AsisTeam\CSOBBC\Entity\IFile;
use AsisTeam\CSOBBC\Entity\ImportProtocol\IImportProtocol;

interface IImportProtocolReader
{

	public function read(IFile $file): IImportProtocol;

}
