<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Client;

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

}
