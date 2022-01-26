<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class TelegramService extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return 'Telegram';
    }
}
