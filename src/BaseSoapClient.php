<?php

namespace QNBFinansbank;

use SoapClient;

class BaseSoapClient
{
    private string $url;
    private string $vkn;
    private string $userName = '';
    private string $password;
    private ?SoapClient $client = null;

    public function __construct(string $url, string $vkn, string $password)
    {
        $this->setUrl($url);
        $this->setVkn($vkn);
        $this->setPassword($password);
    }

    function client(): SoapClient
    {
        if (!$this->client) {
            $this->client = new \SoapClient($this->url);
            $auth = $this->soapClientWSSecurityHeader($this->userName === '' ? $this->vkn : $this->userName, $this->password);
            $this->client->__setSoapHeaders($auth);
        }
        return $this->client;
    }

    function soapClientWSSecurityHeader($user, $password)
    {
        $tm_created = gmdate('Y-m-d\TH:i:s\Z');
        $tm_expires = gmdate('Y-m-d\TH:i:s\Z', gmdate('U') + 180);

        $simple_nonce = mt_rand();
        $encoded_nonce = base64_encode($simple_nonce);

        $ns_wsse = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
        $ns_wsu = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
        $password_type = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest';
        $encoding_type = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary';

        $root = new \SimpleXMLElement('<root/>');
        $security = $root->addChild('wsse:Security', null, $ns_wsse);
        $timestamp = $security->addChild('wsu:Timestamp', null, $ns_wsu);
        $timestamp->addAttribute('wsu:Id', 'Timestamp-28');
        $timestamp->addChild('wsu:Created', $tm_created, $ns_wsu);
        $timestamp->addChild('wsu:Expires', $tm_expires, $ns_wsu);
        $usernameToken = $security->addChild('wsse:UsernameToken', null, $ns_wsse);
        $usernameToken->addChild('wsse:Username', $user, $ns_wsse);
        $usernameToken->addChild('wsse:Password', $password, $ns_wsse)->addAttribute('Type', $password_type);
        $usernameToken->addChild('wsse:Nonce', $encoded_nonce, $ns_wsse)->addAttribute('EncodingType', $encoding_type);
        $usernameToken->addChild('wsu:Created', $tm_created, $ns_wsu);
        $root->registerXPathNamespace('wsse', $ns_wsse);
        $full = $root->xpath('/root/wsse:Security');
        $auth = $full[0]->asXML();

        return new \SoapHeader($ns_wsse, 'Security', new \SoapVar($auth, XSD_ANYXML), true);
    }


    private function setUrl(string $url): void
    {
        $this->url = $url;
    }

    private function setVkn(string $vkn): void
    {
        $this->vkn = $vkn;
    }

    private function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setUserName(string $userName): self
    {
        $this->userName = $userName;
        return $this;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }
}
