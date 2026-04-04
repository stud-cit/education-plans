<?php

namespace App\Enums;

enum Degree: int
{
  case BACHELOR = 2;
  case MAGISTER = 4;
  case POSTGRADUATE = 8;
  case BACHELOR_FOREIGN = 10;
  case BACHELOR_ENGLISH_PROGRAMS = 11;
}
