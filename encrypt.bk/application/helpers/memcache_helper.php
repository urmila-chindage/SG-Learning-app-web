<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

function get_institution_id($params = array(), $scope)
{
    $scope->load->model('Institute_model');
    $ins = $scope->Institute_model->get_institutes($params);
    return $ins;
}

function courses($param = array(), $scope){

    $scope->load->model(array('Course_model'));
    $result                 = array();
    $bundle                 = array();
    $filter                 = array();
    $filter['direction']    = isset($param['direction']) ? $param['direction'] : 'DESC';
    $filter['select']       = isset($param['select']) ? $param['select'] : 'course_basics.id,course_basics.cb_description,course_basics.cb_code,course_basics.cb_is_free,course_basics.cb_price,course_basics.cb_discount,course_basics.cb_title, course_basics.cb_code,course_basics.cb_position,course_basics.cb_access_validity,course_basics.cb_validity_date,course_basics.cb_image,course_basics.cb_language,course_basics.cb_slug,course_basics.cb_status,course_basics.cb_category,course_basics.cb_deleted,course_basics.cb_approved,course_basics.cb_groups,"course" as item_type';
    $courses                = $scope->Course_model->courses($filter);
    foreach($courses as $key => $course)
    {
        $course_id                  = isset($course['id'])?$course['id']:'';
        $objects                    = array();
        $response                   = array();
        $objects['key']             = 'course_'.$course_id;
        $callback                   = 'course_details';
        $params                     = array('id' => $course_id);
        $course_details             = $scope->memcache->get($objects, $callback, $params); 
        $courses[$key]['lectures']  = isset($course_details['lectures'])?$course_details['lectures']:array();
        //if($course['id']=='1'){print_r($courses); die($course['id']);}
    }
    // echo "<pre>";print_r($courses);exit;
    return $courses;
}
function all_courses($param = array(), $scope)
{
    $scope->load->model(array('Course_model','Bundle_model'));
    $result                 = array();
    $bundle                 = array();
    $filter                 = array();
    $filter['active']       = isset($param['active']) ? $param['active'] : 1;
    $filter['not_subscribed']= isset($param['not_subscribed']) ? $param['not_subscribed'] : false;
    $filter['user_id']      = isset($param['user_id']) ? $param['user_id'] : false;
    $filter['filter']       = isset($param['filter']) ? $param['filter'] : 'active';
    $filter['direction']    = isset($param['direction']) ? $param['direction'] : 'DESC';
    $filter['institute_id'] = isset($param['institute_id']) ? $param['institute_id'] : false;
    $filter['select']       = isset($param['select']) ? $param['select'] : 'course_basics.id,course_basics.cb_code,course_basics.cb_is_free,course_basics.cb_price,course_basics.cb_discount,course_basics.cb_title, course_basics.cb_code,course_basics.cb_position,course_basics.cb_access_validity,course_basics.cb_image,course_basics.cb_language,course_basics.cb_slug,course_basics.cb_status,course_basics.cb_category,course_basics.cb_deleted,course_basics.cb_approved,course_basics.cb_groups,"course" as item_type';
    $result                 = $scope->Course_model->courses($filter);
    
    //echo $scope->db->last_query();die;
    foreach ($result as $key => $course) {
        $result[$key]['assigned_tutors']    = $scope->Course_model->assigned_tutors(array('course_id' => $course['id']));
        $result[$key]['ratting']            = $scope->Course_model->get_ratting(array('course_id' => $course['id']));
    }
    $filter                 = array();
    $filter['status']       = isset($param['status']) ? $param['status'] : 1;
    $filter['filter']       = isset($param['filter']) ? $param['filter'] : 'active';
    $filter['direction']    = isset($param['direction']) ? $param['direction'] : 'DESC';
    $filter['select']       = isset($param['select']) ? $param['select'] : 'catalogs.id,catalogs.c_courses,catalogs.c_code,catalogs.c_is_free,catalogs.c_price,catalogs.c_discount,catalogs.c_title,catalogs.c_access_validity,catalogs.c_image,catalogs.c_slug,catalogs.c_status,catalogs.c_category,catalogs.c_deleted,catalogs.c_groups, "bundle" as item_type';
    $bundle                 = $scope->Bundle_model->bundles($filter);
    $result                 = array_merge($result,$bundle);
    shuffle($result);
    return $result;
}

function sales_manager_all_sorted_courses($param = array(), $scope)
{
    $scope->load->model(array('Course_model', 'Bundle_model'));
    $all_sorted_courses  = array();
    $all_courses         = $scope->Course_model->item_courses();
    if(!empty($all_courses))
    {
        foreach($all_courses as $course_key => $course)
        {
            if($course['iso_item_type']  == 'course')
            {
                $scope->load->model(array('Course_model','Bundle_model'));
                $course_id                          = isset($course['iso_item_id'])?$course['iso_item_id']:'0';
                $filter_param                       = array();
                $filter_param['course_id']          = $course_id;
                $course_details                     = $scope->Course_model->short_courses( $filter_param);
                $filter_param                       = array('course_id'=>$course_id,'cc_status' => '1');
                $course_details['rating']           = $scope->Course_model->get_ratting($filter_param);

                $course_details['assigned_tutors']  = $scope->Course_model->assigned_tutors(array('course_id' => $course_details['id']));
                $course_details['ratting']          = $scope->Course_model->get_ratting(array('course_id' => $course_details['id']));
                array_push($all_sorted_courses, $course_details);
            }
            elseif($course['iso_item_type'] == 'bundle')
            {
                $bundle_id                  = isset($course['iso_item_id'])?$course['iso_item_id']:'0';
                $filter_param               = array();
                $filter_param['bundle_id']  = $bundle_id;
                $bundle_details             = $scope->Bundle_model->short_bundles( $filter_param);
                $bundle_details['rating']   = $scope->Bundle_model->get_ratting(array('bundle_id' => $course['iso_item_id'],'cc_status' => '1'));

                array_push($all_sorted_courses, $bundle_details);
            }
            
        }
    }
    return $all_sorted_courses;
}

function transition_contents($param = array(), $scope)
{
    $scope->load->model('Content_model');
    $result = $scope->Content_model->transition_contents();
    return $result;
}

function course_details($params = array(), $scope)
{ 
    
    $scope->load->model('Course_model');
    //Load languages from memcache.

    $objects            = array();
    $objects['key']     = 'course_languages';
    $callback           = 'course_languages';
    $languages          = $scope->memcache->get($objects, $callback, array());
    $courseParams       = array();
    $courseParams['id'] =   $params['id'];
    // if(!isset($params['bundle']))
    // {
    //     $courseParams['status']    =   '1';
    // }
    // echo "<pre>";print_r($courseParams); die;
    $course_details = $scope->Course_model->course($courseParams);
    // echo $this->db->last_query();exit;
    //$courseParams = array();
    // print_r($course_details); die;
    if(!empty($course_details))
    {
        $course_details['tutors'] = $scope->Course_model->assigned_tutors(array('course_id' => $params['id']));
        $course_details['rating'] = $scope->Course_model->course_overall_rating(array('course_id' => $params['id'],'cc_status' => '1'));
        $course_details['total_ratting'] = $scope->Course_model->get_ratting(array('course_id' => $params['id'],'cc_status' => '1'));

        $override_data              = $scope->Course_model->lecute_override(array('course_id' => $params['id'],'source'=>'course'));
        $lecture_override           = array();

        foreach($override_data as $override)
        {
            $batches                = explode(',',$override['lo_override_batches']);
            foreach($batches as $batch)
            {
                if(is_numeric($batch))
                {
                    $lecture_override[$override['lo_lecture_id']][$batch]   = $override;
                }
            }
        }

        $course_details['override'] = $lecture_override;

        $course_langs = array();
        $course_details['cb_language'] = str_replace(" ", "", trim($course_details['cb_language']));
        $course_langs = explode(',', $course_details['cb_language']);
        $course_details['cb_language'] = array();
        if (isset($course_langs[0]) && $course_langs[0] != '') {
            foreach ($course_langs as $cl) {
                $course_details['cb_language'][] = $languages[$cl];
            }
        }

        $course_details['self_enroll']  = 0;

        if($course_details['cb_has_self_enroll'] && strtotime($course_details['cb_self_enroll_date']) > strtotime("today midnight")){
            $course_details['self_enroll']  = 1;
        }

        $course_details['sections']      = $scope->Course_model->sections(array('course_id' => $params['id'], 'limit' => '', 'status' => '1', 'order_by' => 's_order_no', 'direction' => 'ASC'));
        $course_details['lectures']      = $scope->Course_model->get_lectures(array('select' => 'course_lectures.id,course_lectures.cl_duration,course_lectures.cl_filename,course_lectures.cl_lecture_content,course_lectures.cl_org_file_name,course_lectures.cl_limited_access,course_lectures.cl_total_page,course_lectures.cl_lecture_type,course_lectures.cl_section_id,course_lectures.cl_lecture_name,course_lectures.cl_access_restriction,course_lectures.cl_order_no, course_lectures.cl_support_files,course_lectures.cl_lecture_image,cl_lecture_preview, assessments.a_duration', 'direction' => 'ASC', 'status' => 1, 'order_by' => 'cl_order_no', 'course_id' => $params['id']));
        $course_details['lecture_count'] = count($course_details['lectures']);

        //Initial 4 reviews
        $reviewLimt = 4;
        $course_details['course_reviews']['reviews'] = $scope->Course_model->db_get_rating(array('course_id' => $params['id'], 'limit' => $reviewLimt));
        $course_details['course_reviews']['limit'] = $reviewLimt;
        $course_details['course_reviews']['count'] = $scope->Course_model->db_get_rating(array('course_id' => $params['id'], 'count' => true)); // Total reviews for pagination/Loadmore
        //echo '<pre>';print_r($course_details['rating']); die;
        foreach ($course_details['lectures'] as $lkey => $lecture) {
            $course_details['lectures'][$lkey]['unique'] = '';
            switch ($lecture['cl_lecture_type']) {
                case 1:
                    $num = gmdate("i", $lecture['cl_duration']) + (gmdate("H", $lecture['cl_duration']) * 60);

                    $course_details['lectures'][$lkey]['unique'] = isset($lecture['cl_duration']) ? (gmdate("i", $lecture['cl_duration']) + (gmdate("H", $lecture['cl_duration']) * 60) <= 9 ? sprintf("%02d", $num) : gmdate("i", $lecture['cl_duration']) + (gmdate("H", $lecture['cl_duration']) * 60)) . ':' . gmdate("s", $lecture['cl_duration']) : '00:00';
                    break;

                case 12:
                    $num = gmdate("i", $lecture['cl_duration']) + (gmdate("H", $lecture['cl_duration']) * 60);

                    $course_details['lectures'][$lkey]['unique'] = isset($lecture['cl_duration']) ? (gmdate("i", $lecture['cl_duration']) + (gmdate("H", $lecture['cl_duration']) * 60) <= 9 ? sprintf("%02d", $num) : gmdate("i", $lecture['cl_duration']) + (gmdate("H", $lecture['cl_duration']) * 60)) . ':' . gmdate("s", $lecture['cl_duration']) : '00:00';
                    break;

                case 2:
                    $course_details['lectures'][$lkey]['unique'] = $lecture['cl_total_page'];
                    break;

                case 8:
                    $course_details['lectures'][$lkey]['unique'] = $lecture['cl_total_page'];
                    break;

                case 3:
                    $course_details['lectures'][$lkey]['unique'] = $scope->Course_model->db_get_question_count($lecture['id']);
                    break;

                case 4:
                    $course_details['lectures'][$lkey]['unique'] = '';
                    break;

                case 5:
                    $course_details['lectures'][$lkey]['unique'] = '';
                    break;

                case 6:
                    $course_details['lectures'][$lkey]['unique'] = '';
                    break;

                case 7:
                    $course_details['lectures'][$lkey]['unique'] = $scope->Course_model->db_get_ll_duration($lecture['id']);
                    $course_details['lectures'][$lkey]['unique'] = $course_details['lectures'][$lkey]['unique']['ll_duration'] * 60;
                    $num = gmdate("i", $course_details['lectures'][$lkey]['unique']) + (gmdate("H", $course_details['lectures'][$lkey]['unique']) * 60);

                    $course_details['lectures'][$lkey]['unique'] = isset($course_details['lectures'][$lkey]['unique']) ? (gmdate("i", $course_details['lectures'][$lkey]['unique']) + (gmdate("H", $course_details['lectures'][$lkey]['unique']) * 60) <= 9 ? sprintf("%02d", $num) : gmdate("i", $course_details['lectures'][$lkey]['unique']) + (gmdate("H", $course_details['lectures'][$lkey]['unique']) * 60)) . ':' . gmdate("s", $course_details['lectures'][$lkey]['unique']) : '00:00';
                    break;

            }
        }

        $course_details['enrolled_students'] = $scope->Course_model->get_subscription_count($params['id']);
        //print_r($course_details['enrolled_students']);die;
        return $course_details;
    }
    return false;
}

