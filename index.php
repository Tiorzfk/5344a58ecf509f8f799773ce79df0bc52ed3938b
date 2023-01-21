<?php 
    require __DIR__ . '/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    require_once __DIR__.'/server.php';

    $router = new \Bramus\Router\Router();

    $router->set404(function() {
        header('HTTP/1.1 404 Not Found');
        header('Content-Type: application/json');

        echo json_encode([
            'status' => 404,
            'message' => 'route not defined'
        ]);
    });

    $router->before('GET|POST', '/api/mail/.*', function() {
        require __DIR__.'/server.php';
        if (!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $server->getResponse()->send();

            echo json_encode(array('success' => false, 'message' => 'Unauthorize'));
            exit;
        }
    });

    
    $router->mount('/api', function() use ($router) {
        $router->post('/token', '\App\Controllers\AuthController@getToken');
        $router->post('/login', '\App\Controllers\AuthController@login');
        $router->post('/register', '\App\Controllers\AuthController@register');
        $router->mount('/mail', function() use ($router) {
            $router->post('/send', '\App\Controllers\MailController@send');
            $router->get('/status/{id}', '\App\Controllers\MailController@status');
            $router->post('/run', '\App\Controllers\MailController@runWorker');
        });
    });
    
    // Run it!
    $router->run();

?>