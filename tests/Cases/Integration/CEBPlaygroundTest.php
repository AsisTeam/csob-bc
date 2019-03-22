<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Tests\Cases\Integration;

use AsisTeam\CSOBBC\CEB;
use AsisTeam\CSOBBC\CEBFactory;
use AsisTeam\CSOBBC\Client\Options;
use AsisTeam\CSOBBC\Entity\InlandPayment;
use AsisTeam\CSOBBC\Enum\FileTypeEnum;
use AsisTeam\CSOBBC\Enum\PaymentOrderType;
use AsisTeam\CSOBBC\Request\Filter;
use Money\Currency;
use Money\Money;
use Tester\Assert;
use Tester\Environment;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';


final class CEBPlaygroundTest extends TestCase
{

	/** @var CEB */
	private $ceb;

	public function setUp(): void
	{
		Environment::skip('This test should be run manually. It is prepared for you cto test usage of CEB. Just fill valid options details and play with CEB api.');

		$tmp = __DIR__ . '/../../../tmp/';
		$cert = __DIR__ . '/../../../cert/bccert.pem';
		$passphrase = ''; // fill key passphrase
		$contract = ''; // fill contract number
		$guid = ''; // fill app guid (uuidv4)

		$options = new Options($cert, $passphrase, $contract, $guid);

		$this->ceb = (new CEBFactory($options, $tmp))->create();
	}

	public function testSomething(): void
	{
		$payment1 = new InlandPayment(PaymentOrderType::WITH_PRIORITY, '226047602', new Money('12345', new Currency('CZK')), '43-14680297', '0100');
		$payment1->setOriginatorMessage('Test CEB')->setRecipientMessage('Test CEB');

		$payment2 = new InlandPayment(PaymentOrderType::WITH_PRIORITY, '226047600', new Money('12345', new Currency('CZK')), '43-14680297', '0100');
		$payment2->setOriginatorMessage('Test CEB')->setRecipientMessage('Test CEB');

		$pmntFile = $this->ceb->generatePaymentFile([$payment1, $payment2]);
		$this->ceb->upload([$pmntFile]);

		$filter = new Filter();
		$filter->setFileTypes([FileTypeEnum::IMPPROT]);
		$files = $this->ceb->listFiles(null, $filter);

		$file = $files->getFiles()[0];
		$prot = $this->ceb->downloadAndRead($file);
		Assert::true($prot->isOk());
	}

}

(new CEBPlaygroundTest())->run();
