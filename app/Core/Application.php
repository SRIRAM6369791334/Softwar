<?php

namespace App\Core;

class Application
{
    private static ?Application $instance = null;
    public Router $router;
    public Database $db;
    public Request $request;
    public Response $response;

    public function __construct()
    {
        self::$instance = $this;
        
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        
        // Database is initialized lazily or here based on config
        // $this->db = Database::getInstance();
    }

    public static function app(): Application
    {
        return self::$instance;
    }

    public function run(): void
    {
        echo $this->router->resolve();
    }
}