function lecture_info($params = array(), $scope)
{
    $scope->load->model('Course_model');
    $lecture_param              = array();
    $lecture_param['select']    = 'course_lectures.id,course_lectures.cl_lecture_type,course_lectures.cl_lecture_name';
    $lecture_param['direction'] = 'ASC';
    $lecture_param['status']    = 1;
    $lecture_param['course_id'] = isset($params['id'])?$params['id']:0;
    $lecture_param['order_by']  = 'cl_order_no';
    $lectures                   = $scope->Course_model->get_lectures($lecture_param);
    return $lectures;
}

function get_institution_by_id($params = array(), $scope)
{
    $scope->load->model('Institute_model');
    $ins = $scope->Institute_model->get_institute($params);
    return $ins;
}
function course_languages($param = array(), $scope)
{
    $scope->load->model('Course_model');
    $filter             = array();
    $filter['order_by'] = 'cl_lang_name';
    $result             = $scope->Course_model->languages($filter);
    $languages          = array();
    if (!empty($result)) {
        foreach ($result as $lang) {
            $languages[$lang['id']] = $lang;
        }
    }
    return $languages;
}

function get_categories($param = array(), $scope)
{
    $scope->load->model('Category_model');
    $direction                      = isset($param['direction']) ? $param['direction'] : 'ASC';
    $parent                         = isset($param['parent_id']) ? $param['parent_id'] : 0;
    $status                         = isset($param['status']) ? $param['status'] : 1;
    $category_param                 = array();
    $category_param['direction']    = $direction;
    $category_param['parent_id']    = $parent;
    if(!isset($param['inactive']))
    {
        $category_param['status']       = $status;
    }
    $category_param['not_deleted']  = true;
    $category_param['select']       = 'id,ct_name,ct_slug,ct_route_id,ct_status,ct_deleted';

    $categories = $scope->Category_model->categories($category_param);

    return $categories;
}

function subscription_details($param = array(), $scope)
{
    $scope->load->model('Course_model');
    $user_id = isset($param['user_id']) ? $param['user_id'] : 0;
    $course_id = isset($param['course_id']) ? $param['course_id'] : 0;
    $course_ids = isset($param['course_ids']) ? $param['course_ids'] : 0;
    $s_param = array('user_id' => $user_id, 'course_id' => $course_id, 'course_ids' => $course_ids, 'select' => 'course_subscription.id,course_subscription.cs_course_id,course_subscription.cs_user_id,course_subscription.cs_course_validity_status,course_subscription.cs_approved,course_subscription.cs_certificate_issued,course_subscription.cs_forum_blocked,course_subscription.cs_percentage,course_subscription.cs_topic_progress,course_subscription.cs_course_validity_status,course_subscription.cs_subscription_date,course_subscription.cs_start_date,course_subscription.cs_end_date,course_subscription.cs_auto_grade,course_subscription.cs_manual_grade,course_subscription.cs_lecture_log,course_subscription.cs_last_played_lecture');
    
    $result = $scope->Course_model->subscription_details($s_param);
    //echo $scope->db->last_query();die;
    //print_r($s_param);die;
    $result['my_rating'] = $scope->Course_model->get_user_ratting(array('course_id' => $course_id, 'user_id' => $user_id));
    if (isset($result['id'])) {
        $lecture_count = $scope->Course_model->db_completed_lectures(array('user_id' => $user_id, 'course_id' => $course_id));
        $result['percentage'] = $result['cs_percentage']; //$scope->Course_model->course_percentage(array('user_id' => $user_id, 'course_id' => $course_id));
        $result['completed_lectures'] = $lecture_count['count'];
    }
    //print_r($result);die;
    return $result;
}


function assesment_details($params = array(), $scope)
{
    
    $assessment_params = array('assessment_id' => $params['assesment_id'], 'select' => 'assessments.*');
    $scope->load->model('Course_model');
    $assesment['assesment_details']                   = $scope->Course_model->assesment($assessment_params);
    if(!empty($assesment['assesment_details'])){
        $lecture_id                                   = $assesment['assesment_details']['a_lecture_id'];
        $course_id                                    = $assesment['assesment_details']['a_course_id'];
    }

    $course                                           = $scope->Course_model->course(array('id' => $course_id,'select'=>'cb_title'));
    $lecture                                          = $scope->Course_model->lecture(array('id' => $lecture_id,'select' => 'cl_lecture_name'));
    $assesment['assesment_details']['a_title']        = $lecture['cl_lecture_name'];
    $assesment['assesment_details']['a_course_title'] = $course['cb_title'];


    $assesment_questions                    = $scope->Course_model->assessment_questions(array('assesment_id'=>$params['assesment_id']));
    foreach($assesment_questions as $assesment_question){
        $assesment['assesment_questions'][$assesment_question['aq_question_id']] = $assesment_question;
    }
    $question_params                        = array();
    $question_params['assesment_id']        = $params['assesment_id'];
    if ($assesment['assesment_details']['a_qshuffling']) {
        $question_params['order_by_random'] = 'RAND()';
    }
    //$assesment['questions'] = $scope->Course_model->questions($question_params);
    $questions = $scope->Course_model->questions($question_params);
    if (!empty($questions)) {
        foreach ($questions as $question) {
            //unset($question['q_answer']);
            $options = $scope->Course_model->options(array('q_answer' => $question['q_options']));
            
            $question['q_answer']   = explode(',', $question['q_answer']);
            $optArray               = $question['q_answer'];
            sort($question['q_answer']);
            $question['q_answer'] = implode(',', $question['q_answer']);
            //processing options
            $question['options']            = array();
            if (!empty($options)) {
                foreach ($options as $option) {
                    if (($question['q_type'] == 1) || ($question['q_type'] == 2)) {
                        foreach ($optArray as $optValue) {
                            if ($optValue == $option['id']) {
                                $question['correct_answer'][] = $option;
                            }
                        }
                    }

                    $option['qo_options']  = json_decode($option['qo_options'], true);

                    if (!(json_last_error() == JSON_ERROR_NONE)) {
                        $option = array();
                    }
                    $option_out = array();
                    if (!empty($option['qo_options'])) {
                        foreach ($option['qo_options'] as $key => $opt) {
                            $option['qo_options'][$key] = stripslashes($opt);
                        }
                    }
                    $question['options'][] = $option;
                }
            } else {
                $question['correct_answer'] = array();
            }
            //end

            //processing direction
            $q_direction = json_decode($question['q_directions'], true);
            if (!(json_last_error() == JSON_ERROR_NONE)) {
                $q_direction = array();
            }
            $q_direction_out = array();
            if (!empty($q_direction)) {
                foreach ($q_direction as $key => $value) {
                    $q_direction_out[$key] = stripslashes($value);
                }
            }
            $question['q_directions'] = $q_direction_out;
            //end

            //processing question
            $q_question = json_decode($question['q_question'], true);
            if (!(json_last_error() == JSON_ERROR_NONE)) {
                $q_question = array();
            }
            $q_question_out = array();
            if (!empty($q_question)) {
                foreach ($q_question as $key => $value) {
                    $q_question_out[$key] = stripslashes($value);
                }
            }
            $question['q_question'] = $q_question_out;
            //end

            //processing explanation
            $q_explanation = json_decode($question['q_explanation'], true);
            if (!(json_last_error() == JSON_ERROR_NONE)) {
                $q_explanation = array();
            }
            $q_explanation_out = array();
            if (!empty($q_explanation)) {
                foreach ($q_explanation as $key => $value) {
                    $q_explanation_out[$key] = stripslashes($value);
                }
            }
            $question['q_explanation'] = $q_explanation_out;
            //end

            $assesment['questions'][$question['id']] = $question;
        }
    }

    return $assesment;
}

