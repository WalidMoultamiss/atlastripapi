<?php

namespace App;

// use Ratchet\Server\IoServer;
// use Ratchet\Http\HttpServer;
// use Ratchet\WebSocket\WsServer;
// use App\src\Chat;
use App\Routing\Route;

class Application
{

  public function __construct()
  {
    $this->route = new Route();
  }

  public function run()
  {
    // $server = IoServer::factory(
    //   new HttpServer(
    //     new WsServer(
    //       new Chat()
    //     )
    //   ),
    //   8000
    // );
    // $server->run();
    // new Chat();

    $this->route->call();
  }

  public function cors()
  {
    header('Access-Control-Allow-Origin: *');
  }
}
