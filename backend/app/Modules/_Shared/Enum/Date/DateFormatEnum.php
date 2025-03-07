<?php

namespace App\Modules\_Shared\Enum\Date;

enum DateFormatEnum: string
{
    case AppDefault = 'Y-m-d H:i:s';
    case Brazilian = 'd/m/Y H:i:s';
}
