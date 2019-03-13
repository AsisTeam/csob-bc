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

This PHP API wrapper allows you to work with CSOB Business Connector. Official documentation to be found [here](https://www.csob.cz/portal/documents/10710/15532355/csob-business-connector-implementacni-prirucka.pdf).

- [Business Connector API - usage documentation](https://github.com/AsisTeam/csob-bc/blob/master/.docs/README.md)

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


