<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class ScanControllerTemp extends Controller
{
    public function index(): Response
    {
        return Inertia::render('ScannerTest/Index');
    }
}
