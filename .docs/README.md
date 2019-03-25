# CSOB Business Connector API

PHP implementation of communication with CSOB CEB API.

Can be used for 2 main scenarios:
1. downloading files from your CEB account (bank account reports, payment advices) and/or 
2. uploading files containing payment orders information. (signing of the uploaded files by client's certificate must be done before payment is proceeded)

Please see the official [implementation guide](https://github.com/AsisTeam/csob-bc/blob/master/.docs/official/csob-business-connector-implementacni-prirucka.pdf) to find out all details:   

## How to use?

Before you start using this library, you need to have:
- generated certificate and key for communication with the API (please follow the instructions in `Generating certificate` section or follow `official guide`).
- added and activated previously created certificate and key in CEB application
- be sure you know your __contract number__ and __app guid__ (will be needed when creating instance of the CEB client)

__Test environment__
By setting `Options` `test` param to `true`, test CEB server will be used. 

You can develop against the CEB test environment. You don't need to use valid contract_id and guid from CSOB for the test communication, but guid must be valid uuidv4.

### Generating certificate

Please enter the `cert` directory where `Makefile` and `bccert.cnf.dist` files are present.
You may use `Makefile` to run desired commands by typing `make command-name`

1. In some text editor open `bccert.cnf.dist` file, change the line 'CN = <BC server>'. Replace <BC server> by your local machine name and save the file as `bccert.cnf`.
2. run `make generate-request`. It creates `bccert.csr` certificate request file.
3. Upload `bccert.csr` file to CSOB CEB application and in it's UI generate and download certificate file (name it `bccert.crt`)
4. run `make generate-cert` and set some passphrase if you wish so. New files `bccert.p12` and `bccert.pem` files should appear in `cert` folder.
5. use path to `bccert.pem` file and the passphrase you typed before as parameters for creation of Options object.


### Example usage 
```php
// use factory to create CEB instance
// factory creates and registers file readers and generators so you don't have to do it manually
$options = new Options('path/to/bccert.pem', 'certPassPhrase', 'contractId', 'appGuid');
$factory = new CEBFactory($options, '/tmp/');
$ceb = $factory->create();

// returns files from CEB API
$list = $ceb->listFiles();
Assert::count(2, $list->getFiles());

// You can read and parse files content

// first one is VYPIS type
$as = $ceb->downloadAndRead($list->getFiles()[0]);
Assert::true($as instanceof IReport);
Assert::count(11, $as->getEntries());

// second one is AVIZO type
$adv = $ceb->downloadAndRead($list->getFiles()[1]);
Assert::true($adv instanceof IAdvice);
Assert::count(3, $adv->getTransactions());

// generate and upload payment batch file to CEB
$payments = []; // create list of IPaymentOrder entities eg by: new InlandPayment(...)
$file = $ceb->generatePaymentFile($payments);
$ceb->upload([$file]);

// search for import protocol files only and read the first one
$filter = new Filter();
$filter->setFileTypes([FileTypeEnum::IMPPROT]);
$files = $this->ceb->listFiles(null, $filter);
$protocol = $this->ceb->downloadAndRead($files->getFiles[0]);
Assert::true($protocol->isOk());
```

## File formats

Various file formats may be used for holding data about payment orders and reports.
List of all available file formats is listed at: https://www.csob.cz/portal/podnikatele-firmy-a-instituce/produkty/internetove-a-mobilni-bankovnictvi/csob-ceb#podpora in "Podpora section" and it's  "Formaty pro import plateb" or "Formaty pro vypisi z uctu a aviza" subsections.

Currently implemented file formats are:
- payment order: _TXT_ - [txt format definition](https://github.com/AsisTeam/csob-bc/blob/master/.docs/official/formats/zadani-platb-txt.pdf)
- payment reports: _XML CSOB_ - [xml csob format definition](https://github.com/AsisTeam/csob-bc/blob/master/.docs/official/formats/report-xml-csob.pdf)
- payment advises: _MT942_ - [mt942 format definition](https://github.com/AsisTeam/csob-bc/blob/master/.docs/official/formats/aviza-mt942.pdf)
- import protocol: _XML CSOB_ - [implementation guide](https://github.com/AsisTeam/csob-bc/blob/master/.docs/official/csob-business-connector-implementacni-prirucka.pdf)

Please visit [.docs/official/formats](https://github.com/AsisTeam/csob-bc/blob/master/.docs/official/formats/) to see official documentation for these formats if cannot be found on official website.

If you prefer different file format for communication with CSOB bank, you can make your own implementation of:
- Payment Orders `src/Generator/Payment/Impl/YourPaymentOrderGenerator` that implements `IPaymentFileGenerator` interface
- Advises `src/Reader/Advice/Impl/YourAdviceReader` that implements `IAdviceReader` interface
- Reports `src/Reader/Report/Impl/YourReportReader` that implements `IReportReader` interface
- ImportProtocols `src/Reader/Import/Impl/YourReportReader` that implements `IImportProtocolReader` interface

Note: historically there was also implemented `XmlCBA` report reader that was abandoned, but it's implementation still resided in `xml-cba-report` GIT branch. It extracts the data from XmlCBA file format but does not implement `IReportReader` interface properly, thus it must be edited before using it to comply with this interface.

When you use CEBFactory, formats listed above will be used. If you wish to use different format, implement it first and then override corresponding methods in CEBFactory class. 

## Nette

You can setup package as Nette compiler extension using neon config
Extension will create all client factories as services

### Usage

```yml
extensions:
    ceb: AsisTeam\CSOBBC\DI\CsobBusinessConnectorExtension

ceb:
    
    # temp directory - set null or not present for `sys_get_temp_dir()`
    tmp_dir: path/to/tmp/dir
    
    # certificate location
    cert_path: path/to/cert/bccert.pem
    
    # certificate passphrase
    passphrase: ""
    
    # CEM contract number
    contract: ""
    
    # Appl GUID
    guid: ""
    
    # run in test environment - default is FALSE
    test: false
```
