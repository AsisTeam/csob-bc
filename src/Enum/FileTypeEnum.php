<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Enum;

final class FileTypeEnum
{

	public const VYPIS  = 'VYPIS';
	public const AVIZO  = 'AVIZO';
	public const KURZY  = 'KURZY';
	public const IMPORT = 'IMPORT';

	public static function isValid(string $value): bool
	{
		return $value === self::VYPIS ||
			$value === self::AVIZO ||
			$value === self::KURZY ||
			$value === self::IMPORT;
	}

}
