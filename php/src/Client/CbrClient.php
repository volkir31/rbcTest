<?php

declare(strict_types=1);

namespace App\Client;

use App\Entity\Currency;
use App\VO\CurrencyCode;
use DateTimeInterface;
use Exception;
use Psr\Log\LoggerInterface;
use SimpleXMLElement;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class CbrClient {
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client, private readonly LoggerInterface $logger) {
        $this->client = $client->withOptions([
            'headers' => [
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => 'http://web.cbr.ru/GetCursOnDate',
                'Content-Length' => 'length'
            ]
        ]);
    }

    /**
     * @param DateTimeInterface $date
     * @return Currency[]
     * @throws Exception|TransportExceptionInterface
     */
    public function getCurrencyOnDate(DateTimeInterface $date): array {
        $xml = new SimpleXMLElement('<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"/>');
        $body = $xml->addChild('soap:Body');
        $req = $body->addChild('GetCursOnDate', null, 'http://web.cbr.ru/');
        $req->addChild('On_date', $date->format('Y-m-d'), null);
        $resp = $this->client->request('POST', 'https://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx', ['body' => $xml->asXML()]);
        $content = $resp->getContent();

        $xml = new SimpleXMLElement($content);

        return array_filter(array_map(function (SimpleXMLElement $currency) {
            $name = (string)$currency->Vname;
            $price = (float)$currency->VunitRate;
            $code = (int)$currency->Vcode;
            try {
                $charCode = CurrencyCode::from((string)$currency->VchCode);
            } catch (Exception $exception) {
                $this->logger->error($exception->getMessage());
                return null;
            }

            return new Currency(
                name: $name,
                price: $price,
                code: $code,
                charCode: $charCode
            );
        }, $xml->xpath('//ValuteCursOnDate')));
    }
}
