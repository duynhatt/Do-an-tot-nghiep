<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use LDAP\Result;
use App\Http\Requests;
class DashboardController extends Controller
{
      public function DashBoard()
    {
        return view('admin.DashBoard');
    }
}