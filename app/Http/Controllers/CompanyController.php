<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function dashboard()
    {
        return view('auth.register'); // make sure this view exists or change accordingly
    }
}
