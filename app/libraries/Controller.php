<?php 

/*
 *
 * 
 * Base Controller 
 * load models and views
 * 
 */

 class Controller {
     //load model 
     public function model($model){
         require_once '../app/models/'.$model.'.php';
         return new $model();
     }
     //load view 
     public function view ($view, $data=[]){
         if(file_exists('../app/views/'.$view.'.php')){
             require_once '../app/views/'.$view.'.php';

         }else{
             die($view.' view does not exists');
         }

     }

     public function response($data,$success,$status,$message=null){
         $res=array(
             'success'=>$success,
             'result'=>$data,
             'msg'=>$message
         );
         Rest::sendResponse($status,$res); 
         

     }
 }