<?php

namespace QNBFinansbank\Invoice;

use Transliterator;
use App\Integration\Invoice\ItemAdaptor;

class Adaptor
{
    private string $ublVersion = '2.1';
    private string $customizationId = 'TR1.2.1';
    private string $profileId = 'TEMELFATURA';
    private string $listAgentyName = 'United Nations Economic Commission for Europe';
    private string $listId = 'ISO 4217 Alpha';
    private string $listName = 'Currency';
    private string $note = 'Sicil No: 123 İşletme Merkezi: Ankara';
    private int $listVersionID = 2001;
    protected ItemAdaptor $itemAdaptor;
    protected string $issueDate;
    protected string $uuid;
    protected string $invoiceTypeCode; // Satış, İade, İptal, Kredi Notu
    protected string $documentCurrencyCode; //TRY
    protected string $lineCountNumeric;
    //Supplier
    protected string $vkn;
    protected string $companyName;
    protected string $streetName;
    protected string $country;
    protected string $district;
    protected string $city;
    protected string $taxScheme;
    protected string $authorizedName;
    protected string $authorizedSurname;
    //Customer
    protected string $customerVkn;
    protected string $customerName;
    protected string $customerEmail;
    protected string $customerAuthorizedName;
    protected string $customerAuthorizedSurname;
    protected string $customerRoom = '';
    protected string $customerStreetName = '';
    protected string $customerCountry = '';
    protected string $customerCity = '';
    protected string $customerTaxScheme = '';
    protected string $customerBuildingName = '';
    protected string $customerBuildingNumber = '';
    protected string $customerCitySubdivisionName = '';
    protected string $customerPostalZone = '';
    protected string $customerRegion = '';
    protected string $customerTelephone = '';
    protected string $customerTelefax = '';

    public function __construct(string $vkn, string $companyName, string $streetName, string $city, string $district, string $country)
    {
        $this->vkn = $vkn;
        $this->companyName = $companyName;
        $this->streetName = $streetName;
        $this->country = $country;
        $this->city = $city;
        $this->district = $district;
    }

