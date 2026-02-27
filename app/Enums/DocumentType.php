<?php

namespace App\Enums;

enum DocumentType: string
{
  case NationalID = 'national_id';
  case ResidenceProof = 'residence_proof';
}
