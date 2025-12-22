<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return view('auth.home');
    }
}