function assessment_reports($params = array(), $scope)
{
    $attempt_params     = array('attempt_id' =>  $params['attempt_id']);
    $scope->load->model('Course_model');
    $assessment_reports = $scope->Course_model->assesment_reports($attempt_params);
    $results            = array();
    $results['assessment_report'] = array();
    $results['assessment_attempt'] = array();
    foreach($assessment_reports as $assessment_report){
        $results['assessment_report'][$assessment_report['ar_question_id']]=$assessment_report;
    }
    
    $results['assessment_attempt'] = $scope->Course_model->attempt(array('id'=>$params['attempt_id']));
    return $results;
} 

function my_subscriptions($param = array(), $scope)
{
    $scope->load->model(array('Report_model', 'Course_model', 'User_model'));
    $user_id            = isset($param['user_id']) ? $param['user_id'] : 0;
    $courses_only       = isset($param['courses_only']) ? $param['courses_only'] : false;
    $response           = array();
    $order_by           = isset($param['order_by']) ? $param['order_by'] : false;
  
    $enrolled_courses   = $scope->Report_model->enrolled_course(
                                                                array(
                                                                    'user_id'       => $user_id, 
                                                                    'courses_only'  => $courses_only,
                                                                    'order_by'      => $order_by
                                                                )
                                                            );
    foreach ($enrolled_courses as $course) {
        $course_tutors = $scope->Course_model->assigned_tutors(array('course_id' => $course['course_id']));
        $course['tutors'] = $course_tutors;
        $params                     = array('course_id' => $course['course_id'], 'status' => true);
        
        $percentage = $course['cs_percentage']; //$scope->Course_model->course_percentage(array('user_id' => $user_id, 'course_id' => $course['course_id']));
        $response[$course['course_id']] = $course;
        $response[$course['course_id']]['percentage'] = round($percentage, 2); 
        $response[$course['course_id']]['lectures'] = $scope->Course_model->lectures($params);//course_details($params, $scope);
        // $response[$course['course_id']]['ratting'] = $scope->Course_model->get_ratting(array('course_id' => $course['course_id']));
    }
    return $response;
}



function institutes($param = array(), $scope)
{
    $scope->load->model('Institute_model');
    $select = isset($param['select']) ? $param['select'] : 'id, ib_name, ib_institute_code';
    //$institute_id = isset($param['institute_id']) ? $param['institute_id'] : false;
    
    $institutes = $scope->Institute_model->institutes(array(
        'select' => $select,
        'not_deleted' => true,
        'status' => '1',
        'order_by' => 'ib_name',
        'direction' => 'ASC'
    ));
    return $institutes;
}

function institute($param = array(), $scope)
{
    $scope->load->model('Institute_model');
    $id = isset($param['id']) ? $param['id'] : 0;
    // $institute_id = isset($param['id']) ? $param['id'] : 0;
    $institute = $scope->Institute_model->institute(array(
        'id' => $id,
    ));
    return $institute;
}

function notifications()
{
    $CI =& get_instance();
    $CI->load->model(array('Notification_model'));
    $notifications = $CI->Notification_model->notifications(array('select'=>'n_content,n_notification_bar_type', 'status' => 1, 'date' => date('Y-m-d')));
    return $notifications;
}

function top_courses($param = array(), $scope)
{
    $scope->load->model(array('Homepage_model', 'Course_model','Bundle_model'));
    $limit              = isset($param['limit']) ? $param['limit'] : '8';
    $filter             = array();
    $filter['limit']    = $limit;
    $result             = $scope->Homepage_model->home_course($filter);
    $admin_name         = $scope->config->item('acct_name');
    $default_tutor      = array();
    $default_tutor[]    = array('us_name'=>$admin_name);

    foreach ($result as $t_key => $top) {
        $course_tutors              = $scope->Course_model->assigned_tutors(array('course_id' => $top['cs_course_id']));
        $result[$t_key]['tutors']   = (!empty($course_tutors))?$course_tutors:$default_tutor;
        $result[$t_key]['ratting']  = $scope->Course_model->get_ratting(array('course_id' => $top['cs_course_id'],'cc_status' => '1'));
    }
    
    $filter                 = array();
    $filter['status']       = isset($param['status']) ? $param['status'] : 1;
    $filter['limit']        = isset($param['limit']) ? $param['limit'] : '8';
    $filter['order_by']     = isset($param['order_by']) ? $param['order_by'] : 'created_date';
    $filter['filter']       = isset($param['filter']) ? $param['filter'] : 'active';
    $filter['direction']    = isset($param['direction']) ? $param['direction'] : 'DESC';
    $filter['select']       = isset($param['select']) ? $param['select'] : 'catalogs.id,catalogs.c_courses,catalogs.c_code,catalogs.c_is_free,catalogs.c_price,catalogs.c_discount,catalogs.c_title,catalogs.c_access_validity,catalogs.c_image,catalogs.c_slug,catalogs.c_status,catalogs.c_category,catalogs.c_deleted,catalogs.c_groups, "bundle" as item_type';
    $bundle                 = $scope->Bundle_model->bundles($filter);
    $result                 = array_merge($result,$bundle);
    shuffle($result); 

    return $result;
}

function mail_template($param = array(), $scope)
{
    $scope->load->model(array('Email_template_model'));
    return $scope->Email_template_model->mail_template($param);
}

function branches($param = array(), $scope)
{
    $scope->load->model('Institute_model');
    $result = $scope->Institute_model->get_branches();
    $branches = array();
    if (!empty($result)) {
        foreach ($result as $branch) {
            $branches[$branch['id']] = $branch;
        }
    }
    return $branches;
}

function branch($param = array(), $scope)
{
    $scope->load->model('Institute_model');
    $branch = $scope->Institute_model->get_branch(array('id' => $param['id']));
    return $branch;
}

function institute_batches($param = array(), $scope)
{
    $scope->load->model('Group_model');
    $batches = $scope->Group_model->groups(array('institute_id' => $param['institute_id'], 'not_deleted' => true, 'status' => '1', 'select' => $param['select']));
    return $batches;
}

function my_score($param = array(), $scope)
{
    $scope->load->model('User_model');
    $user_id = isset($param['user_id']) ? $param['user_id'] : 0;
    $batches = $scope->User_model->score(array('user_id' => $user_id));
    return $batches;
}

function grade_scale($param = array(), $scope)
{
    $scope->load->model('Course_model');
    $grades     = $scope->Course_model->grade();
    $grade_scale = array();
    if(!empty($grades))
    {
        foreach($grades as $grade)
        {
           $grade_scale[$grade['id']] = $grade; 
        }    
    }
    return $grade_scale;
}

function convert_percentage_to_grade( $percentage = 0 )
{
    
    $CI             = & get_instance();
    $grade          = '';
    $objects        = array();
    $objects['key'] = 'grade_scale';
    $callback       = 'grade_scale';
    $grade_objects  = $CI->memcache->get($objects, $callback, array()); 
    if(!empty($grade_objects))
    {
        foreach($grade_objects as $grade_object)
        {
            if($percentage >= $grade_object['gr_range_from'] && $percentage <= $grade_object['gr_range_to'])
            {
                $grade = $grade_object;   
                break;                 
            }
            else if($percentage < 0)
            {
                $grade = $grade_object;   
                $grade['gr_name'] = 'E';   
                break; 
            }
        }
    }
    return $grade;
}

function topic_wise_progress($param = array(),$scope)
{
    $user_id        = isset($param['user_id'])?$param['user_id']:0;
    $course_id      = isset($param['course_id'])?$param['course_id']:0;

    $scope->load->model('Course_model');
    $progress_data  = $scope->Course_model->course_topic_progress(array('user_id'=>$user_id,'course_id'=>$course_id));
    return $progress_data;
}

function update_lecture_log_wiht_subscription($param = array())
{
    
    $course_id           = isset($param['course_id'])?$param['course_id']:0;
    $lecture_id          = isset($param['lecture_id'])?$param['lecture_id']:0;
    $user_id             = isset($param['user_id'])?$param['user_id']:0;
    $grade               = isset($param['grade'])?$param['grade']:false;
    $percentage_of_marks = isset($param['percentage_of_marks'])?$param['percentage_of_marks']:false;
    if( $course_id > 0 && $lecture_id > 0 && $user_id > 0 && $grade )
    {
        $scope              = & get_instance();
        $scope->load->model('User_model');
        $subscription       = $scope->User_model->subscription(array('user_id' => $user_id, 'course_id' => $course_id, 'limit' => 1));
    
        $cs_lecture_log     = ($subscription['cs_lecture_log'])?json_decode($subscription['cs_lecture_log'], true):array();
        $cs_lecture_log[$lecture_id]['grade'] = $grade;
        if($percentage_of_marks)
        {
            $cs_lecture_log[$lecture_id]['percentage_of_marks'] = $grade;   
        }
        $condition              = array();
        $condition['update']    = true;
        $condition['id']        = $subscription['id'];
        $save                   = array();
        $save['id']             = $subscription['id'];
        $save['cs_lecture_log'] = json_encode($cs_lecture_log);
        // echo '<pre>';print_r($condition);die();
       // echo '<pre>';print_r($save);die();
        $scope->User_model->save_subscription_new($save, $condition);    
    }
}

function send_notification_to_mongo( $system_messages = array(), $token = '' )  
{
    $payload                = array();
    $payload['messages']    = $system_messages;
    // echo '<pre>'; print_r($payload);die;
    $payload                = json_encode($payload);
    $curlHandle             = curl_init(config_item('message_api_url').'messages');
    $defaultOptions         = array (
                                CURLOPT_POST => 1,
                                CURLOPT_POSTFIELDS => $payload,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_TIMEOUT_MS => 1000,
                            );
    curl_setopt_array($curlHandle , $defaultOptions);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, FALSE);     
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2); 
    curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer '.$token,
        'Content-Type: application/json',
        'Origin: '.site_url()
    ));
    $buffer = curl_exec($curlHandle);
    curl_close($curlHandle);
    return $buffer;
}

function events($param = array(), $scope)
{
    $scope->load->model('Events_model');
    $events                    = $scope->Events_model->getEvents($param);
    return $events;
}

function total_students($param = array(), $scope)
{
    $scope->load->model('User_model');
    $total_users  = $scope->User_model->users($param);
    return $total_users;
}

function enrolled_students($param = array(), $scope)
{
    $scope->load->model('Course_model');
    $total_enrolled_students  = $scope->Course_model->total_enrolled_students();
    return $total_enrolled_students;
}


