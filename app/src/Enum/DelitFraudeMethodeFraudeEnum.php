<?php

namespace App\Enum;

enum DelitFraudeMethodeFraudeEnum: string
{
    case UsurpationIdentite = 'usurpation_identite';
    case FauxDocuments = 'faux_documents';
    case ManipulationComptes = 'manipulation_comptes';
    case Corruption = 'corruption';
    case BlanchimentArgent = 'blanchiment_argent';
    case CyberFraude = 'cyber_fraude';
}