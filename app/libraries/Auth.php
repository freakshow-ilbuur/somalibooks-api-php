<?php

class Auth
{

    private $db;

    public function __construct()
    {
        $this->db = new Database;

    }

    private function getHeader()
    {
        $headers = null;

        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER['Authorization']);
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));

            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }

        return $headers;

    }
    private function getBearerToken()
    {
        $header = $this->getHeader();

        if (!empty($header)) {
            if (preg_match('/JWT\s(\S+)/', $header, $matches)) {
                return $matches[1];
            }
            return null;

        }
    }
    public function authenticate($refreshToken)
    {

        $data = explode(':', $refreshToken);

        $token = $this->getBearerToken();

        if (isset($token)) {

            $decoded = decode($token);
            
            // if jwt it expired issue a new token 
            if (!isset($decoded)) {
                
               
                require_once '../app/models/User.php';
                $userModel = new User;
                $row = $userModel->getUserByUsername($data[0]);

                if ($row['count'] > 0) {
                    $user = $row['row'];
                    $expectedToken = Token::generateToken($user['id']);
                    
                    
                    if (Token::isEqual($expectedToken,$data[1])) {
                        $date = date_create(date("Y-m-d"));
                        $d2 = date_create($user['date_iss']);
                        $interval = date_diff($d2,$date);
                        $months =  $interval->format('%m');
                        

                        if ($months <6 ) {
                            $newToken = array(
                                'id' => $user['id'],
                                'username' => $user['username'],
                                'exp' => time() + (60*60),
                            );
                            $jwt = encode($newToken);
                            
                            
                            return $jwt;
                        }

                    } 

                } 

            } else {return  "JWT ".$token;}

        }
        return null;

    }
}