function get_support_chat()
{
    $CI              =& get_instance();
    $objects         = array();
    $objects['key']  = 'support_chat';
    $callback        = 'support_chat_objects';
    return $CI->memcache->get($objects, $callback, array()); 
}

function support_chat_objects()
{
    $CI =& get_instance();
    $CI->load->database();
    $query   = 'SELECT * from support_chat WHERE support_chat_status = 1';
    $result  = $CI->db->query($query)->row_array();
    return $result;
}
function all_bundles($param = array(), $scope){

    $scope->load->model('Bundle_model');
    $filter                 = array();
    // $filter['status']       = isset($param['status']) ? $param['status'] : 1;
    // $filter['filter']       = isset($param['filter']) ? $param['filter'] : 'active';
    $filter['match']        = isset($param['match']) ? $param['match'] : false;
    $filter['course_id']    = isset($param['course_id']) ? $param['course_id'] : false;
    $filter['direction']    = isset($param['direction']) ? $param['direction'] : 'DESC';
    $filter['select']       = isset($param['select']) ? $param['select'] : 'catalogs.id,catalogs.c_code,catalogs.c_is_free,catalogs.c_price,catalogs.c_discount,catalogs.c_title,catalogs.c_access_validity,catalogs.c_image,catalogs.c_slug,catalogs.c_status,catalogs.c_category,catalogs.c_deleted,catalogs.c_groups,catalogs.c_courses, "bundle" as item_type';
    if($filter['match'] === false)
    {
        $result = $scope->Bundle_model->bundles($filter);
    }
    else
    {
        $result = $scope->Bundle_model->get_all_match($filter);
    }
    
    return $result;
}
function my_bundle_subscriptions($param = array(), $scope)
{
    $scope->load->model(array('Bundle_model', 'Course_model', 'User_model'));
    $user_id            = isset($param['user_id']) ? $param['user_id'] : 0;
    $order_by           = isset($param['order_by']) ? $param['order_by'] : false;
    $response           = array();
    $enrolled_bundles   = $scope->Bundle_model->enrolled_bundles(array('user_id' => $user_id,'order_by' => $order_by));
    
    //echo "<pre>";print_r($enrolled_bundles);exit('memcached');
    foreach ($enrolled_bundles as $bundle) {
        
        $response[$bundle['bundle_id']] = $bundle;
    }
    return $response;
}
function bundle_details($params = array(), $scope)
{
    $scope->load->model('Bundle_model');
    //Load languages from memcache.

    $course_details = $scope->Bundle_model->bundle(array('bundle_id' => $params['id']));

    if(!empty($course_details))
    {
        $course_details['rating'] = $scope->Bundle_model->course_overall_rating(array('bundle_id' => $params['id'],'cc_status' => '1'));
        $course_details['total_ratting'] = $scope->Bundle_model->get_ratting(array('bundle_id' => $params['id'],'cc_status' => '1'));
        //Initial 4 reviews
        $reviewLimt = 4;
        $course_details['course_reviews']['reviews'] = $scope->Bundle_model->db_get_rating(array('bundle_id' => $params['id'], 'limit' => $reviewLimt));
        $course_details['course_reviews']['limit'] = $reviewLimt;
        $course_details['course_reviews']['count'] = $scope->Bundle_model->db_get_rating(array('bundle_id' => $params['id'], 'count' => true)); // Total reviews for pagination/Loadmore
        $course_details['enrolled_students'] = $scope->Bundle_model->get_subscription_count($params['id']);
    }
    return $course_details;
}

function bundle_subscription_details($param = array(), $scope){

    $scope->load->model('Bundle_model');

    $user_id                = isset($param['user_id']) ? $param['user_id'] : 0;
    $bundle_id              = isset($param['bundle_id']) ? $param['bundle_id'] : 0;
    $subscribe_param        = array();
    $subscribe_param['user_id']     = $user_id;
    $subscribe_param['bundle_id']   = $bundle_id;
    $subscribe_param['select']      = 'bs_bundle_id,bs_user_name,bs_approved';
    
    $result                         = $scope->Bundle_model->subscription_details($subscribe_param);
    //echo '<pre>';print_r($result);die;
    if(!empty($result))
    {
        $result['my_rating']            = $scope->Bundle_model->get_user_ratting(array('bundle_id' => $bundle_id, 'user_id' => $user_id));
        $result['c_courses']            = json_decode($result['bs_bundle_details'],true);
        $result['user_id']     = $user_id;
    }
    
    return $result;
}

function check_user_valid($param = array(), $scope)
{
    $scope->load->model('User_model');

    $user_id                    = isset($param['user_id']) ? $param['user_id'] : 0;
    $check_param                = array();
    $check_param['user_id']     = $user_id;
    $check_param['select']      = 'users.id,users.us_name,users.us_country_code,users.us_email,users.us_image,users.us_about,users.us_phone,users.us_phone_verfified,users.us_email_verified,users.us_role_id,users.us_category_id,users.us_account_id,users.us_institute_id,users.us_branch,users.us_branch_code,users.us_institute_code,users.us_groups,users.us_status,users.us_deleted,users.us_language_speaks,users.us_course_first_view,users.us_profile_fields,users.us_profile_completed,users.us_token,users.us_session_id, roles.rl_name, roles.rl_status, roles.rl_type, roles.rl_deleted, roles.rl_account, roles.id as role_id, roles.rl_full_course,roles.rl_content_types';
    $result                     = $scope->User_model->check_if_exist($check_param);
    return $result;
}

function page($params = array(), $scope)
{
    $scope->load->model('page_model');
    $page = $scope->page_model->page($params);
    return $page;
}

function page_menu($param){
    $CI                 = & get_instance();
    $objects            = array();
    $objects['key']     = 'pages';
    $callback           = 'page_menu';
    $pages              = $CI->memcache->get($objects);
    $return             = array();
    if(empty($pages))
    {
        $CI->load->model('page_model');
        $pages   = $CI->page_model->pages(array('status' => '1', 'select' => 'pages.id, p_show_page_in, p_title, p_short_description, p_position, p_category, p_slug, p_seo_title, p_external_url, p_goto_external_url, p_new_window, p_status', 'not_deleted' => true, 'order_by' => 'p_position', 'direction' => 'ASC'));
        $CI->memcache->set($objects['key'], $pages);
    }

    if(isset($param['type']))
    {
        foreach($pages as $page)
        {
            switch ($param['type']) 
            {
                case 'header':
                    if($page['p_show_page_in'] == '1' || $page['p_show_page_in'] == '3')
                    {
                        $return[] = $page;
                    }
                break;
                case 'footer':
                    if($page['p_show_page_in'] == '2' || $page['p_show_page_in'] == '3')
                    {
                        $return[] = $page;
                    }
                break;  
            }
        }
    }
    die('page_menu 828 memcached');
    return $return;
}

function menu_pages($param){
    $CI                 = & get_instance();
    $objects            = array();
    $objects['key']     = 'menus';
    $callback           = 'page_menu';
    $menus              = $CI->memcache->get($objects);
    $return             = array();
    if(empty($menus))
    {
        $CI->load->model('Menu_model');
        $menus   = $CI->Menu_model->menus(array('status' => '1', 'select' => 'menu_manager.id, mm_item_connected_slug, mm_external_url, mm_name, mm_show_in, mm_parent_id, mm_connected_as_external, mm_item_connected_id, mm_new_window,mm_status, pages.p_title,pages.p_status, pages.id as pageid', 'order_by' => 'mm_sort_order',  'direction' => 'ASC'));
        $CI->memcache->set($objects['key'], $menus);
    }
    
    if(isset($param['type']))
    {   
        $parentIds  = array();
        $childmenus = array();
        foreach($menus as $key => $menu)
        {
            switch ($param['type']) 
            {
                case 'header':
                    
                    if($menu['mm_show_in'] == '1')
                    {
                        if($menu['mm_parent_id'] == '0')
                        {
                            if(isset($param['backend']) && $param['backend'])
                            {
                                $return['parent'][$menu['id']]          = $menu;
                                $parentIds[]                            = $menu['id'];
                            }else{
                                if($menu['mm_status'] == '1')
                                {
                                    //if($menu['mm_connected_as_external'] == '1' || $menu['mm_item_connected_slug'] != '')
                                    //{
                                        $return['parent'][$menu['id']]  = $menu;
                                        $parentIds[]                    = $menu['id'];
                                    //}
                                }
                            }
                        }
                        else
                        {
                            if(isset($param['backend']) && $param['backend'])
                            {
                                $childmenus[]                           = $menu;
                            }
                            else
                            {
                                if($menu['mm_status'] == '1' || $menu['p_status'] == '1')
                                {
                                    if($menu['mm_connected_as_external'] == '1' || $menu['mm_item_connected_slug'] != '')
                                    {
                                        $childmenus[]                   = $menu;
                                    }
                                }
                            }
                        }
                    }
                   // echo '<pre>';print_r($menu);die;
                break;
                case 'footer':
                    if($menu['mm_show_in'] == '2' )
                    {
                        $return[]                                       = $menu;
                    }
                break;  
            }
        }
    }
    if(!isset($param['backend'])){
    //echo '<pre>';print_r($return);
    //echo '<pre>';print_r($childmenus);die;
    }
    $menureturn = $return;
    if($param['type'] == 'header' && isset($menureturn['parent']))
    {
        foreach($childmenus as $key => $child)
        {
            //if(isset($param['backend']) && $param['backend'])
            //{ 
                if(in_array($child['mm_parent_id'], $parentIds)){
                    $menureturn['parent'][$child['mm_parent_id']]['child'][] = $child;
                }
            /*}
            else
            { 
                if($child['mm_status'] == '1' && $child['p_status'] == '1'){
                    if($child['mm_connected_as_external'] == '1' || $child['mm_item_connected_slug'] != ''){
                        if(in_array($child['mm_parent_id'], $parentIds)){
                            $menureturn['parent'][$child['mm_parent_id']]['child'][] = $child;
                        }
                    }
                }else{
                    $menureturn['parent'][$child['mm_parent_id']] = array();
                    //echo die('not active');
                }
            }*/
            
        }
    }
    //echo '<pre>';print_r($menureturn);die;
    return $menureturn;
}

function testimonials($param = array(), $scope)
{
    $scope->load->model('Settings_model');
    $testimonials           = $scope->Settings_model->get_testimonials($param);
    return $testimonials;
}

