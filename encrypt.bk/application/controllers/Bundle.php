<?php
class Bundle extends CI_Controller 
{
    function __construct()
    {
        parent::__construct();
        $this->__limit                                                      = 4;
    }

    public function basic($id = false)
    { 
        $user                                                               = $this->auth->get_current_user_session('user');
        $objects                                                            = array();
        $objects['key']                                                     = 'bundle_'.$id;
        $this->memcache->delete($objects['key']);
        $callback                                                           = 'bundle_details';
        $params                                                             = array('id' => $id);
        $bundle_details                                                     = $this->memcache->get($objects, $callback, $params);
        $objects                                                            = array();
        $objects['key']                                                     = 'my_bundle_subscription_'.$id.'_'.$user['id'];
        $callback                                                           = 'bundle_subscription_details';
        $param                                                              = array('user_id' => $user['id'], 'bundle_id' => $id);
        $my_bundle_subscriptions                                            = $this->memcache->get($objects, $callback, $param);

        if(!isset($my_bundle_subscriptions['user_id']) || $my_bundle_subscriptions['user_id'] != $user['id'] || $my_bundle_subscriptions['user_id'] == '0')
        {
            if($bundle_details['c_status'] == '0'  || $bundle_details['c_deleted'] == '1' )
            {
                $this->load->view($this->config->item('theme').'/404_error'); return false;
            }
        }

        if($bundle_details['c_deleted'] == '1' || (!isset($my_bundle_subscriptions['user_id']) && $bundle_details['c_status'] == '0'))
        {
            $this->load->view($this->config->item('theme').'/404_error'); return;
        }
        else
        {
            $data                                                           = array();
            $data['course_details']                                         = array();
            $data['session']                                                = $user;
            $data['bundle_id']                                              = $id;

            if(!empty($my_bundle_subscriptions))
            {
                $data['subscription']                                       = $my_bundle_subscriptions;
            }
            
            if($user['id'] && !empty($my_bundle_subscriptions['c_courses']))
            {
                if(is_array($my_bundle_subscriptions['c_courses']))
                {
                    $c_courses                                              = $my_bundle_subscriptions['c_courses'];
                }
                else
                {
                    $c_courses                                              = json_decode($my_bundle_subscriptions['c_courses'],true);
                }
            }
            else
            {
                $c_courses                                                  = json_decode($bundle_details['c_courses'],true);
            }
            
            $data['course_count']                                           = count($c_courses);
            $data['admin']                                                  = $this->config->item('acct_name');
            $data['admin_name']                                             = $this->config->item('us_name');
            
            if($c_courses)
            {
                foreach($c_courses as $course)
                {
                    $objects                                                = array();
                    $objects['key']                                         = 'course_'.$course['id'];
                    $callback                                               = 'course_details';
                    $params                                                 = array('id' => $course['id'], 'bundle' => true);
                    $course_details                                         = $this->memcache->get($objects, $callback, $params);
                    $courses[]                                              = $course_details; 
                }
            }
            
            $data['bundle_subscribed']                                      = '';
            if (isset($user['id'])) 
            {
                //courses
                $objects                                                    = array();
                $objects['key']                                             = 'enrolled_' . $user['id'];
                $objects_key                                                = 'enrolled_item_ids_' . $user['id'];
                $this->memcache->delete($objects_key);
                $this->memcache->delete($objects['key']);
                $callback                                                   = 'my_subscriptions';
                $params                                                     = array('user_id' => $user['id']);
                $enrolled_courses                                           = $this->memcache->get($objects, $callback, $params);
                foreach ($enrolled_courses as $enrolled) 
                {
                    $enrolled_courses[$enrolled['course_id']] = $enrolled;
                }

                $objects                                                    = array();
                $objects['key']                                             = 'bundle_enrolled_' . $user['id'];
                $this->memcache->delete($objects['key']);
                $callback                                                   = 'my_bundle_subscriptions';
                $params                                                     = array('user_id' => $user['id']);
                $enrolled_bundles                                           = $this->memcache->get($objects, $callback, $params);
                $bundle_exist                                               = 0;
                
                foreach ($enrolled_bundles as $enrolled)
                {
                    if($enrolled['bundle_id'] == $id)
                    {
                        $bundle_exist                                       = 1;
                    }
                    $enrolled_bundles[$enrolled['bundle_id']]               = $enrolled;
                }

                $data['bundle_subscribed']                                  = $bundle_exist;
                if(!empty($bundle_details))
                {
                    $enrolled_keys                                          = (!empty($enrolled_bundles))?array_column($enrolled_bundles, 'bundle_id'):array();
                    
                    if(in_array($bundle_details['id'],$enrolled_keys))
                    {
                        $bundle_details['enrolled']                         = true;
                    }
                    else
                    {
                        $bundle_details['enrolled']                         = false;
                    }
                }
                
                if(!empty($courses))
                {
                    foreach ($courses as $c_key => $course) 
                    {
                        $courses[$c_key]['enrolled']                        = isset($enrolled_courses[$course['id']]);
                        if($courses[$c_key]['enrolled'])
                        {
                            $courses[$c_key]['cs_end_date']                 = $enrolled_courses[$course['id']]['cs_end_date'];
                            $courses[$c_key]['cs_course_validity_status']   = $enrolled_courses[$course['id']]['cs_course_validity_status'];
                            $courses[$c_key]['cs_approved']                 = $enrolled_courses[$course['id']]['cs_approved'];
                            $courses[$c_key]['percentage']                  = $enrolled_courses[$course['id']]['percentage'];
                            $courses[$c_key]['cs_last_played_lecture']      = $enrolled_courses[$course['id']]['cs_last_played_lecture'];
                            $today                                          = date('Y-m-d');
                            $expire                                         = date_diff(date_create($today),date_create($courses[$c_key]['cs_end_date'])); 
                            $now                                            = time();
                            $your_date                                      = strtotime($courses[$c_key]['cs_end_date'] .' +1 day');
                            $datediff                                       = $your_date - $now;
                            $courses[$c_key]['expired']                     = ceil($datediff / (60 * 60 * 24)) > 0?false:true;
                            $courses[$c_key]['expire_in']                   = $expire->format("%R%a");
                            $courses[$c_key]['expire_in_days']              = $expire->format("%a");
                            $courses[$c_key]['validity_format_date']        = date('d-m-Y',strtotime($courses[$c_key]['cs_end_date']));
                        }
                    }
                }
                
            } else {
                foreach ($courses as $key => $course) {
                    $courses[$key]['enrolled'] = false;
                }
            }
            $gst_setting                                                    = $this->settings->setting('has_tax');

            if ($gst_setting['as_setting_value']['setting_value']->cgst && $gst_setting['as_setting_value']['setting_value']->cgst != '') {
                $data['cgst']                                               = $gst_setting['as_setting_value']['setting_value']->cgst;
            } 
            else 
            {
                $data['cgst']                                               = 0;  
            } 

            if ($gst_setting['as_setting_value']['setting_value']->sgst && $gst_setting['as_setting_value']['setting_value']->sgst != '') {
                $data['sgst']                                               = $gst_setting['as_setting_value']['setting_value']->sgst;
            } 
            else 
            {
                $data['sgst']                                               = 0;  
            } 
            $data['bundle']                                                 = $bundle_details;
            $today                                                          = date('Y-m-d');
            $valid_till                                                     = $bundle_details['c_validity_date'];
            $data['bundle']['c_validity_expired']                           = strtotime($valid_till) >= strtotime($today) ? false : true;
            $data['course_details']                                         = empty($courses)?array():$courses;
            
            if(!isset($data['bundle']['c_title']))
            {
                $this->session->set_flashdata('error','This Bundle seems to be deleted, please contact admin.'); 
                redirect('dashboard/courses');exit;
            }

            $data['meta_original_title']                                    = $data['bundle']['c_title'];
            $data['meta_title']                                             = $data['bundle']['c_meta'];
            $data['meta_description']                                       = $data['bundle']['c_meta_description'];
            $this->load->view($this->config->item('theme').'/bundle_description_beta', $data);
        }

    }

