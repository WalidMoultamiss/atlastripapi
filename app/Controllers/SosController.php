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
    $sql = "DELETE FROM sos where user_id = $sos->user_id;";
    \App\Models\Sos::deleteByQuery($sql);
    Response::json(["message" => "SOS cancelled"]);
  }
}
