<?php
class Deleted_ax extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
        
        $this->load->model(array('Deleted_ax_model'));
    }
    
    function index()
    {
        //delete page
        $data = array();
        $data['users'] = $this->Deleted_ax_model->users();
        foreach ($data['users'] as $key => $user){
            $data['users'][$key]['courses'] = $this->Deleted_ax_model->subscribed_courses(array('user_id'=>$user['id']));
        }
        echo '<pre>';print_r($data);
    }

    function delete_pages(){
        $data = array();
        $data['pages'] = $this->Deleted_ax_model->cms_pages();
        foreach ($data['pages'] as $key => $page){
            $data['pages'][$key]['route'] = $this->Deleted_ax_model->route_id(array('route_id'=>$page['p_route_id']));
            $data['pages'][$key]['recent'] = $this->Deleted_ax_model->recent_pages(array('page_id'=>$page['id']));
        }
        echo '<pre>';print_r($data);
    }

    function delete_terms(){
        $data = array();
        $data['terms'] = $this->Deleted_ax_model->terms();
        foreach ($data['terms'] as $key => $term){
            $data['terms'][$key]['route'] = $this->Deleted_ax_model->route_id(array('route_id'=>$term['t_route_id']));
            $data['terms'][$key]['recent'] = $this->Deleted_ax_model->recent_terms(array('term_id'=>$term['id']));
        }
        echo '<pre>';print_r($data);
    }

    function delete_notifications(){
        $data = array();
        $data['notifications'] = $this->Deleted_ax_model->cms_notifications();
        foreach ($data['notifications'] as $key => $notification){
            $data['notifications'][$key]['route'] = $this->Deleted_ax_model->route_id(array('route_id'=>$notification['n_route_id']));
        }
        echo '<pre>';print_r($data);
    }

    function delete_challenges(){
        $data = array();
        $data['challenge_zones'] = $this->Deleted_ax_model->challenge_zones();
        foreach ($data['challenge_zones'] as $key => $challenge){
            $data['challenge_zones'][$key]['questions'] = $this->Deleted_ax_model->challenge_questions(array('challenge_id'=>$challenge['id']));
            $data['challenge_zones'][$key]['attempts'] = $this->Deleted_ax_model->challenge_attempts(array('challenge_id'=>$challenge['id']));
            foreach ($data['challenge_zones'][$key]['attempts'] as $key1 => $attempt){
                $data['challenge_zones'][$key]['attempts'][$key1]['report'] = $this->Deleted_ax_model->challenge_attempt_report(array('attempt_id'=>$attempt['id']));
            }
        }
        echo '<pre>';print_r($data);die;
    }

    function delete_expert_lectures(){
        $data = array();
        $data['expert_lectures'] = $this->Deleted_ax_model->expert_lectures();
        echo '<pre>';print_r($data);die;
    }

    function delete_daily_news(){
        $data = array();
        $data['daily_news'] = $this->Deleted_ax_model->daily_news();
        echo '<pre>';print_r($data);die;
    }
}
    
