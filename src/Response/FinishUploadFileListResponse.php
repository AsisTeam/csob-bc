<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Response;

use AsisTeam\CSOBBC\Entity\File;
use AsisTeam\CSOBBC\Entity\IFile;
use AsisTeam\CSOBBC\Exception\Runtime\ResponseException;
use stdClass;

final class FinishUploadFileListResponse extends AbstractResponse
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

	public static function fromResponse(stdClass $resp): self
	{
		self::assertResponse($resp);

		$files = [];
		if (isset($resp->FileList->FileStatus) && is_array($resp->FileList->FileStatus)) {
			foreach ($resp->FileList->FileStatus as $f) {
				$files[] = self::fillFile((array) $f);
			}
		}

		return new self((string) $resp->TicketId, $files);
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
	 */
	private static function fillFile(array $data): IFile
	{
		$file = new File();
		$file->setFileName($data['Filename'] ?? '');
		$file->setStatus($data['Status'] ?? '');
		$file->setHash($data['Hash'] ?? '');

		return $file;
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

}
