<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Enum;

final class PaymentOrderType
{

	public const WITH_PRIORITY = '01'; // prioritní příkazy k úhradě
	public const COMMON        = '11'; // běžné příkazy k úhradě
	public const CASHING       = '32'; // Inkaso

	public static function isValid(string $type): bool
	{
		return $type === self::WITH_PRIORITY ||
			$type === self::COMMON ||
			$type === self::CASHING;
	}

}
