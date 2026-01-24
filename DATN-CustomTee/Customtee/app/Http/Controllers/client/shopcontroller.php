<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function Shop()
    {
        return view('client.Shop');
    }
     public function ShopSingle()
    {
        return view('client.ShopSingle');
    }
}
