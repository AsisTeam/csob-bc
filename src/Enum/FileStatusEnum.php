<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Enum;

final class FileStatusEnum
{

	public const ALREADY_IMPORTED   = 'R';
	public const DOWNLOAD_AVAILABLE = 'D';
	public const PERMANENT_ERROR    = 'F';
	public const UPLOAD_AVAILABLE   = 'U';

}
