<?php

namespace App\Enum;

enum PartenaireNiveauRisqueEnum: string
{
    case TresFaible = 'tres_faible';
    case Faible = 'faible';
    case Modere = 'modere';
    case Eleve = 'eleve';
    case TresEleve = 'tres_eleve';
}