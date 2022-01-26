<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Facades\ConnectService;

class TelegramCheckReq
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $request->merge(
            [
                'token'       => env('TELEGRAM_BOT_TOKEN'),
                'bot_name'    => env('TELEGRAM_NAME'),
            ]
        );

        return $next($request);
    }
}
