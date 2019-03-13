<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Response;

use AsisTeam\CSOBBC\Entity\IFile;
use AsisTeam\CSOBBC\Exception\LogicalException;
use AsisTeam\CSOBBC\Exception\Runtime\ResponseException;
use stdClass;

final class StartUploadFileListResponse extends AbstractResponse
{

	/** @var IFile[] */
	private $files;

	/**
	 * @param IFile[] $files
	 */
	public function __construct(string $ticketId, array $files)
	{
		$this->ticketId = $ticketId;
		$this->files    = $files;
	}

	/**
	 * @param IFile[] $originalFiles
	 */
	public static function fromResponse(stdClass $resp, array $originalFiles): self
	{
		self::assertResponse($resp);

		if (isset($resp->FileList->FileUrl) && is_array($resp->FileList->FileUrl)) {
			foreach ($resp->FileList->FileUrl as $f) {
				self::findAndUpdateOriginalFile((array) $f, $originalFiles);
			}
		}

		self::assertAllUploadUrls($originalFiles);

		return new self((string) $resp->TicketId, $originalFiles);
	}

	/**
	 * @return IFile[]
	 */
	public function getFiles(): array
	{
		return $this->files;
	}

	/**
	 * @param mixed[] $data
	 * @param IFile[] $originalFiles
	 */
	private static function findAndUpdateOriginalFile(array $data, array $originalFiles): void
	{
		if (!isset($data['Hash'])) {
			throw new ResponseException('Received file does not contain valid hash');
		}

		if (!isset($data['Url'])) {
			throw new ResponseException('Received file does not contain valid url');
		}

		if (!isset($data['Status'])) {
			throw new ResponseException('Received file does not contain valid status');
		}

		foreach ($originalFiles as $original) {
			// on match - update status and url
			if ($original->getHash() === $data['Hash']) {
				$original->setStatus($data['Status']);
				$original->setUploadUrl($data['Url']);
			}
		}
	}

	private static function assertResponse(stdClass $resp): void
	{
		if (!isset($resp->TicketId)) {
			throw new ResponseException('Missing "TicketId" in response.');
		}

		if (!isset($resp->FileList)) {
			throw new ResponseException('Missing "FileList" in response.');
		}
	}

	/**
	 * @param IFile[] $files
	 */
	private static function assertAllUploadUrls(array $files): void
	{
		foreach ($files as $f) {
			if ($f->getUploadUrl() === null) {
				throw new LogicalException(sprintf('File "%s" does not have upload URL set', $f->getFileName()));
			}
		}
	}

}
