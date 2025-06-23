<?php

namespace App\Enum;

enum DocumentNiveauConfidentialiteEnum: string
{
  case Public = 'public';
  case Interne = 'interne';
  case Restreint = 'restreint';
  case Secret = 'secret';
}