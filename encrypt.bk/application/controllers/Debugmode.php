<?php
class Debugmode extends CI_Controller
{
    public function __construct()
    {
        header("Access-Control-Allow-Origin: http://accounts.enfinlabs.dev");
        $this->CI = &get_instance();
        parent::__construct();
        $this->__loggedInUser['id'] = '1';
        $this->load->model('Course_model');
    }

    /*function get_course_image()
    {
        $course_ids     = $this->Course_model->course_image_old();
        // $image_path = 'https://d38v42jd03kwqt.cloudfront.net/uploads/SGlearningapp.com/course/81/course/81.jpg';
        $image_path = 'https://d38v42jd03kwqt.cloudfront.net/uploads/SGlearningapp.com/course/';
        foreach($course_ids as $course)
        {
            $course_id = $course['id'];
            if(!is_dir($_SERVER['DOCUMENT_ROOT'].'/uploads/course/'.$course_id.'/course/'))
            {
                mkdir($_SERVER['DOCUMENT_ROOT'].'/uploads/course/'.$course_id.'/course/', 0777, true);
            }
            // copy($image_path.$course_id.'/course/'.$course_id.'.jpg', $_SERVER['DOCUMENT_ROOT'].'/uploads/course/'.$course_id.'/course/'.$course_id.'.jpg');
            // copy($image_path.$course_id.'/course/'.$course_id.'_300x160.jpg', $_SERVER['DOCUMENT_ROOT'].'/uploads/course/'.$course_id.'/course/'.$course_id.'_300x160.jpg');
            // copy($image_path.$course_id.'/course/'.$course_id.'_85x85.jpg', $_SERVER['DOCUMENT_ROOT'].'/uploads/course/'.$course_id.'/course/'.$course_id.'_85x85.jpg');
            if(file_exists($_SERVER['DOCUMENT_ROOT'].'/uploads/course/'.$course_id.'/course/'.$course_id.'.jpg'))
            {
                $files = array(".", "_300x160.", "_85x85.");                
                foreach($files as $file_resolution)
                {
                    $file_origin    = $_SERVER['DOCUMENT_ROOT'].'/uploads/course/'.$course_id.'/course/'.$course_id;
                    $file           = $file_origin.$file_resolution."jpg";
                    // $image=  imagecreatefromjpeg($file);
                    // ob_start();
                    // imagejpeg($image,NULL,100);
                    // $cont=  ob_get_contents();
                    // ob_end_clean();
                    // imagedestroy($image);
                    // $content =  imagecreatefromstring($cont);
                    // imagewebp($content, $file_origin.$file_resolution.'webp');
                    // imagedestroy($content);
                    // unlink($file);
                }
                echo "completed image to webp ".$course_id."<br />";
            }
        }
    }*/

    function imgtowebp(){
        //$file_origin    = $_SERVER['DOCUMENT_ROOT'].'/uploads/course/'.$course_id.'/course/'.$course_id;
        //$file           = $file_origin.$file_resolution."jpg";
        $file    = $_SERVER['DOCUMENT_ROOT'].'/uploads/course/img1.jpg';
        $image=  imagecreatefromjpeg($file);
        ob_start();
        imagejpeg($image,NULL,100);
        $cont=  ob_get_contents();
        ob_end_clean();
        imagedestroy($image);
        $content =  imagecreatefromstring($cont);
        imagewebp($content, $_SERVER['DOCUMENT_ROOT'].'/uploads/course/img1.webp');
        imagedestroy($content);
        //unlink($file);
    }

    function timenow(){
        echo date('Y-m-d H:i:s');
    }

  

    function info_php()
    {
        phpinfo();
    }
    // function updatedb()
    // {
    //     $i = 1;
    //     $payment_items = array();
    //     $order_history = $this->db->query("SELECT * FROM payment_history WHERE ph_item_type = '2' AND ph_item_amount_received = '0' AND ph_item_other_details IS NOT NULL")->result_array();
    //     foreach($order_history as $order)
    //     {
    //         $order['ph_item_other_details'] = json_decode($order['ph_item_other_details'], true);
    //         if(is_string($order['ph_item_other_details']['c_courses']))
    //         {
    //             $order['ph_item_other_details']['c_courses'] = json_decode($order['ph_item_other_details']['c_courses'], true); 
    //         }
    //         // $payment_items[] = $payment;

    /*function update_bundle()
    {
        $query = 'SELECT bs_bundle_id, bs_user_id FROM `bundle_subscription` WHERE bs_bundle_details IS NULL';
        $demo_result = $this->db->query($query)->result_array();
        if(!empty($demo_result))
        {
            foreach($demo_result as $result)
            {
                if($result['bs_bundle_id'] > 0  && $result['bs_user_id'] > 0)
                {
                    $bundle_query = 'SELECT cs_course_id FROM `course_subscription` WHERE cs_bundle_id = '.$result['bs_bundle_id'].' AND cs_user_id = '.$result['bs_user_id'];
                    echo $bundle_query.'<br />';
                    $bundle_result = $this->db->query($bundle_query)->result_array();
                    // echo '<pre>'; print_r($bundle_result);
                }
            }
        }

    }

    function updatedb()
    {
        $i = 1;
        $payment_items = array();
        $order_history = $this->db->query("SELECT * FROM payment_history WHERE ph_item_type = '2' AND ph_item_amount_received = '0' AND ph_item_other_details IS NOT NULL")->result_array();
        foreach($order_history as $order)
        {
            $order['ph_item_other_details'] = json_decode($order['ph_item_other_details'], true);
            if(is_string($order['ph_item_other_details']['c_courses']))
            {
                $order['ph_item_other_details']['c_courses'] = json_decode($order['ph_item_other_details']['c_courses'], true); 
            }
            // $payment_items[] = $payment;

            $course_ids = array();
            if(!empty($order['ph_item_other_details']['c_courses']))
            {
                foreach($order['ph_item_other_details']['c_courses'] as $course)
                {
                    $course_ids[] = $course['id'];
                }

                if($order['ph_item_id']>0 && $order['ph_user_id'] >0 && !empty($course_ids))
                {
                    $query = 'UPDATE course_subscription SET cs_bundle_id = '.$order['ph_item_id'].' WHERE cs_user_id = '.$order['ph_user_id'].' AND cs_course_id IN ('.implode(",", $course_ids).')';
                    // echo $i.'----'. $query.'<br />';
                    $demoquery = 'SELECT id, cs_course_id, cs_user_id, cs_bundle_id FROM  course_subscription WHERE cs_user_id = '.$order['ph_user_id'].' AND cs_course_id IN ('.implode(",", $course_ids).')';
                    $demo_result = $this->db->query($demoquery)->result_array();

                    if(!empty($demo_result))
                    {
                    // echo $i.'-----------------------------------------<pre>'; print_r($demo_result);echo '-----------------------------<br />';
                    // echo '<pre>';
                    // print_r($demo_result);
                    echo $query.';<br />';    
                    $i++;
                }
                }
            }
        }
        // echo '<pre>';
        // print_r($payment_items);
    }*/

    function drm()
    {
        $this->load->view($this->config->item('theme') . '/drm_view');
    }

    function databinary(){

        $assessment_attempts = $this->db->select('id,aa_assessment_detail')->get('assessment_attempts')->result();
        $i=1;
        foreach($assessment_attempts as $attempts)
        {
            $this->db->where('id', $attempts->id);
            $this->db->update('assessment_attempts', array('aa_assessment_detail' => base64_decode($attempts->aa_assessment_detail)));
            $i++;
        }

        echo $i .'records completed';
    }

   

    function curl_target()
    {
        echo 'target reached';
    }

    function server_data()
    {
        
        // echo '<pre>'; print_r($_SERVER);die;
        echo $this->config->item('site_logo');
    }
    function resetAccountMemcache(){
        $this->load->library('ofacrypt');
        $account_id = $this->ofacrypt->decrypt($this->input->post('data'), '4tra3j');
        $setting_website = 'setting_website'.$this->account_to_alpha($account_id);
        $this->memcache->resetAccountMemcache($setting_website);
        $setting_website = $account_id.'_web_configs';
        $this->memcache->resetAccountMemcache($setting_website);
    }

    // function table_migration()
    // {
    //     $query = "SELECT id,cb_title,cb_validity,cb_validity_date FROM course_basics WHERE cb_validity_date = '2100-01-01' ";
    //     $result = $this->db->query($query)->result_array();
    //     if(!empty($result))
    //     {
    //         foreach($result as $item)
    //         {
    //             $sql = "UPDATE course_basics SET cb_validity_date='0000-00-00' WHERE id=".$item['id'];
    //             $this->db->query($sql);
    //         }
    //     }
    //     echo '<pre>';print_r($result);die();

    // }
    
    function check_code($id = 0)
    {
        
        $str = 'gw9%R=X<bb';
        $str_en = base64_encode($str);
        $str_dc = base64_decode($str_en);
        $this->Course_model->check_course_delete($id);
    }

    function learn()
    {
        $pc_user_detail = array();
        $pc_user_detail[1] = array( "id" => "1",
                                    "name" => "test",
                                    "email" => "test@gmail.com"
                                );
        $pc_user_detail[2] = array( "id" => "1",
                                "name" => "sdfgsdfgsdfg",
                                "email" => "sdfsdfgsdfgsdfgtest@gmail.com"
                            );
        $user = array(
                        "id" => 3, 
                        "name" => "test",
                        "email" => "test@gmail.com"
        );
        $pc_user_detail[$user['id']] = $user;
        echo '<pre>';
        print_r($pc_user_detail);
    }

    function outer_ajax()
    {
        $user               = $this->auth->get_current_user_session('user');
        $response           = array();
        $response['id']     = $user['id'];
        $this->load->library('JWT');
        $key                = 'yHFNF84ywxMvGBy';
        $payload            = $user['id'].'-'.$user['us_email'].'-'.$user['us_register_number'];
        $response['token']  = $this->jwt->encode($payload, $key);
        echo json_encode($response);
    }
    
    function clear(){
        $myfile = fopen("uploads/upload.txt", "w");
        $txt = 'test';
        fwrite($myfile, $txt);
        fclose($myfile);
    }

    function check()
    {
        //checkinf valid branch
        $request['row']             = 2;
        $request['institute_id']    = 18;
        $request['field_name']      = 'batch[1][2]';
        $request['field_value']     = 'Group CSsE';
        $response = $this->is_valid_batch_name($request);
        echo '<pre>';
        print_r($response);
        die;


        //checkinf valid branch
        $request['row']             = 2;
        $request['field_name']      = 'branch[1][2]';
        $request['field_value']     = 'CaE';
        $response = $this->is_valid_branch_code($request);
        echo '<pre>';
        print_r($response);
    }

    function preview($key='')
    {
        if(!$key)
        {
            //redirect(admin_url('users'));
        }
        $users_content = $this->memcache->get(array('key' => $key));
        if(empty($institutes_content['content']))
        {
            //redirect(admin_url('users'));
        }
        $data               = array();
        $affected_rows      = array();
        $data['excell']     = $users_content;
        $data['action']     = admin_url('users/preview').$key;
        $data['headers']    = array();
        $this->load->library('form_validation');
        if ($this->input->server('REQUEST_METHOD') != 'POST')
        {
            $this->load->view($this->config->item('admin_folder').'/import_preview_users', $data);
        }
    }

    function is_valid_branch_code( $request = array() )
    {
        $request['field_label']     = 'Choose Branch';
        $request['dropdown_for']    = 'all_branches_selector';
        //setting branch html and branch code array
        $this->process_branch_objects();
        //end

        $response           = array();
        $response['valid']  = true;

        $branch_name = trim($request['field_value']);
        if(!$branch_name)
        {
            $request['field_value'] = '--branch_not_assinged--';
            $response['valid']      = false;
            //$request['values']      = $this->branch_html;
            $response['content']    = $this->render_option_html($request);
        }
        else
        {
            if(!array_key_exists($branch_name, $this->branch_codes))
            {
                $response['valid']      = false;
                //$request['values']      = $this->branch_html;
                $response['content']    = $this->render_option_html($request);    
            }
            else
            {
                $response['valid']      = true;    
                $branch_object          = $this->branch_codes[$branch_name];
                $response['data']       = array(
                                                'branch_id' => $branch_object['id'],
                                                'branch_code' => $branch_object['branch_code']
                );    
            }
        }
        return $response;
    }

