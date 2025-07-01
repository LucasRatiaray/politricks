<?php

namespace App\Enum;

enum DelitGraviteEnum: string
{
    case Mineur = 'Mineur';
    case Modere = 'Modéré';
    case Grave = 'Grave';
    case TresGrave = "Ça va piquer";
    case ExtremementGrave = "C'est chaud 💀";
}