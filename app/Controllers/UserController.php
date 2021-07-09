<?php

namespace App\Controllers;

use App\Http\Middleware\Auth;
use App\Http\middleware\middleware;
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
    $middleware = new middleware();
    $user = $request->json();

    $userFind = Users::findBy(['email' => $user->email]);

    if (!count($userFind)) {
      return Response::json(["message" => "Email Not Found"]);
    }

    $rules = [
      "email" => "required|email",
      "password" => "required",
    ];

    $middle = $middleware->validate($user, $rules);
    if ($middle->error) {
      Response::json($middle);
    } else {
      if (password_hash($middle->password, $userFind[0]['password'])) {
        unset($userFind[0]['password']);
        $response = Auth::create($userFind[0]);
        $response->dd = $userFind[0];
        Response::json($response);
      } else {
        Response::json(["message" => "password incorrect"]);
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
    $dbh = (new \App\Config\Database())->connect();
    $middleware = new \App\Http\middleware\Middle();
    exit;
    $user = $request->json();

    $rules = [
      "first_name" => "required|min:3|max:25",
      "last_name" => "required|min:3|max:25",
      "email" => "required|email",
      "password" => "required",
      "phone" => "required|integer|min:9"
    ];

    $middle = $middleware->validate($user, $rules);

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
    $response = Coords::showAllByquery($sql);
    Response::json($response);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Http\Request  $request
   */
  public static function update(Request $request)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \Http\Request  $request
   */
  public static function destroy(Request $request)
  {
    //
  }
}
