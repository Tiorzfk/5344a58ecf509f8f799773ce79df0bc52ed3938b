<?php

namespace App\Services;

use App\Config\Database;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailService {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function findBySql($query){
        $this->db->query($query);
        $set = $this->db->resultSet();
        return $set;
    }

    public function getAllQueueSentNull(){
        $query = "SELECT * FROM queue where sent IS NULL";
        return $this->findBySql($query);
    }

    public function addQueue($data){
        $this->db->query('INSERT into queue(mail_to, mail_from, subject, body, created) VALUES (:to,:from,:subject,:body, now())');
        $this->db->bind(':to',$data['mail_to']);
        $this->db->bind(':from',$data['mail_from']);
        $this->db->bind(':subject',$data['subject']);
        $this->db->bind(':body',$data['body']);
        if($this->db->execute()){
            return true;
        }else{
            return false;
        }
    }

    public function updateQueue($id){
        $queue = $this->getQueueById($id);

        if($queue){
            $mail = new PHPMailer; 
            $mail->IsSMTP();
            $mail->SMTPSecure = 'none'; 
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->SMTPDebug = 2;
            $mail->Port =  $_ENV['MAIL_PORT'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USERNAME']; 
            $mail->Password =  $_ENV['MAIL_PASSWORD']; ; 
            $mail->SetFrom( $_ENV['MAIL_FROM_ADDRESS'],$queue['mail_from']); 
            $mail->Subject = $queue['subject']; 
            $mail->AddAddress($queue['mail_to'], $queue['mail_to']);  
            $mail->MsgHTML($queue['body']);

            if($mail->Send()){
                $this->db->query('UPDATE queue SET sent = now() WHERE id = :id');
                $this->db->bind(':id',$id);
                if($this->db->execute()){
                    return true;
                }
            }
        }
        return false;
    }

    public function getQueueById($id){
        $this->db->query("SELECT * FROM queue where id = ?");
        $this->db->execute([$id]);
        return $this->db->fetch();
    }
}

?>