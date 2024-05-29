<?php
    class M_Task extends CI_Model
    {        
        function fetch_all()
        {
            $this->db->order_by('id', 'ASC');
            $query = $this->db->get('tasks');
            $data = $query->result_array();

            // foreach ($data as &$value) {
            //     $lists = $this->db->where('tasks_id',$value['id']);
            //     $taskList = $lists->get('task_lists');
            //     $value['task_lists'] = $taskList->result_array();
            // }

            return $data;
        }

        function fetch_single_data($id)
        {
            $this->db->where("id", $id);
            $query = $this->db->get('tasks');                
            $data = $query->row();
            
            return $data;
        }

        function fetch_single_data_with_child($id)
        {
            $this->db->where("id", $id);
            $query = $this->db->get('tasks');                
            $data = $query->row();
            $lists = $this->db->where('tasks_id',$data->id);
            $taskList = $lists->get('task_lists');
            $data->task_lists = $taskList->result_array();

            return $data;
        }

        function check_data($id)
        {
            $this->db->where("id", $id);
            $query = $this->db->get('tasks');
                
            if ($query->row()) {
                return true;
            } else {
                return false;
            }
            
        }

        function insert_api($param)
        {
            $this->db->insert('tasks', $param);
            if ($this->db->affected_rows() > 0){
                return true;
            } else {
                return false;
            }
        }                

        function update_data($param1, $param2)
        {
            $this->db->where("id", $param1);
            $this->db->update("tasks", $param2);
            if ($this->db->affected_rows() > 0){
                return true;
            } else {
                return false;
            }
        }

        function delete_data($paramId)
        {
            $this->db->where("id", $paramId);
            $this->db->delete("tasks");
            if ($this->db->affected_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }
?>