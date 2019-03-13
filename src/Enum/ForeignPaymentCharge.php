<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Enum;

final class ForeignPaymentCharge
{

	public const CHARGE_SHA = 'SHA';
	public const CHARGE_OUR = 'OUR';
	public const CHARGE_BEN = 'BEN';

	public static function isValid(string $type): bool
	{
		return $type === self::CHARGE_SHA ||
			$type === self::CHARGE_OUR ||
			$type === self::CHARGE_BEN;
	}

}
