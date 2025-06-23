<?php

namespace App\Enum;

enum DelitFinancierDeviseEnum: string
{
    case USD = 'USD';
    case EUR = 'EUR';
    case GBP = 'GBP';
    case JPY = 'JPY';
    case CHF = 'CHF';
    case AUD = 'AUD';
    case CAD = 'CAD';
    case CNY = 'CNY';
    case INR = 'INR';
    case RUB = 'RUB';
    case BRL = 'BRL';
    case ZAR = 'ZAR';
    case OTHER = 'AUTRE';
}