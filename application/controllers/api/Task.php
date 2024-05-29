<?php

    defined('BASEPATH') OR exit('No direct script access allowed');

    require APPPATH . '/libraries/REST_Controller.php';
    require_once FCPATH . 'vendor/autoload.php';

    use Restserver\Libraries\REST_Controller;

    class Task extends REST_Controller {

        function __construct($config = 'rest') {            
            parent::__construct($config);       
            //  paste di sini
            header('Access-Control-Allow-Origin:*');
            header("Access-Control-Allow-Headers:X-API-KEY,Origin,X-Requested-With,Content-Type,Accept,Access-Control-Request-Method,Authorization");
            header("Access-Control-Allow-Methods:GET,POST,OPTIONS,PUT,DELETE");
            $method = $_SERVER['REQUEST_METHOD'];
            if ($method == "OPTIONS") {
                die();
            }
            $this->load->database(); //optional
            $this->load->model('M_Task');
            $this->load->library('form_validation');
            $this->load->library('jwt');
            
        }            

         // In your CodeIgniter controller
         public function options_get() {
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
            header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
            exit();
        }

        function index_get()
        {                           
            $id = $this->get('id');
            if ($id == '') {
                $data = $this->M_Task->fetch_all();
            } else {            
                $data = $this->M_Task->fetch_single_data($id);
            }                

            $this->response($data, 200);
        }

        function index_post()
        {           
            $name = $this->post('name');
            if ($this->post('name') == '') {
                $response = array(
                    'status' => 'fail',
                    'field' => 'nama',
                    'message' => 'Isian nama tidak boleh kosong!',
                    'status_code' => 502
                );

                return $this->response($response);
            }
            $completed = $this->post('completed');
            if ($completed == '') {
                $completed = 0;
            }else {
                $listStatus = [1,0,true,false];
                if (!in_array($completed, $listStatus)) {
                    $response = array(
                        'status' => 'fail',
                        'field' => 'completed',
                        'message' => 'Pilihan kolom completed tidak tersedia!',
                        'status_code' => 502
                    );            
                    return $this->response($response);
                }                             
            }

            $data = array(
                'name' => $name,
                'completed' => $completed,
                'created_at'  => date("Y-m-d H:i:s"),
            );            

            $insert = $this->M_Task->insert_api($data);
            if ($insert) {
                $last_row = $this->db->select('*')->order_by('id',"desc")->limit(1)->get('tasks')->row();
                $response = array(
                    'status' => 'success',
                    'data' => $last_row,
                    'status_code' => 201,
                );
            } else {
                $response = array(
                    'status' => 'fail',
                    'data' => null,
                    'status_code' => 500,
                );
            }            

            return $this->response($response);        
        }

        function index_put()
        {                       
            $id = $this->put('id');
            $check = $this->M_Task->check_data($id);        
            if ($check == false) {
                $error = array(
                    'status' => 'fail',
                    'field' => 'id',
                    'message' => 'Data tidak ditemukan!',
                    'status_code' => 404
                );

                return $this->response($error);
            }
            $nama = $this->put('name');
            $completed = $this->put('completed');
            if ($nama == '') {
                $response = array(
                    'status' => 'fail',
                    'field' => 'name',
                    'message' => 'Isian nama tidak boleh kosong!',
                    'status_code' => 502
                );

                return $this->response($response);
            }
            
            
            if ($completed == '') {
                $completed = 0;
            }else {
                $listStatus = [1,0,true,false];
                if (!in_array($completed, $listStatus)) {
                    $response = array(
                        'status' => 'fail',
                        'field' => 'completed',
                        'message' => 'Pilihan kolom completed tidak tersedia!',
                        'status_code' => 502
                    );            
                    return $this->response($response);
                }                
            }

            $data = array(
                'name' => $nama,
                'completed'  => $completed
            );

            $update = $this->M_Task->update_data($id,$data);
            if ($update) {
                $newData = $this->M_Task->fetch_single_data($id);                            
                return $this->response($this->responseSuccess('success',$newData,200));
            } else {                
                return $this->response($this->responseFailed('fail',500));
            }                                
        }

        function index_delete() {
            $id = $this->delete('id');    
            $check = $this->M_Task->check_data($id);                
            if ($check == false) {
                $error = array(
                    'status' => 'fail',
                    'field' => 'id',
                    'message' => 'Data tidak ditemukan!',
                    'status_code' => 502
                );

                return $this->response($error);
            }
            $delete = $this->M_Task->delete_data($id);            

            return $this->response($this->responseSuccess('success',null,200));
        }

        
        private function responseSuccess($param1,$param2,$param3)
        {
            $data = array(
                'status' => $param1,
                'data' => $param2,
                'status_code' => $param3,
            );

            return $data;
        }

        private function responseFailed($param1,$param2)
        {
            $data = array(
                'status' => $param1,
                'data' => null,
                'status_code' => $param2,
            );

            return $data;
        }
    }
?>