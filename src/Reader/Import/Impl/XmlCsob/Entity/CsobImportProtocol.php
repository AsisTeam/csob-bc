<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Import\Impl\XmlCsob\Entity;

use AsisTeam\CSOBBC\Entity\ImportProtocol\IImportProtocol;
use AsisTeam\CSOBBC\Entity\ImportProtocol\IImportProtocolTransaction;
use DateTimeImmutable;
use Money\Currency;
use Money\Money;
use SimpleXMLElement;

final class CsobImportProtocol implements IImportProtocol
{

	public const STATUS_SUCCESS = 'ACCP';

	/** @var string */
	private $id = '';

	/** @var DateTimeImmutable */
	private $created;

	/** @var string */
	private $cebImportId = '';

	/** @var Money */
	private $totalAmount;

	/** @var string */
	private $status = '';

	/** @var string */
	private $errorMsg = '';

	/** @var IImportProtocolTransaction[] */
	private $trx = [];

	public static function fromXml(SimpleXMLElement $xml): self
	{
		$prot = new self();

		$prot->id = (string) $xml->CstmrPmtStsRpt->GrpHdr->MsgId ?? '';
		$prot->created = isset($xml->CstmrPmtStsRpt->GrpHdr->CreDtTm) ?
			new DateTimeImmutable((string) $xml->CstmrPmtStsRpt->GrpHdr->CreDtTm) :
			new DateTimeImmutable();
		$prot->cebImportId = (string) $xml->CstmrPmtStsRpt->OrgnlGrpInfAndSts->OrgnlMsgId ?? '';
		$prot->totalAmount = new Money(
			str_replace('.', '', (string) $xml->CstmrPmtStsRpt->OrgnlGrpInfAndSts->OrgnlCtrlSum),
			new Currency('CZK')
		);
		$prot->status = (string) $xml->CstmrPmtStsRpt->OrgnlGrpInfAndSts->GrpSts ?? '';

		if (isset($xml->CstmrPmtStsRpt->OrgnlGrpInfAndSts->StsRsnIn)) {
			if (isset($xml->CstmrPmtStsRpt->OrgnlGrpInfAndSts->StsRsnIn->Rsn)) {
				$prot->errorMsg = (string) $xml->CstmrPmtStsRpt->OrgnlGrpInfAndSts->StsRsnIn->Rsn->Cd . ' ' .
					(string) $xml->CstmrPmtStsRpt->OrgnlGrpInfAndSts->StsRsnIn->Orgtr->Nm;
			}
			if (isset($xml->CstmrPmtStsRpt->OrgnlGrpInfAndSts->StsRsnIn->Orgtr)) {
				$prot->errorMsg .= ' ' . (string) $xml->CstmrPmtStsRpt->OrgnlGrpInfAndSts->StsRsnIn->Orgtr->Nm;
			}
		}

		// populate transactions
		foreach ($xml->CstmrPmtStsRpt->OrgnlPmtInfAndSts as $tr) {
			$prot->trx[] = ImprotTransaction::fromXml($tr);
		}

		return $prot;
	}

	public function isOk(): bool
	{
		return $this->status === self::STATUS_SUCCESS;
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function getCreated(): DateTimeImmutable
	{
		return $this->created;
	}

	public function getCebImportId(): string
	{
		return $this->cebImportId;
	}

	public function getTotalAmount(): Money
	{
		return $this->totalAmount;
	}

	public function getStatus(): string
	{
		return $this->status;
	}

	public function getError(): string
	{
		return $this->errorMsg;
	}

	/**
	 * @return IImportProtocolTransaction[]
	 */
	public function getAllTransactions(): array
	{
		return $this->trx;
	}

	/**
	 * @return IImportProtocolTransaction[]
	 */
	public function getInvalidTransactions(): array
	{
		$invalid = [];

		foreach ($this->getAllTransactions() as $tr) {
			if (!$tr->isOk()) {
				$invalid[] = $tr;
			}
		}

		return $invalid;
	}

}
