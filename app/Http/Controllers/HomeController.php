<?php

namespace App\Http\Controllers;

use App\AmazonSku;
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
    public function index()
    {
        return view('home');
    }

    public function addAmazonSku($sku)
    {
        if (strlen($sku) != 10) {
            echo "sku error.";
        }

        (new AmazonSku())->firstOrCreate(['sku' => $sku]);

        return view('addAmazonSku')->with('sku', $sku);
    }
}
