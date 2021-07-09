<?php

use App\Application;
use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Http\Middleware\Auth;
use App\Http\middleware\middleware;
use App\Http\Request;
use App\Http\Response;
use App\Models\Coords;
use App\Models\Users;
use App\Routing\Route;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

$app = new Application();

$app->cors();

/*
|------------------------------------------------------------------
| API Routes
|------------------------------------------------------------------
|
| Here is where you can register API routes for your application. 
|
*/

Route::post('/login', [UserController::class, 'index']);
Route::post('/register', function (Request $request) {
  $middleware = new middleware();
  $user = $request->json();

  $rules = [
    "first_name" => "required|min:3|max:25",
    "last_name" => "required|min:3|max:25",
    "email" => "required|email",
    "password" => "required",
    "phone" => "required|integer|min:9"
  ];

  $middle = $middleware->validate($user, $rules);

  if (!!count(Users::findBy(['email' => $user->email], ["email"]))) {
    return Response::json(["message" => "Email must be unique, already taken"]);
  }

  if ($middle->error) {
    Response::json($middle);
  } else {
    $password = password_hash($user->password, PASSWORD_DEFAULT);
    $user->password = $password;
    $id = Users::create($user);
    Coords::create((object)["user_id" => $id]);
    unset($user->password);
    $user->id = $id;
    $response = Auth::create($user);
    Response::json($response);
  }
});
Route::post('/distance', [UserController::class, 'show']);
Route::post('/auth', [AuthController::class, 'index']);


Route::get('/hello', function (Request $request) {
  Response::json(['hello' => 'atlastrip']);
});

$app->run();
