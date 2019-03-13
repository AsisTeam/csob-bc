<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Advice\Impl\MT942;

final class LineFinder
{

	/**
	 * @param string[] $lines
	 */
	public static function find(array $lines, string $prefix): ?string
	{
		foreach ($lines as $line) {
			if (substr($line, 0, strlen($prefix)) === $prefix) {
				return substr($line, strlen($prefix));
			}
		}

		return null;
	}

	/**
	 * @param string[] $lines
	 */
	public static function get(array $lines, string $prefix): string
	{
		$match = self::find($lines, $prefix);

		if ($match === null || $match === '.') {
			return '';
		}

		return $match;
	}

}
