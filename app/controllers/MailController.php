<?php
namespace App\Controllers;

require_once __DIR__.'/../../vendor/autoload.php';

use Rakit\Validation\Validator;
use App\Services\MailService;

class MailController {
    private $service;
    private $validator;

    public function __construct(){
        $this->service = new MailService;
        $this->validator = new Validator;
    }

    public function send(){
        $entityBody = file_get_contents('php://input');
        $body = (array)json_decode($entityBody);

        $validation = $this->validator->validate($body, [
            'mail_to' => 'required|email',
            'mail_from' => 'required|email',
            'subject' => 'required',
            'body' => 'required'
        ]);
        
        header('Content-Type: application/json; charset=UTF-8');
        if ($validation->fails()) {
            $errors = $validation->errors();
            echo json_encode([
                'status' => 400, 
                'message' => 'Bad Request',
                'result' => $errors->firstOfAll()
            ]);
            exit;
        }

        $result = $this->service->addQueue($body);

        echo json_encode(array('status' => 200, 'message' => 'Send Email Success added to Queue, Please Check Your Email Send Status, to Confirm The Email Has Sended'));
    }

    public function status($id){
        header('Content-Type: application/json; charset=UTF-8');
        $result = $this->service->getQueueById($id);

        if(!$result)
        {
            echo json_encode(array('status' => 400, 'message' => 'Not Found'));
            exit;
        }

        if(!$result['sent'])
        {
            $status = 'Waiting to be sent';
        }else{
            $status = 'Sent on '.$result['sent'];
        }

        echo json_encode(array('status' => 200, 'message' => 'Success', 'result' => $status));
    }

    public function runWorker(){
        $result = $this->service->getAllQueueSentNull();

        foreach ($result as $value) {
            $dtime = date('r');
            $message = "Failure Update !";
            $exec = $this->service->updateQueue($value->id);

            if($exec){
                $message = "Job Success Execute!";
                $message_log = "Job Success Execute!\n";
                echo $message_log;
            }else{
                echo $message;
            }
            $entry_line = "Time Jobs: $dtime | Id: $value->id | Messages: $message";
            $fp = fopen("logs/jobs.log", "a");
            fputs($fp, $entry_line);
            fclose($fp);

            if(!$exec){
                // sleep(30);
            }
        }

        echo "asd";
    }
}

?>