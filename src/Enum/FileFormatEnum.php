<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Enum;

final class FileFormatEnum
{

	public const ABO      = 'ABO';
	public const DUZ      = 'DUZ';
	public const MC_TPS   = 'MC TPS';
	public const MC_ZPS   = 'MC ZPS';
	public const TXT_TPS  = 'TXT TPS';
	public const TXT_ZPS  = 'TXT ZPS';
	public const XLS_TPS  = 'XLS TPS';
	public const XLS_ZPS  = 'XLS ZPS';
	public const XLSX_TPS = 'XLSX TPS';
	public const XLSX_ZPS = 'XLSX ZPS';
	public const MT101    = 'MT101';
	public const SEPA_XML = 'SEPA_XML';

	public static function isValid(string $value): bool
	{
		return $value === self::ABO ||
			$value === self::DUZ ||
			$value === self::MC_TPS ||
			$value === self::MC_ZPS ||
			$value === self::TXT_TPS ||
			$value === self::TXT_ZPS ||
			$value === self::XLS_TPS ||
			$value === self::XLS_ZPS ||
			$value === self::XLSX_TPS ||
			$value === self::XLSX_ZPS ||
			$value === self::MT101 ||
			$value === self::SEPA_XML;
	}

}
