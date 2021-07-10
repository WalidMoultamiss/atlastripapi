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
        return Response::json(["message" => "Email Not Found"]);
      }
      Response::json($userFind);
      // var_dump($userFind);
      // var_dump(password_hash($user->password, $userFind[0]['password']));
      // echo "1";
      // if (password_hash($user->password, $userFind[0]['password'])) {
      //   echo "2";
      //   unset($userFind[0]['password']);
      //   $response = \App\Http\Middleware\Auth::create($userFind[0]);
      //   $response->dd = $userFind[0];
      //   print_r($response);
      //   Response::json($response);
      // } else {
      //   echo "3";
      //   Response::json(["message" => "password incorrect"]);
      // }
      // echo "4";
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
      "phone" => "required|integer|min:9"
    ];

    $middle = (object) $middleware->validate($user, $rules);

    if ($middle->error) {
      Response::json($middle);
    } else {
      if (!!count(\App\Models\Users::findBy(['email' => $user->email], ["email"]))) {
        return Response::json(["message" => "Email must be unique, already taken"]);
      } else {
        $password = password_hash($user->password, PASSWORD_DEFAULT);
        $user->password = $password;
        $id = \App\Models\Users::create($user);
        \App\Models\Coords::create((object)["user_id" => $id]);
        unset($user->password);
        $user->id = $id;
        $response = \App\Http\Middleware\Auth::create($user);
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
}
