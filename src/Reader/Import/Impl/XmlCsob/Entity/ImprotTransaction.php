<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Reader\Import\Impl\XmlCsob\Entity;

use AsisTeam\CSOBBC\Entity\ImportProtocol\IImportProtocolTransaction;
use Money\Currency;
use Money\Money;
use SimpleXMLElement;

final class ImprotTransaction implements IImportProtocolTransaction
{

	/** @var Money */
	private $amount;

	/** @var string */
	private $counterpartyAccount = '';

	/** @var string */
	private $counterpartyBank = '';

	/** @var string */
	private $senderAccount = '';

	/** @var string */
	private $status = '';

	/** @var string */
	private $error = '';

	public static function fromXml(SimpleXMLElement $xml): self
	{
		$tr = new self();

		$tr->status = (string) $xml->PmtInfSts;

		// amount
		$amnt       = (array) $xml->TxInfAndSts->OrgnlTxRef->Amt->InstdAmt;
		$tr->amount = new Money(str_replace('.', '', $amnt[0]), new Currency($amnt['@attributes']['Ccy']));

		// sender
		$tr->senderAccount = (string) $xml->TxInfAndSts->OrgnlTxRef->DbtrAcct->Id->IBAN;

		// receiver
		$tr->counterpartyBank    = (string) $xml->TxInfAndSts->OrgnlTxRef->CdtrAgt->FinInstnId->Othr->Id;
		$tr->counterpartyAccount = (string) $xml->TxInfAndSts->OrgnlTxRef->CdtrAcct->Id->IBAN;

		// error message
		if (isset($xml->StsRsnInf)) {
			if (isset($xml->StsRsnInf->Rsn)) {
				$tr->error = (string) $xml->StsRsnInf->Rsn->Cd;
			}
			if (isset($xml->StsRsnInf->Orgtr)) {
				$tr->error .= ' ' . (string) $xml->StsRsnInf->Orgtr->Nm;
			}
		}
		if (isset($xml->StsId)) {
			$tr->error .= ' ' . (string) $xml->StsId;
		}

		return $tr;
	}

	public function isOk(): bool
	{
		return $this->status === CsobImportProtocol::STATUS_SUCCESS;
	}

	public function getAmount(): Money
	{
		return $this->amount;
	}

	public function getCounterpartyAccount(): string
	{
		return $this->counterpartyAccount;
	}

	public function getCounterpartyBank(): string
	{
		return $this->counterpartyBank;
	}

	public function getSenderAccount(): string
	{
		return $this->senderAccount;
	}

	public function getStatus(): string
	{
		return $this->status;
	}

	public function getError(): string
	{
		return $this->error;
	}

}
