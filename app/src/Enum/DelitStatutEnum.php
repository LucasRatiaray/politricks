<?php

namespace App\Enum;

enum DelitStatutEnum: string
{
    case EnCours = 'En cours';
    case ClasseSansSuite = 'Classé sans suite';
    case Rejete = 'Rejeté';
    case EnInstruction = 'En instruction';
    case Condamne = 'Condamné';
    case Acquitte = 'Acquitté';
}