    function is_valid_batch_name( $request = array() )
    {
        $request['field_label']     = 'Choose Batch';
        $request['dropdown_for']    = 'all_batches_selector';
        if(!isset($this->batches))
        {
            $objects              = array();
            $objects['key']       = 'insbtch'.$this->__loggedInUser['id'];
            $callback             = 'institute_batches';
            $batches              = $this->memcache->get($objects, $callback, array('institute_id' => $request['institute_id'], 'select' => 'id, gp_name')); 

            //$this->batch_html  = '';
            if(!empty($batches))
            {
                $this->batches = array();
                foreach($batches as $b_obj)
                {
                    //$this->batch_html                .= '<option value ="'.$b_obj['gp_name'].'">'.$b_obj['gp_name'].'</option>';
                    $this->batches[$b_obj['gp_name']] = $b_obj;
                }
            }
        }

        $batch_name = trim($request['field_value']);
        if(!$batch_name)
        {
            $request['field_value'] = '--batch_not_assinged--';
            $response['valid']      = false;
            //$request['values']      = $this->batch_html;
            $response['content']    = $this->render_option_html($request);
        }
        else
        {
            if(!array_key_exists($batch_name, $this->batches))
            {
                $response['valid']      = false;
                //$request['values']      = $this->batch_html;
                $response['content']    = $this->render_option_html($request);    
            }
            else
            {
                $response['valid']      = true;    
                $batch_object           = $this->batches[$batch_name];
                $response['data']       = array(
                                                'batch_id' => $batch_object['id'],
                                                'batch_name' => $batch_object['gp_name']
                );    
            }
        }
        return $response;
    }

    function render_option_html( $request = array() )
    {
        $field_name     = str_replace(" ","_",$request['field_value']);
        $field_name     = str_replace(".","_",$field_name);
        $option_html    = '<select class="'.$request['dropdown_for'].' '.$field_name.'" name="'.$request['field_name'].'">';
        $option_html    .=  '<option value="">'.$request['field_label'].'</option>';
        //$option_html    .=  $request['values'];
        $option_html    .= '</select>';
        return $option_html;
    }

    function process_branch_objects()
    {
        if(!isset($this->branches))
        {
            $this->branches       = array();
            $objects              = array();
            $objects['key']       = 'branches';
            $callback             = 'branches';
            $this->branches       = $this->memcache->get($objects, $callback); 
        }
        if(!isset($this->branch_codes))
        {
            //$this->branch_html = '';
            $this->branch_codes = array();
            if(!empty($this->branches))
            {
                foreach($this->branches as $branch)
                {
                    $this->branch_codes[$branch['branch_code']] = $branch; 
                    //$this->branch_html   .= '<option value ="'.$branch['branch_code'].'">'.$branch['branch_code'].'-'.$branch['branch_name'].'</option>';
                }
            }
        }
    }

    public function set_cache($key = 'sample', $preview_contents = 'Sample content')
    {
        $response = $this->memcache->set($key, $preview_contents);
        echo '---<pre>'; print_r($response);
    }



    public function get_cache($key = 'homepage', $callback = false)
    {
        $objects        = array();
        $objects['key'] = $key;
        $content        = $this->memcache->get($objects,$callback,array());
        echo '<pre>';
        print_r($content);
        if (!$content) {
            echo 'No content avalable using index <b>' . $key . '</b>';die;
        }

        // $objects            = array();
        // $objects['key']     = 'all_bundles';
        // $callback           = $key;

        // $courses = $this->memcache->get($objects, $callback,array());
       
    }

    public function delete_cache($key = 'homepage')
    {
        $this->memcache->delete($key);
        echo 'memcache  index <b>' . $key . '</b> deleted';die;
    }

    function test()
    {
        /*//Format to check the privilage for role in particular module
        $param              = array();
        $param['module']    = '<module_name>';
        $param['role_id']   = '<role_id>';
        $permission = $this->accesspermission->get_permission($param);


        //Format to check the privilage for role in course related module
        $param              = array();
        $param['module']    = '<current_module_name>';
        $param['role_id']   = '<role_id>';
        $param['user_id']   = '<user_id>';
        $param['course_id'] = '<course_id>';
        //if role has full course access call method get_permission()
        $permission = $this->accesspermission->get_permission($param);
        //if role does not have full course access call method get_permission_course()
        $permission = $this->accesspermission->get_permission_course($param);*/

        $param              = array();
        $param['module']    = 'course';
        $param['role_id']   = '3';
        $param['user_id']   = '151';
        $param['course_id'] = '4';
        $permission = $this->accesspermission->get_permission_course($param);
        echo '<pre>'; print_r($permission);

    }

    function mail()
    {
        $template           = $this->ofabeemailer->template(array('email_code' => 'registration_mail'));
        $param['to'] 	    = array('email2thanveer@gmail.com');
        $param['subject'] 	= $template['em_subject'];
        $contents           = array(
                                    'user_name' => 'Thanveer Ahmed'
                                   ,'site_name' => config_item('site_name')
                              );
        $param['body']      = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
        $test = $this->ofabeemailer->send_mail($param);
        echo '<pre>'; print_r($test);die('--');


        // $template           = $this->ofabeemailer->template(array('email_code' => '<email_code>'));
        // $param['to'] 	    = array('<email_id_1>', '<email_id_2>', '<email_id_n>');
        // $param['subject'] 	= $template['em_subject'];
        // $contents           = array(
        //                              'word_to_replace_1' => 'word_to_replace_with_1'
        //                             ,'word_to_replace_2' => 'word_to_replace_with_2'
        //                             ,'word_to_replace_n' => 'word_to_replace_with_n'
        // );
        // $param['body']      = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
        // $this->ofabeemailer->send_mail($param);


        $param['to'] 	    = array('santhoshkumar@enfintechnologies.com');
        $param['subject'] 	= 'sample mail santhosh';
        $contents           = array(
                                     'word_to_replace_1' => 'word_to_replace_with_1'
                                    ,'word_to_replace_2' => 'word_to_replace_with_2'
                                    ,'word_to_replace_n' => 'word_to_replace_with_n'
        );
        $param['body']      = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
        $send = $this->ofabeemailer->send_mail($param);
        echo '<pre>'; print_r($send);die; 

    }

    function test_copy(){

        copy($_SERVER['DOCUMENT_ROOT'].'/uploads/global_files', $_SERVER['DOCUMENT_ROOT'].'/uploads/testcopy');

    }


    function copyfiles(){

        $src = $_SERVER['DOCUMENT_ROOT'].'/uploads/global_files';
        $dst = $_SERVER['DOCUMENT_ROOT'].'/uploads/newfiles1';
        $this->custom_copy($src, $dst);
    }
    
    function custom_copy($src, $dst) {

        // open the source directory 
        $dir = opendir($src);  
      
        // Make the destination directory if not exist 
        if (!is_dir($dst)){
            @mkdir($dst, 0755, true);  
        }
      
        // Loop through the files in source directory 
        while( $file = readdir($dir) ) {  
      
            if (( $file != '.' ) && ( $file != '..' )) {  
                if ( is_dir($src . '/' . $file) )  
                {  
      
                    // Recursively calling custom copy function 
                    // for sub directory  
                    $this->custom_copy($src . '/' . $file, $dst . '/' . $file);  
      
                }  
                else {  
                    copy($src . '/' . $file, $dst . '/' . $file);  
                }  
            }  
        }  
      
        closedir($dir); 
    } 

    function log()
    {

        log_activity(153, 'create', 'Thanveer AHmed has created an account in SGlearningapp', array('user_name' => 'Thanveer Ahmed'));
        //log_activity(0, 'delete', 'Admin created a new course ');
        /*log_activity('<user_id>', '<action>', '<action_text>', '<additional_params>');
        user_id => type is integer. It is Optional. If user triggers any activity, then user id along with user name is passed as aditional params. 
        actions => type is string. It is mandatory. This is a unique code from the table web_actions. Refer column 'wa_code'. You can create your own code
        actions_text => type is string. It is mandatory. Any text.
        additional_params => type is array. It is Optional. If user_id is passed then send user_name as a additional param. 
        //sample function call with and without user_id
        log_activity(154, 'create', 'Ankit verma has created an account in SGlearningapp', array('user_name' => 'Ankit Varma'));
        log_activity(0, 'delete', 'Admin created a new course named Java - An Introduction ');*/
    }

    function test_mail()
    {
        /*$bcc = array(
            'email2thanveer+2@gmail.com', 
            'email2thanveer+3@gmail.com', 
            'email2thanveer+4@gmail.com', 
            'email2thanveer+5@gmail.com', 
            'email2thanveer+6@gmail.com', 
            'email2thanveer+7@gmail.com', 
            'email2thanveer+8@gmail.com',
            'email2thanveer+9@gmail.com',
            'email2thanveer+10@gmail.com',
            'email2thanveer+11@gmail.com', 
            'email2thanveer+12@gmail.com', 
            'email2thanveer+13@gmail.com', 
            'email2thanveer+14@gmail.com', 
            'email2thanveer+15@gmail.com', 
            'email2thanveer+16@gmail.com', 
            'email2thanveer+17@gmail.com', 
            'email2thanveer+18@gmail.com',
            'email2thanveer+19@gmail.com',
            'email2thanveer+20@gmail.com',
            'emailtothanveer@gmail.com', 
            'emailtothanveer+2@gmail.com', 
            'emailtothanveer+3@gmail.com', 
            'emailtothanveer+4@gmail.com', 
            'emailtothanveer+5@gmail.com', 
            'emailtothanveer+6@gmail.com', 
            'emailtothanveer+7@gmail.com', 
            'emailtothanveer+8@gmail.com',
            'emailtothanveer+9@gmail.com',
            'emailtothanveer+10@gmail.com',
            'emailtothanveer+11@gmail.com', 
            'emailtothanveer+12@gmail.com', 
            'emailtothanveer+13@gmail.com', 
            'emailtothanveer+14@gmail.com', 
            'emailtothanveer+15@gmail.com', 
            'emailtothanveer+16@gmail.com', 
            'emailtothanveer+17@gmail.com', 
            'emailtothanveer+18@gmail.com',
            'emailtothanveer+19@gmail.com',
            'emailtothanveer+20@gmail.com',
            'thanveer.a+1@enfintechnologies.com', 
            'thanveer.a+2@enfintechnologies.com', 
    );*/

        $bcc                = array(
                                    'thanveer.a@enfintechnologies.com',                    
                                    'rahul.s@enfintechnologies.com',                    
                                    'santhoshkumar@enfintechnologies.com',                    
                                    'email2thanveer@gmail.com',                    
                                    'kiran.jb@enfintechnologies.com',                    
                                    'gauri@enfintechnologies.com',                    
                                    'vishnu.sr@enfintechnologies.com',                    
                                    'emailtothanveer@gmail.com',                    
                                    'hariharan.b@enfintechnologies.com',                    
                                    'alex@enfintechnologies.com',                    
                                    'edwin@enfintechnologies.com',                    
                                    'email2alx@gmail.com',                    
                                );
        $bcc = array();
        for($i=0;$i<=200;$i++)
        {
            $bcc[] = 'email2thanveer+'.$i.'@gmail.com';
        }

        //$bcc                = array();
        $param              = array();
        $param['to'] 	    = array('thanveer.a@enfintechnologies.com');
        $param['bcc'] 	    = $bcc;
        $param['subject'] 	= 'Updated Message From SGlearningapp ';
        $param['body']      = 'This is updated Message SGlearningapp';
        $param['force_recipient'] = true;
        $this->ofabeemailer->send_mail($param);
    }

    function sample()
    {
        ?>
        <!DOCTYPE html>
<html>
<head>
    <title>Redactor</title>
    <meta charset="utf-8">

    <!--css -->
    <link rel="stylesheet" href="<?php echo assets_url() ?>/css/redactor/css/redactor.css" />
</head>
<body>
    <textarea id="content"></textarea>

    <!-- js -->
    <script src="<?php echo theme_url() ?>/js/jquery.min.js"></script>
    <script src="<?php echo assets_url() ?>/js/redactor/js/redactor.js"></script>
    <script src="<?php echo assets_url() ?>/js/redactor/js/table.js"></script>
    <script src="<?php echo assets_url() ?>/js/redactor/js/alignment.js"></script>
    
    <!-- call -->
    <script>
        var admin_url = '<?php echo admin_url() ?>';
        $(document).ready(function(){
            $R('#content', 
                        { 
                            plugins: ['table', 'alignment'] ,
                            imageUpload : admin_url+'configuration/redactore_image_upload'
                        }
                );
        })
    </script>
</body>
</html>
        <?php
    }


    function ldap()
    {
        $options = array();
        $options['base_dn'] = 'dc=ofabee,dc=com';
        $options['domain_controllers'] = array('ldap.ofabee.com');
        $options['admin_username'] = 'ldapuser1';
        $options['admin_password'] = 'redhat';
        $options['ad_port'] = 389;
        $this->load->library('Ldap', $options);
        $username = 'ldapuser1';
        $password = 'redhat';
        $authUser = $this->ldap->authenticate($username, $password);
        if ($authUser == true) {
        echo "User authenticated successfully";
        }
        else {
        echo "User authentication unsuccessful";
        }
    }