function home($param = array(), $scope)
{
    $scope->load->model(array('Homepage_model', 'Course_model','Category_model','Settings_model'));
    
    $return                   = array();
    $return['banner']         = $scope->Homepage_model->get_banner();
    $return['title']          = $scope->config->item('site_name');
    $return['categories']     = $scope->Category_model->categories(array('direction'=>'ASC', 'parent_id'=>0, 'status'=>1, 'not_deleted' => true, 'limit' => 4));
    $return['testimonials']   = $scope->Settings_model->get_testimonials(array('select'=>'t_name, t_other_detail, t_image, t_text', 'limit' => 6, 'featured' => true));

    return $return;
}

function page_categories()
{
    $CI                     = & get_instance();
    $objects                = array();
    $objects['key']         = 'categories';
    $callback               = 'get_categories';
    $categories             = $CI->memcache->get($objects, $callback,array()); 
    return $categories;
}

function current_user_session($params = array(), $scope)
{
    $scope->load->model('Authenticate_model');
    return $scope->Authenticate_model->validate_user_session( $params );
}

function short_courses_list($param = array(), $scope)
{
    $scope->load->model(array('Course_model','Bundle_model'));
    $course_id                  = isset($param['id'])?$param['id']:'0';
    $filter_param               = array();
    $filter_param['course_id']  = $course_id;
    $courses                    = $scope->Course_model->short_courses( $filter_param);
    $filter_param               = array('course_id'=>$course_id,'cc_status' => '1');
    $courses['rating']          = $scope->Course_model->get_ratting($filter_param);
   
    return $courses;
}

function short_bundle_list($param = array(), $scope)
{
    $scope->load->model(array('Course_model','Bundle_model'));
    $bundle_id                  = isset($param['id'])?$param['id']:'0';
    $filter_param               = array();
    $filter_param['bundle_id']  = $bundle_id;
    $bundles                    = $scope->Bundle_model->short_bundles( $filter_param);
    $bundles['rating']          = $scope->Bundle_model->get_ratting(array('bundle_id' => $param['id'],'cc_status' => '1'));
    return $bundles;
}
function all_sorted_course($param = array(), $scope)
{
    $scope->load->model(array('Course_model','Bundle_model', 'Category_model'));
    $all_sorted_courses                 = array();
    $all_courses                        = $scope->Course_model->item_courses();
    $image_dimension                    = '_300x160.jpg';
    if(!empty($all_courses))
    {
        foreach($all_courses as $course_key => $course)
        {
            if($course['iso_item_type'] == 'course')
            {
                $course_id                          = isset($course['iso_item_id'])?$course['iso_item_id']:'0';
                $filter_param                       = array();
                $filter_param['course_id']          = $course_id;
                $course_details                     = $scope->Course_model->short_courses( $filter_param);
                $filter_param                       = array('course_id'=>$course_id,'cc_status' => '1');
                $course_details['rating']           = $scope->Course_model->get_ratting($filter_param);
                
                if($course_details)
                {
                    $items_list = array();
                    $items_list['item_id']         = $course_details['id'];
                    $items_list['item_name']       = $course_details['cb_title'];
                    $items_list['item_price']      = $course_details['cb_price'];
                    $items_list['item_discount']   = $course_details['cb_discount'];
                    $items_list['item_is_free']    = ($course_details['cb_is_free']=='1')?'1':'0';
                    $items_list['item_type']       = $course_details['item_type'];
                    $course_categories             = explode(",",$course_details['cb_category']); 

                    $category_param['direction']   = 'ASC';
                    $category_param['parent_id']   = 0;
                    $category_param['not_deleted'] = true;
                    $category_param['select']      = 'id,ct_name,ct_slug,ct_route_id,ct_status,ct_deleted';
                    $all_categories                = $scope->Category_model->course_categories($category_param);
                   
                    $categories                    = array();
                    $categories_title              = array();
                    if(!empty($all_categories))
                    {
                        foreach($all_categories as $category)
                        {
                            if(in_array($category['id'],$course_categories))
                            {
                                array_push($categories_title,$category['ct_name']);
                                array_push($categories,$category);
                            }
                            
                        }
                    }
                    $items_list['item_search']     = $course_details['cb_category'];
                    $items_list['item_category']   = $categories;
                    $items_list['item_category_titles'] =  $categories_title;
                    // $image_first_name              = substr($course_details['cb_image'],0,-4);
                    // $image_new_name                = $image_first_name.$image_dimension;
                    $image_new_name                = $course_details['cb_image'];
                    $course_image                  = (($course_details['cb_image'] == 'default.jpg')?default_course_path():  course_path(array('course_id' => $course_details['id']))).$image_new_name;
                    $items_list['item_image']      = $course_image;
                    $items_list['item_has_rating'] = strval($course_details['cb_has_rating']);
                    $items_list['item_rating']     = strval($course_details['rating']);
                    array_push($all_sorted_courses,$items_list);
                }
            }
            elseif($course['iso_item_type'] == 'bundle')
            {
                $scope->load->model(array('Course_model','Bundle_model'));
                $bundle_id                          = isset($course['iso_item_id'])?$course['iso_item_id']:'0';
                $filter_param                       = array();
                $filter_param['bundle_id']          = $bundle_id;
                $bundle_details                     = $scope->Bundle_model->short_bundles( $filter_param);
                $bundle_details['rating']           = $scope->Bundle_model->get_ratting(array('bundle_id' => $course['iso_item_id'],'cc_status' => '1'));
                if($bundle_details)
                {
                    $items_list                     = array();
                    $items_list['item_id']          = $bundle_details['id'];
                    $items_list['item_name']        = $bundle_details['c_title'];
                    $items_list['item_price']       = $bundle_details['c_price'];
                    $items_list['item_discount']    = $bundle_details['c_discount'];
                    $items_list['item_is_free']     = ($bundle_details['c_is_free']=='1')?'1':'0';
                    $items_list['item_type']        = $bundle_details['item_type'];
                    $course_categories              = explode(",",$bundle_details['c_category']); 

                    $category_param['direction']    = 'ASC';
                    $category_param['parent_id']    = 0;
                    $category_param['not_deleted']  = true;
                    $category_param['select']       = 'id,ct_name,ct_slug,ct_route_id,ct_status,ct_deleted';
                    $all_categories                 = $scope->Category_model->course_categories($category_param);

                    $categories                     = array();
                    $categories_title              = array();
                    if(!empty($all_categories))
                    {
                        foreach($all_categories as $category)
                        {
                            if(in_array($category['id'],$course_categories))
                            {
                                array_push($categories_title,$category['ct_name']);
                                array_push($categories,$category);
                            }
                            
                        }
                    }
                    
                    $items_list['item_category_titles'] =  $categories_title;
                    $items_list['item_search']      = $bundle_details['c_category'];
                    $items_list['item_category']    = $categories;
                    // $image_first_name               = substr($bundle_details['c_image'],0,-4);
                    // $image_new_name                 = $image_first_name.$image_dimension;
                    $image_new_name                 = $bundle_details['c_image'];
                    $bundle_image                   = ($bundle_details['c_image'] == 'default.jpg')?default_catalog_path(): catalog_path(array('bundle_id' => $bundle_details['id'])).$image_new_name;
                    $items_list['item_image']       = $bundle_image;
                    $items_list['item_has_rating']  = strval($bundle_details['c_rating_enabled']);
                    $items_list['item_rating']      = strval($bundle_details['rating']);
                    array_push($all_sorted_courses,$items_list);
                }
            }
        }
    }
    return $all_sorted_courses;
}
function popular_courses($param = array(), $scope)
{
    $scope->load->model(array('Course_model','Bundle_model', 'Category_model'));
    $popular_courses_list       = array();
    $filter_param               = array();
    $image_dimension            = '_300x160.jpg';
    $filter_param['popular']    = true;
    $popular_courses            = $scope->Course_model->item_courses($filter_param);
    if(!empty($popular_courses))
    {
        foreach($popular_courses as $course_key => $course)
        {
            if($course['iso_item_type'] == 'course')
            {
                $course_id                          = isset($course['iso_item_id'])?$course['iso_item_id']:'0';
                $filter_param                       = array();
                $filter_param['course_id']          = $course_id;
                $course_details                     = $scope->Course_model->short_courses( $filter_param);
                $filter_param                       = array('course_id'=>$course_id,'cc_status' => '1');
                $course_details['rating']           = $scope->Course_model->get_ratting($filter_param);

                if($course_details)
                {
                    $items_list = array();
                    $items_list['item_id']         = $course_details['id'];
                    $items_list['item_name']       = $course_details['cb_title'];
                    $items_list['item_price']      = $course_details['cb_price'];
                    $items_list['item_discount']   = $course_details['cb_discount'];
                    $items_list['item_is_free']    = ($course_details['cb_is_free']=='1')?'1':'0';
                    $items_list['item_type']       = $course_details['item_type'];
                    $course_categories              = explode(",",$course_details['cb_category']); 

                    $category_param['direction']    = 'ASC';
                    $category_param['parent_id']    = 0;
                    $category_param['not_deleted']  = true;
                    $category_param['select']       = 'id,ct_name,ct_slug,ct_route_id,ct_status,ct_deleted';
                    $all_categories                 = $scope->Category_model->course_categories($category_param);

                    $categories                     = array();
                    if(!empty($all_categories))
                    {
                        foreach($all_categories as $category)
                        {
                            if(in_array($category['id'],$course_categories))
                            {
                                array_push($categories,$category);
                            }
                            
                        }
                    }
                    $items_list['item_search']     = $course_details['cb_category'];
                    $items_list['item_category']   = $categories;

                    // $image_first_name              = substr($course_details['cb_image'],0,-4);
                    // $image_new_name                = $image_first_name.$image_dimension;
                    $image_new_name                = $course_details['cb_image'];
                    $course_image                  = (($course_details['cb_image'] == 'default.jpg')?default_course_path():  course_path(array('course_id' => $course_details['id']))).$image_new_name;
                    $items_list['item_image']      = $course_image;
                    $items_list['item_has_rating'] = strval($course_details['cb_has_rating']);
                    $items_list['item_rating']     = strval($course_details['rating']);

                    array_push($popular_courses_list,$items_list);
                }
            }
            elseif($course['iso_item_type'] == 'bundle')
            {
                $bundle_id                          = isset($course['iso_item_id'])?$course['iso_item_id']:'0';
                $filter_param                       = array();
                $filter_param['bundle_id']          = $bundle_id;
                $bundle_details                     = $scope->Bundle_model->short_bundles( $filter_param);
                $bundle_details['rating']           = $scope->Bundle_model->get_ratting(array('bundle_id' => $bundle_id,'cc_status' => '1'));
                if($bundle_details)
                {
                    $items_list                     = array();
                    $items_list['item_id']          = $bundle_details['id'];
                    $items_list['item_name']        = $bundle_details['c_title'];
                    $items_list['item_price']       = $bundle_details['c_price'];
                    $items_list['item_discount']    = $bundle_details['c_discount'];
                    $items_list['item_is_free']     = ($bundle_details['c_is_free']=='1')?'1':'0';
                    $items_list['item_type']        = $bundle_details['item_type'];
                    $course_categories              = explode(",",$bundle_details['c_category']); 

                    $category_param['direction']    = 'ASC';
                    $category_param['parent_id']    = 0;
                    $category_param['not_deleted']  = true;
                    $category_param['select']       = 'id,ct_name,ct_slug,ct_route_id,ct_status,ct_deleted';
                    $all_categories                 = $scope->Category_model->course_categories($category_param);

                    $categories                     = array();
                    if(!empty($all_categories))
                    {
                        foreach($all_categories as $category)
                        {
                            if(in_array($category['id'],$course_categories))
                            {
                                array_push($categories,$category);
                            }
                            
                        }
                    }
                    $items_list['item_search']      = $bundle_details['c_category'];
                    $items_list['item_category']    = $categories;

                    // $image_first_name               = substr($bundle_details['c_image'],0,-4);
                    // $image_new_name                 = $image_first_name.$image_dimension;
                    $image_new_name                 = $bundle_details['c_image'];
                    $bundle_image                   = ($bundle_details['c_image'] == 'default.jpg')?default_catalog_path(): catalog_path(array('bundle_id' => $bundle_details['id'])).$image_new_name;
                    $items_list['item_image']       = $bundle_image;
                    $items_list['item_has_rating']  = strval($bundle_details['c_rating_enabled']);
                    $items_list['item_rating']      = strval($bundle_details['rating']);

                    array_push($popular_courses_list,$items_list);
                }
            }
           
        }
    }
    
    return $popular_courses_list;
}
function featured_courses($param = array(), $scope)
{
    $scope->load->model(array('Course_model','Bundle_model', 'Category_model'));
    $featured_courses_list      = array();
    $filter_param               = array();
    $image_dimension            = '_300x160.jpg';
    $filter_param['featured']   = true;
    $featured_courses           = $scope->Course_model->item_courses($filter_param);
    if(!empty($featured_courses))
    {
        foreach($featured_courses as $course_key => $course)
        {
            if($course['iso_item_type'] == 'course')
            {
                $course_id                          = isset($course['iso_item_id'])?$course['iso_item_id']:'0';
                $filter_param                       = array();
                $filter_param['course_id']          = $course_id;
                $course_details                     = $scope->Course_model->short_courses( $filter_param);
                $filter_param                       = array('course_id'=>$course_id,'cc_status' => '1');
                $course_details['rating']           = $scope->Course_model->get_ratting($filter_param);

                if($course_details)
                {
                    $items_list                     = array();
                    $items_list['item_id']          = $course_details['id'];
                    $items_list['item_name']        = $course_details['cb_title'];
                    $items_list['item_price']       = $course_details['cb_price'];
                    $items_list['item_discount']    = $course_details['cb_discount'];
                    $items_list['item_is_free']     = ($course_details['cb_is_free']=='1')?'1':'0';
                    $items_list['item_type']        = $course_details['item_type'];
                    $course_categories              = explode(",",$course_details['cb_category']); 

                    $category_param['direction']    = 'ASC';
                    $category_param['parent_id']    = 0;
                    $category_param['not_deleted']  = true;
                    $category_param['select']       = 'id,ct_name,ct_slug,ct_route_id,ct_status,ct_deleted';
                    $all_categories                 = $scope->Category_model->course_categories($category_param);

                    $categories                     = array();
                    if(!empty($all_categories))
                    {
                        foreach($all_categories as $category)
                        {
                            if(in_array($category['id'],$course_categories))
                            {
                                array_push($categories,$category);
                            }
                            
                        }
                    }
                    $items_list['item_search']      = $course_details['cb_category'];
                    $items_list['item_category']    = $categories;

                    // $image_first_name              = substr($course_details['cb_image'],0,-4);
                    // $image_new_name                = $image_first_name.$image_dimension;
                    $image_new_name                 = $course_details['cb_image'];
                    $course_image                   = (($course_details['cb_image'] == 'default.jpg')?default_course_path():  course_path(array('course_id' => $course_details['id']))).$image_new_name;
                    $items_list['item_image']       = $course_image;
                    $items_list['item_has_rating']  = strval($course_details['cb_has_rating']);
                    $items_list['item_rating']      = strval($course_details['rating']);
                    array_push($featured_courses_list,$items_list);
                }
            }
            elseif($course['iso_item_type'] == 'bundle')
            {
                $bundle_id                          = isset($course['iso_item_id'])?$course['iso_item_id']:'0';
                $filter_param                       = array();
                $filter_param['bundle_id']          = $bundle_id;
                $bundle_details                     = $scope->Bundle_model->short_bundles( $filter_param);
                $bundle_details['rating']           = $scope->Bundle_model->get_ratting(array('bundle_id' => $bundle_id,'cc_status' => '1'));
                if($bundle_details)
                {
                    $items_list                     = array();
                    $items_list['item_id']          = $bundle_details['id'];
                    $items_list['item_name']        = $bundle_details['c_title'];
                    $items_list['item_price']       = $bundle_details['c_price'];
                    $items_list['item_discount']    = $bundle_details['c_discount'];
                    $items_list['item_is_free']     = ($bundle_details['c_is_free']=='1')?'1':'0';
                    $items_list['item_type']        = $bundle_details['item_type'];
                    $course_categories              = explode(",",$bundle_details['c_category']); 

                    $category_param['direction']    = 'ASC';
                    $category_param['parent_id']    = 0;
                    $category_param['not_deleted']  = true;
                    $category_param['select']       = 'id,ct_name,ct_slug,ct_route_id,ct_status,ct_deleted';
                    $all_categories                 = $scope->Category_model->course_categories($category_param);

                    $categories                     = array();
                    if(!empty($all_categories))
                    {
                        foreach($all_categories as $category)
                        {
                            if(in_array($category['id'],$course_categories))
                            {
                                array_push($categories,$category);
                            }
                            
                        }
                    }
                    $items_list['item_search']      = $bundle_details['c_category'];
                    $items_list['item_category']    = $categories;

                    // $image_first_name               = substr($bundle_details['c_image'],0,-4);
                    // $image_new_name                 = $image_first_name.$image_dimension;
                    $image_new_name                 = $bundle_details['c_image'];
                    $bundle_image                   = ($bundle_details['c_image'] == 'default.jpg')?default_catalog_path(): catalog_path(array('bundle_id' => $bundle_details['id'])).$image_new_name;
                    $items_list['item_image']       = $bundle_image;
                    $items_list['item_has_rating']  = strval($bundle_details['c_rating_enabled']);
                    $items_list['item_rating']      = strval($bundle_details['rating']);
                    array_push($featured_courses_list,$items_list);
                }
            }
        }
    }
    
    return $featured_courses_list;
}

