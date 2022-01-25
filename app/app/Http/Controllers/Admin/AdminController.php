<?php
namespace App\Http\Controllers\Admin;

use App\GoogleSheet\GSClient;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use function PHPUnit\Framework\isNull;

class AdminController extends Controller
{

    /**
     * AdminController constructor.
     */
    public function __construct()
    {
        $this->middleware("auth");
    }

    public function index()
    {
        $data = [
            'url_sheet' => Setting::get(Setting::GS_URL),
            'telegram_header' => Setting::get(Setting::TELEGRAM_HEADER)
        ];
        return view('admin.settings', $data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Request $request)
    {
        $data = $request->validate([
            'url_sheet' => 'required|max:255',
            'telegram_header' => 'required|max:255',
        ]);

        Setting::set(Setting::GS_URL, $data['url_sheet']);
        Setting::set(Setting::TELEGRAM_HEADER, $data['telegram_header']);

        return response()->redirectToRoute('admin.settings');
    }

    public function import()
    {
        $client = new GSClient();
        $products = $client->import();

        if(is_null($products)) {
            $status = -1;
            $message = "Import error";
        } else {
            $status = 0;
            $message = sprintf("Import done. %s products imported", count($products));
        }

        $data = [
            'status'            => $status,
            'gs_message'        => $message,
            'url_sheet'         => Setting::get(Setting::GS_URL),
            'telegram_header'   => Setting::get(Setting::TELEGRAM_HEADER)
        ];
        return view('admin.settings', $data);
    }

}
