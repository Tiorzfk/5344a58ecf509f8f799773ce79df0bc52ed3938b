<?php

namespace App\Services;

use App\Config\Database;

class AuthService {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function findBySql($query){
        $this->db->query($query);
        $set = $this->db->resultSet();
        return $set;
    }

    public function authenticate(){
        $query = "SELECT * FROM oauth_clients";
            return $this->findBySql($query);
    }

    public function register($data){
        $this->db->query('INSERT into oauth_users(username, password, first_name, last_name, email) VALUES (:username,:password,:first_name,:last_name, :email)');
        $this->db->bind(':username',$data['username']);
        $this->db->bind(':password',$data['password']);
        $this->db->bind(':first_name',$data['first_name']);
        $this->db->bind(':last_name',$data['last_name']);
        $this->db->bind(':email',$data['email']);
        if($this->db->execute()){
            return true;
        }else{
            return false;
        }

    }

    public function getUserByEmail($email){
        $this->db->query("SELECT * FROM oauth_users where email = ?");
        $this->db->execute([$email]);
        return $this->db->fetch();
    }
}

?>