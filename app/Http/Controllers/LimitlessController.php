<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LimitlessController extends Controller
{
    public function index(Request $request): View
    {
        return view('index');
    }
}
