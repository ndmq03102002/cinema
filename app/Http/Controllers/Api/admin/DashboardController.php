<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
    public function index()
    {
       
        return view('admin.dashboard.home.index');
    }

    public function homepage()
    {
        return view('admin.auth.index');
    }

    public function home()
    {
        return view('admin.dashboard.home.homepage');
    }
}
