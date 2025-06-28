<?php

namespace App\Enum;

enum DelitGraviteEnum: string
{
    case Mineur = 'mineur';
    case Modere = 'modere';
    case Grave = 'grave';
    case TresGrave = 'tres_grave';
    case ExtremementGrave = 'extremement_grave';
}