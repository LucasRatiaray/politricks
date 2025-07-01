<?php

namespace App\Enum;

enum DelitTypeEnum: string
{
    case Vol = 'Vol';
    case Agression = 'Agression';
    case IllegalInterests = "Prise d'intérêts illégaux";
    case Fraude = 'Fraude';
    case Homicide = 'Homicide';
    case Vandalism = 'Vandalisme';
    case TraficDrogue = 'Trafic de drogue';
    case Cybercriminalite = 'Cybercriminalité';
    case Harcelement = 'Harcèlement';
    case Escroquerie = 'Escroquerie';
    case Contrebande = 'Contrebande';
    case CorruptionOfAMinor = 'Corruption de mineur';
}