    public function getXml(): ?string
    {
        return '<Invoice' . $this->xmlVersion() . '
    <cbc:UBLVersionID>' . $this->ublVersion . '</cbc:UBLVersionID>
    <cbc:CustomizationID>' . $this->customizationId . '</cbc:CustomizationID>
    <cbc:ProfileID>' . $this->profileId . '</cbc:ProfileID>
    <cbc:ID>' . $this->belgeId() . '</cbc:ID>
    <cbc:CopyIndicator>false</cbc:CopyIndicator>
    <cbc:UUID>' . $this->uuid . '</cbc:UUID>
    <cbc:IssueDate>' . $this->issueDate . '</cbc:IssueDate>
    <cbc:InvoiceTypeCode>' . $this->invoiceTypeCode . '</cbc:InvoiceTypeCode>
    <cbc:Note>' . $this->note . '</cbc:Note>
    <cbc:DocumentCurrencyCode listAgencyName="' . $this->listAgentyName . '" listID="' . $this->listId . '"
        listName="' . $this->listName . '" listVersionID="' . $this->listVersionID . '">
        ' . $this->documentCurrencyCode . '
    </cbc:DocumentCurrencyCode>
    <cbc:LineCountNumeric>' . $this->lineCountNumeric . '</cbc:LineCountNumeric>
    <cac:AdditionalDocumentReference>
        <cbc:ID>' . $this->uuid . '</cbc:ID>
        <cbc:IssueDate>' . $this->issueDate . '</cbc:IssueDate>
    </cac:AdditionalDocumentReference>
    <cac:AccountingSupplierParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID="' . $this->isVknOrTckn($this->vkn) . '">' . $this->vkn . '</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name>' . Transliterator::create('tr-upper')->transliterate($this->companyName) . '</cbc:Name>
            </cac:PartyName>
            <cac:PostalAddress>
                <cbc:StreetName>' . Transliterator::create('tr-upper')->transliterate($this->streetName) . '</cbc:StreetName>
                <cbc:CitySubdivisionName>' . Transliterator::create('tr-upper')->transliterate($this->district) . '</cbc:CitySubdivisionName>
                <cbc:CityName>' . Transliterator::create('tr-upper')->transliterate($this->city) . '</cbc:CityName>
                <cac:Country>
                    <cbc:Name>' . Transliterator::create('tr-upper')->transliterate($this->country) . '</cbc:Name>
                </cac:Country>
            </cac:PostalAddress>
            <cac:PartyTaxScheme>
                <cac:TaxScheme>
                    <cbc:Name>' . $this->taxScheme . '</cbc:Name>
                </cac:TaxScheme>
            </cac:PartyTaxScheme>
            <cac:Person>
                <cbc:FirstName>' . Transliterator::create('tr-upper')->transliterate($this->authorizedName) . '</cbc:FirstName>
                <cbc:FamilyName>' . Transliterator::create('tr-upper')->transliterate($this->authorizedSurname) . '</cbc:FamilyName>
            </cac:Person>
        </cac:Party>
    </cac:AccountingSupplierParty>
    <cac:AccountingCustomerParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID="' . $this->isVknOrTckn($this->customerVkn) . '">' . $this->customerVkn . '</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name>' . $this->customerName . '</cbc:Name>
            </cac:PartyName>
            <cac:PostalAddress>
                <cbc:Room>' . $this->customerRoom . '</cbc:Room>
                <cbc:StreetName>' . $this->customerStreetName . '</cbc:StreetName>
                <cbc:BuildingName>' . $this->customerBuildingName . '</cbc:BuildingName>
                <cbc:BuildingNumber>' . $this->customerBuildingNumber . '</cbc:BuildingNumber>
                <cbc:CitySubdivisionName>' . $this->customerCitySubdivisionName . '</cbc:CitySubdivisionName>
                <cbc:CityName>' . $this->customerCity . '</cbc:CityName>
                <cbc:PostalZone>' . $this->customerPostalZone . '</cbc:PostalZone>
                <cbc:Region>' . $this->customerRegion . '</cbc:Region>
                <cac:Country>
                    <cbc:Name>' . $this->customerCountry . '</cbc:Name>
                </cac:Country>
            </cac:PostalAddress>
            <cac:PartyTaxScheme>
                <cac:TaxScheme>
                    <cbc:Name>' . $this->customerTaxScheme . '</cbc:Name>
                </cac:TaxScheme>
            </cac:PartyTaxScheme>
            <cac:Contact>
                <cbc:Telephone>' . $this->customerTelephone . '</cbc:Telephone>
                <cbc:Telefax>' . $this->customerTelefax . '</cbc:Telefax>
                <cbc:ElectronicMail>' . $this->customerEmail . '</cbc:ElectronicMail>
            </cac:Contact>
            <cac:Person>
                <cbc:FirstName>' . $this->customerAuthorizedName . '</cbc:FirstName>
                <cbc:FamilyName>' . $this->customerAuthorizedSurname . '</cbc:FamilyName>
            </cac:Person>
        </cac:Party>
    </cac:AccountingCustomerParty>

    <cac:TaxTotal>
        <cbc:TaxAmount currencyID="TRY"></cbc:TaxAmount>
        <cac:TaxSubtotal>
            <cbc:TaxableAmount currencyID="TRY"></cbc:TaxableAmount>
            <cbc:TaxAmount currencyID="TRY"></cbc:TaxAmount>
            <cac:TaxCategory>
                <cac:TaxScheme>
                    <cbc:Name></cbc:Name>
                    <cbc:TaxTypeCode></cbc:TaxTypeCode>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>
    </cac:TaxTotal>
    <cac:LegalMonetaryTotal>

        <cbc:LineExtensionAmount currencyID="TRY"></cbc:LineExtensionAmount>
        <cbc:TaxExclusiveAmount currencyID="TRY"></cbc:TaxExclusiveAmount>
        <cbc:TaxInclusiveAmount currencyID="TRY"></cbc:TaxInclusiveAmount>
        <cbc:PayableRoundingAmount currencyID="TRY"></cbc:PayableRoundingAmount>
        <cbc:PayableAmount currencyID="TRY"></cbc:PayableAmount>
    </cac:LegalMonetaryTotal>
    ' . $this->itemAdaptor->xmlInvoiceLines() . '
</Invoice>
';
    }

