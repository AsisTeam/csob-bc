<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Entity;

final class Upload
{

	/** @var string */
	private $fileId;

	/** @var string */
	private $fileName;

	public function __construct(string $fileId, string $fileName)
	{
		$this->fileId   = $fileId;
		$this->fileName = $fileName;
	}

	public function getFileId(): string
	{
		return $this->fileId;
	}

	public function getFileName(): string
	{
		return $this->fileName;
	}

}
