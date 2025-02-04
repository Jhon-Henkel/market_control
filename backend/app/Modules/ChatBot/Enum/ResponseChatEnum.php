<?php

namespace App\Modules\ChatBot\Enum;

enum ResponseChatEnum: string
{
    case Ok = 'ok';
    case NoMessage = 'no message';
    case InvalidOption = 'invalid option';
    case CancelOption = 'cancel option';
    case Unauthorized = 'unauthorized';
    case InvalidUrl = 'invalid url';
    case ErrorToProcessNfce = 'error to process nfce';
    case NfceAlreadyProcessed = 'nfce already processed';
}
