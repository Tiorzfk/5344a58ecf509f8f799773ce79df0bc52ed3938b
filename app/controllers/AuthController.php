<?php
namespace App\Controllers;

require_once __DIR__.'/../../vendor/autoload.php';

use App\Services\AuthService;
use Rakit\Validation\Validator;

class AuthController {

    private $service;
    private $validator;

    public function __construct(){
        $this->service = new AuthService;
        $this->validator = new Validator;
        // $this->validator->addValidator('unique', new UniqueRule($pdo));
    }

    public function login(){
        $request = \OAuth2\Request::createFromGlobals();
        $response = new \OAuth2\Response();
        $entityBody = file_get_contents('php://input');
        $body = (array)json_decode($entityBody);
        $validation = $this->validator->validate($body, [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'client_id' => 'required',
            'response_type' => 'required',
            'state' => 'required',
        ]);
        
        if ($validation->fails()) {
            $errors = $validation->errors();
            echo json_encode([
                'status' => 400, 
                'message' => 'Bad Request',
                'result' => $errors->firstOfAll()
            ]);
            exit;
        }
        
        $user = $this->service->getUserByEmail($body['email']);
        
        header('Content-Type: application/json; charset=UTF-8');
        if(!$user)
        {
            echo json_encode(array('status' => 401, 'message' => 'Wrong Email or Password', 'result' => []));
            exit;
        }
        
        if(!password_verify($body['password'], $user['password']))
        {
            echo json_encode(array('status' => 401, 'message' => 'Wrong Email or Password', 'result' => []));
            exit;
        }
        
        require __DIR__.'/../../server.php';
        $server->handleAuthorizeRequest($request, $response, true, $user['email']);
        $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);

        echo json_encode(array('status' => 200, 'message' => 'Login Success', 'result' => [
            'user' => $user,
            'code' => $code
        ]));
    }
    
    public function register(){
        $entityBody = file_get_contents('php://input');
        $body = (array)json_decode($entityBody);
        $validation = $this->validator->validate($body, [
            'username' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'first_name' => 'required',
            'last_name' => 'required',
        ]);
        
        if ($validation->fails()) {
            header('Content-Type: application/json; charset=UTF-8');
            $errors = $validation->errors();
            echo json_encode([
                'status' => 400, 
                'message' => 'Bad Request',
                'result' => $errors->firstOfAll()
            ]);
            exit;
        } else {
            try {
                $body['password'] = password_hash($body['password'], PASSWORD_DEFAULT);
                $save = $this->service->register($body);

                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode([
                    'status' => 200, 
                    'message' => 'Success Created User',
                    'result' => $body
                ]);
            } catch (\Throwable $th) {
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode([
                    'status' => 500, 
                    'message' => 'Terjadi Kesalahan',
                    'result' => $th
                ]);
            }
        }
    }

    public function getToken(){
        require __DIR__.'/../../server.php';

        $server->handleTokenRequest(\OAuth2\Request::createFromGlobals())->send();
    }
}

?>