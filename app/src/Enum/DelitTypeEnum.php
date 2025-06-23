<?php

namespace App\Enum;

enum DelitTypeEnum: string
{
    case Vol = 'vol';
    case Agression = 'agression';
    case Fraude = 'fraude';
    case Homicide = 'homicide';
    case Vandalism = 'vandalism';
    case TraficDrogue = 'trafic_drogue';
    case Cybercriminalite = 'cybercriminalite';
    case Harcelement = 'harcelement';
    case Escroquerie = 'escroquerie';
    case Contrebande = 'contreband';
}