<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Response;

use AsisTeam\CSOBBC\Entity\File;
use AsisTeam\CSOBBC\Entity\IFile;
use AsisTeam\CSOBBC\Exception\Runtime\ResponseException;
use DateTimeImmutable;
use stdClass;

final class GetDownloadFileListResponse extends AbstractResponse
{

	/** @var DateTimeImmutable */
	private $date;

	/** @var IFile[] */
	private $files = [];

	/**
	 * @param IFile[] $files
	 */
	public function __construct(DateTimeImmutable $date, string $ticketId, array $files)
	{
		$this->date     = $date;
		$this->ticketId = $ticketId;
		$this->files    = $files;
	}

	public function getDate(): DateTimeImmutable
	{
		return $this->date;
	}

	/**
	 * @return IFile[]
	 */
	public function getFiles(): array
	{
		return $this->files;
	}

	public static function fromResponse(stdClass $resp): self
	{
		self::assertResponse($resp);

		$files = [];
		if (isset($resp->FileList) && $resp->FileList->FileDetail && is_array($resp->FileList->FileDetail)) {
			foreach ($resp->FileList->FileDetail as $f) {
				$files[] = self::fillFile((array) $f);
			}
		}

		$date = new DateTimeImmutable($resp->QueryTimestamp);

		return new self($date, (string) $resp->TicketId, $files);
	}

	/**
	 * @param mixed[] $data
	 */
	private static function fillFile(array $data): IFile
	{
		$file = new File();
		$file->setFileName($data['Filename'] ?? '');
		$file->setHash($data['UploadFileHash'] ?? '');
		$file->setSize(isset($data['Size']) ? (int) $data['Size'] : 0);
		$file->setCreated(new DateTimeImmutable($data['CreationDateTime'] ?? 'now'));
		$file->setType($data['Type'] ?? '');
		$file->setStatus($data['Status'] ?? '');
		$file->setDownloadUrl($data['Url'] ?? null);

		return $file;
	}

	private static function assertResponse(stdClass $resp): void
	{
		if (!isset($resp->QueryTimestamp)) {
			throw new ResponseException('Missing "QueryTimestamp" in response.');
		}

		if (!isset($resp->TicketId)) {
			throw new ResponseException('Missing "TicketId" in response.');
		}
	}

}
