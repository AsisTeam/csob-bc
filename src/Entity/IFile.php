<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Entity;

use DateTimeImmutable;

interface IFile
{

	public function getFileName(): string;
	public function getHash(): string;
	public function getSize(): int;
	public function getFormat(): string;
	public function getUploadMode(): string;
	public function getType(): ?string;
	public function getSeparator(): ?string;
	public function getCreated(): DateTimeImmutable;
	public function getStatus(): string;
	public function setStatus(string $status): void;
	public function getDownloadUrl(): ?string;
	public function setDownloadUrl(?string $url): void;
	public function getUploadUrl(): ?string;
	public function setUploadUrl(?string $url): void;
	public function getContent(): string;
	public function setContent(?string $content): void;
	public function setUpload(?Upload $upload): void;
	public function getUpload(): ?Upload;

}