    function process_role()
    {
        $this->load->database();
        $this->db->query("TRUNCATE TABLE roles_modules_meta;");
        $roles = $this->db->query('SELECT id FROM roles WHERE id != 2')->result_array();
        $modules = $this->db->query('SELECT id, module_permissions FROM modules')->result_array();
        // echo '<pre>'; print_r($modules);die;

        foreach($roles as $role)
        {
            // $this->db->trans_start();
            foreach($modules as $module)
            {
                $query = "INSERT INTO roles_modules_meta (role_id, module_id, permissions) VALUES ('".$role['id']."', '".$module['id']."', '".$module['module_permissions']."');";
                echo $query.'<br />';
                // $this->db->query($query);
            }
            // $this->db->trans_complete(); 
        }
    }

    function qrcode($code = '')
    {
        $code = urldecode($code);
        $this->load->library('qrlib');
        // $code = 'testsdpk.ofabee.com/course-123';
        $qr_code = $this->qrlib->qrcode(array('input' => $code));
        if(isset($qr_code['data']['file_name']))
        {
            echo '<img src="'.qrcode_path().$qr_code['data']['file_name'].'">';
        }
        //echo '<pre>'; print_r($qr_code);
    }

    function tests(){
        echo '<pre>';print_r($_SERVER);
    }

    function sub()
    {
        $param = array();
        $param['course_id'] = 9;
        $param['lecture_id'] = 18;
        $param['user_id'] = 339;
        $param['grade'] = 'E';
        update_lecture_log_wiht_subscription($param);
    }
    
    function check_id()
    {
        for($i=1; $i<=100;$i++)
        {
            echo $i.'--'.$this->account_to_alpha($i).'<br />';
        }
    }

    function account_to_alpha($number)
    {
        $alpha = '';
        $alphabet = range('a','z');
        $count = count($alphabet);
        if($number <= $count)
        {
            $alpha = $alphabet[$number-1];
        }
        else
        {
            while($number > 0)
            {
                $modulo     = ($number - 1) % $count;
                $alpha      = $alphabet[$modulo].$alpha;
                $number     = floor((($number - $modulo) / $count));
            }    
        }
        return $alpha;
    }

    function dummy_config()
    {
        $user_data  = array();
        $user_data['user_id'] = '1';
        $user_data['register_number'] = '';
        $user_data['username'] = 'mike thomas';
        $user_data['useremail'] = '';
        $user_data['user_type'] = '1';

        $message_template = array();
        $message_template['student_name'] = 'bumrah';
        $message_template['course_name']  = 'Dot net';
        $triggered_activity = 'user_login';

        log_activity($triggered_activity, $user_data, $message_template);

    }
    
    function notification()
    {
        $this->load->library('Notifier');
        $response = $this->notifier->push(
            array(
                'action_code' => 'course_subscribed',
                'assets' => array('course_name' => 'Engineering Graphics.'),
                'target' => 2,
                'individual' => true,
                'push_to' => array(8,9)
            )
        );
        // $response = $this->notifier->push(
        //     array(
        //         'action_code' => 'student_registered',
        //         'assets' => array('student_name' => 'Thanveer Ahmed'),
        //         'individual' => false,
        //         'push_to' => array(
        //             6 => array(20),
        //             7 => array(20,21)
        //         )
        //     )
        // );
        $response = $this->notifier->push(
            array(
                'action_code' => 'student_registered',
                'assets' => array('student_name' => 'Thanveer Ahmed'),
                'individual' => false,
                'push_to' => array(
                    1 => array(20)
                    )
            )
        );
        $response = $this->notifier->push(
            array(
                'action_code' => 'student_account_deactivated',
                'individual' => true,
                'push_to' => array(8,9,10,11,12)
            )
        );

        $response = $this->notifier->push(
            array(
                'action_code' => 'student_account_created',
                'individual' => true,
                'push_to' => array(8,9,10,11,12)
            )
        );

        $response = $this->notifier->push(
            array(
                'action_code' => 'student_account_created',
                'individual' => false,
                'push_to' => array(
                    1 => array(8,9,10,11,12),
                    2 => array(8,9,10),
                    3 => array(11,12)
                )
            )
        );

        // $response = $this->notifier->fetch(
        //     array(
        //         'user_id' => 1
        //     )
        // );
        echo '<pre>';print_r($response);

        // $user_ids = $this->accesspermission->previleged_users(array('module' => 'user'));
        // echo '<pre>';print_r($user_ids);

        // $session              = $this->auth->get_current_user_session('user');
        // echo '<pre>'; print_r($session);die;

        // $this->load->model('Action_model');

        // $user_datas = $this->Action_model->get_existing();
        // $user_ids = array();
        // foreach($user_datas as $user_data)
        // {
        //     $user_ids[] = $user_data['um_user_id'];
        // }

        // $all_users = $this->Action_model->get_all_users();

        // $user_messages = array();

        // foreach($all_users as $all_user)
        // {
        //     $user_message = array();
        //     if(!in_array($all_user['id'],$user_ids))
        //     {
        //         $user_message['um_user_id'] = $all_user['id'];
        //         $user_message['um_messages'] = '{}';

        //         $user_messages[] = $user_message;
        //     }
        // }

        // // $this->Action_model->insert_batch($user_messages);

        // echo '<pre>';print_r($user_messages);
    }

    public function override(){
        $this->load->model('Course_model');
        $course_id                  = 6;
        $override_data              = $this->Course_model->lecute_override(array('course_id' => $course_id,'source'=>'course'));
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

        $this->__student    = $this->auth->get_current_user_session('user');
        $batches            = explode(',',$this->__student['us_groups']);

        $user_id            = $this->__student['id'];

        $course_objects                    = array();
        $course_objects['key']             = 'course_'.$course_id;
        $course_callback                   = 'course_details';
        $course_params                     = array();
        $course_params['id']               = $course_id;
        $course_details                    = $this->memcache->get($course_objects, $course_callback, $course_params);

        foreach($course_details['lectures'] as $l_key => $lecture)
        {
            if(isset($lecture_override[$lecture['id']]))
            {
                foreach($batches as $batch)
                {
                    if(is_numeric($batch) && isset($lecture_override[$lecture['id']][$batch]))
                    {
                        $lecture['cl_limited_access']           = $lecture_override[$lecture['id']][$batch]['lo_attempts'];
                        $course_details['lectures'][$l_key]     = $lecture;
                    }
                }
            }
        }

        echo '<pre>';print_r($batches);
    }


    function get_current_user_session()
    {die('jhgfd');
        return array(
                    'id' => '59',
                    'us_name' => 'Bincy Mary Baby',
                    'us_email' => 'vini+bincy@enfintechnologies.com',
                    'us_register_number' => 'Bincy',
                    'us_image' => '59.jpg?v=4',
                    'us_about' => '',
                    'us_phone' => '9876543210',
                    'us_phone_verfified' => '1',
                    'us_email_verified' => '1',
                    'us_role_id' => '2',
                    'us_category_id' => '',
                    'us_account_id' => '1',
                    'us_institute_id' => '1',
                    'us_branch' => '1',
                    'us_branch_code' => '0',
                    'us_institute_code' => 'NI',
                    'us_register_no' => '',
                    'us_invited' => '0',
                    'us_groups' => '0,4,5',
                    'us_reset_password' => '0',
                    'action_id' => '1',
                    'action_by' => '1',
                    'us_status' => '1',
                    'us_deleted' => '0',
                    'us_degree' => '',
                    'us_experiance' => '0',
                    'us_native' => '',
                    'us_language_speaks' => '',
                    'us_expertise' => '',
                    'us_youtube_url' => '',
                    'us_badge' => '0',
                    'us_course_first_view' => '1',
                    'us_messages' => '',
                    'us_profile_fields' => '',
                    'us_profile_completed' => '1',
                    'us_email_exist' => '0',
                    'created_date' => '2019-03-19 13:13:26',
                    'updated_date' => '2019-03-19 17:44:38',
                    'rl_name' => 'Student',
                    'rl_status' => '1',
                    'rl_type' => '2',
                    'rl_deleted' => '0',
                    'rl_account' => '0',
                    'role_id' => '2',
                    'rl_full_course' => '1',
                    'rl_content_types' => '{"1":"video","2":"document","3":"quiz","4":"youtube","5":"text","6":"wikipedia","7":"live","8":"descriptive_test","9":"recorded_videos","10":"scorm","11":"cisco_recorded_videos", "12":"audio", "13":"survey", "14":"certificate"}',
                    'institute_name' => 'SGlearningapp',
                    'institute_code' => 'NI',
                    'branch_name' => 'Aeronautical Engineering',
                    'notification' => array
                        (
                            'count' => '0'
                        )
                );
        // echo '<pre>'; print_r($this->auth->get_current_user_session('user'));die;
    }
    function save_answer()
    {
        die('lkjhgfd');
        // $this->load->model('Course_model');
        $aa_mark_scored                      = 0;
        $total_duration                      = 0;
        $assets                              = array();
        $assesment_id                        = 8;//$this->input->post('assesment_id');
        $attempt_id                          = 43;//$this->input->post('attempt_id');
        $answers                             = '{"1593":{"question_id":"1593","duration":0},"1594":{"question_id":"1594","answer":"6526","duration":1},"1595":{"question_id":"1595","answer":{"6534":"6534"},"duration":1},"1596":{"question_id":"1596","answer":"sample cometnt checking sample cometnt checking sample cometnt checking ","duration":3},"1597":{"question_id":"1597","answer":"\n\t\t\t","duration":2}}';//$this->input->post('answer_queue');
        $this->_answers                      = json_decode($answers);

        $assesment_objects                   = array();
        $assesment_objects['key']            = 'assesment_'.$assesment_id;
        $assesment_callback                  = 'assesment_details';
        $assesment_params                    = array();
        $assesment_params['assesment_id']    = $assesment_id;
        $assesment_details                   = $this->memcache->get($assesment_objects, $assesment_callback, $assesment_params);
        
        $questions                           = $assesment_details['questions'];
        $assesment                           = $assesment_details['assesment_details'];
        
        $assets['error']                     = false;
        $user                                = $this->get_current_user_session();

        $save_attempt                        = array();
        $save_attempt['id']                  = $attempt_id;
        
        //getting attempt details
        $objects        = array();
        $objects['key'] = 'attempt_'.$attempt_id;
        $attempt        = $this->memcache->get($objects);
        if(!$attempt) 
        {
            $attempt            = $this->Course_model->attempt(array('select'=>'aa_duration,aa_assessment_detail','id'=>$attempt_id));
            $this->memcache->set($objects['key'], $attempt);
        } 
        //End

        $assessment_json                     = ($attempt['aa_assessment_detail']!=NULL)?json_decode($attempt['aa_assessment_detail']):array();
        if(empty($assessment_json)) {
            $exam_submission_data                  = array();
            $exam_submission_data['assesment_id']  = $assesment_id;
            $exam_submission_data['attempt_id']    = $attempt_id;
            $exam_submission_data['user_id']       = $user['id'];
            $exam_submission_data['course_id']     = $assesment['a_course_id'];
            $exam_submission_data['lecture_id']    = $assesment['a_lecture_id'];
            if(!empty($questions))
            {
                foreach($questions as $question)
                {
                    $question_topic                                                         = array();
                    $question_topic['id']                                                   = $question['q_topic'];
                    $question_topic['topic_name']                                           = $question['qt_topic_name'];
                    $question_subject                                                       = array();
                    $question_subject['id']                                                 = $question['q_subject'];
                    $question_subject['subject_name']                                       = $question['qs_subject_name'];
                    $question_options                                                       = $question['options'];
                    $exam_submission_data['questions'][$question['id']]                     = array();
                    $exam_submission_data['questions'][$question['id']]['report_id']        = '';
                    $exam_submission_data['questions'][$question['id']]['type']             = $question['q_type'];
                    $exam_submission_data['questions'][$question['id']]['q_question']       = $question['q_question'];
                    $exam_submission_data['questions'][$question['id']]['q_option']         = json_encode($question['options']);
                    $exam_submission_data['questions'][$question['id']]['q_actual_answer']  = isset($question['correct_answer'])?$question['correct_answer']:array();
                    $exam_submission_data['questions'][$question['id']]['q_negative_mark']  = $question['aq_negative_mark'];
                    $exam_submission_data['questions'][$question['id']]['q_positive_mark']  = $question['aq_positive_mark'];
                    $exam_submission_data['questions'][$question['id']]['q_explanation']    = $question['q_explanation'];
                    $exam_submission_data['questions'][$question['id']]['subject']          = json_encode($question_subject);
                    $exam_submission_data['questions'][$question['id']]['topics']           = json_encode($question_topic);
                    $exam_submission_data['questions'][$question['id']]['answer_time_log']  = '';
                    $exam_submission_data['questions'][$question['id']]['user_answers']     = '';
                    $exam_submission_data['questions'][$question['id']]['user_mark']        = '';
                }
            
            }
        } else {
            $exam_submission_data           = json_decode(json_encode($assessment_json), True);
        }
        //$report_ids                      = array();
        foreach($this->_answers as $key => &$answer)
        {
            $save                            = array();
            $question_id                     = $answer->question_id;
            $duration                        = $answer->duration;
            $question                        = $questions[$question_id];
            $report_id                       = ($exam_submission_data['questions'][$question_id]['report_id']!='')?$exam_submission_data['questions'][$question_id]['report_id']:0;
            
            if($report_id!=0)
            {
                $attempt_duration            = ($exam_submission_data['questions'][$question_id]['answer_time_log']!='')?$exam_submission_data['questions'][$question_id]['answer_time_log']:0;
                $save['id']                  = $report_id;
                $save['ar_duration']         = $attempt_duration+$duration;        
            }
            else
            {
                $save['id']                  = false;
                $save['ar_duration']         = $duration;             
            }
            //$report_ids[]                    = $report_id;
            
            $save['ar_attempt_id']           = $attempt_id;
            $save['ar_question_id']          = $question_id;
            $save['ar_course_id']            = $assesment['a_course_id'];
            $save['ar_lecture_id']           = $assesment['a_lecture_id'];
            $save['ar_user_id']              = $user['id'];
            $marked_right                    = false;

           
            switch ($question['q_type'])
            {
                case "1":
                    $answer                  = isset($answer->answer)?$answer->answer:'';
                    $save['ar_answer']       = $answer;
                    $correct_answer          = $question['q_answer'];
                    if($question['q_answer'] == $answer)
                    {
                        $marked_right        = true;
                        $save['ar_mark']     = isset($question['aq_positive_mark'])?$question['aq_positive_mark']:$question['q_positive_mark'];
                    }
                    else
                    {
                        $save['ar_mark'] = 0;
                        if($answer)
                        {
                            $save['ar_mark']  = isset($question['aq_negative_mark'])?$question['aq_negative_mark']:$question['q_negative_mark'];
                            $save['ar_mark']  = $save['ar_mark'];                                                       
                        }
                    }
                break;

                case "2":
                    // $key_answer               = explode(',', $question['q_answer']);
                    // $correct_answer           = array();
                    // if(!empty($key_answer))
                    // {
                    //     foreach ($key_answer as $t_key)
                    //     {
                    //         $correct_answer[$t_key] = $t_key;
                    //     }
                    // }
                    // $answer = (array)$answer->answer;
                    // sort($key_answer);
                    // sort($answer);
                    // $key_answer                 = implode(',', $key_answer);
                    // $answer                     = implode(',', $answer);
                    // $save['ar_answer']          = $answer;
                    // if($key_answer == $answer)
                    // {
                    //     $save['ar_mark']        = isset($question['aq_positive_mark'])?$question['aq_positive_mark']:$question['q_positive_mark'];
                    //     $marked_right           = true;
                    // }
                    // else
                    // {
                    //     $save['ar_mark'] = 0;
                    //     if($answer)
                    //     {
                    //     $save['ar_mark']         = isset($question['aq_negative_mark'])?$question['aq_negative_mark']:$question['q_negative_mark'];
                    //         $save['ar_mark']     = $save['ar_mark'];                                                       
                    //     }
                    // }

                    $save['ar_answer']           = '';
                    $save['ar_mark']             = '1';
                break;

                case "3":
                    $answer                      = isset($answer->answer)?$answer->answer:'';
                    $save['ar_answer']           = $answer;
                    $save['ar_mark']             = '';
                    $correct_answer              = '';
                break;

                case "4":
                    $answer                      = isset($answer->answer)?$answer->answer:'';
                    $save['ar_answer']           = $answer;
                    $save['ar_mark']             = '';
                    $correct_answer              = '';
                break;
                
            }
            
            $exam_submission_data['questions'][$question_id]['answer_time_log'] = $duration;
            $exam_submission_data['questions'][$question_id]['user_answers']    = $answer;
            $exam_submission_data['questions'][$question_id]['user_mark']       = $save['ar_mark'];
            $save_bulk[] = $save;
            //$save_report = $this->Course_model->save_assessment_report($save);
            //$exam_submission_data['questions'][$question_id]['report_id']       = $save_report;


            $total_duration                                += $duration;
            $answer_report                       = array();
            $answer_report['question_id']        = $question_id;
            $answer_report['ar_answer']          = $correct_answer;
            $answer_report['marked_right']       = $marked_right;
        }
        die('without database');
        $save_answers_bulk = $this->Course_model->save_assessment_report_bulk($save_bulk);
        //echo '<pre>';print_r($save_answers_bulk);die();
        foreach($save_answers_bulk as $save_answer)
        {
            $exam_submission_data['questions'][$save_answer['ar_question_id']]['report_id'] = $save_answer['id'];  
        }
        
        // foreach($exam_submission_data['questions'] as $report_object) {
        //     $aa_mark_scored += $report_object['user_mark'];
        // }
        
        $json_submission_data                    = json_encode($exam_submission_data);
        $save_attempt['aa_assessment_detail']    = $json_submission_data;
        $save_attempt['aa_duration']             = $attempt['aa_duration']+$total_duration;
        $save_attempt['aa_valuated']             = '0';
        $save_attempt['aa_mark_scored']          = $aa_mark_scored;
        $this->Course_model->save_assessment_attempts($save_attempt);

        $assets['error']                     = false;
        $assets['message']                   = 'Answer saved';
        $assets['attempt_id']                = $attempt_id;
        //$assets['rports']                    = $report_ids;
        if($assesment['a_que_report'] == '1')
        {
            $assets['report']                = $answer_report;
        }
        echo json_encode($assets);die;
    }


