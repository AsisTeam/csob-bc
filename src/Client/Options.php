<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Client;

use AsisTeam\CSOBBC\Exception\Logical\OptionsException;

final class Options
{

	/** @var string */
	private $certPath;

	/** @var string */
	private $certPassphrase;

	/** @var string */
	private $contractNo;

	/** @var string */
	private $clientAppGuid;

	/** @var bool */
	private $test;

	public function __construct(
		string $certPath,
		string $certPassphrase,
		string $contractNo,
		string $clientAppGuid,
		bool $test = false
	)
	{
		$this->assertCertFile($certPath);
		$this->assertGuid($clientAppGuid);

		$this->certPath       = $certPath;
		$this->certPassphrase = $certPassphrase;
		$this->contractNo     = $contractNo;
		$this->clientAppGuid  = $clientAppGuid;
		$this->test           = $test;
	}

	public function getCertPath(): string
	{
		return $this->certPath;
	}

	public function getCertPassphrase(): string
	{
		return $this->certPassphrase;
	}

	public function getContractNo(): string
	{
		return $this->contractNo;
	}

	public function getClientAppGuid(): string
	{
		return $this->clientAppGuid;
	}

	public function isTest(): bool
	{
		return $this->test;
	}

	private function assertCertFile(string $certPath): void
	{
		if (!file_exists($certPath)) {
			throw new OptionsException(sprintf('File "%s" does not exist.', $certPath));
		}
	}

	private function assertGuid(string $clientAppGuid): void
	{
		if (strlen($clientAppGuid) !== 36 ||
			preg_match('/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/', $clientAppGuid) !== 1
		) {
			throw new OptionsException(sprintf('AppGUID must be valid UUIDv4 string. "%s" given.', $clientAppGuid));
		}
	}

}
