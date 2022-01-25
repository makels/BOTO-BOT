<?php
namespace App\GoogleSheet;

use App\Models\Product;
use App\Models\Setting;
use Google\Service\Sheets;
use Google_Service_Sheets;
use function PHPUnit\Framework\isNull;

/**
 * Class GSClient
 * @package App\GoogleSheet
 */
class GSClient
{

    /** @var string */
    protected $sheet_url;


    public function __construct()
    {
        $this->sheet_url = Setting::get(Setting::GS_URL);
    }

    /**
     * @return null|array
     */
    public function import()
    {
        $data = $this->load();
        if(is_null($data)) {
            return null;
        }

        Product::clear();

        $products = [];
        foreach ($data as $row) {
            $products[] = Product::add([
                'name'          => $row[1],
                'desc'          => $row[3],
                'qty'           => $row[2],
                'price'         => $row[4],
                'img'           => $row[5],
                'availability'  => $row[6],
            ]);
        }

        return $products;
    }

    /**
     * @return array[]|null
     */
    private function load()
    {
        $client = new \Google_Client();
        $client->setApplicationName(env('GS_NAME'));
        $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');

        try {
            $client->setAuthConfig(base_path() . env('GS_CREDENTIALS'));

            $service = new Google_Service_Sheets($client);
            $spreadsheetId = Setting::get(Setting::GS_URL)->value;
            $get_range = env("GS_RANGE");

            $response = $service->spreadsheets_values->get($spreadsheetId, $get_range);
            return $response->getValues();
        } catch (\Exception $ex) {
            return null;
        }

    }

    /**
     * @param array $data
     */
    private function importProducts(array $data) {

    }
}
