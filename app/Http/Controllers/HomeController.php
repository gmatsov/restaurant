<?php

namespace App\Http\Controllers;

use App\Models\Tables;
use Illuminate\Support\Facades\DB;

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
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $current_user_id = auth()->id();
        $orders = null;
        $tables = Tables::all();
        return view('home', compact('orders', 'tables'));
    }
}