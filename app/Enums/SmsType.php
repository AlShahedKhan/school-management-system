<?php

namespace App\Enums;

enum SmsType: string
{
    case Admission = 'admission';

    case Readmission = 'readmission';

    case Promotion = 'promotion';

    case TeacherRegistration = 'teacher_registration';

    case Income = 'income';
}
