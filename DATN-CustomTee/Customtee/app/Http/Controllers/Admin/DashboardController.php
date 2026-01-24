<?php

namespace App\Http\Controllers\Admin;
use DB;
use Illuminate\Http\Request;
use LDAP\Result;
use App\Http\Requests;
use session;
use Illuminate\Support\Facades\Redirect;
// session_start();

class DashboardController extends Controller
{
      public function DashBoard()
    {
        return view('admin.DashBoard');
    }
}