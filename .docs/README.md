# CSOB Business Connector API

PHP implementation of communication with CSOB CEB API.

Can be used for 2 main scenarios:
1. downloading files from your CEB account (bank account reports, payment advices) and/or 
2. uploading files containing payment orders information. (signing of the uploaded files by client's certificate must be done before payment is proceeded)

Please see the official [implementation guide](https://github.com/AsisTeam/csob-bc/blob/master/.docs/official/csob-business-connector-implementacni-prirucka.pdf) to find out all details: 

## How to use?

Before you start using this library, you need to have:
1. generated certificate and key for communication with the API (please follow the instructions in `Makefile` or follow `official guide`).
2. added and activated previously created certificate and key in CEB application
3. be sure you know your __contract number__ and __app guid__ (will be needed when creating instance of the CEB client)

```php
// use factory to create CEB instance
$options = new Options('path/to/certificate', 'certPassPhrase', 'contractId', 'appGuid');
$ceb = (new CEBFactory($options, '/tmp/'))->create();

$list = $ceb->listFiles();
Assert::count(2, $list->getFiles());

// first one is VYPIS type
$as = $ceb->downloadAndRead($list->getFiles()[0]);
Assert::true($as instanceof IReport);
Assert::count(11, $as->getEntries());

// second one is AVIZO type
$adv = $ceb->downloadAndRead($list->getFiles()[1]);
Assert::true($adv instanceof IAdvice);
Assert::count(3, $adv->getTransactions());

// generate and upload payment batch file to CEB
$payments = []; // create list of IPaymentOrder entities
$file = $ceb->generatePaymentFile($payments);
$ceb->upload([$file]);
```

## File formats

Various file formats may be used for holding data about payment orders and reports.
List of all available file formats is listed at: https://www.csob.cz/portal/podnikatele-firmy-a-instituce/produkty/internetove-a-mobilni-bankovnictvi/csob-ceb#podpora in "Podpora section" and it's  "Formaty pro import plateb" or "Formaty pro vypisi z uctu a aviza" subsections.

Currently implemented file formats are:
- payment order: _TXT_ - [txt format definition](https://github.com/AsisTeam/csob-bc/blob/master/.docs/official/formats/zadani-platb-txt.pdf)
- payment reports: _XML CSOB_ - [xml csob format definition](https://github.com/AsisTeam/csob-bc/blob/master/.docs/official/formats/report-xml-csob.pdf)
- payment advises: _MT942_ - [mt942 format definition](https://github.com/AsisTeam/csob-bc/blob/master/.docs/official/formats/aviza-mt942.pdf)

Please visit [.docs/official/formats](https://github.com/AsisTeam/csob-bc/blob/master/.docs/official/formats/) to see official documentation for these formats if cannot be found on official website.

If you prefer different file format for communication with CSOB bank, you can make your own implementation of:
- Payment Orders `src/Generator/Payment/Impl/YourPaymentOrderGenerator` that implements `IPaymentFileGenerator` interface
- Advises `src/Reader/Advice/Impl/YourAdviceReader` that implements `IAdviceReader` interface
- Reports `src/Reader/Report/Impl/YourReportReader` that implements `IReportReader` interface

Note: historically there was also implemented `XmlCBA` report read that was abandoned, but it's implementation still resided in `xml-cba-report` GIT branch. It extracts the data from XmlCBA file format but does not implement `IReportReader` interface properly, thus it must be edited before using it to comply with this interface.

When you use CEBFactory, formats listed above will be used. If you wish to use different format, implement it first and then override corresponding methods in CEBFactory class. 

## Nette

You can setup package as Nette compiler extension using neon config
Extension will create all client factories as services

### Usage

```neon
extensions:
    ceb: AsisTeam\CSOBBC\DI\CsobBusinessConnectorExtension

ceb:
    
    # temp directory
    tmp_dir: path/to/tmp/dir
    
    # certificate location
    cert_path: path/to/cert/bccert.pem'
    
    # certificate passphrase
    passphrase: ""
    
    # CEM contract number
    contract: ""
    
    # Appl GUID
    guid: ""
    
    # run in test environment
    test: false
```
