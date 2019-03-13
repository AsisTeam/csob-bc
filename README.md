# AsisTeam > CSOB BC (CSOB Business Connector)

[![Build Status](https://img.shields.io/travis/com/AsisTeam/csob-bc.svg?style=flat-square)](https://travis-ci.com/AsisTeam/csob-bc)
[![Licence](https://img.shields.io/packagist/l/AsisTeam/csob-bc.svg?style=flat-square)](https://packagist.org/packages/AsisTeam/csob-bc)
[![Downloads this Month](https://img.shields.io/packagist/dm/AsisTeam/csob-bc.svg?style=flat-square)](https://packagist.org/packages/AsisTeam/csob-bc)
[![Downloads total](https://img.shields.io/packagist/dt/AsisTeam/csob-bc.svg?style=flat-square)](https://packagist.org/packages/AsisTeam/csob-bc)
[![Latest stable](https://img.shields.io/packagist/v/AsisTeam/csob-bc.svg?style=flat-square)](https://packagist.org/packages/AsisTeam/csob-bc)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat)](https://github.com/phpstan/phpstan)

## Credits

The development is under [AsisTeam s.r.o.](https://www.asisteam.cz/).
Feel free to use. Your contributions are very welcome. Feel free to publish pull requests.

<img src="https://www.asisteam.cz/img/logo.svg" width="200" alt="Asisteam" title="Asisteam"/>

## Overview

This PHP API wrapper allows you to work with CSOB Business Connector [PDF official implementation documentation](https://www.csob.cz/portal/documents/10710/15532355/csob-business-connector-implementacni-prirucka.pdf).

This library follows the official docs and allows you to:
- list and read files (AVIZO, VYPIS) from CEB
- generate and upload payment orders to CEB

Please see the [Business Connector API - usage documentation](https://github.com/AsisTeam/csob-bc/blob/master/.docs/README.md)

<img src="https://www.csob.cz/portal/documents/10710/4049264/logo.svg" width="50" alt="Insolvencni rejstrik logo" title="Insolvencni rejstrik"/>

## Install

```
composer require asisteam/csob-bc
```

## Versions

| State       | Version | Branch   | PHP      |
|-------------|---------|----------|----------|
| stable      | `^1.0`  | `master` | `>= 7.1` |


## Tests

Check code quality and run tests
```
composer phpstan-install
composer ci
```

or separately

```
composer qa
composer phpstan-install
composer phpstan
composer tests
```

Note: integration tests are skipped as they do request to real api endpoints.
The validity of assertions in integration tests may change too.

## Example usage 
```php
// use factory to create CEB instance
// factory creates and registers file readers and generators so you don't have to do it manually
$options = new Options('path/to/bccert.pem', 'certPassPhrase', 'contractId', 'appGuid');
$factory = new CEBFactory($options, '/tmp/dir/path');
$ceb = $factory->create();

// returns API response with files listed in CEB API
$list = $ceb->listFiles();
Assert::count(2, $list->getFiles());

// You can read and parse files content

// first one is VYPIS type
$as = $ceb->downloadAndRead($list->getFiles()[0]);
Assert::true($as instanceof IReport);
// You can iterate entries and get details about each transaction
Assert::count(11, $as->getEntries());

// second one is AVIZO type
$adv = $ceb->downloadAndRead($list->getFiles()[1]);
Assert::true($adv instanceof IAdvice);
// You can iterate entries and get details about each transaction
Assert::count(3, $adv->getTransactions());

// generate and upload payment batch file to CEB
$payments = []; // create list of IPaymentOrder entities eg by: new InlandPayment(...)
$file = $ceb->generatePaymentFile($payments);
$ceb->upload([$file]);
```

## Authors

<table>
  <tbody>
    <tr>
      <td align="center">
        <a href="https://github.com/kedlas">
            <img width="100" height="100" src="https://avatars3.githubusercontent.com/u/3510893?s=460&v=4&s=150">
        </a>
        <br/>
        <a href="https://github.com/kedlas">Tomas Sedlacek</a></p>
      </td>
      <td align="center">
        <a href="https://github.com/holantomas">
            <img width="100" height="100" src="https://avatars3.githubusercontent.com/u/5030499?s=460&v=4&s=150">
        </a>
        <br/>
        <a href="https://github.com/holantomas">Tomas Holan</a></p>
      </td>
    </tr>
  </tbody>
</table>


