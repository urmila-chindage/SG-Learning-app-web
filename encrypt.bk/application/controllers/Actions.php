<?php
class Actions extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $redirect = $this->auth->is_logged_in(false, false);
        $this->__loggedInUser = $this->auth->get_current_user_session('admin');
        if (!$redirect) {
            redirect('login');
        }
    }

    public function index($token = '')
    {
        $this->load->model('Action_model');
        $response = array();
        $response['success'] = false;
        $response['message'] = '';
        $token_details = $this->Action_model->token(array('token' => $token, 'status' => '1'));
        if (empty($token_details)) {
            $response['message'] = 'Invalid token/token expired.';
            $this->session->set_flashdata('popup',$response);
            redirect(admin_url());
        }

        if ($token_details['at_expire'] != '' && $token_details['at_expire'] > date("Y-m-d H:i:s")) {
            $response['message'] = 'Token expired.';
            $this->session->set_flashdata('popup',$response);
            redirect(admin_url());
        }
        $action_response = array();
        switch ($token_details['at_purpose']) {
            case 1:
                $action_response = $this->approve_profile(json_decode($token_details['at_params'], true));
                break;
            default:
                $response['message'] = 'Invalid rules in token.';
                $this->session->set_flashdata('popup',$response);
                redirect(admin_url());
                break;
        }

        if ($action_response['success']) {
            $response['success'] = true;
            $response['message'] = $action_response['message'];
        } else {
            $response['message'] = $action_response['message'];
        }
        $token_details['at_status'] = '0';
        $token_details['updated'] = date("Y-m-d H:i:s");
        $this->Action_model->save($token_details);
        $this->session->set_flashdata('popup',$response);
        redirect(admin_url());
    }

    private function approve_profile($param = array())
    {
        $this->__permission     = $this->accesspermission->get_permission(array(
                                'role_id' => $this->__loggedInUser['role_id'],
                                'module' => 'user'
                                ));
        $this->load->model('User_model');
        $response = array();
        $response['success'] = false;
        $response['message'] = '';
        if(!in_array('3', $this->__permission)){
            $response['message'] = 'You dont have permission to perform this action.';
        }else{
            $user_id = $param['user_id'];
            $user = $this->User_model->user(array('id' => $user_id));
            $save = array();
            $save['id'] = $user_id;
            $save['action_by'] = $this->__loggedInUser['id'];
            $save['updated_date'] = date('Y-m-d H:i:s');
            $save['us_status'] = '1';

            if ($user['us_deleted'] == 0 && $user['us_status'] != 0) {
                $this->User_model->save($save);
                $param_admin = array();
                $param_admin['from'] = config_item('site_name') . '<' . $this->config->item('site_email') . '>';
                $param_admin['to'] = array($user['us_email']);
                $param_admin['subject'] = 'Profile Approved';
                $param_admin['body'] = 'Dear ' . $user['us_name'] . ',<br /><br />Your profile has been approved by admin and now you can login to the portal though the link given.<br/><a href="' . site_url('login') . '">Click here</a>.';
                $send_admin = $this->ofabeemailer->send_mail($param_admin);

                $this->load->library('ofabeenotifier');
                // $param = array();
                // $param['ids'] = array($user['id']);
                // $param['message'] = 'Your profile has been activated by admin.';
                // $this->ofabeenotifier->push_notification($param);
                $response['success'] = true;
                $response['message'] = 'Profile of ' . $user['us_name'] . ' has been approved.';
            } else {
                $response['message'] = 'Approval failed. Profile of ' . $user['us_name'] . ' is deleted/suspended one.';
            }
        }

        return $response;
    }
}