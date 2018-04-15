<?php


class Users extends Controller {
    public function __construct (){
        $this->userModel = $this->model('User');

    }
    public function register(){
        $filters = array(

            'email'=>FILTER_VALIDATE_EMAIL,
            'username'=>FILTER_SANITIZE_STRING,
            'password'=>FILTER_SANITIZE_STRING,

        );
        $option = array(
            'email'=>array(
                'flag'=>FILTER_NULL_ON_FAILURE
            ),
            'username'=>array(
                'flag'=>FILTER_NULL_ON_FAILURE
            ),
            'password'=>array(
                'flag'=>FILTER_NULL_ON_FAILURE
            ),

        );
        $input = json_decode(file_get_contents("php://input"));
        $filtered = array();
        $err='';
        foreach($input as $key=>$value){
            $filtered[$key] = filter_var($value,$filters[$key],$option[$key]);
        }
        if($filtered['username']==false){
            $err = 'Username must not be empty';
        }
        if($filtered['password']==false){
            $err = 'Password must not be empty';
        }
        if($filtered['email']==false){
            $err = 'Email must be a valid email';
        }

        $row = $this->userModel->getUserByUsername($filtered['username']);
        if($row['count']>0){
            $err='User with this username already exists';
        }

        if($err!==''){
            $this->response(null,false,200,$err);
        }




        $filtered['password'] = password_hash($filtered['password'],PASSWORD_DEFAULT);

        if($this->userModel->addUser($filtered)){

            $this->response(null,true,200);

        }
        else {
           $this->response(null,false,200,'Something went wrong');
        }


    }
    public function login(){
        $filters = array(


            'username'=>FILTER_SANITIZE_STRING,
            'password'=>FILTER_SANITIZE_STRING,

        );
        $option = array(
            'username'=>array(
                'flag'=>FILTER_NULL_ON_FAILURE
            ),
            'password'=>array(
                'flag'=>FILTER_NULL_ON_FAILURE
            ),

        );

        $input = json_decode(file_get_contents("php://input"));
        $filtered = array();
        foreach($input as $key=>$value){
            $filtered[$key] = filter_var($value,$filters[$key],$option[$key]);
        }

        $result = $this->userModel->getUserByUsername($filtered['username']);

        if($result['count']>0){
            $password = $result['row']['password'];
            if(password_verify($filtered['password'],$password)){
                $user = $result['row'];

                $token = array(
                    'id'=>$user['id'],
                    'username'=>$user['username'],
                    'exp'=>time()+(60*60)
                );

                $jwt = encode($token);
                $exp = 60*30*24*3600;
                
                $refreshToken =$user['username'].':'.Token::generateToken($user['id']);
                


                if($this->userModel->addRefreshToken($refreshToken,$user['username'])){
                    setcookie('refresh_token',$refreshToken,0,'/','localhost',isset($_SERVER['HTTPS']),true);
                    
                    $this->response($jwt,true,200);
                }


                


            }else{
                $this->response(null,false,200,'Password is incorrect');

            }
            
        }else {
            $this->response(null,false,200,'No user associated with that email');
        }


    }
}