function instructor_details($params = array(), $scope)
{
    $scope->load->model(array('Faculty_model', 'Location_model', 'Course_model', 'Category_model'));
    $instructor_params                          = 'users.id, users.us_name, users.us_email, users.us_image, users.us_about, users.us_phone, users.us_status, users.us_deleted, users.us_degree, users.us_experiance, users.us_native, users.us_language_speaks, users.us_expertise, users.us_youtube_url';
    $instructor_details                         = $scope->Faculty_model->faculty(array('id'=>$params['id'], 'select' =>$instructor_params, 'role_id' => 3));
    if(sizeof($instructor_details) > 0)
    {
        $instructor_courses                         = $scope->Faculty_model->course_details(array('tutor_id' => $instructor_details['id']));
        
        $instructor_details['courses']              = array();
        if(!empty($instructor_courses))
        {
            foreach($instructor_courses as $i_courses)
            {
                $course_basices                     = 'course_basics.id, course_basics.cb_title, course_basics.cb_description, course_basics.cb_price, course_basics.cb_discount, course_basics.cb_image, course_basics.cb_category';
                $course_details                     = $scope->Course_model->course(array('id' => $i_courses['course_id'], 'select' => $course_basices));
                $course_details['cb_category']      = $scope->Category_model->categories(array('ids' => explode(',', $course_details['cb_category']), 'select' => 'categories.ct_name'));
                $course_details['rating']           = $scope->Course_model->course_overall_rating(array('course_id' => $i_courses['course_id'],'cc_status' => '1'));
                $course_details['total_ratting']    = $scope->Course_model->get_ratting(array('course_id' => $i_courses['course_id'],'cc_status' => '1'));
                $instructor_details['courses'][]    = $course_details;
            }
        }
        
        $instructor_details['us_youtube_url']       = json_decode($instructor_details['us_youtube_url']);
        
        $scope->memcache->delete('course_languages'); 
        $objects                                    = array();
        $objects['key']                             = 'course_languages';
        $callback                                   = 'course_languages';
        $params                                     = array();
        $course_languages                           = $scope->memcache->get($objects, $callback, $params); 
        
        $language_speaks_ids                        = explode(',', $instructor_details['us_language_speaks']);

        $language_names                             = array();
        if(!empty($language_speaks_ids))
        {
            foreach($language_speaks_ids as $language_id)
            {
                $language_names[]   = isset($course_languages[$language_id]['cl_lang_name'])?$course_languages[$language_id]['cl_lang_name']:'';
            }
        }
        if(!empty($language_names))
        {
            $instructor_details['us_language_speaks'] = implode(', ', $language_names);
        }
        $instructor_details['expertise']            = $scope->Faculty_model->expertises(array('ids' => explode(',', $instructor_details['us_expertise'])));

        $instructor_details['faculty_city']         = array();
        $instructor_details['faculty_state']        = array();
        $instructor_details['faculty_city']['city_name']   = '';
        $instructor_details['faculty_state']['state_name'] = '';
        if($instructor_details['us_native'])
        {
            $instructor_details['faculty_city']     = $scope->Location_model->city(array('id' => $instructor_details['us_native']));
            if($instructor_details['faculty_city'])
            {
                $instructor_details['faculty_state'] = $scope->Location_model->state(array('id' => $instructor_details['faculty_city']['state_id']));
            }            
        }
    }
    return $instructor_details;
}

