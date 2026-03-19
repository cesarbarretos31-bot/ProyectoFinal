<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        // Esta vista será el contenedor principal
        return view('dashboard_view');
    }
}