<?php
// from users, not in course 3.

class Machine extends CI_Controller 
{
    function __construct()
    {
        parent::__construct();
        $this->load->database('default');
    }

    function index()
    {
        //echo 'tony machine test';

        $results = [];

        // $this->db->select('*');
        // $this->db->from('users');
        // $this->db->join('course_subscription', 'users.id = course_subscription.cs_user_id');
        // $this->db->where(array('course_subscription.cs_course_id' => 3));         
        // $query = $this->db->get();
        // $results = $query->result();


        //echo count($results);
        
        $data['results'] = $results;
        $this->load->view('ofabee/test', $data);
    }

    function getTotal(){

        $this->db->select('*');
        $this->db->from('users');
        $this->db->join('course_subscription', 'users.id = course_subscription.cs_user_id');
        $this->db->where(array('course_subscription.cs_course_id' => 3));         
        $query = $this->db->get();
        $results = $query->result();


        return count($results);        
    }

    function getData(){

        $draw = intval($this->input->get("draw"));
        $start = intval($this->input->get("start"));
        $length = intval($this->input->get("length"));

        $this->db->select('*');
        $this->db->from('users');
        $this->db->join('course_subscription', 'users.id = course_subscription.cs_user_id');
        $this->db->where(array('course_subscription.cs_course_id' => 3));      
        $this->db->limit($length,$start);
        $query = $this->db->get();
  
  
        $data = [];
  
  
        foreach($query->result() as $r) {
             $data[] = array(
                  $r->id,
                  $r->us_name,
                  $r->us_email,
                  $r->cs_course_id
             );
        }
  
  
        $result = array(
                 "draw" => $draw,
                   "recordsTotal" => $this->getTotal(),
                   "recordsFiltered" => $this->getTotal(),
                   "data" => $data
              );
  
  
        echo json_encode($result);
        exit();        
    }
}

