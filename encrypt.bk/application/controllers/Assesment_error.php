<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Assesment_error extends CI_Controller {

  function __construct()
  {
    parent::__construct();
    $redirect   = $this->auth->is_logged_in_user(false, false);
    $this->load->library('PHPWord');
    $this->load->library('session');
    $explicit_method  = array('live_class', 'save_lecture', 'convert_live_lecture');
    if (!$redirect && !in_array($this->router->fetch_method(), $explicit_method))
    {
        $this->session->set_flashdata('redirect',current_url());
        redirect('login');
    }
    $this->lang->load('material');
    $this->load->model('Assesment_error_model');
  }


    function index()
    { 

        $data = array();
        $user = $this->auth->get_current_user_session('user');
        //$wrong_answer = $this->Assesment_error_model->get_wrong_answer(3);
        $wrong_answer = $this->Assesment_error_model->get_wrong_answer(array('user_id'=>$user['id']));
        
        foreach ($wrong_answer as $wrong) {
          
          if($wrong['status'] == 0){
            $data['wrong_answer'][$wrong['id']] =$wrong; 
          }

        }

        $options_op = array();

        foreach ($data['wrong_answer'] as $value) {
            $options = array();
            $options[$value['id']] = $value['q_options'];
            $finds_op = $this->Assesment_error_model->get_options($options); 
            
            if(is_array($finds_op)){
                foreach ($finds_op as $find) {
                    $options_op[$value['id']][$find['id']]= $find;
                }
            }
            
            if(is_array($options_op)){

                foreach ($options_op as $key => $values) {
                  if ($value['id'] == $key){
                    $data['wrong_answer'][$value['id']]['options'] = $values;
                  }
                }
            }
        }

        $un_attmpts = $this->Assesment_error_model->get_unattempt(array('user_id'=>$user['id']));

        foreach ($un_attmpts as $un_attmpt){
          
          $options = array();
          $options[$un_attmpt['id']] = $un_attmpt['q_options'];
          $data['unattemt'][$un_attmpt['id']] =$un_attmpt;
          $un_finds_op = $this->Assesment_error_model->get_options($options);
          
          foreach ($un_finds_op as $find_op) {
            $options_un_op[$un_attmpt['id']][$find_op['id']]= $find_op;
          }

          foreach ($options_un_op as $key => $values){

            if ($un_attmpt['id'] == $key) {
              $data['unattemt'][$un_attmpt['id']]['options'] = $values;
            }
          }

        }
        return $data;
    }
}
?>