    public function xmlVersion(): string
    {
        return 'xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"
        xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
        xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
        xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
        xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2"
        xmlns:n4="http://www.altova.com/samplexml/other-namespace" xmlns:xades="http://uri.etsi.org/01903/v1.3.2#"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2 ../xsdrt/maindoc/UBL-Invoice-2.1.xsd">';
    }

    public function getVkn(): string
    {
        return $this->vkn;
    }

    public function belgeId($prefix = 'CKD')
    {
        //
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setItemAdaptor(ItemAdaptor $itemAdaptor): self
    {
        $this->itemAdaptor = $itemAdaptor;
        return $this;
    }

    public function setInvoiceTypeCode(string $invoiceTypeCode): self
    {
        $this->invoiceTypeCode = $invoiceTypeCode;
        return $this;
    }

    public function setDocumentCurrencyCode(string $documentCurrencyCode): self
    {
        $this->documentCurrencyCode = $documentCurrencyCode;
        return $this;
    }

    public function setLineCountNumeric(string $lineCountNumeric): self
    {
        $this->lineCountNumeric = $lineCountNumeric;
        return $this;
    }

    public function setIssueDate(string $issueDate): self
    {
        $this->issueDate = $issueDate;
        return $this;
    }

    public function setTaxSheme(string $taxScheme): self
    {
        $this->taxScheme = $taxScheme;
        return $this;
    }

    public function setAuthorizedName(string $authorizedName): self
    {
        $this->authorizedName = $authorizedName;
        return $this;
    }

    public function setAuthorizedSurname(string $authorizedSurname): self
    {
        $this->authorizedSurname = $authorizedSurname;
        return $this;
    }

    public function setCustomerVkn(string $customerVkn): self
    {
        $this->customerVkn = $customerVkn;
        return $this;
    }

    public function setCustomerName(string $customerName): self
    {
        $this->customerName = $customerName;
        return $this;
    }

    public function setCustomerRoom(string $customerRoom): self
    {
        $this->customerRoom = $customerRoom;
        return $this;
    }

    public function setCustomerStreetName(string $customerStreetName): self
    {
        $this->customerStreetName = $customerStreetName;
        return $this;
    }

    public function setCustomerCountry(string $customerCountry): self
    {
        $this->customerCountry = $customerCountry;
        return $this;
    }

    public function setCustomerCity(string $customerCity): self
    {
        $this->customerCity = $customerCity;
        return $this;
    }

    public function setCustomerTaxScheme(string $customerTaxScheme): self
    {
        $this->customerTaxScheme = $customerTaxScheme;
        return $this;
    }

    public function setCustomerAuthorizedName(string $customerAuthorizedName): self
    {
        $this->customerAuthorizedName = $customerAuthorizedName;
        return $this;
    }

    public function setCustomerAuthorizedSurname(string $customerAuthorizedSurname): self
    {
        $this->customerAuthorizedSurname = $customerAuthorizedSurname;
        return $this;
    }

    public function setCustomerBuildingName(string $customerBuildingName): self
    {
        $this->customerBuildingName = $customerBuildingName;
        return $this;
    }

    public function setCustomerBuildingNumber(string $customerBuildingNumber): self
    {
        $this->customerBuildingNumber = $customerBuildingNumber;
        return $this;
    }

    public function setCustomerCitySubdivisionName(string $customerCitySubdivisionName): self
    {
        $this->customerCitySubdivisionName = $customerCitySubdivisionName;
        return $this;
    }

    public function setCustomerPostalZone(string $customerPostalZone): self
    {
        $this->customerPostalZone = $customerPostalZone;
        return $this;
    }

    public function setCustomerRegion(string $customerRegion): self
    {
        $this->customerRegion = $customerRegion;
        return $this;
    }

    public function setCustomerTelephone(string $customerTelephone): self
    {
        $this->customerTelephone = $customerTelephone;
        return $this;
    }

    public function setCustomerTelefax(string $customerTelefax): self
    {
        $this->customerTelefax = $customerTelefax;
        return $this;
    }

    public function setCustomerEmail(string $customerEmail): self
    {
        $this->customerEmail = $customerEmail;
        return $this;
    }

    public function isVknOrTckn($vknOrTckn): string
    {
        if (strlen($vknOrTckn) == 11) {
            return 'TCKN';
        } elseif (strlen($vknOrTckn) == 10) {
            return 'VKN';
        }
    }
}
