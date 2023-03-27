<?php

namespace QNBFinansbank\Invoice\EArchive;

use Exception;
use QNBFinansbank\BaseSoapClient;
use QNBFinansbank\Invoice\Adaptor;

class Archive
{
    private $sube = "DFLT";
    private $kasa = "DFLT";
    protected Adaptor $data;
    protected BaseSoapClient $soapClient;

    public function __construct(Adaptor $data, BaseSoapClient $soapClient)
    {
        $this->data = $data;
        $this->soapClient = $soapClient;
    }

    public function sendArchive(): object
    {
        $result = $this->soapClient->client()->faturaOlustur(
            [
                "input" => json_encode([
                    "donenBelgeFormati" => 9,
                    "islemId" => $this->data->getUuid(),
                    "vkn" => $this->data->getVkn(),
                    "sube" => $this->sube,
                    "kasa" => $this->kasa,

                ]),
                "fatura" => [
                    "belgeFormati" => "UBL",
                    "belgeIcerigi" => $this->data->getXml(),
                ]
            ],

        )->return;

        if ($result->resultCode != 'AE0000') {
            throw new Exception($result->resultCode . ' - ' . $result->resultText);
        }
        return $result;
    }

    public function inguireArchive(string $uuid): object
    {
        $result = $this->soapClient->client()->faturaSorgula([
            "input" => json_encode([
                "faturaUuid" => $uuid,
            ]),
        ])->return;

        return $result;
    }

    public function hasEInvoice(string $vkn): object
    {
        try {
            return $this->soapClient->client()->efaturaKullanicisi(
                array(
                    "vergiTcKimlikNo" => $vkn,
                )
            );
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function getZipArchive(string $uuid): object
    {
        return $this->soapClient->client()->faturaZipiAl([
            "input" => json_encode([
                "uuidList" => [$uuid],
                'donenBelgeFormati' => 3
            ]),
        ]);
    }

    public function callToCancelArchive(string $vergiTcKimlikNo, string $uuid, string $invoiceNo): object
    {
        return $this->soapClient->client()->faturaIptalEt([
            "input" => json_encode([
                "vkn" => $vergiTcKimlikNo,
                "faturaUuid" => $uuid,
                "faturaNo" => $invoiceNo,
            ])
        ]);
    }
}
