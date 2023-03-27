<?php

namespace QNBFinansbank\Invoice\EInvoice;

use QNBFinansbank\BaseSoapClient;
use QNBFinansbank\Invoice\Adaptor;

class EInvoice
{
    protected Adaptor $data;
    protected BaseSoapClient $soapClient;
    protected string $erpCode;

    public function __construct(string $erpCode, Adaptor $data, BaseSoapClient $soapClient)
    {
        $this->erpCode = $erpCode;
        $this->data = $data;
        $this->soapClient = $soapClient;
    }

    public function sendInvoice(): object
    {
        return $this->soapClient->client()->belgeGonderExt([
            'parametreler' => [
                'belgeNo' => $this->data->getUuid(),
                'vergiTcKimlikNo' => $this->data->getVkn(),
                'belgeTuru' => 'FATURA_UBL',
                'veri' => $this->data->getXml(),
                'belgeHash' => md5($this->data->getXml()),
                'mimeType' => 'application/xml',
                'belgeVersiyon' => '1.3',
                'erpKodu' => $this->erpCode,
            ]
        ]);
    }

    public function invoiceInquiry(string $ettn): object
    {
        try {
            return $this->soapClient->client()->gelenBelgeDurumSorgulaExt([
                'parametreler' => [
                    'vergiTcKimlikNo' => $this->data->getVkn(),
                    'belgeTuru' => 'FATURA',
                    'ettn' => $ettn,
                ]

            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function EInvoiceUser(string $vkn): object
    {
        return $this->soapClient->client()->efaturaKullanicisi([
            "vergiTcKimlikNo" => $vkn,
        ]);
    }

    public function listIncomingDocuments(): object
    {
        return $this->soapClient->client()->gelenBelgeleriListeleExt([
            "parametreler" => [
                "vergiTcKimlikNo" => $this->data->getVkn(),
                "belgeTuru" => "FATURA",
                "erpKodu" => $this->erpCode,
            ]

        ]);
    }

    public function listOutGoingDocuments(string $startDate, string $endDate): object
    {
        $result = $this->soapClient->client()->gidenBelgeDurumSorgula([
            "parametreler" => [
                "baslangicGonderimTarihi" => $startDate,
                "bitisGonderimTarihi" => $endDate,
                "belgeTuru" => "FATURA_UBL",
                "vkn" => $this->data->getVkn(),
            ]
        ]);

        return $result->return;
    }

    public function getIncomingDocuments(string $startDate, string $endDate): object
    {
        return $this->soapClient->client()->gelenBelgeleriAlExt([
            "parametreler" => [
                "erpKodu" => $this->erpCode,
                "vergiTcKimlikNo" => $this->data->getVkn(),
                "baslangicGonderimTarihi" => $startDate,
                "bitisGonderimTarihi" => $endDate, //example Format  20230301
                "belgeTuru" => "FATURA_UBL",
                "vergiTcKimlikNo" => $this->data->getVkn(),
            ]

        ]);
    }

    public function getOutGoingDocuments(string $startDate, string $endDate): object
    {
        return $this->soapClient->client()->gidenBelgeleriAlExt([
            "parametreler" => [
                "erpKodu" => $this->erpCode,
                "vergiTcKimlikNo" => $this->data->getVkn(),
                "baslangicGonderimTarihi" => $startDate,
                "bitisGonderimTarihi" => $endDate, //example Format  20230301
                "belgeTuru" => "FATURA_UBL",
                "vergiTcKimlikNo" => $this->data->getVkn(),
            ]

        ]);
    }

    public function DownloadOutGoingDocuments(string $belgeOid): object
    {
        return $this->soapClient->client()->gidenBelgeleriIndir([
            "vergiTcKimlikNo" => $this->data->getVkn(),
            "belgeFormati" => "PDF",
            "belgeOidListesi" => $belgeOid,
            "belgeTuru" => "FATURA",
        ]);
    }
}