    function load_reviews($course_id = false,$offset = 0)
    {
        $this->load->model(array('Bundle_model'));
        $data                                                               = array();
        $data['limit']                                                      = empty($this->input->post('limit')) ? $this->__limit : $this->input->post('limit');
        $course_id                                                          = empty($this->input->post('course_id')) ? false : $this->input->post('course_id');
        $is_ajax                                                            = $this->input->post('is_ajax');
        $offset                                                             = empty($this->input->post('offset')) ? 0 : $this->input->post('offset');
        $data['show_load_button']                                           = false;
        $data['default_user_path']                                          = default_user_path();
        $data['user_path']                                                  = user_path();
        $data['title']                                                      = 'Reviews';
        $reviews_param                                                      = array('course_id' => $course_id, 'count' => true);
        $data['total_records']                                              = $this->Bundle_model->db_get_rating($reviews_param);
        $reviews_param                                                      = array('course_id' => $course_id, 'limit' => $data['limit'], 'offset' => $offset);
        $reviews                                                            = $this->Bundle_model->db_get_rating($reviews_param);
        $data['start']                                                      = $offset + $data['limit'];
        $count                                                              = empty($count) ? $data['total_records'] : $count;
        
        if ($data['start'] < $data['total_records']) 
        {
            $data['show_load_button']                                       = true;
        } 
        else 
        {
            $data['show_load_button']                                       = false;
        }
        
        $data['reviews']                                                    = $reviews;
        $data['success']                                                    = true;
        echo json_encode($data);
    }

}
?>