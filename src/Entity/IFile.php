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

	/**
	 * @return mixed
	 */
	public function setStatus(string $status);

	public function getDownloadUrl(): ?string;

	/**
	 * @return mixed
	 */
	public function setDownloadUrl(?string $url);

	public function getUploadUrl(): ?string;

	/**
	 * @return mixed
	 */
	public function setUploadUrl(?string $url);

	public function getContent(): string;

	/**
	 * @return mixed
	 */
	public function setContent(?string $content);

	/**
	 * @return mixed
	 */
	public function setUpload(?Upload $upload);

	public function getUpload(): ?Upload;

}
