<?php

namespace App\Enum;

enum DocumentLangueDocumentEnum: string
{
    case FR = 'français';
    case EN = 'anglais';
    case ES = 'espagnol';
    case DE = 'allemand';
    case IT = 'italien';
    case PT = 'portugais';
    case ZH = 'chinois';
    case JA = 'japonais';
    case AR = 'arabe';
    case RU = 'russe';
    case HI = 'hindi';
    case KO = 'coréen';
    case OTHER = 'autre'; 
}