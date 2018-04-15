<?php

require '../vendor/autoload.php';


use Firebase\JWT\JWT;


 function encode($token){
     
     $jwt = JWT::encode($token,SECRET);
     return 'JWT '.$jwt;
 }

 function decode($token){
     $result=null;
     try{
        $result  =JWT::decode($token,SECRET,array('HS256'));
 
     }
     catch(\Firebase\JWT\ExpiredException $e){

        
     }
     finally{
         return $result;
     }
     return $result;

     
    
 }


 