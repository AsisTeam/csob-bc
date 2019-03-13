<?php declare(strict_types = 1);

namespace AsisTeam\CSOBBC\Bridges\Nette\DI;

use AsisTeam\CSOBBC\CEBFactory;
use AsisTeam\CSOBBC\Client\Options;
use Nette\DI\CompilerExtension;

class BusinessConnectorExtension extends CompilerExtension
{

	/** @var mixed[] */
	public $defaults = [
		'cert_path'  => __DIR__ . '/../../../../cert/bccert.pem',
		'passphrase' => '',
		'contract'   => '',
		'guid'       => '',
		'test'       => false,
		'tmp_dir'    => null,
	];

	/**
	 * @inheritDoc
	 */
	public function loadConfiguration(): void
	{
		$config  = $this->validateConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		$opts = new Options(
			$config['cert_path'],
			$config['passphrase'],
			$config['contract'],
			$config['guid'],
			$config['test']
		);

		$builder->addDefinition($this->prefix('ceb'))
			->setFactory(CEBFactory::class, [$opts, $config['tmp_dir'] ?? sys_get_temp_dir()]);
	}

}
