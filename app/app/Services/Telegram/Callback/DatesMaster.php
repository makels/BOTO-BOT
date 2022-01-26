<?php

namespace App\Services\Telegram\Callback;


use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserTimetable;
use Jenssegers\Date\Date;
use Illuminate\Support\Carbon;

class DatesMaster extends CallbackQuery
{
    /**
     * DatesMaster constructor.
     * @param Request $request
     * @param null $month
     */
    public function __construct(Request $request, $month = null)
    {
        parent::__construct($request);
        $master_id = parent::setMasterID();
        $this->back = 'Master_'.parent::getAddressID();
        return parent::editMessage(__('Выберете дату'), $this->masterDate($master_id, $month));
    }

    private function masterDate($master_id, $month) {

        switch ($month) {
            case 'DateNext':
                $first_day = new Carbon('first day of next month');
                $date[] = [
                    ['text' => hex2bin('e28faa'), 'callback_data' => 'DatesMaster_'.$master_id],
                    $this->getNameOfMonth($first_day),
                    ['text' => hex2bin('e28fa9'), 'callback_data' => 'DateLater_'.$master_id]
                ];
                $month = UserTimetable::getNextMonthBot();
                break;
            case 'DateLater':
                $first_day = new Carbon('first day of 2 months');
                $date[] = [
                    ['text' => hex2bin('e28faa'), 'callback_data' => 'DateNext_'.$master_id],
                    $this->getNameOfMonth($first_day),
                    ['text' => ' ', 'callback_data' => '-']
                ];
                $month = UserTimetable::getMonthLaterBot();
                break;
            default:
                $first_day = Carbon::now();
                $date[] = [
                    ['text' => ' ', 'callback_data' => '-'],
                    $this->getNameOfMonth($first_day),
                    ['text' => hex2bin('e28fa9'), 'callback_data' => 'DateNext_'.$master_id]
                ];
                $month = UserTimetable::getCurrentMonthBot();
        }

        $days = []; //name of the days of the week
        foreach (UserTimetable::getDays() as $day)
            $days[] = ['text' => $day, 'callback_data' => '-'];
        $date[] = $days;
        unset($days);

        $master = User::find($master_id);
        $master_days = [];
        $i = 1;
        foreach ($month as $k => $day) {

            if (UserTimetable::isWorkDay($master, parent::getAddressID(), parent::getServiceID(), Carbon::parse($k), $first_day)) {
                $master_days[] = ['text' => $day, 'callback_data' => 'Time_'.$k];
            } else {
                $master_days[] = ['text' => ' ', 'callback_data' => '-'];
            }

            if ($i % 7 == 0) {
                $date[] = $master_days;
                $master_days = [];
            }

            $i++;
        }
        $i--;
        while ($i % 7 != 0) {
            $master_days[] = ['text' => ' ', 'callback_data' => '-'];
            $i++;
        }
        $date[] = $master_days;
        return parent::buildInlineKeyboard($date);
    }

    private function getNameOfMonth (Carbon $date) {
        return ['text' => Date::parse($date->toFormattedDateString())->format('F'), 'callback_data' => '-'];
    }
}
