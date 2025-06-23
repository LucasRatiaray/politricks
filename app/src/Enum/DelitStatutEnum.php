<?php

namespace App\Enum;

enum DelitStatutEnum: string
{
    case EnCours = 'en_cours';
    case Termine = 'termine';
    case ClasseSansSuite = 'classe_sans_suite';
    case Rejete = 'rejete';
    case EnInstruction = 'en_instruction';
    case Condamne = 'condamne';
    case Acquitte = 'acquitte';
}