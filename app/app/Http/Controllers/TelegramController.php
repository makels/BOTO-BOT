<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{

    public function main(Request $request)
    {
        Log::info("Begin command");
    }

}