    function quiz_answers()
    {
        // die('lkjhgfd');
        // return '{"answer":{"1434":{"5891":"5891"},"1435":{"5896":"5896"},"1436":{"5901":"5901"},"1437":{"5907":"5907"},"1438":{"5911":"5911"},"1439":{"5916":"5916"},"1440":{"5922":"5922"},"1441":{"5927":"5927"},"1442":{"5933":"5933"},"1443":{"5937":"5937"},"1444":{"5941":"5941","5942":"5942"},"1445":{"5946":"5946"},"1446":{"5951":"5951"},"1447":{"5956":"5956"},"1448":{"5961":"5961"},"1449":{"5966":"5966"},"1450":{"5972":"5972"},"1451":{"5976":"5976"},"1452":{"5982":"5982"},"1453":{"5986":"5986"},"1454":{"5992":"5992"},"1455":{"5998":"5998"},"1456":{"6002":"6002"},"1457":{"6006":"6006"},"1458":{"6013":"6013"},"1459":{"6018":"6018"},"1460":{"6022":"6022"},"1461":{"6029":"6029"},"1462":{"6034":"6034"},"1463":{"6038":"6038"},"1464":{"6041":"6041"},"1465":{"6047":"6047"},"1466":{"6052":"6052"},"1467":{"6059":"6059"},"1468":{"6064":"6064"},"1469":{"6068":"6068"},"1470":{"6072":"6072"},"1471":{"5564":"5564"},"1472":{"6078":"6078"},"1473":{"6082":"6082"},"1474":{"6087":"6087"},"1475":{"6093":"6093"},"1476":{"6099":"6099"},"1477":{"6103":"6103"},"1478":{"6108":"6108","6110":"6110"},"1479":{"6115":"6115"},"1480":{"6118":"6118"},"1481":{"6125":"6125"},"1482":{"6130":"6130"},"1483":{"6135":"6135"},"1484":{"6140":"6140"},"1485":{"6144":"6144"},"1486":{"6150":"6150"},"1487":{"6153":"6153"},"1488":{"6158":"6158"},"1489":{"6162":"6162"},"1490":{"6167":"6167","6168":"6168","6170":"6170"},"1491":{"6172":"6172"},"1492":{"6178":"6178"},"1493":{"6183":"6183"},"1494":{"6188":"6188"},"1495":{"6192":"6192"},"1496":{"6198":"6198"},"1497":{"6203":"6203"},"1498":{"6209":"6209"},"1499":{"6213":"6213"},"1500":{"6217":"6217","6218":"6218","6220":"6220"},"1501":{"6224":"6224"},"1502":{"6229":"6229"},"1503":{"6233":"6233"},"1504":{"6238":"6238"},"1505":{"6243":"6243"},"1506":{"6248":"6248"},"1507":{"6253":"6253"},"1508":{"6257":"6257"},"1509":{"6263":"6263"},"1510":{"6268":"6268"},"1511":{"6272":"6272"},"1512":{"6277":"6277"},"1513":{"6285":"6285"},"1514":"6286","1515":{"6294":"6294"},"1516":"sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking ","1517":"\n\t\t\t","1518":"6297","1519":"6302","1520":{"6310":"6310"},"1521":"sample cometnt checking sample cometnt checking a fdasdfasdfas d","1522":"\n\t\t\t","1523":"6312","1524":"6316","1525":{"6323":"6323"},"1526":"sample cometnt checking sample cometnt checking sample cometnt checking ","1527":"\n\t\t\t","1528":"6328","1529":"6331","1530":{"6337":"6337"},"1531":"sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking ","1532":"\n\t\t\t","1533":"6343","1534":"6346","1535":{"6354":"6354"},"1536":"sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking ","1537":"\n\t\t\t","1538":"6357","1539":"6361","1540":{"6368":"6368"},"1541":"sample cometnt checking ","1542":"\n\t\t\t","1543":"6372","1544":"6376","1545":{"6383":"6383"},"1546":"sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking ","1547":"\n\t\t\t","1548":"6387","1549":"6391","1550":{"6398":"6398"},"1551":"sample cometnt checking sample cometnt checking sample cometnt checking ","1552":"\n\t\t\t","1553":"6402","1554":"6406","1555":{"6413":"6413"},"1556":"sample cometnt checking ","1557":"\n\t\t\t","1558":"6419","1559":"6422","1560":{"6428":"6428"},"1561":"sample cometnt checking sample cometnt checking sample cometnt checking ","1562":"\n\t\t\t","1563":"6433","1564":"6436","1565":{"6443":"6443"},"1566":"sample cometnt checking ","1567":"\n\t\t\t","1568":"6448","1569":"6451","1570":{"6459":"6459"},"1571":"sample cometnt checking sample cometnt checking sample cometnt checking ","1572":"\n\t\t\t","1573":"6464","1574":"6466","1575":{"6474":"6474"},"1576":"sample cometnt checking sample cometnt checking sample cometnt checking ","1577":"\n\t\t\t","1578":"6476","1579":"6481","1580":{"6488":"6488"},"1581":"sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking ","1582":"\n\t\t\t","1583":"6493","1584":"6496","1585":{"6502":"6502","6503":"6503"},"1586":"sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking ","1587":"\n\t\t\t","1588":"6506","1589":"6511","1590":{"6517":"6517","6519":"6519"},"1591":"sample cometnt checking ","1592":"\n\t\t\t","1593":"6522","1594":"6526","1595":{"6534":"6534"},"1596":"sample cometnt checking sample cometnt checking sample cometnt checking ","1597":"\n\t\t\t","1598":"6537","1599":"6541","1600":{"6548":"6548"},"1601":"Zvf gsdfgsdfgsfdgf sdfg","1602":"\n\t\t\t","1603":"6553","1604":"6556","1605":{"6563":"6563","6565":"6565"},"1606":"sdfg dgsd gsdfgsdfg","1607":"\n\t\t\t","1608":"6568","1609":"6571","1610":{"6578":"6578","6579":"6579"},"1611":"sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking ","1612":"\n\t\t\t","1613":"6583","1614":"6586","1615":{"6593":"6593"},"1616":"sample cometnt checking ","1617":"\n\t\t\t","1618":"6598","1619":"6601","1620":{"6608":"6608"},"1621":"sample cometnt checking ","1622":"\n\t\t\t","1623":"6614","1624":"6616","1625":{"6624":"6624"},"1626":"sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking v","1627":"\n\t\t\t","1628":"6627","1629":"6631","1630":{"6638":"6638"},"1631":"sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking ","1632":"\n\t\t\t","1633":"6642"}}';
    }

