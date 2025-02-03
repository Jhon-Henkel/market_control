<?php

namespace App\Modules\ChatBot\Enum;

enum ResponseChatEnum: string
{
    case Ok = 'ok';
    case NoMessage = 'no message';
    case Unauthorized = 'unauthorized';
    case InvalidUrl = 'invalid url';
    case ErrorToProcessNfce = 'error to process nfce';
}
