<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotFoundController extends Controller
{
    //
    public function index()
    {
        return response()->json(['error' => 'Route not found.'], 404);
    }
}
