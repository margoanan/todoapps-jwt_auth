<?php

    defined('BASEPATH') OR exit('No direct script access allowed');

    require APPPATH . '/libraries/REST_Controller.php';
    require_once FCPATH . 'vendor/autoload.php';
    use Restserver\Libraries\REST_Controller;

    class Authenticate extends REST_Controller {

        function __construct($config = 'rest') {
            parent::__construct($config);             
            $this->load->library('jwt'); 
            $this->load->database();
            $this->load->model('M_User');              
        }                   

        public function generateSecretKey_get()
        {
            $length = 32;
            $secretKey = bin2hex(random_bytes($length));

            return $this->response(["jwt_secret_key"  => $secretKey]);
        }

        public function getToken_post()
        {                                       
            $u = $this->input->post('username_inputan');
            $p = $this->input->post('password_inputan');
            if ($this->M_User->is_valid_user($u,$p)) {
                $dataKirim = array(
                    "username" => $this->input->post('username_inputan')                     
                );
                $token = $this->jwt->encode($dataKirim);            
                $output = [
                            'status' => 200,
                            'message' => 'Berhasil login',
                            "token" => $token
                        ];      
                $data = array($output);
    
                $this->response($data, 200 );       
            }else {
                $output = [
                            'status' => 403,
                            'message' => 'User Tidak Ditemukan'                            
                        ];
                $data = array($output);

                $this->response($data,403);
            }            
        }        
    }
?>

