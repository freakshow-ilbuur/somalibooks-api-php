<?php


class User {
    private $db;

    public function __construct(){
    $this->db = new Database;

    }
    public function addUser($user){
       

        $query = 'INSERT INTO users (username,password,email) VALUES (:username,:password,:email);';
        $this->db->query($query);
        
        
        $this->db->bind(":password",$user["password"]);
        $this->db->bind(":username",$user["username"]);
        $this->db->bind(":email",$user["email"]);

        return $this->db->execute();

    }

    public function getUserByEmail($email){

        $query = 'SELECT  * FROM users WHERE email = :email';
        $this->db->query($query);
        $this->db->bind(':email',$email);
        $row = $this->db->singleResult();

        return array(
            'row'=>$row,
            'count'=>$this->db->rowCount()
        );
        
    }

    public function getUserByUsername($username){
        $query = 'SELECT  * FROM users WHERE  username=:username';
        $this->db->query($query);
        $this->db->bind(':username',$username);
        $row = $this->db->singleResult();

        return array(
            'row'=>$row,
            'count'=>$this->db->rowCount()
        );
    }
    public function addRefreshToken($token,$username){
        $query = 'UPDATE  users SET refresh_token =:token, date_iss = NOW()  WHERE  username=:username';
        $this->db->query($query);
        $this->db->bind(':username',$username);
        $this->db->bind(':token',$token);
        $row = $this->db->execute();
        return $row;
    }

}