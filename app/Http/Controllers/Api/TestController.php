<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TestController extends Controller
{
    public function index()
    {
        broadcast(new \App\Events\ExampleEvent);
        dd(1);
    }
}
