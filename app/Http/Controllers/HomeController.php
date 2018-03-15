<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        //forget any session with order_id we are using in to payment for orders
        session()->forget('order_id');

        //check user if admin and redirect to admin panel
        //if not admin redirect to default home page
        if (Auth::user()->type == "admin")
            return redirect(route('admin.dashboard'));

        return view('home');
    }
}
