<?php

namespace App\Services\Telegram\Callback;

use Illuminate\Http\Request;

class DateLater extends CallbackQuery
{
    /**
     * DateLater constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        if( is_null(parent::getMasterID()) )
            return new DatesService($request, 'DateLater');
        return new DatesMaster($request, 'DateLater');
    }
}