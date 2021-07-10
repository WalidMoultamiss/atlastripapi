<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;


class UserController extends Controller
{

  /**
   * Display a listing of the resource.
   *
   * @return \Http\Request
   */
  public static function index(Request $request)
  {
    $middleware = new \App\Http\Middleware\Middle();
    $user = $request->json();
    $userFind = \App\Models\Users::findBy(['email' => $user->email]);

    $rules = [
      "email" => "required|email",
      "password" => "required",
    ];

    $middle = (object) $middleware->validate($user, $rules);

    if ($middle->error) {
      Response::json($middle);
    } else {
      if (!count($userFind)) {
        return Response::json(["message" => "Email Not Found", "error" => true]);
      } else {
        if (password_verify($user->password, $userFind[0]['password'])) {
          unset($userFind[0]['password']);
          $response = \App\Http\Middleware\Auth::create($userFind[0]);
          $response->dd = $userFind[0];
          $response->error = false;
          Response::json($response);
        } else {
          Response::json(["message" => "password incorrect", "error" => true]);
        }
      }
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Http\Request  $request
   */
  public static function store(Request $request)
  {
    $middleware = new \App\Http\Middleware\Middle();
    $user = $request->json();

    $rules = [
      "first_name" => "required|min:3|max:25",
      "last_name" => "required|min:3|max:25",
      "email" => "required|email",
      "password" => "required",
      "phone" => "required|min:9"
    ];

    $middle = (object) $middleware->validate($user, $rules);

    if ($middle->error) {
      Response::json($middle);
    } else {
      if (!!count(\App\Models\Users::findBy(['email' => $user->email], ["email"]))) {
        return Response::json(["message" => "Email must be unique, already taken", "error" => true]);
      } else {
        $password = password_hash($user->password, PASSWORD_DEFAULT);
        $user->password = $password;
        $id = \App\Models\Users::create($user);
        \App\Models\Coords::create((object)["user_id" => $id]);
        unset($user->password);
        $user->id = $id;
        $response = \App\Http\Middleware\Auth::create($user);
        $response->error = false;
        Response::json($response);
      }
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  \Http\Request  $request
   */
  public static function show(Request $request)
  {
    $coords = $request->json();
    $sql = "SELECT user_id FROM coords WHERE ( 6371 * ACOS(COS(RADIANS($coords->lan)) * COS(RADIANS(lan)) * COS(RADIANS(lon) - RADIANS($coords->lon)) + SIN(RADIANS($coords->lan)) * SIN(RADIANS(lan)))) < 2;";
    $response = \App\Models\Coords::showAllByquery($sql);
    Response::json($response);
  }



  public static function sos(Request $request)
  {
    $sos = $request->json();
    \App\Models\Sos::create($sos);
  }
}