function course_notification($param = array(), $scope)
{
    $scope->load->model(array('User_model','Tutor_model'));
    $course_id                      = isset($param['course_id']) ? $param['course_id'] : 0;

    $assigned_tutors_id             = array();
    $site_admins_id                 = array();
    $institute_manager_id           = array();

    $assigned_tutors                = $scope->Tutor_model->get_tutors_assigned_course(array('course' => $course_id));
    foreach($assigned_tutors as $assigned_tutor)
    {
        $assigned_tutors_id[]       = $assigned_tutor['ct_tutor_id'];
    }
    $site_admins                    = $scope->User_model->get_user_by_role(array('role_id' => 1));
    foreach($site_admins as $site_admin)
    {
        $site_admins_id[]           = $site_admin['id'];
    }
    $institute_managers             = $scope->User_model->get_user_by_role(array('role_id' => 8));
    foreach($institute_managers as $institute_manager)
    {
        $institute_manager_id[]     = $institute_manager['id'];
    }
    $response['preveleged_users']   = array_merge($assigned_tutors_id, $site_admins_id, $institute_manager_id);
    return $response;
}

function bundle_notification($param = array(), $scope)
{
    $scope->load->model(array('Bundle_model'));
    $bundle_id                      = isset($param['bundle_id']) ? $param['bundle_id'] : 0;
    $bundles                        = $scope->Bundle_model->bundle(array('bundle_id' => $bundle_id, 'select' => 'c_courses'));
    $bundle_courses                 = json_decode($bundles['c_courses'], true);
    $preveleged_users               = array();
    foreach($bundle_courses as $bundle_course)
    {
        $objects                    = array();
        $objects['key']             = 'course_notification_' . $bundle_course['id'];
        $callback                   = 'course_notification';
        $params                     = array('course_id' => $bundle_course['id']);
        $discussion_forum           = $scope->memcache->get($objects, $callback, $params);
        $preveleged_users[]         = $discussion_forum['preveleged_users'];
    }
    $response['preveleged_users']   = array_unique(call_user_func_array('array_merge', $preveleged_users));
    return $response;
}
function enrolled_item_ids($param = array(), $scope)
{
    $scope->load->model(array('Bundle_model','Report_model'));
    $user_id            = isset($param['user_id']) ? $param['user_id'] : 0;
    $response           = array();
    $bundles            = array();
    $courses            = array();
    $enrolled_bundles   = $scope->Bundle_model->enrolled_bundles(array('user_id' => $user_id));
    $enrolled_courses   = $scope->Report_model->enrolled_course(array('user_id' => $user_id));
    if(!empty($enrolled_bundles))
    {
        foreach($enrolled_bundles as $enrolled_bundle)
        {
            $bundles[]  = array('bundles'=>$enrolled_bundle['bundle_id'],'status'=>$enrolled_bundle['bs_approved']);
        }
    }
    if(!empty($enrolled_courses))
    {
        foreach($enrolled_courses as $enrolled_course)
        {
            $courses[]  = array('courses'=>$enrolled_course['course_id'],'status'=>$enrolled_course['cs_approved']);
        }
    }
    $response['enrolled_courses'] = $courses;
    $response['enrolled_bundles'] = $bundles;
    return $response;
}
/*
    purpose : used to fetch course details 
    params  : course_id

*/
function mobile_course_details($params = array(), $scope)
{
    $scope->load->model('Course_model');
    /* Load course details from memcache */
    $course_id          = isset($params['id'])?$params['id']:'0';
    $route              = true;
    $select             = 'course_basics.id, course_basics.cb_title,course_basics.cb_is_free, course_basics.cb_description, course_basics.cb_access_validity, course_basics.cb_price, course_basics.cb_discount, course_basics.cb_validity, course_basics.cb_validity_date, course_basics.cb_image, course_basics.cb_language, course_basics.cb_status, course_basics.cb_approved, course_basics.cb_deleted,course_basics.cb_short_description,course_basics.cb_tax_method,course_basics.cb_what_u_get,course_basics.cb_requirements,course_basics.cb_has_rating,course_basics.cb_preview,course_basics.cb_preview_time,course_basics.cb_has_lecture_image,course_basics.cb_has_self_enroll,course_basics.cb_self_enroll_date,routes.slug,routes.id as route_id,"course" as item_type';
    $course_details     = $scope->Course_model->course(array('id' => $course_id,'route' => $route ,'select' => $select));
    if(!empty($course_details))
    {
        /* Load ratings of course */
        $filter_param                           = array('course_id'=>$course_id,'cc_status' => '1');
        $course_details['rating']               = $scope->Course_model->get_ratting($filter_param);

        // $image_dimension                        = '_300x160.jpg';
        // $image_first_name                       = substr($course_details['cb_image'],0,-4);
        // $image_new_name                         = $image_first_name.$image_dimension;
        $image_new_name                         = $course_details['cb_image'];
        $course_image                           = (($course_details['cb_image'] == 'default.jpg')?default_course_path():  course_path(array('course_id' => $course_details['id']))).$image_new_name;
        $course_details['cb_image']             = $course_image;
        $course_details['cb_what_u_get']        = json_decode($course_details['cb_what_u_get']);
        $course_details['cb_requirements']      = json_decode($course_details['cb_requirements']);
        $course_details['access_expired']       = false;
        if($course_details['cb_access_validity'] == 2)
        {
            $today                              = date('Y-m-d');
            $valid_till                         = $course_details['cb_validity_date'];
            $course_details['access_expired']   = (strtotime($valid_till)>=strtotime($today))?false:true;  
        }
        $course_details['self_enroll_expired']  = false;
        if($course_details['cb_has_self_enroll'] == 1)
        {
            $today                              = date('Y-m-d');
            $enroll_valid_till                  = $course_details['cb_self_enroll_date'];
            $course_details['self_enroll_expired']   = (strtotime($enroll_valid_till)>=strtotime($today))?false:true;  
        }
        /* Load sections of course */
        $section_param                          = array();
        $section_param['course_id']             = $course_id;
        $section_param['limit']                 = '';
        $section_param['status']                = '1';
        $section_param['order_by']              = 's_order_no';
        $section_param['direction']             = 'ASC';
        $sections                               = $scope->Course_model->sections($section_param);
        
        $course_details['preview_link']         = site_url('materials/course/' . $course_id);
        $course_details['curriculum']           = array();
        $course_details['curriculum']['title']  = 'Topics & Classes';
        $course_details['curriculum']['list']   = array();
        if(!empty($sections))
        {
            foreach($sections as $section)
            {
                 /* Load lectures of course */
                $lecture_param                          = array();
                $lecture_param['course_id']             = $course_id;
                $lecture_param['section_id']            = $section['id'];
                $lecture_param['order_by']              = 'cl_order_no';
                $lecture_param['status']                = '1';
                $lecture_param['direction']             = 'ASC';
                $lecture_param['not_lecture_type']      = '13';
                $lecture_param['select']                = 'course_lectures.id,course_lectures.cl_filename,course_lectures.cl_section_id,course_lectures.cl_lecture_name, course_lectures.cl_lecture_type,course_lectures.cl_duration,course_lectures.cl_lecture_preview,course_lectures.cl_course_id,course_lectures.cl_lecture_image';
                $lectures                               = $scope->Course_model->get_lectures($lecture_param);
                if(!empty($lectures))
                {
                    $section_details                    = array();
                    $section_details['topic_id']        = $section['id'];
                    $section_details['topic_name']      = $section['s_name'];
                    $section_details['topic_course_id'] = $section['s_course_id'];
                    $section_details['topic_order']     = $section['s_order_no'];
                    $section_details['topic_status']    = $section['s_status'];
                    $section_details['topic_deleted']   = $section['s_deleted'];
                    $topic_image                        = '';
                    if($course_details['cb_has_lecture_image'] == '1')
                    {
                        $topic_image                    = ($section['s_image'] != 'default-section.jpg') ? course_section_image_path(array('course_id' => $section['s_course_id'])) . $section['s_image'] : default_course_path()."default-section.jpg";
                    }
                    $section_details['topic_image']     = $topic_image;
                    $section_details['topic_classes']   = array();
               
                    $quiz_count                     = 0;
                    foreach($lectures as $key => $lecture)
                    {
                        if($section['id'] == $lecture['cl_section_id'] )
                        {
                            if($lecture['cl_lecture_type'] == '3')
                            {
                                $quiz_count++;
                            }
                            $lecture['lecture_preview_enabled'] = $lecture['cl_lecture_preview'];
                            if($lecture['cl_lecture_type'] == '1')
                            {
                                $file_name                              = empty($lecture['cl_filename'])?'':explode('/',$lecture['cl_filename']);
                                $file_url                               = isset($file_name[2])?$file_name[2]:false;
                                $lecture['lecture_preview']             = array();
                                $lecture['lecture_preview']['vimeo_id'] = $file_url;
                                $lecture['lecture_preview']['provider'] = 'vimeo';
                            }
                            $lecture_image                              = '';
                            if($course_details['cb_has_lecture_image'] == '1')
                            {
                                $lecture_image                          = ($lecture['cl_lecture_image']!='default-lecture.jpg') ? course_lecture_image_path(array('course_id' => $lecture['cl_course_id'])) . $lecture['cl_lecture_image'] : default_course_path()."default-lecture.jpg";
                            }
                            $lecture['lecture_image']                   = $lecture_image;
                            
                            $section_details['topic_classes'][]         = $lecture;
                            unset($lectures[$key]);
                        }
                    }
                    $section_details['topic_test_count']        = $quiz_count;
                    $section_details['topic_class_count']       = count($section_details['topic_classes']);
                    $course_details['curriculum']['list'][]     = $section_details;
                }
            }
        }
        /* Load enrolled students of course */
        $course_details['enrolled_students']    = $scope->Course_model->get_subscription_count($course_id);;
        /* Load user reviews of course */
        $course_details['course_reviews']       = $scope->Course_model->db_get_rating(array('course_id' => $params['id']));   
    
        $gst_setting                            = $scope->settings->setting('has_tax');
        $cgst                                   = ($gst_setting['as_setting_value']['setting_value']->cgst != '') ? $gst_setting['as_setting_value']['setting_value']->cgst:'0';
        $sgst                                   = ($gst_setting['as_setting_value']['setting_value']->sgst != '') ? $gst_setting['as_setting_value']['setting_value']->sgst:'0';
        $course_details['tax_details']          = array('sgst' => (double)$sgst,'cgst' => (double)$cgst);
        $payment_keys                           = $scope->settings->setting('payment_gateway');
        $payment_keys                           = $payment_keys['as_setting_value']['setting_value']->razorpay;
        $api_key                                = $payment_keys->creditionals->key;
        $auth_token                             = $payment_keys->creditionals->secret;
        $course_details['course_key_pass']      = array('key'=> base64_encode($api_key),'pass'=> base64_encode($auth_token));
    }
    // $course_details['tutors']           = $scope->Course_model->assigned_tutors(array('course_id' => $params['id']));
    
    return $course_details;
}
/*
    purpose : used to fetch course details 
    params  : course_id

*/
function mobile_bundle_details($params = array(), $scope)
{
    $bundle_id                                      = isset($params['id'])?$params['id']:'0';
    $scope->load->model(array('Bundle_model', 'Course_model', 'Category_model'));
    $image_dimension                                = '_300x160.jpg';
    $route                                          = true;
    $bundle_params                                  = 'catalogs.id, catalogs.c_title, catalogs.c_is_free, catalogs.c_description, catalogs.c_access_validity, catalogs.c_price, catalogs.c_discount, catalogs.c_validity, catalogs.c_validity_date, catalogs.c_image, catalogs.c_status, catalogs.c_deleted, catalogs.c_courses, catalogs.c_tax_method, catalogs.c_rating_enabled,routes.slug,routes.id as route_id,"bundle" as item_type';
    $bundle_details                                 = $scope->Bundle_model->bundle(array('bundle_id' => $bundle_id,'route' => $route ,'select' => $bundle_params));
    if(!empty($bundle_details))
    {

        $bundle_details['rating']                   = $scope->Bundle_model->get_ratting(array('bundle_id' => $bundle_id,'cc_status' => '1'));
        $bundle_details['reviews']                  = $scope->Bundle_model->db_get_rating(array('bundle_id' => $bundle_id));
        
        // $image_first_name                           = substr($bundle_details['c_image'],0,-4);
        // $image_new_name                             = $image_first_name.$image_dimension;
        $image_new_name                             = $bundle_details['c_image'];
        $bundle_image                               = ($bundle_details['c_image'] == 'default.jpg')?default_catalog_path(): catalog_path(array('bundle_id' => $bundle_id)).$image_new_name;
        $bundle_details['c_image']                  = $bundle_image;
        $bundle_details['access_expired']           = false;
        if($bundle_details['c_access_validity'] == 2)
        {
             
            $today                                  = date('Y-m-d');
            $valid_till                             = $bundle_details['c_validity_date'];
            $bundle_details['access_expired']       = (strtotime($valid_till)>=strtotime($today))?false:true;  
        }
        $bundle_courses                             = json_decode($bundle_details['c_courses'],true);
        $bundle_details['courses']                  = array();
        if($bundle_courses)
        {
            $bundle_course_ids                      = array();
            foreach($bundle_courses as $course)
            {
                $course_id                          = $course['id'];
                $select                             = 'course_basics.id, course_basics.cb_title, course_basics.cb_description, course_basics.cb_category, course_basics.cb_image';
                $course_details                     = $scope->Course_model->course(array('id' => $course_id, 'select' => $select));
                $class_count                        = 0;
                $test_count                         = 0;
                if(!empty($course_details))
                {
                    $image_first_name               = substr($course_details['cb_image'],0,-4);
                    $image_new_name                 = $image_first_name.$image_dimension;
                    $course_image                   = (($course_details['cb_image'] == 'default.jpg')?default_course_path():  course_path(array('course_id' => $course_details['id']))).$image_new_name;
                    $course_details['cb_image']     = $course_image;

                    $lecture_param                  = array();
                    $lecture_param['course_id']     = $course_id;
                    $lecture_param['order_by']      = 'cl_order_no';
                    $lecture_param['status']        = '1';
                    $lecture_param['direction']     = 'ASC';
                    $lecture_param['select']        = 'course_lectures.id,course_lectures.cl_lecture_type';
                    $lectures                       = $scope->Course_model->get_lectures($lecture_param);
                    if(!empty($lectures))
                    {
                        $class_count                = count($lectures);
                        $test_count                 = 0;
                        foreach($lectures as $lecture)
                        {
                            if($lecture['cl_lecture_type'] == '3')
                            {
                                $test_count++;
                            }
                        }
                    }
                }
                $course_details['class_count']      = $class_count;
                $course_details['test_count']       = $test_count;
                $bundle_details['courses'][]        = $course_details;
                $bundle_course_ids[]                = $course_id;      
            }
            $bundle_details['course_count']         = count($bundle_courses);
            /* Load lectures of course */
            $lecture_param                          = array();
            $lecture_param['course_ids']            = $bundle_course_ids;
            $lecture_param['order_by']              = 'cl_order_no';
            $lecture_param['status']                = '1';
            $lecture_param['direction']             = 'ASC';
            $lecture_param['select']                = 'course_lectures.id';
            $lectures                               = $scope->Course_model->get_lectures($lecture_param);
            $bundle_details['topic_class_count']    = empty($lectures)?'0':count($lectures);
            $lecture_param['lecture_type']          = '3';
            $lectures                               = $scope->Course_model->get_lectures($lecture_param);
            $bundle_details['topic_test_count']     = empty($lectures)?'0':count($lectures);
        }   
        $gst_setting                                = $scope->settings->setting('has_tax');
        $cgst                                       = ($gst_setting['as_setting_value']['setting_value']->cgst != '') ? $gst_setting['as_setting_value']['setting_value']->cgst:'0';
        $sgst                                       = ($gst_setting['as_setting_value']['setting_value']->sgst != '') ? $gst_setting['as_setting_value']['setting_value']->sgst:'0';
        $bundle_details['tax_details']              = array('sgst' => (double)$sgst,'cgst' => (double)$cgst);
        $payment_keys                               = $scope->settings->setting('payment_gateway');
        $payment_keys                               = $payment_keys['as_setting_value']['setting_value']->razorpay;
        $api_key                                    = $payment_keys->creditionals->key;
        $auth_token                                 = $payment_keys->creditionals->secret;
        $bundle_details['course_key_pass']          = array('key'=> base64_encode($api_key),'pass'=> base64_encode($auth_token));
            
        
    }
    return $bundle_details;
}

