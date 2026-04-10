<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function index()
    {
        return view('home');
    }

    /**
     * @return Factory|View|Application
     */
    public function termsAndConditions()
    {
        return view('site.terms-and-conditons');
    }
}
