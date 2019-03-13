<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Enum;

final class FileSeparatorEnum
{

	public static function isValid(string $sep): bool
	{
		return $sep === '|' ||
			$sep === '/' ||
			$sep === ':' ||
			$sep === '::' ||
			$sep === ';' ||
			$sep === ';;';
	}

}
