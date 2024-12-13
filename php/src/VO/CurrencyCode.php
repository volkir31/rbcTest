<?php

namespace App\VO;

enum CurrencyCode: string {
    case AUD = 'AUD';
    case AZN = 'AZN';
    case GBP = 'GBP';
    case AMD = 'AMD';
    case BYN = 'BYN';
    case BGN = 'BGN';
    case BRL = 'BRL';
    case HUF = 'HUF';
    case VND = 'VND';
    case HKD = 'HKD';
    case GEL = 'GEL';
    case DKK = 'DKK';
    case AED = 'AED';
    case USD = 'USD';
    case EUR = 'EUR';
    case EGP = 'EGP';
    case INR = 'INR';
    case IDR = 'IDR';
    case KZT = 'KZT';
    case CAD = 'CAD';
    case QAR = 'QAR';
    case KGS = 'KGS';
    case CNY = 'CNY';
    case MDL = 'MDL';
    case NZD = 'NZD';
    case NOK = 'NOK';
    case PLN = 'PLN';
    case RON = 'RON';
    case XDR = 'XDR';
    case SGD = 'SGD';
    case TJS = 'TJS';
    case THB = 'THB';
    case TRY = 'TRY';
    case TMT = 'TMT';
    case UZS = 'UZS';
    case UAH = 'UAH';
    case CZK = 'CZK';
    case SEK = 'SEK';
    case CHF = 'CHF';
    case RSD = 'RSD';
    case ZAR = 'ZAR';
    case KRW = 'KRW';
    case JPY = 'JPY';
    case RUR = 'RUR';

    public function isRur(): bool {
        return $this === self::RUR;
    }
}
