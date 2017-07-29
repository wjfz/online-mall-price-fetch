<?php

namespace App\Http\Controllers;

use App\AmazonSku;
use App\Sku;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($sku = '', $result = '')
    {
        $data = [
            'sources' => Sku::$sources,
            'sku'     => $sku,
            'result'  => $result,
        ];

        return view('home')->with($data);
    }

    public function addAmazonSku($sku)
    {
        if (strlen($sku) != 10) {
            echo "sku error.";
        }

        (new AmazonSku())->firstOrCreate(['sku' => $sku]);

        return view('addAmazonSku')->with('sku', $sku);
    }

    public function addSku(Request $request)
    {
        $source = $request->input('source');
        if (!array_key_exists($source, Sku::$sources)) {
            throw new \Exception("source不对".$source);
        }

        $sku = $request->input('sku');

        $model = Sku::addSourceSku($source, $sku);

        return redirect()->action(
            'HomeController@index', ['sku' => $sku, 'failed' => $model ? 'success' : 'failed']
        );
    }
}
