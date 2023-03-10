<?php

namespace App\Enums;

enum RequestEnum: string
{
    use EnumHelper;

    case Pending = 'pending';
    case Accepted = 'accepted';
    case Refused = 'refused';
    case Assignment = 'assignment';
    case Viewing = 'viewing';

}