    function save_exam()
    {
        die('jhgfd');
        $this->load->model('Course_model');
        $debug                                 = array();
        $user                                  = $this->get_current_user_session();
        $assesment_id                          = 8;//$this->input->post('assesment_id');
        $answers                               = $this->quiz_answers();//$this->input->post('answer');
        $check_need_valuation                  = false;
        $total_mark                            = 0;

        $answer_queues                         = '{"1631":{"question_id":"1631","answer":"sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking sample cometnt checking ","duration":6},"1632":{"question_id":"1632","answer":"\n\t\t\t","duration":1},"1633":{"question_id":"1633","answer":"6642","duration":2}}';//$this->input->post('answer_queue');
        $this->_answer_queue                   = json_decode($answer_queues);


        $attempt_id                            = 43;//$this->input->post('attempt_id');
        $this->_answers                        = json_decode($answers);
        $this->_answers                        = $this->_answers->answer;
        $answer_time_log                       = json_decode('{"1434":12,"1435":1,"1436":1,"1437":1,"1438":3,"1439":38,"1440":1,"1441":2,"1442":1,"1443":1,"1444":2,"1445":2,"1446":4,"1447":3,"1448":2,"1449":1,"1450":1,"1451":1,"1452":1,"1453":0,"1454":2,"1455":2,"1456":1,"1457":4,"1458":1,"1459":1,"1460":0,"1461":0,"1462":1,"1463":3,"1464":2,"1465":1,"1466":1,"1467":0,"1468":2,"1469":1,"1470":1,"1471":1,"1472":1,"1473":1,"1474":1,"1475":1,"1476":1,"1477":1,"1478":4,"1479":16,"1480":0,"1481":1,"1482":0,"1483":1,"1484":1,"1485":1,"1486":1,"1487":0,"1488":0,"1489":5,"1490":2,"1491":1,"1492":1,"1493":1,"1494":2,"1495":0,"1496":0,"1497":1,"1498":2,"1499":1,"1500":2,"1501":1,"1502":1,"1503":0,"1504":1,"1505":1,"1506":1,"1507":1,"1508":8,"1509":1,"1510":1,"1511":1,"1512":1,"1513":1,"1514":2,"1515":1,"1516":10,"1517":5,"1518":6,"1519":2,"1520":1,"1521":3,"1522":5,"1523":1,"1524":2,"1525":1,"1526":2,"1527":6,"1528":1,"1529":1,"1530":1,"1531":3,"1532":2,"1533":1,"1534":1,"1535":1,"1536":5,"1537":2,"1538":2,"1539":1,"1540":0,"1541":1,"1542":2,"1543":1,"1544":1,"1545":0,"1546":1,"1547":3,"1548":7,"1549":1,"1550":1,"1551":1,"1552":2,"1553":1,"1554":1,"1555":1,"1556":1,"1557":2,"1558":2,"1559":1,"1560":3,"1561":1,"1562":2,"1563":1,"1564":1,"1565":1,"1566":1,"1567":2,"1568":1,"1569":1,"1570":2,"1571":1,"1572":1,"1573":1,"1574":1,"1575":0,"1576":1,"1577":1,"1578":2,"1579":1,"1580":1,"1581":1,"1582":1,"1583":3,"1584":1,"1585":4,"1586":1,"1587":3,"1588":1,"1589":1,"1590":4,"1591":1,"1592":1,"1593":1,"1594":1,"1595":1,"1596":3,"1597":2,"1598":1,"1599":1,"1600":1,"1601":2,"1602":2,"1603":1,"1604":1,"1605":1,"1606":2,"1607":2,"1608":1,"1609":1,"1610":1,"1611":3,"1612":2,"1613":1,"1614":1,"1615":2,"1616":2,"1617":2,"1618":1,"1619":1,"1620":1,"1621":2,"1622":2,"1623":1,"1624":2,"1625":1,"1626":3,"1627":2,"1628":1,"1629":1,"1630":1,"1631":6,"1632":1,"1633":2}');//json_decode($this->input->post('answer_time_log'));
        
        $assesment_objects                     = array();
        $assesment_objects['key']              = 'assesment_'.$assesment_id;

        $assesment_callback                    = 'assesment_details';
        $assesment_params                      = array();
        $assesment_params['assesment_id']      = $assesment_id;

        $assesment_details                     = $this->memcache->get($assesment_objects, $assesment_callback, $assesment_params);
        $assesment                             = $assesment_details['assesment_details'];
        $questions                             = $assesment_details['questions'];
        $course_id                             = $assesment_details['assesment_details']['a_course_id'];

        //Subscription invalidation update 
        $save_subs                            = array();
        $save_subs['cs_user_id']              = $user['id'];
        $save_subs['cs_course_id']            = $course_id;
        $save_subs['cs_invalidate_topic']     = '1';
        $this->Course_model->save_last_played_lecture($save_subs); 
        $this->invalidate_subscription(array('user_id'=>$user['id'],'course_id'=>$course_id));
        //End invalidation update.

        //getting attempt details
        $objects        = array();
        $objects['key'] = 'attempt_'.$attempt_id;
        $attempt        = $this->memcache->get($objects);
        if(!$attempt)  
        {
            $attempt            = $this->Course_model->attempt(array('select'=>'aa_duration,aa_assessment_detail','id'=>$attempt_id));
            $this->memcache->set($objects['key'], $attempt);
        } 
        //End
                
        //saving the assesment attempts
        $assessment_json                           = ($attempt['aa_assessment_detail']!=NULL)?json_decode($attempt['aa_assessment_detail'], true):array();
        if(empty($assessment_json)) {
            $exam_submission_data                  = array();
            $exam_submission_data['assesment_id']  = $assesment_id;
            $exam_submission_data['attempt_id']    = $attempt_id;
            $exam_submission_data['user_id']       = $user['id'];
            $exam_submission_data['course_id']     = $assesment['a_course_id'];
            $exam_submission_data['lecture_id']    = $assesment['a_lecture_id'];
            if(!empty($questions))
            {
                foreach($questions as $question)
                {

                    $question_topic                                                         = array();
                    $question_topic['id']                                                   = $question['q_topic'];
                    $question_topic['topic_name']                                           = $question['qt_topic_name'];
                    $question_subject                                                       = array();
                    $question_subject['id']                                                 = $question['q_subject'];
                    $question_subject['subject_name']                                       = $question['qs_subject_name'];
                    $question['q_question'][1]                                              = strip_tags($question['q_question'][1]);
                    $question_options                                                       = $question['options'];
                    $options                                                                = array();
                    $option_count   = intval(0);
                    foreach($question_options as $question_option)
                    {
                        $option_value                            = '';
                        $options[$option_count]['id']            = $question_option['id'];
                        $options[$option_count]['qo_options'][1] = strip_tags($question_option['qo_options'][1],"<img>");
                        $option_count++;
                    }
                    $question['options']                                  = $options;
                    $question['q_explanation'][1]                         = strip_tags($question['q_explanation'][1]);
                    $exam_submission_data['questions'][$question['id']]                     = array();
                    $exam_submission_data['questions'][$question['id']]['report_id']        = '';
                    $exam_submission_data['questions'][$question['id']]['type']             = $question['q_type'];
                    $exam_submission_data['questions'][$question['id']]['q_question']       = $question['q_question'];
                    $exam_submission_data['questions'][$question['id']]['q_option']         = json_encode($question['options']);
                    $exam_submission_data['questions'][$question['id']]['q_actual_answer']  = isset($question['correct_answer'])?$question['correct_answer']:array();
                    $exam_submission_data['questions'][$question['id']]['q_negative_mark']  = $question['aq_negative_mark'];
                    $exam_submission_data['questions'][$question['id']]['q_positive_mark']  = $question['aq_positive_mark'];
                    $exam_submission_data['questions'][$question['id']]['q_explanation']    = $question['q_explanation'];
                    $exam_submission_data['questions'][$question['id']]['subject']          = json_encode($question_subject);
                    $exam_submission_data['questions'][$question['id']]['topics']           = json_encode($question_topic);
                    $exam_submission_data['questions'][$question['id']]['answer_time_log']  = '';
                    $exam_submission_data['questions'][$question['id']]['user_answers']     = '';
                    $exam_submission_data['questions'][$question['id']]['user_mark']        = '';
                }
            }
        } else {
            $exam_submission_data = $assessment_json;
        }

        if(!empty($this->_answer_queue)) {
            foreach($this->_answer_queue as $key => &$answer_queue)
            {
                $save                            = array();
                $question_id                     = $answer_queue->question_id;
                $duration                        = $answer_queue->duration;
                $report_id                       = ($exam_submission_data['questions'][$question_id]['report_id']!='')?$exam_submission_data['questions'][$question_id]['report_id']:0;
                if($report_id!=0)
                {
                    $attempt_duration            = ($exam_submission_data['questions'][$question_id]['answer_time_log']!='')?$exam_submission_data['questions'][$question_id]['answer_time_log']:0;
                    $save['id']                  = $report_id;
                    $save['ar_duration']         = $attempt_duration+$duration;        
                }
                else
                {
                    $save['id']                  = false;
                    $save['ar_duration']         = $duration;             
                }
                $save['ar_attempt_id']           = $attempt_id;
                $save['ar_question_id']          = $key;
                $save['ar_course_id']            = $assesment['a_course_id'];
                $save['ar_lecture_id']           = $assesment['a_lecture_id'];
                $save['ar_user_id']              = $user['id'];
                $save['ar_duration']             = $duration;
                switch ($questions[$key]['q_type'])
                {
                    case "1":
                        $answer                  = $answer_queue->answer;
                        $save['ar_answer']       = $answer;
                        if($questions[$key]['q_answer'] == $answer)
                        {
                            $save['ar_mark']     = isset($questions[$key]['aq_positive_mark'])?$questions[$key]['aq_positive_mark']:$questions[$key]['q_positive_mark'];
                        }
                        else
                        {
                            $save['ar_mark']     = 0;
                            if($answer)
                            {
                                $save['ar_mark'] = isset($questions[$key]['aq_negative_mark'])?$questions[$key]['aq_negative_mark']:$questions[$key]['q_negative_mark'];
                                $save['ar_mark'] = (int)$save['ar_mark'];                                                       
                                
                            }
                        }
                        
                    break;

                    case "2":
                        $key_answer              = explode(',', $questions[$key]['q_answer']);
                        $answer                  = (array)$answer_queue->answer;
                        sort($key_answer);
                        sort($answer);
                        $key_answer              = implode(',', $key_answer);
                        $answer                  = implode(',', $answer);
                        $save['ar_answer']       = $answer;
                        if($key_answer == $answer)
                        {
                            $save['ar_mark']     = isset($questions[$key]['aq_positive_mark'])?$questions[$key]['aq_positive_mark']:$questions[$key]['q_positive_mark'];                       
                        }
                        else
                        {
                            $save['ar_mark']     = 0;
                            if($answer)
                            {
                            
                                $save['ar_mark'] = isset($questions[$key]['aq_negative_mark'])?$questions[$key]['aq_negative_mark']:$questions[$key]['q_negative_mark']; 
                                $save['ar_mark'] = (int)$save['ar_mark'];                                                       
                            }
                        }

                    break;

                    case "3":
                        $answer                  = $answer_queue->answer;
                        $save['ar_answer']       = $answer_queue->answer;
                        $save['ar_mark']         = '';
                    break;

                    case "4":
                        $answer                  = $answer_queue->answer;
                        $save['ar_answer']       = $answer_queue->answer;
                        $save['ar_mark']         = '';
                    break;
                }
                $exam_submission_data['questions'][$question_id]['answer_time_log'] = $duration;
                $exam_submission_data['questions'][$question_id]['user_answers']    = $answer;
                $exam_submission_data['questions'][$question_id]['user_mark']       = $save['ar_mark'];
                $save_bulk[] = $save;
                // $save_assessment_report           = $this->Course_model->save_assessment_report($save);
                // $exam_submission_data['questions'][$question_id]['report_id']       = $save_assessment_report;
            }
            $save_answers_bulk = $this->Course_model->save_assessment_report_bulk($save_bulk);
            foreach($save_answers_bulk as $save_answer)
            {
                $exam_submission_data['questions'][$save_answer['ar_question_id']]['report_id']       = $save_answer['id'];  
            }

        }
        // echo '<pre>'; print_r($exam_submission_data['questions']);die;

        if(!empty($assesment_details['questions']))
        {
            foreach($assesment_details['questions'] as $question)
            {
                switch ($question['q_type'])
                {
                    case "3":
                        $check_need_valuation    = true;
                    break;
                    case "4":
                        $check_need_valuation    = true;
                    break;
                }
            }
        }
        foreach($exam_submission_data['questions'] as $report_object) {
            $total_mark += $report_object['user_mark'];
        }
       
        $json_submission_data                   = json_encode($exam_submission_data);
        $attempt_param                          = array();
        $attempt_param['id']                    = $attempt_id;
        $attempt_param['aa_assessment_detail']  = $json_submission_data;
        $time_taken                             = $this->input->post('time_taken');
        $attempt_param['aa_duration']           = ($time_taken)?$time_taken:'0';
        $attempt_param['aa_completed']          = "1";
        $attempt_param['aa_mark_scored']        = $total_mark;
        $attempt_param['aa_total_mark']         = $assesment['a_mark'];

        //Updating lecture grade in course subscription table
        $log_param                              = array();
        $log_param['course_id']                 = $assesment['a_course_id'];
        $log_param['lecture_id']                = $assesment['a_lecture_id'];
        $log_param['user_id']                   = $user['id'];
        //End of Updating lecture grade in course subscription table
        
        if($check_need_valuation == true)
        {
            //$debug['check_need_valuation'] = 'in';
            $attempt_param['aa_valuated']        = "0";
            $log_param['grade']                  = "-";
        } 
        else 
        {
            //$debug['check_need_valuation'] = 'out';
            $assessment_mark                     = $assesment['a_mark'];
            if($total_mark>=0)
            {
                $grade_percentage                = (($total_mark/$assessment_mark)*100);
            } else {
                $grade_percentage                = '0';
            }
            $grade                               = convert_percentage_to_grade($grade_percentage);
            $attempt_param['aa_valuated']        = "1";
            $attempt_param['aa_grade']           = $grade['gr_name'];

            //Updating lecture grade in course subscription table
            $log_param['grade']                  = $grade['gr_name'];
            $log_param['percentage_of_marks']    = $grade_percentage;
            //End of Updating lecture grade in course subscription table
        }
        update_lecture_log_wiht_subscription($log_param);
        
        $submit_exam = $this->Course_model->save_assessment_attempts($attempt_param);
        if($submit_exam)
        {
            $user               = $this->get_current_user_session();
            $template           = $this->ofabeemailer->template(array('email_code' => 'assessment_submission_success'));
            $param['to']        = $user['us_email'];
            $param['subject']   = $template['em_subject'];
            $contents = array(
                'site_name' => config_item('site_name')
                , 'username' => $user['us_name']
                , 'course_title' => $assesment['a_course_title']
                , 'assessment'   => $assesment['a_title']
                , 'site_url' => config_item('site_url')
                , 'date' => date('d-m-Y')
            );
            $param['body']      = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
            $this->ofabeemailer->send_mail($param);

            $preveleged_users = $this->accesspermission->previleged_users(array('module' => 'course_content'));
            foreach($preveleged_users as $preveleged_user)
            {
            $notify_to[$preveleged_user['id']] = array($user['id']);
            }
            
            //Notification
            $this->load->library('Notifier');
            $this->notifier->push(
                array(
                    'action_code' => 'quiz_submitted',
                    'assets' => array('quiz_name' => $assesment['a_title'],'course_name' => $assesment['a_course_title'],'course_id'=>$log_param['course_id']),
                    'target' => $assesment['a_lecture_id'],
                    'individual' => false,
                    'push_to' => $notify_to
                )
            );
            //End notification

            /*Log creation*/
            $user_data                              = array();
            $user_data['user_id']                   = $user['id'];
            $user_data['username']                  = $user['us_name'];
            $user_data['useremail']                  = $user['us_email'];
            $user_data['user_type']                 = $user['us_role_id'];
            $user_data['register_number']           = $user['us_register_number'];
            $message_template                       = array();
            $message_template['username']           = $user['us_name'];
            $message_template['quiz_title']         = $assesment['a_title'];
            $message_template['course_name']        = $assesment['a_course_title'];
            $triggered_activity                     = 'quiz_submitted';
            log_activity($triggered_activity, $user_data, $message_template);
            /*End log creation*/

        }
        echo json_encode(array('error' => 'false', 'message' => 'Report submitted successfully', 'attempt_id' => $attempt_id, 'debug' => $debug));
    }

