<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Request;

use AsisTeam\CSOBBC\Enum\FileTypeEnum;
use AsisTeam\CSOBBC\Exception\Logical\InvalidArgumentException;
use DateTimeImmutable;

final class Filter
{

	/** @var string[]|null */
	private $fileTypes;

	/** @var string|null */
	private $fileName;

	/** @var DateTimeImmutable|null */
	private $createdBefore;

	/** @var DateTimeImmutable|null */
	private $createdAfter;

	/** @var string|null */
	private $clientAppGuid;

	/**
	 * @return string[]
	 */
	public function getFileTypes(): ?array
	{
		return $this->fileTypes;
	}

	/**
	 * @param string[] $fileTypes
	 */
	public function setFileTypes(array $fileTypes): void
	{
		foreach ($fileTypes as $type) {
			if (!FileTypeEnum::isValid($type)) {
				throw new InvalidArgumentException(sprintf('Invalid filter\'s file type %s given.', $type));
			}
		}

		$this->fileTypes = $fileTypes;
	}

	public function getFileName(): ?string
	{
		return $this->fileName;
	}

	public function setFileName(?string $fileName): void
	{
		$this->fileName = $fileName;
	}

	public function getCreatedBefore(): ?DateTimeImmutable
	{
		return $this->createdBefore;
	}

	public function setCreatedBefore(?DateTimeImmutable $createdBefore): void
	{
		$this->createdBefore = $createdBefore;
	}

	public function getCreatedAfter(): ?DateTimeImmutable
	{
		return $this->createdAfter;
	}

	public function setCreatedAfter(?DateTimeImmutable $createdAfter): void
	{
		$this->createdAfter = $createdAfter;
	}

	public function getClientAppGuid(): ?string
	{
		return $this->clientAppGuid;
	}

	public function setClientAppGuid(?string $clientAppGuid): void
	{
		$this->clientAppGuid = $clientAppGuid;
	}

}
