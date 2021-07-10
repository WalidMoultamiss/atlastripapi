<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;


class SosController extends Controller
{


  public static function store(Request $request)
  {
    $sos = $request->json();
    \App\Models\Sos::create($sos);
  }

  public static function destroy(Request $request)
  {
    $sos = $request->json();
    Response::json($sos);
  }
}