    public function invalidate_subscription($param = array())
    {
        //Invalidate cache
        $user_id = isset($param['user_id']) ? $param['user_id'] : false;
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        if ($user_id && $course_id) {
            $this->memcache->delete('enrolled_' . $user_id);
            $this->memcache->delete('subscription_' . $course_id . '_' . $user_id);
            $objects_key        = 'enrolled_item_ids_' .$user_id;
            $this->memcache->delete($objects_key);
        }
        if ($user_id) {
            $this->memcache->delete('mobile_enrolled_'.$user_id);
            $this->memcache->delete('enrolled_' . $user_id);
            $objects_key        = 'enrolled_item_ids_' .$user_id;
            $this->memcache->delete($objects_key);
        }
    }
    function get_client_ip($ipaddress = '') {
        
        switch($ipaddress){
            case 1:$v = getenv('HTTP_CLIENT_IP');break;
            case 2:$v =  getenv('HTTP_X_FORWARDED_FOR');break;//working
            case 3:$v =  getenv('HTTP_X_FORWARDED');break;
            case 4:$v =  getenv('HTTP_FORWARDED_FOR');break;
            case 5:$v =  getenv('HTTP_FORWARDED');break;
            case 6:$v =  getenv('REMOTE_ADDR');break;

        }    
        echo json_encode(array('ip'=>$v));    
    }
    function categories($param = array())
    {
        $response       = array();
        $objects        = array();
        $status_code    = '404';
        $headers        = array('error' => true, 'message' => 'no categories');
        
        $objects['key'] = 'categories';
        $callback       = 'get_categories';
        $categories     = $this->memcache->get($objects, $callback,array());
        if(!empty($categories))
        {
            $status_code        = '200';
            $headers            = array('error' => false, 'message' => 'successfully fetched');
            $body               = $categories;
        }
        send_response($status_code ,$headers, $body);
    }

    function get_video_list($offset = 0, $limit = 100){

        $vimeo_lectures = $this->db->select('id, cl_filename')->where('status_check','0')->limit($limit, $offset)->get('course_lectures_bk')->result_array();
        //echo "<pre>";print_r($vimeo_lectures);die;
        
        $this->db->trans_start();
        
        
        
        ?>
        <?php
        foreach($vimeo_lectures as $lectures){
            $vimeo_id = explode('/',$lectures['cl_filename'])[2]; 
            $url = "https://vimeo.com/api/oembed.json?url=https://vimeo.com/".$vimeo_id; 
            $headers = @get_headers($url); 
            
            if($headers && strpos( $headers[0], '200')) { 
                
                // $data = array(
                //     'status_check' => '1',
                //     'conversion_status' => 'complete'
                // );
                $this->db->query('update course_lectures_bk set status_check = "1",conversion_status="complete" where id='.$lectures['id']);
                // $this->db->where('id', $lectures['id']);
                // $this->db->update('course_lectures_bk', $data);
                

                ?>
                <?php 
            } 
        }
        $this->db->trans_complete();
        echo $this->db->trans_status();
    }
    public function check_valid_json()
    {
        $this->load->model('Bundle_model');
        $this->Bundle_model->get_all_match();
    }

    public function getAllKeys()
    {
        echo '<pre>';print_r($this->memcache->getAllKeys());
    }

    public function getAllValues()
    {
        echo '<pre>';print_r($this->memcache->getAllValues());
    }

    public function deleteAllKeys()
    {
        print_r($this->memcache->deleteAllKeys());
    }
    public function check_token()
    {
        // $this->load->library('JWT');
        // // $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6Ijg3IiwiZW1haWxfaWQiOiJraXJhbi5qYisxMjNAZW5maW50ZWNobm9sb2dpZXMuY29tIiwibW9iaWxlIjoiOTg3NDU2MzIxNSIsInNlc3Npb25faWQiOiI2NTlhNTNjNTBiYzFjNDBmZTUyNjNmNDU3MDdlY2RmNzBkZTkxZjg3In0.vsguIFPNKU1RwINxdDZRHYIjZayv6nNuzd0ZYgipF5g";
        // $token ="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6Ijg3IiwiZW1haWxfaWQiOiJraXJhbi5qYisxMjNAZW5maW50ZWNobm9sb2dpZXMuY29tIiwibW9iaWxlIjoiOTg3NDU2MzIxNSIsInNlc3Npb25faWQiOiI2NTlhNTNjNTBiYzFjNDBmZTUyNjNmNDU3MDdlY2RmNzBkZTkxZjg3In0.vsguIFPNKU1RwINxdDZRHYIjZayv6nNuzd0ZYgipF5g";
        // $key                        = config_item('jwt_token');
        // $dumpload                   = $this->jwt->decode($token,$key);
        // echo "<pre>";print_r($dumpload );exit;


        $ff= "hello+how.are_you.hope-you@##$%^&*(() fine";
        echo md5($ff);
    }
    public function GetClientMac(){
    //     if (isset($_SERVER['HTTP_CLIENT_IP']))
    //     $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    // else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
    //     $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    // else if(isset($_SERVER['HTTP_X_FORWARDED']))
    //     $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    // else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
    //     $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    // else if(isset($_SERVER['HTTP_FORWARDED']))
    //     $ipaddress = $_SERVER['HTTP_FORWARDED'];
    // else if(isset($_SERVER['REMOTE_ADDR']))
    //     $ipaddress = $_SERVER['REMOTE_ADDR'];
    // else
    //     $ipaddress = 'UNKNOWN';

    // $macCommandString   =   "arp " . $ipaddress . " | awk 'BEGIN{ i=1; } { i++; if(i==3) print $3 }'";

    // $mac = exec($macCommandString);

    // var_dump(['ip' => $ipaddress, 'mac' => $mac]);

    echo uploads_url() . config_item('upload_folder').'/'.config_item('acct_domain').'/mobile_banners';
    }
    public function check_query()
    {
        $filter_param['popular']    = true;
        $popular_courses            = $this->Course_model->item_courses($filter_param);
        echo $this->db->last_query();
    }
    public function get_items()
    {
        $this->load->model(array('Bundle_model','Report_model'));
        $user_id            = isset($param['user_id']) ? $param['user_id'] : 87;
        $response           = array();
        $bundles            = array();
        $courses            = array();
        $enrolled_bundles   = $this->Bundle_model->enrolled_bundles(array('user_id' => $user_id));
        $enrolled_courses   = $this->Report_model->enrolled_course(array('user_id' => $user_id));
        if(!empty($enrolled_bundles))
        {
            foreach($enrolled_bundles as $enrolled_bundle)
            {
                $bundles[] = $enrolled_bundle['bundle_id'];
            }
        }
        if(!empty($enrolled_courses))
        {
            foreach($enrolled_courses as $enrolled_course)
            {
                $courses[] = $enrolled_course['course_id'];
            }
        }
        $response['enrolled_courses'] = $courses;
        $response['enrolled_bundles'] = $bundles;

        return $response;
    }
    public function ratings()
    {
        $scope = &get_instance();
        $bundle_id = '8';
        $scope->load->model(array('Bundle_model', 'Course_model', 'Category_model'));
        $bundle_params                              = 'catalogs.id, catalogs.c_title, catalogs.c_description, catalogs.c_access_validity, catalogs.c_price, catalogs.c_discount, catalogs.c_validity, catalogs.c_validity_date, catalogs.c_image, catalogs.c_status, catalogs.c_deleted, catalogs.c_courses, catalogs.c_tax_method';
        $bundle_details                             = $scope->Bundle_model->bundle(array('bundle_id' => $bundle_id, 'select' => $bundle_params));
        if(!empty($bundle_details))
        {
            $bundle_courses                         = json_decode($bundle_details['c_courses'],true);
            $bundle_details['courses']              = array();
            if($bundle_courses)
            {
                foreach($bundle_courses as $course)
                {
                    $course_id                      = $course['id'];
                    $select                         = 'course_basics.id, course_basics.cb_title, course_basics.cb_description, course_basics.cb_category, course_basics.cb_image';
                    $course_details                 = $scope->Course_model->course(array('id' => $course_id, 'select' => $select));
                    if(!empty($course_details))
                    {
                        $image_dimension            = '_300x160.jpg';
                        $image_first_name           = substr($course_details['cb_image'],0,-4);
                        $image_new_name             = $image_first_name.$image_dimension;
                        $course_image               = (($course_details['cb_image'] == 'default.jpg')?default_course_path():  course_path(array('course_id' => $course_details['id']))).$image_new_name;
                        $course_details['cb_image'] = $course_image;
                    }
                    $bundle_details['courses'][]    = $course_details;
                }
            }   
        }
        echo "<pre>";print_r($bundle_details);exit;
        /* Load languages from memcache */
        // $course_languages               = array();
        // $objects                        = array();
        // $objects['key']                 = 'course_languages';
        // $callback                       = 'course_languages';
        // $all_languages                  = $scope->memcache->get($objects, $callback, array());
        // $saved_languages                = isset($course_details['cb_language'])?$course_details['cb_language']:'';
        // $course_languages               = explode(',', $saved_languages);
        // $course_details['cb_language']  = array();
        // if( !empty($course_languages) ) 
        // {
        //     foreach ($course_languages as $language) 
        //     {
        //         $course_details['cb_language'][] = $all_languages[$language];
        //     }
        // }


                
            //     $course_details['cb_category']      = $scope->Category_model->categories(array('ids' => explode(',', $course_details['cb_category']), 'select' => 'categories.ct_name'));
            
        // }
        // $bundle_details['enrolled_students']        = $scope->Bundle_model->get_subscription_count($params['id']);
        // $bundle_details['course_count']             = count($bundle_courses);
        // return $bundle_details;

        // $course_categories              = explode(",",$course_details['cb_category']); 
        // $objects['key']                 = 'categories';
        // $callback                       = 'get_categories';
        // $all_categories                 = $scope->memcache->get($objects, $callback, array());
        // $categories                     = array();
        // if(!empty($all_categories))
        // {
        //     foreach($all_categories as $category)
        //     {
        //         if(in_array($category['id'],$course_categories))
        //         {
        //             array_push($categories,$category);
        //         }
                
        //     }
        // }
        // $items_list['item_search']     = $course_details['cb_category'];
        // $items_list['item_category']   = $categories;
    }

