<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Advice;

use AsisTeam\CSOBBC\Entity\Advice\IAdvice;
use AsisTeam\CSOBBC\Entity\IFile;

interface IAdviceReader
{

	public function read(IFile $file): IAdvice;

}
