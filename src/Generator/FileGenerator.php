<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Generator;

use AsisTeam\CSOBBC\Entity\ForeignPayment;
use AsisTeam\CSOBBC\Entity\IFile;
use AsisTeam\CSOBBC\Entity\InlandPayment;
use AsisTeam\CSOBBC\Entity\IPaymentOrder;
use AsisTeam\CSOBBC\Enum\FileFormatEnum;
use AsisTeam\CSOBBC\Exception\LogicalException;
use AsisTeam\CSOBBC\Generator\Payment\IPaymentFileGenerator;

class FileGenerator
{

	/** @var IPaymentFileGenerator[] */
	private $generators = [];

	public function addGenerator(string $format, IPaymentFileGenerator $generator): void
	{
		$this->generators[$format] = $generator;
	}

	/**
	 * @param IPaymentOrder[] $payments
	 */
	public function generatePaymentFile(array $payments, ?string $format = FileFormatEnum::TXT_TPS): IFile
	{
		if (!isset($this->generators[$format])) {
			throw new LogicalException(sprintf('No generator registered for format "%s"', $format));
		}

		return $this->generators[$format]->generate($payments, $this->detectType($payments));
	}

	/**
	 * @param IPaymentOrder[] $payments
	 */
	private function detectType(array $payments): string
	{
		$total   = count($payments);
		$inland  = 0;
		$foreign = 0;

		if ($total === 0) {
			throw new LogicalException('No payment orders given to generate file from');
		}

		foreach ($payments as $p) {
			if ($p instanceof InlandPayment) {
				$inland++;
			}
			if ($p instanceof ForeignPayment) {
				$foreign++;
			}
		}

		if ($inland > 0 && $foreign > 0) {
			throw new LogicalException('Mixing of inland/foreign payment for single is not allowed.');
		}

		if ($inland === $total) {
			return IPaymentFileGenerator::TYPE_INLAND;
		}

		if ($foreign === $total) {
			return IPaymentFileGenerator::TYPE_FOREIGN;
		}

		throw new LogicalException('Unknown payment types detected');
	}

}
