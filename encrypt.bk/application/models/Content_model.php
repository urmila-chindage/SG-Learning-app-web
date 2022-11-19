<?php
class Content_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function transition_contents()
    {
        $this->db->select('*');
        return $this->db->get('transition_contents')->result_array();     
    }
}