function get_subscriptions($param = array(), $scope)
{
    $scope->load->model(array('Report_model', 'Bundle_model'));
    $user_id                                = isset($param['user_id']) ? $param['user_id'] : 0;
    
    $enrolled_param                         = array();
    $enrolled_param['user_id']              = $user_id;
    $enrolled_param['courses_only']         = true;
    $subscribed_courses                     = $scope->Report_model->my_courses($enrolled_param);
    $courses                                = array();
    if(!empty($subscribed_courses))
    {
        foreach ($subscribed_courses as $subscribed) 
        {
            $course_completion                  = 0;
            $percentage                         = $subscribed['cs_percentage'];
            $subscribed['cs_percentage']        = round($percentage, 2);
            $image_new_name                     = $subscribed['cb_image'];
            $course_image                       = ($subscribed['cb_image'] == 'default.jpg') ? default_course_path() : course_path(array('course_id' => $subscribed['course_id'])) . $image_new_name;
            $subscribed['cb_image']             = $course_image;

            $course_completion                 += $subscribed['cs_percentage'];
            $subscribed['course_completion']    = round($course_completion);
            $now                                = time(); 
            $start_date                         = strtotime(date('Y-m-d'));
            $your_date                          = strtotime($subscribed['cs_end_date'].' + 1 days');
            $datediff                           = $your_date - $now;
            $today                              = date('Y-m-d');
            $expire                             = date_diff(date_create($today), date_create($subscribed['cs_end_date']));
            $subscribed['expired']              = ceil($datediff / (60 * 60 * 24)) > 0? false:true;
            $subscribed['expire_in']            = $expire->format("%R%a");
            $expires_in                         = ceil($datediff / (60 * 60 * 24));
            
            $subscribed['expire_in_days']       = $expires_in;
            $subscribed['validity_format_date'] = date('d-m-Y', strtotime($subscribed['cs_end_date']));
            $courses[]                          = $subscribed;
        }
    }
    
    $bundle_enroll_param                    = array();
    $bundle_enroll_param['user_id']         = $user_id;
    $bundle_enroll_param['order_by']        = true;
    $enrolled_bundles                       = $scope->Bundle_model->enrolled_bundles($bundle_enroll_param);
    $bundles                                = array();
    if(!empty($enrolled_bundles))
    {
        foreach ($enrolled_bundles as $enrolled) 
        {
            $image_new_name                     = $enrolled['c_image'];
            $course_image                       = ($enrolled['c_image'] == 'default.jpg') ? default_catalog_path() : catalog_path(array('bundle_id' => $enrolled['bundle_id'])) . $image_new_name;
            $enrolled['c_image']                = $course_image;

            $now                                = time(); 
            $start_date                         = strtotime(date('Y-m-d'));
            $your_date                          = strtotime($enrolled['bs_end_date'].' + 1 days');
            $datediff                           = $your_date - $now;
            $today                              = date('Y-m-d');
            $expire                             = date_diff(date_create($today), date_create($enrolled['bs_end_date']));
            $enrolled['expired']                = ceil($datediff / (60 * 60 * 24)) > 0? false:true;
            $enrolled['expire_in']              = $expire->format("%R%a");
            $expires_in                         = ceil($datediff / (60 * 60 * 24));
            $enrolled['expire_in_days']         = $expires_in;
            $enrolled['validity_format_date']   = date('d-m-Y', strtotime($enrolled['bs_end_date']));
            $bundles[]                          = $enrolled;
        }
        
    }
    $subscriptions                          = array_merge($courses,$bundles);
    return $subscriptions;
}

function course_lecture_activity_save($save = array())
{
    $scope                          =& get_instance();

    $data                           = array();
    $data['cla_controller']         = $scope->router->fetch_class();
    $data['cla_method']             = $scope->router->fetch_method();
    $data['cla_post']               = json_encode($scope->input->post());
    $data['cla_get']                = json_encode($scope->input->get());
    $data['cla_model_data']         = json_encode($save);
    $data['cla_application_time']   = date('Y-m-d h:i:s');
    $scope->db->insert('course_lecture_activity', $data);
}
?>
