<?php

namespace App\Enums;

enum AcademicRecordStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Graduated = 'graduated';
}
