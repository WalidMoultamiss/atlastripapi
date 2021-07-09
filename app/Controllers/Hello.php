<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

class Hello extends Controller
{
  public static function index(Request $request)
  {
    Response::json(['hello' => 'atlastrip']);
  }
}
