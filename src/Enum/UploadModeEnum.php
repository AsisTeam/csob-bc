<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Enum;

final class UploadModeEnum
{

	public const INCLUDE_INCORRECT     = 'IncludeIncorrect';
	public const ONLY_CORRECT          = 'OnlyCorrect';
	public const ALL_OR_NOTHING        = 'AllOrNothing';
	public const SIGNED_ALL_OR_NOTHING = 'SignedAllOrNothing';

	public static function isValid(string $mode): bool
	{
		return $mode === self::INCLUDE_INCORRECT ||
			$mode === self::ONLY_CORRECT ||
			$mode === self::ALL_OR_NOTHING ||
			$mode === self::SIGNED_ALL_OR_NOTHING;
	}

}
