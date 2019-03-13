<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Tests\Cases\Unit\Request;

use AsisTeam\CSOBBC\Enum\FileTypeEnum;
use AsisTeam\CSOBBC\Request\Filter;
use DateTimeImmutable;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../../bootstrap.php';

class FilterTest extends TestCase
{

	public function testToArray(): void
	{
		$filter = new Filter();
		$filter->setCreatedBefore(new DateTimeImmutable('2019-02-05 10:55:00'));
		$filter->setFileName('test.pdf');
		$filter->setFileTypes([FileTypeEnum::IMPORT, FileTypeEnum::VYPIS]);
		$filter->setClientAppGuid('AsisteamUUID');

		Assert::equal(
			[
				'FileTypes'     => ['IMPORT', 'VYPIS'],
				'FileName'      => 'test.pdf',
				'CreatedBefore' => '2019-02-05T10:55:00+01:00',
				'ClientAppGuid' => 'AsisteamUUID',
			],
			$filter->toArray()
		);
	}

}

(new FilterTest())->run();
