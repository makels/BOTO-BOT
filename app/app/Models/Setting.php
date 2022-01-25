<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Setting
 * @package App\Models
 */
class Setting extends Model
{

    /** @var string  */
    const GS_URL = "GS_SpreadsheetID";

    /** @var string  */
    const TELEGRAM_HEADER = "TELEGRAM_HEADER";

    /** @var bool  */
    public $timestamps = false;

    /** @var string[]  */
    protected $fillable = [
        'name',
        'value'
    ];

    /**
     * @param $option
     * @return mixed
     */
    public static function get($option)
    {
        return self::where('name', $option)->first();
    }

    /**
     * @param $option
     * @param $value
     */
    public static function set($option, $value)
    {
        $setting = new Setting();
        $setting->where('name', $option)->delete();

        $setting->name = $option;
        $setting->value = $value;

        $setting->save();
    }


}