    public function tryblock()
    {
        $name = '';
        $email = '';
        $password = '';
        try 
        { 
            if($name == '')
            {
                throw new Exception('Name Required.');
            }
            if($email == '')
            {
                throw new Exception('Email Required');
            }
            if($password == '')
            {
                throw new Exception('Password Required');
            }
            
        }
        catch(Exception $err) {
          echo  $err->getMessage();
        }
       
    }
    public function add_sort_courses()
    {
        $this->db->select('*');
        $items = $this->db->get('course_basics');
        $unsort_items = $items->result_array();
        
    //     $this->db->select('iso_item_id,iso_item_type');
    //     $this->db->where('iso_item_type','course');
    //     $sorted_ids = $this->db->get('item_sort_order');
    //     $sorted_items = $sorted_ids->result_array();

    //     $all_exist_ids = array_column($sorted_items,'iso_item_id');
    //     $batch_items        = array();
    //     foreach($unsort_items as $unsort_item)
    //     {
    //         if(!in_array($unsort_item['id'],$all_exist_ids))
    //         {
    //             $item_param                          = array();
    //             $item_param['iso_item_type']         = 'course';
    //             $item_param['iso_item_id']           = $unsort_item['id'];
    //             $item_param['iso_item_name']         = $unsort_item['cb_title'];
    //             $item_param['iso_item_sort_order']   = '0';
    //             $item_param['iso_item_price']        = $unsort_item['cb_price'];
    //             $item_param['iso_item_discount_price']   = $unsort_item['cb_discount'];
    //             $item_param['iso_item_status']       = $unsort_item['cb_status'];
    //             $item_param['iso_item_deleted']      = $unsort_item['cb_deleted'];
    //             $item_param['iso_item_rating']       = '0';
    //             $item_param['iso_item_is_free']      = $unsort_item['cb_is_free'];
    //             $item_param['iso_item_featured']     = '0';
    //             $item_param['iso_item_popular']      = '0';
    //             $item_param['iso_account_id']        = config_item('id');

    //             $batch_items[] = $item_param;
    //         }
    //     }
    //     $this->db->insert_batch('item_sort_order', $batch_items); 
    // }
    // public function add_sort_bundles()
    // {
    //     $this->db->select('*');
    //     $items = $this->db->get('catalogs');
    //     $unsort_items = $items->result_array();
        
    //     $this->db->select('iso_item_id,iso_item_type');
    //     $this->db->where('iso_item_type','bundle');
    //     $sorted_ids = $this->db->get('item_sort_order');
    //     $sorted_items = $sorted_ids->result_array();

    //     $all_exist_ids = array_column($sorted_items,'iso_item_id');
    //     $batch_items        = array();
    //     foreach($unsort_items as $unsort_item)
    //     {
    //         if(!in_array($unsort_item['id'],$all_exist_ids))
    //         {
    //             $item_param                          = array();
    //             $item_param['iso_item_type']         = 'bundle';
    //             $item_param['iso_item_id']           = $unsort_item['id'];
    //             $item_param['iso_item_name']         = $unsort_item['c_title'];
    //             $item_param['iso_item_sort_order']   = '0';
    //             $item_param['iso_item_price']        = $unsort_item['c_price'];
    //             $item_param['iso_item_discount_price']   = $unsort_item['c_discount'];
    //             $item_param['iso_item_status']       = $unsort_item['c_status'];
    //             $item_param['iso_item_deleted']      = $unsort_item['c_deleted'];
    //             $item_param['iso_item_rating']       = '0';
    //             $item_param['iso_item_is_free']      = $unsort_item['c_is_free'];
    //             $item_param['iso_item_featured']     = '0';
    //             $item_param['iso_item_popular']      = '0';
    //             $item_param['iso_account_id']        = config_item('id');

    //             $batch_items[] = $item_param;
    //         }
    //     }
        
    //     $this->db->insert_batch('item_sort_order', $batch_items); 
        
    }
    public function import_database() 
    {
        $this->load->dbforge();
        $this->dbforge->create_database('sample_db7');
        $sql_file   = assets_url().'neyyar_testing.sql';
        
        //$command = 'mysql -u '.$this->db->username.' -p '.$this->db->password.' sample_db < '.$sql_file;
        //$command1   = 'mysql -u '.$this->db->username.' -p';
        //$command2   =  $this->db->password.' -keypass '.$this->db->password.' -keystore';
        //$command3   = 'use sample_db6';
        //$command4   = 'source '.$sql_file;
        //shell_exec($command1 && $command2 && $command3 && $command4);
        die('lin');



        $this->db->query('use sample_db');
        $temp_line = '';
        $lines = file(assets_url().'neyyar_testing.sql'); 
        foreach ($lines as $line)
        {
            if (substr($line, 0, 2) == '--' || $line == '' || substr($line, 0, 1) == '#')
                continue;
            $temp_line .= $line;
            if (substr(trim($line), -1, 1) == ';')
            {
                $this->db->query($temp_line);
                $temp_line = '';
            }
        }
        //     if (file_exists(assets_url().'neyyar_testing.sql'))
        // {
        //     $lines = file(assets_url().'neyyar_testing.sql');
        //     $statement = '';
        //     foreach ($lines as $line)
        //     {
        //         $statement .= $line;
        //         if (substr(trim($line), -1) === ';')
        //         {
        //             $this->db->simple_query($statement);
        //             $statement = '';
        //         }
        //     }
        // }
            die('lin');
    }


    function msqlcommand(){ //echo logo_path();die;
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        
        $dir = $_SERVER['DOCUMENT_ROOT'].'/uploads/dump.sql';
        $cmd="(mysql --user=root --password=enfin123 --host=localhost --database testdb < $dir) 2>&1";
        exec($cmd, $output, $return_var);
    
    }


    public function curriculum_list($param = array())
    {
        $course_id                         = $_GET['course_id'];
        $user_id                           = $_GET['user_id'];

        if(!$course_id)
        {
            die('user_id and couser_id not found, ?course_id=1&user_id=109');
            //$this->set_header(array('error' => true,'code'=>601, 'message' => 'Course id is not found!'));
            //$this->set_body();
            //$this->set_response();
        }

        // Get course details
        $course_objects                    = array();
        $course_objects['key']             = 'course_'.$course_id;
        $course_callback                   = 'course_details';
        $course_params                     = array();
        $course_params['id']               = $course_id;
        $course_details                    = $this->memcache->get($course_objects, $course_callback, $course_params);
        
        // Get override details
        $course_override                   = $this->Course_model->lecute_override(array('course_id' => $course_id, 'source'=>'course'));

        $s_param = array('user_id' => $user_id, 'course_id' => $course_id, 'select' => 'course_subscription.id,course_subscription.cs_course_id,course_subscription.cs_user_id,course_subscription.cs_course_validity_status,course_subscription.cs_approved,course_subscription.cs_certificate_issued,course_subscription.cs_forum_blocked,course_subscription.cs_percentage,course_subscription.cs_course_validity_status,course_subscription.cs_subscription_date,course_subscription.cs_start_date,course_subscription.cs_end_date,course_subscription.cs_auto_grade,course_subscription.cs_manual_grade,course_subscription.cs_lecture_log,course_subscription.cs_last_played_lecture');
        $this->load->model('Course_model');
        $subscription_details              = $this->Course_model->subscription_details($s_param);
        // Get transition details
        $transition_objects                = array();
        $transition_objects['key']         = 'transition_contents';
        $transition_callback               = 'transition_contents';
        $transition_params                 = array();
        $transition_contents               = $this->memcache->get($transition_objects, $transition_callback, $transition_params);
        $random_transitions                = array();
        if(!empty($transition_contents) && (count($transition_contents)>=count($course_details['lectures'])+5)){
            $random_transitions                = array_rand($transition_contents,count($course_details['lectures'])+5);
        }
        
        $transition_messages               = array();
        foreach($random_transitions as $random_transition)
        {
            $transition_messages[]         = $transition_contents[$random_transition];
        }

        $sections                          = $course_details['sections'];
        $lectures                          = $course_details['lectures'];
        $section_lectures                  = array();

        $log_data                          = json_decode($subscription_details['cs_lecture_log'],true);

        foreach($lectures as $lecture)
        {
            if($lecture['cl_lecture_type'] == 7){
                if(!isset($log_data[$lecture['id']])){
                    $section_lectures[$lecture['cl_section_id']][] = $lecture;
                }
            }else{
                $section_lectures[$lecture['cl_section_id']][] = $lecture;
            }
        }
        
        $section_details                              = array();
        foreach($sections as $section)
        {
            $section_id                               = $section['id'];
            $section_detail                           = array();
            $section_detail['id']                     = $section['id'];
            $section_detail['s_name']                 = $section['s_name'];
            $section_detail['s_course_id']            = $section['s_course_id'];
            $section_detail['s_order_no']             = $section['s_order_no'];
            $section_detail['s_lectures']             = (isset($section_lectures[$section_id]))?$section_lectures[$section_id]:array();
            //$section_detail['cl_batch_override']      = '';
            $section_details[]                        = $section_detail;
        }
         
        $body                                          = array();
        $body['curriculum']['sections']                = $section_details;
        $body['curriculum']['override']                = $course_override;
        $body['curriculum']['subscription']            = $subscription_details;
        $body['transition']['transition_contents']     = $transition_messages;
        $body['course']                                = array(
            'id' => $course_details['id'],
            'cb_title' => $course_details['cb_title'],
            'cb_slug' => $course_details['cb_slug']
        );
        //print_r($body['curriculum']['override']);
        echo '<pre>'; echo json_encode($body);die;
        $this->set_header(array('error' => false, 'message' => 'Curriculum list fetched successfully!'));
        $this->set_body($body);
        $this->set_response();
    }

    /* 
    ================================================== 
    purpose     : Dont delete for development purpose
    developer   : kiran
    ==================================================
    */
    public function replace_slug()
    {
        
        $result = $this->db->get('routes');
        $routes = $result->result_array();
        if($routes)
        {
            foreach($routes as $route)
            {
                $route_type     = '';
                $route_id       = '';
                if(!empty($route['route']))
                {
                    $route_data = explode("/",$route['route']);
                    $route_type = $route_data[0];
                    $route_id   = $route_data[2];
                }
                $save_param                 = array();
                $save_param['r_item_type']  = $route_type;
                $save_param['r_item_id']    = $route_id;
                // echo "<pre>";print_r($save_param);
                if(!empty($save_param))
                {
                    $this->db->where('id',$route['id']);
                    $this->db->update('routes',$save_param);
                }
                
            }
        }
        echo json_encode(array('message'=>"successfull"));
    }
    public function webpimage()
    {
        $file='d0c57a75e4fbb302e2f3774d76a8d0ad.jpg';
        $image=  imagecreatefromjpeg($file);
        ob_start();
        imagejpeg($image,NULL,100);
        $cont=  ob_get_contents();
        ob_end_clean();
        imagedestroy($image);
        $content =  imagecreatefromstring($cont);
        imagewebp($content,'d0c57a75e4fbb302e2f3774d76a8d0ad.webp');
        imagedestroy($content);
    }

