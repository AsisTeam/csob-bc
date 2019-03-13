<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Report;

use AsisTeam\CSOBBC\Entity\IFile;
use AsisTeam\CSOBBC\Entity\Report\IReport;

interface IReportReader
{

	public function read(IFile $file): IReport;

}
