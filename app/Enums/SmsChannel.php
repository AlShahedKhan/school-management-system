<?php

namespace App\Enums;

enum SmsChannel: string
{
    case Message = 'message';

    case Call = 'call';

    case Both = 'both';
}