    public function get_subscriptions()
    {
        $scope->load->model(array('Report_model', 'Bundle_model'));
        $user_id                                = isset($param['user_id']) ? $param['user_id'] : 881;
        $courses_only                           = isset($param['courses_only']) ? $param['courses_only'] : true;
        
        $enrolled_param                         = array();
        $enrolled_param['user_id']              = $user_id;
        $enrolled_param['courses_only']         = $courses_only;
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

    public function subscription_test_check()
    {
        $this->load->model(array('Bundle_model'));

        $result = $this->db->query("SELECT ph_item_type,ph_item_id,ph_user_id,ph_payment_date FROM payment_history WHERE ph_item_type = 2 AND ph_status = '1' ORDER BY `id` DESC");
        
        $all_data =  $result->result_array();
        if(!empty($all_data))
        {
            foreach($all_data as $data)
            {
                $bundle_id                  = $data['ph_item_id'];
                $subscribtion_date          = date('Y-m-d H:i:s',strtotime($data['ph_payment_date']));
                $user_id                    = $data['ph_user_id'];

                $bundle_param               = array();
                $bundle_param['select']     = 'id,c_code,c_title,c_code,c_is_free,c_courses,c_access_validity,c_validity,c_validity_date,c_price,c_discount,c_tax_method';  
                $bundle_param['bundle_id']  = $bundle_id;
                $bundle                     = $this->Bundle_model->bundle($bundle_param);

     $course_lectures =  $this->db->get('course_lectures')->result_array();
      echo '<pre>'; //print_r($course_lectures);
      foreach($course_lectures as $details){
          echo 'https://demo.ravindrababuravula.com/uploads/demo.ravindrababuravula.com/course/'.$details['cl_course_id'].'/'.$details['cl_filename'].'<br />';
          echo 'https://demo.ravindrababuravula.com/uploads/demo.ravindrababuravula.com/course/'.$details['cl_course_id'].'/'.$details['cl_org_file_name'].'<br />';
     }
    }
}
}

    function testmessage($phone = null, $message = null, $email = null){

            if($phone && $message){
                $curl = curl_init();

                    curl_setopt_array($curl, array(
                    CURLOPT_URL => "http://api.msg91.com/api/sendhttp.php?country=91&sender=TESTIN&route=4&mobiles=9744156727&authkey=303962ABqrxovtk5dce55c0&message=Hello!+This+is+a+test+message",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_POSTFIELDS => "",
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_HTTPHEADER => array(
                        "authkey: 303962ABqrxovtk5dce55c0",
                        "content-type: application/json"
                    ),
                    ));

                    $response = curl_exec($curl);
                    $err = curl_error($curl);

                    curl_close($curl);

                if ($err) {
                echo "cURL Error #:" . $err;
                } else {
                echo $response;
                }
            }else{
                echo 'Phone number or message is missing<br/>';
                echo 'https://api.msg91.com/api/v5/otp?invisible=1&otp=OTP to send and verify. If not sent, OTP will be generated.&userip=IPV4 User IP&authkey=Authentication Key&email=Email ID&mobile=Mobile Number&template_id=Template ID';
            }

            //http://api.msg91.com/api/sendhttp.php?country=91&sender=TESTIN&route=4&mobiles=9744156727&authkey=303962ABqrxovtk5dce55c0&message=Hello!+This+is+a+test+message

                //https://api.msg91.com/api/sendhttp.php?route=4&sender=TESTIN&message=Hello&country=91&mobiles=9744156727&authkey=303962ABqrxovtk5dce55c0


    }

    function bundleSubscription(){//->limit(100, 1)
       $bundle_subscription = $this->db->get('bundle_subscription')->result_array();
       //echo '<pre>'; print_r($bundle_subscription);
       if(!empty($bundle_subscription))
       {
           $i = 1;
            foreach($bundle_subscription as $b_subscription)
            {
                $bs_bundle_id   = $b_subscription['bs_bundle_id'];
                $bs_user_id     = $b_subscription['bs_user_id'];
                $bs_end_date    = $b_subscription['bs_end_date'];
                $bundle_details = $this->db->select('c_title, c_code, c_courses')->where('id', $bs_bundle_id)->get('catalogs')->row_array();
                //echo '<pre>'; print_r($bundle_details);
                $c_courses = json_decode($bundle_details['c_courses'], true);
                //echo '<pre>'; print_r($c_courses);
                if(!empty($c_courses))
                {
                    foreach($c_courses as $course)
                    {
                        if(!empty($course)) //
                        {
                            $course_subscription = $this->db->where('cs_course_id', $course['id'])->where('cs_user_id', $bs_user_id)->where('cs_bundle_id !=', '0')->get('course_subscription')->result_array();
                            if(!empty($course_subscription))
                            {
                                /*$cb_bundle_id = $course_subscription['cs_bundle_id'].',';
                                $bundleids = explode(",", $cb_bundle_id);
                                if(!in_array($course_subscription['cs_bundle_id'], $bundleids))
                                {
                                    $bundleids = array_push($bundleids, $course_subscription['cs_bundle_id']);
                                }
                                echo implode(',', $bundleids).'<br/>';*/
                                echo count($course_subscription).'<br/>';
                                //echo $i.' <pre>'; print_r($course_subscription);
                            }else{
                                echo $i.' insert course subscription<br/>';
                            }
                        }
                        $i++;
                    }
                }else{
                    //echo '<pre>'; print_r($bundle_details);
                }
            }
       }  
    }

    function migrate_subscription()
    {
        die('testing only.........');
        $i              = 1;
        $logs           = array();
        $repetation     = array();

        $bundles        = $this->db->select('id, c_title, c_code, c_courses, c_access_validity, c_validity, c_validity_date')->get('catalogs')->result_array();
        if(!empty($bundles))
        {
            foreach($bundles as $bundle)
            {
                if($bundle['c_courses'] != '')
                {
                    if(!isset($logs[$bundle['id']]))
                    {
                        $logs[$bundle['id']]        = array();
                        $repetation[$bundle['id']]  = array();
                    }
                    $bundle_courses = json_decode($bundle['c_courses'], true);
                    if(!empty($bundle_courses))
                    {
                        $bundle['courses'] = array();
                        foreach($bundle_courses as $bundle_courses_object)
                        {
                            $bundle['courses'][] = $bundle_courses_object['id'];
                        }
                        if($bundle['id'] > 0 )
                        {
                            $bundle_subscription = $this->db->query('SELECT bs_bundle_id, bs_user_id FROM bundle_subscription WHERE bs_bundle_id = '.$bundle['id'])->result_array();
                            if(!empty($bundle_subscription))
                            {
                                foreach($bundle_subscription as $b_subscription_object)
                                {
                                    if(in_array($b_subscription_object['bs_user_id'], $logs[$bundle['id']]))
                                    {
                                        $repetation[$bundle['id']][] = $b_subscription_object['bs_user_id'];
                                        echo "repeatation for bundle ".$bundle['id']." and course ".$b_subscription_object['bs_user_id'].'<br />';
                                    }
                                    $logs[$bundle['id']][] = $b_subscription_object['bs_user_id'];

                                    $bundle_course_subscriptions = $this->db->query('SELECT cs_course_id, cs_user_id, cs_bundle_id  FROM course_subscription WHERE cs_user_id = '.$b_subscription_object['bs_user_id'].' AND cs_course_id IN ('.implode(",", $bundle['courses']).')')->result_array();
                                    $already_subscribed = array();
                                    if(!empty($bundle_course_subscriptions))
                                    {
                                        foreach($bundle_course_subscriptions as $bundle_course_subscription_object)
                                        {
                                            $already_subscribed[] = $bundle_course_subscription_object['cs_course_id'];
                                        }
                                    }

                                    $courses         = array();
                                    $course_details = $this->db->select('id, cb_title, cb_access_validity, cb_validity_date, cb_validity')->get('course_basics')->result_array();
                                    if(!empty($course_details))
                                    {
                                        foreach($course_details as $course_objects)
                                        {
                                            $courses[$course_objects['id']] = $course_objects;
                                        }
                                    }
                                    // echo '<pre>'; 
                                    // print_r($bundle['courses']);
                                    // print_r($already_subscribed);
                                    // die;        

                                    if(!empty($bundle['courses']))
                                    {
                                        foreach($bundle['courses'] as $course_id)
                                        {
                                            if(!in_array($course_id, $already_subscribed) && isset($courses[$course_id]))
                                            {
                                                $course                             = $courses[$course_id];
                                                $save                               = array();
                                                $save['id']                         = false;
                                                $save['cs_course_id']               = $course_id;
                                                $save['cs_user_id']                 = $b_subscription_object['bs_user_id'];
                                                $save['cs_subscription_date']       = date('Y-m-d H:i:s');
                                                $save['cs_start_date']              = date('Y-m-d');
                                                $save['cs_course_validity_status']  = $course['cb_access_validity'];
                                                $save['cs_user_groups']             = '';
                                                if ($course['cb_access_validity'] == 2) 
                                                {
                                                    $course_enddate = $course['cb_validity_date'];
                                                } 
                                                else if ($course['cb_access_validity'] == 0) 
                                                {
                                                    $course_enddate = date('Y-m-d', strtotime('+3000 days'));
                                                } 
                                                else 
                                                {
                                                    $duration           = ($course['cb_validity']) ? $course['cb_validity'] : 0;
                                                    $course_enddate     = date('Y-m-d', strtotime('+' . $duration . ' days'));
                                                }
                                                                    
                                                $save['cs_end_date']            = $course_enddate;
                                                $save['cs_approved']            = '1';
                                                $save['action_by']              = $b_subscription_object['bs_user_id'];
                                                $save['action_id']              = '1';  
                                                $save['cs_bundle_id']           = $bundle['id'];
                                                // echo "=====================COURSE DATA - ".$course_id."====================<br /><pre>";
                                                // print_r($save);
                                                //$this->db->insert('course_subscription', $save);
                                                // die;
                                                echo $i." Course subscription success full<br />";
                                                $i++;
                                            }
                                        }
                                    }
                                }
                            }
                        }        
                    }
                }
            }
        }
        echo '<pre>'; print_r($repetation);
    }
    
    function check_work()
    {
        $user_id            = '823';
        $objects['key']     = 'enrolled_item_ids_'.$user_id;
        $callback           = 'enrolled_item_ids';
        $memcache_params    = array('user_id' => $user_id);
        $enrolled_item_ids  = $this->memcache->get($objects, $callback, $memcache_params);
           echo "<pre>";print_r($enrolled_item_ids);exit;
    }

    function bundlemigration()
    { 
        // die('debugmode only----');
        $this->load->model('Bundle_model');
        $catalogs = $this->db->select('id,c_courses')->get('catalogs')->result_array();
        //echo '<pre>';print_r($catalogs);
        foreach($catalogs as $catalog)
        {
            if(!empty($catalog['c_courses']))
            {
                $c_courses = json_decode($catalog['c_courses'], true);
                if(!empty($c_courses))
                {
                    $course_ids = array_column($c_courses, 'id');
                    if(!empty($course_ids))
                    {
                        $this->Bundle_model->migrateCourseSubscription(array('bundle_id' => $catalog['id'], 'course_ids' =>$course_ids));
                        //echo '<pre>';print_r($course_ids);
                    }
                }
            }
        }
    }


    function error_videos()
    {
        die('debugmode only----');
        $html = '<table border="1">';
        $courses = $this->db->query('SELECT cl_course_id, course_basics.cb_title, COUNT(course_lectures_bk.id) as total_lectures
                                        FROM `course_lectures_bk` 
                                        LEFT JOIN course_basics ON cl_course_id = course_basics.id
                                        where status_check = "0"
                                        GROUP BY cl_course_id')->result_array();

        $count = 1;
        $sl_no = 1;
        if(!empty($courses))
        {
            foreach($courses as $course)
            {
                $html .= '<tr><td colspan="6" align="center"><b>'.$course['cb_title'].'</b></td></tr>';
                $lectures = $this->db->query('SELECT id, cl_filename, cl_org_file_name, status_check,conversion_status, cl_file_available_in_s3  
                                                FROM `course_lectures_bk` 
                                                where status_check = "0" AND cl_course_id = "'.$course['cl_course_id'].'"')->result_array();
                if(!empty($lectures))
                {
                    $html .= '<tr><td>Sl.No</td><td>Lecture Id</td><td>Vimeo Id</td><td>Original File Name</td><td>File Exist</td><td>Relaunch Conversion</td></tr>';
                    foreach($lectures as $lecture)
                    {
                        $cl_file_available_in_s3 = $lecture['cl_file_available_in_s3'];                        
                        $link       = 'https://d38v42jd03kwqt.cloudfront.net/uploads/SGlearningapp.com/course/'.$course['cl_course_id'].'/videos/'.$lecture['cl_org_file_name'].'';

                        if($lecture['cl_file_available_in_s3'] == '0')
                        {
                            if($count<=10)
                            {
                                $headers    = get_headers($link);    
                                if($headers && strpos( $headers[0], '200')) 
                                {
                                    $cl_file_available_in_s3 = "yes";
                                }  
                                else
                                {
                                    $cl_file_available_in_s3 = "no";
                                } 
                                $this->db->query('UPDATE course_lectures_bk SET cl_file_available_in_s3 = "'.$cl_file_available_in_s3.'" WHERE id = '.$lecture['id']);
                                $count++;
                            }
                        }
                        
                        $html .= '<tr><td>'.$sl_no.'</td><td>'.$lecture['id'].'</td><td>'.$lecture['cl_filename'].'</td><td>'.$lecture['cl_org_file_name'].'</td>';
                        if($cl_file_available_in_s3 == '0')
                        {
                            $html .= '<td>Not Calculated</td>';
                        }
                        else
                        {
                            $html .= '<td>'.(($cl_file_available_in_s3=="no"?"<a target='_blank' href='".$link."'>No</a>":"<a target='_blank' href='".$link."'>Yes</a>")).'</td>';
                        }
                        $html .= '<td>Relaunch Conversion</td></tr>';
                        $sl_no++;
                    }
                }
            }
        }        
        $html .= '</table>';
        echo $html;
        // echo '<pre>'; print_r($courses);

    }

    function migrate_course_subscription_end_date()
    { die('debugmode only----');
        $subscription_ids = $this->db->query("SELECT `id` FROM `course_subscription` WHERE `cs_end_date` = '0000-00-00' AND `cs_course_validity_status` = '0'")->result_array();
        if(!empty($subscription_ids))
        { //echo '<pre>';print_r($subscription_ids);die;
            foreach($subscription_ids as $subscription_id)
            {
                $this->db->query("UPDATE `course_subscription` SET `cs_end_date`= '2070-12-31' WHERE `id` = $subscription_id[id]");
            }
        }
    }
}