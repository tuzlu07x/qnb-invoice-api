# QFB-API (QNB Finansbank API)

## KURULUM

    composer require fatihtuzlu/qfb-api

## KULLANIM

Paketi kullanmak için, öncelikle BaseSoapClient, Adaptor, ItemAdaptor, Archive ve EInvoice sınıflarını dahil etmelisiniz.

Aşağıdaki örnek kod, Archive ve EInvoice sınıflarını kullanarak E-Arşiv ve E-Fatura işlemlerini gerçekleştirmenize yardımcı olacaktır:

```php

use QNBFinansbank\BaseSoapClient;
use QNBFinansbank\Invoice\EArchive\Archive;
use QNBFinansbank\Invoice\EInvoice\EInvoice;
use QNBFinansbank\Invoice\Adaptor;
use QNBFinansbank\Invoice\ItemAdaptor;

$url = "https://earsivtest.efinans.com.tr/earsiv/ws/EarsivWebService?wsdl";
$vkn = '3234234234';
$password = 'XXXX';
$erpCode = 'XXX';
$companyName = "XXX";
$streetName = "XXX";
$country = "Türkiye";
$city = "Antalya";
$district = "Konyaaltı";

$itemData = [
    [
        'id' => 1,
        'note' => 'test',
        'quantity' => 1,
        'price' => 100,
        'vatRatio' => 18,
        'purePrice' => 100,
        'vatPrice' => 18,
        'taxName' => 'KDV',
        'taxTypeCode' => '9021',
        'description' => 'test'
    ]
];

$itemAdaptor = new ItemAdaptor('TL', $itemDatas);
$client = new BaseSoapClient($url, $vkn, $password);
$client->setUserName('ftuzlu.earsiv');

$adaptor = new Adaptor($vkn, $companyName, $streetName, $country, $city, $district);
$adaptor = $adaptor->setItemAdaptor($itemAdaptor);
$adaptor->setUuid(Str::uuid());
$adaptor->setIssueDate('26.02.2023');
$adaptor->setInvoiceTypeCode('SATIS');
$adaptor->setDocumentCurrencyCode('TRY');
$adaptor->setLineCountNumeric(1);
$adaptor->setTaxSheme('KDV');
$adaptor->setAuthorizedName('Fatih');
$adaptor->setAuthorizedSurname('Tuzlu');
$adaptor->setCustomerVkn('11111111111');
$adaptor->setCustomerName('Fatih');
$adaptor->setCustomerEmail('fatihtuzlu07@gmail.com');
$adaptor->setCustomerAuthorizedName('Fatih');
$adaptor->setCustomerAuthorizedSurname('Tuzlu');
```

### E-Arşiv

```php
$data = new Archive($xml, $client);
$data->sendArchive(); //e arsiv fatura gönderme
$data->inguireArchive('uuid'); //e arsiv fatura sorgulama
$data->hasEInvoice('vkn'); //e fatura sorgulama
$data->getZipArchive('uuid'); //e arsiv fatura zip dosyası indirme
$data->callToCancelArchive('vergiTcKimlikNo', 'uuid', 'invoiceNo'); //e arsiv fatura iptal etme
```

### E-Fatura

```php
$invoice = new EInvoice('erpCode', $xml, $client);
$invoice->sendInvoice(); //e fatura gönderme
$invoice->invoiceInquiry('ettn'); //e fatura sorgulama
$invoice->EInvoiceUser('vkn'); //e fatura kullanıcı bilgileri
$invoice->listIncomingDocuments('vkn'); //gelen e faturalar
$invoice->listOutGoingDocuments('startDate', 'endDate'); //giden e faturalar
$invoice->getIncomingDocuments('startDate', 'endDate');
$invoice->getOutGoingDocuments('startDate', 'endDate');
$invoice->DownloadOutGoingDocuments('belgeOid'); //e fatura indirme
```
