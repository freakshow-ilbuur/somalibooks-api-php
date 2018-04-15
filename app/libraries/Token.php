<?php

class Token {

    public function __construct(){

    }
    public static function generateToken($param){
        $token = hash_hmac('md5',$param,SECRET);
        return $token;

    }

    public static function isEqual($savedToken,$recievedToken){
        return hash_equals($savedToken,$recievedToken);


    }


}