<?php

namespace App\Integration\Invoice;

class ItemAdaptor
{
    protected $unitCode = 'NIU';
    protected array $data = [];
    protected string $currencyId;

    public function __construct(string $currencyId, array $data)
    {
        $this->setCurrencyId($currencyId);
        $this->setDatas($data);
    }

    public function xmlInvoiceLines(): array
    {
        return array_map(function ($taxTotal) {
            return ' <cac:InvoiceLine>
            <cbc:ID>' . $taxTotal["id"] . '</cbc:ID>
            <cbc:Note>' . $taxTotal["note"] . '</cbc:Note>
            <cbc:InvoicedQuantity unitCode="' . $this->unitCode . '">' . $taxTotal["quantity"] . '</cbc:InvoicedQuantity>
            <cbc:LineExtensionAmount currencyID="' . $this->currencyId . '">' . $taxTotal["price"] . '</cbc:LineExtensionAmount>
            <cac:TaxTotal>
                <cbc:TaxAmount currencyID="' . $this->currencyId . '">' . $taxTotal["vatRatio"] . '</cbc:TaxAmount>
                <cac:TaxSubtotal>
                    <cbc:TaxableAmount currencyID="' . $this->currencyId . '">' . $taxTotal["purePrice"] . '</cbc:TaxableAmount>
                    <cbc:TaxAmount currencyID="' . $this->currencyId . '">' . $taxTotal["vatPrice"] . '</cbc:TaxAmount>
                    <cbc:Percent>' . $taxTotal["vatRatio"] . '</cbc:Percent>
                    <cac:TaxCategory>
                        <cac:TaxScheme>
                            <cbc:Name>' . $taxTotal["taxName"] . '</cbc:Name>
                            <cbc:TaxTypeCode>' . $taxTotal["taxTypeCode"] . '</cbc:TaxTypeCode>
                        </cac:TaxScheme>
                    </cac:TaxCategory>
                </cac:TaxSubtotal>
            </cac:TaxTotal>
            <cac:Item>
                <cbc:Name>' . $taxTotal["description"] . '</cbc:Name>
            </cac:Item>
            <cac:Price>
                <cbc:PriceAmount currencyID="' . $this->currencyId . '">' . $taxTotal["purePrice"] . '</cbc:PriceAmount>
            </cac:Price>
        </cac:InvoiceLine>';
        }, $this->data);
    }

    public function setDatas(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function setCurrencyId(string $currencyId): self
    {
        $this->currencyId = $currencyId;
        return $this;
    }
}
