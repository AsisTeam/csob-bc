<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Entity;

use AsisTeam\CSOBBC\Enum\FileFormatEnum;
use AsisTeam\CSOBBC\Enum\FileSeparatorEnum;
use AsisTeam\CSOBBC\Enum\FileTypeEnum;
use AsisTeam\CSOBBC\Enum\UploadModeEnum;
use AsisTeam\CSOBBC\Exception\LogicalException;
use AsisTeam\CSOBBC\Exception\RuntimeException;
use DateTimeImmutable;

final class File implements IFile
{

	/** @var string */
	private $fileName;

	/** @var int */
	private $size = 0;

	/** @var string */
	private $hash = '';

	/** @var string */
	private $format;

	/** @var string */
	private $uploadMode;

	/** @var string */
	private $type;

	/** @var string */
	private $status;

	/** @var DateTimeImmutable */
	private $created;

	/** @var string|null */
	private $separator;

	/** @var string|null */
	private $downloadUrl;

	/** @var string|null */
	private $uploadUrl;

	/** @var string|null */
	private $location;

	/** @var string|null */
	private $content;

	/** @var Upload|null */
	private $upload;

	public function __construct(?string $location = null, ?string $fileName = null)
	{
		if ($location !== null) {
			$this->location = $location;
			$this->setFileInfo($location, $fileName);
		}

		$this->created = new DateTimeImmutable();
	}

	public function setFileName(string $fileName): self
	{
		$this->fileName = $fileName;

		return $this;
	}

	public function setFormat(string $format): self
	{
		$this->assertFormat($format);
		$this->format = $format;

		return $this;
	}

	public function setUploadMode(string $uploadMode): self
	{
		$this->assertUploadMode($uploadMode);
		$this->uploadMode = $uploadMode;

		return $this;
	}

	public function setSeparator(string $separator): self
	{
		$this->assertSeparator($separator);
		$this->separator = $separator;

		return $this;
	}

	public function setCreated(DateTimeImmutable $created): self
	{
		$this->created = $created;

		return $this;
	}

	public function setType(string $type): self
	{
		$this->assertType($type);
		$this->type = $type;

		return $this;
	}

	public function setSize(int $size): self
	{
		$this->size = $size;

		return $this;
	}

	public function setHash(string $hash): self
	{
		$this->hash = $hash;

		return $this;
	}

	public function setStatus(string $status): self
	{
		$this->status = $status;

		return $this;
	}

	public function setDownloadUrl(?string $url): self
	{
		if ($url !== null) {
			$url = trim($url);
		}

		$this->downloadUrl = $url;

		return $this;
	}

	public function setUploadUrl(?string $url): self
	{
		if ($url !== null) {
			$url = trim($url);
		}

		$this->uploadUrl = $url;

		return $this;
	}

	public function setLocation(?string $location): self
	{
		$this->location = $location;

		return $this;
	}

	public function setContent(?string $content): self
	{
		$this->content = $content;

		return $this;
	}

	public function setUpload(?Upload $upload): self
	{
		$this->upload = $upload;

		return $this;
	}

	public function getFileName(): string
	{
		return $this->fileName ?? '';
	}

	public function getHash(): string
	{
		return $this->hash;
	}

	public function getSize(): int
	{
		return $this->size;
	}

	public function getFormat(): string
	{
		$this->assertFormat($this->format);

		return $this->format;
	}

	public function getUploadMode(): string
	{
		$this->assertUploadMode($this->uploadMode);

		return $this->uploadMode;
	}

	public function getType(): ?string
	{
		$this->assertType($this->type);

		return $this->type;
	}

	public function getSeparator(): ?string
	{
		if ($this->separator !== null) {
			$this->assertSeparator($this->separator);
		}
		return $this->separator;
	}

	public function getCreated(): DateTimeImmutable
	{
		return $this->created;
	}

	public function getStatus(): string
	{
		return $this->status;
	}

	public function getDownloadUrl(): ?string
	{
		return $this->downloadUrl;
	}

	public function getUploadUrl(): ?string
	{
		return $this->uploadUrl;
	}

	public function getUpload(): ?Upload
	{
		return $this->upload;
	}

	private function assertFormat(string $format): void
	{
		if (!FileFormatEnum::isValid($format)) {
			throw new LogicalException(sprintf('Invalid file format "%s" given', $format));
		}
	}

	private function assertUploadMode(string $mode): void
	{
		if (!UploadModeEnum::isValid($mode)) {
			throw new LogicalException(sprintf('Invalid file upload mode "%s" given', $mode));
		}
	}

	private function assertSeparator(string $sep): void
	{
		if (!FileSeparatorEnum::isValid($sep)) {
			throw new LogicalException(sprintf('Invalid file separator "%s" given', $sep));
		}
	}

	private function assertType(string $type): void
	{
		if (!FileTypeEnum::isValid($type)) {
			throw new LogicalException(sprintf('Invalid file type "%s" given', $type));
		}
	}

	private function setFileInfo(string $location, ?string $fileName = null): void
	{
		if (!file_exists($location)) {
			throw new LogicalException(sprintf('Creating File instance for non-existing file: %s', $location));
		}

		$this->hash = hash_file('md5', $location);

		$size = filesize($location);
		if ($size === false) {
			throw new RuntimeException(sprintf('Cannot get size of file: %s', $location));
		}

		$this->size = $size;

		$contents = file_get_contents($location);
		if ($contents === false) {
			throw new RuntimeException(sprintf('Unable to get contents of file "%s"', $this->location));
		}

		$this->content = $contents;

		if ($fileName === null) {
			$fileName = basename($location);
		}

		$this->fileName = $fileName;
	}

	public function getContent(): string
	{
		if ($this->content !== null) {
			return $this->content;
		}

		if ($this->location === null) {
			throw new RuntimeException('Cannot get contents of undefined file. No file location specified');
		}

		$contents = file_get_contents($this->location);

		if ($contents === false) {
			throw new RuntimeException(sprintf('Unable to get contents of file "%s"', $this->location));
		}

		return $contents;
	}

	public function getLocation(): ?string
	{
		return $this->location;
	}

}
