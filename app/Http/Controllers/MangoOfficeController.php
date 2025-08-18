<?php

namespace App\Http\Controllers;

use App\Models\CallStat;
use Illuminate\Http\Request;

class MangoOfficeController extends Controller
{
    public function callback(Request $request)
    {
        return CallStat::callback($request);
    }
}
