<?php

namespace App\Enum;

enum DelitFraudeTypeFraudeEnum: string
{
    case FraudeFiscale = 'fraude_fiscale';
    case FraudeSociale = 'fraude_sociale';
    case FraudeDouaniere = 'fraude_douaniere';
    case FraudeFinanciere = 'fraude_financiere';
    case Autre = 'autre';
}