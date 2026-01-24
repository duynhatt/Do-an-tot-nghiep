<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function Home(){
        return view('Admin.Layout.layout');
    }
    public function Dashboard(){
        return view('Admin.Dashboard.Dashboard');
    }
}
