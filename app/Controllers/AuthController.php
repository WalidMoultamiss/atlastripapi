<?php

namespace App\Controllers;

use App\Http\Middleware\Auth;
use App\Http\Request;
use App\Http\Response;

class AuthController extends Controller
{
  public static function index(Request $request)
  {
    $auth = Auth::verify($request->authorization);
    Response::json($auth);
  }
}
