<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

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
