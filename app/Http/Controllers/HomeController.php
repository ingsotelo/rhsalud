<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;



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
        $this->middleware('verified');
    }

    /**
     * Show the application index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        if (\Auth::user()->role == 'Administrador') {
            return view('dashboard');
        }

        $cfdis = \DB::select('SELECT * FROM rhsalud.cfdis WHERE name = "'.\Auth::user()->name.'";');

        $data = [
            'cfdis' => $cfdis,
        ];

        return view('home', $data);

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        if (\Auth::user()->role == 'Usuario') {
            return view('home');
        }

        return view('dashboard');
    }
}
