<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Models\Model;

class Hello extends Controller
{
  public static function index(Request $request)
  {
    $m = new Model();
    Response::json(['hello' => 'atlastrip']);
  }
}
