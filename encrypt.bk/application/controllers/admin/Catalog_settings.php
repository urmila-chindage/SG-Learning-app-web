<?php
class Catalog_settings extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $redirect	= $this->auth->is_logged_in(false, false);
        if (!$redirect)
        {
            redirect('login');
        }
        $this->actions        = $this->config->item('actions');
        $this->load->model(array('Catalog_model', 'Category_model', 'Course_model', 'User_model'));
        $this->lang->load('catalog_settings');
    }

    function index()
    {
        redirect(admin_url('catalog'));
    }
    
    
    function upload_catalog_image_to_localserver()
    {
        $catalog_id             = $this->input->post('id');
        $directory              = catalog_upload_path();
        if(!file_exists($directory)){
            mkdir($directory, 0777, true);
        }
        $config                     = array();
        $config['upload_path']      =  $directory;
        $config['allowed_types']    = 'gif|jpg|png|jpeg';
        $config['encrypt_name']     = true;
        $this->load->library('upload');
        $this->upload->initialize($config);
        $this->upload->do_upload('file');
        $uploaded_data = $this->upload->data();
        
        $save                   = array();
        $save['id']             = $catalog_id;
        $save['c_image']        = $uploaded_data['raw_name'].'.jpg'."?v=".rand(10,1000);
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['action_id']      = $this->actions['create'];
        $save['updated_date']   = date('Y-m-d H:i:s');
        $this->Catalog_model->save($save);
        
        $config                     = array();
        $config['uploaded_data']    = $uploaded_data;

        //convert to given size and return orginal name
        $config['width']            = 450;
        $config['height']           = 255;
        $config['orginal_name']     = true;
        $new_file                   = $this->crop_image($config);//orginal name
        
        $config['width']            = 300;
        $config['height']           = 160;
        $config['orginal_name']     = false;
        $new_file_medium            = $this->crop_image($config);
        
        $config['width']            = 85;
        $config['height']           = 85;
        $config['orginal_name']     = false;
        $new_file_small             = $this->crop_image($config);
        
        $has_s3     = $this->settings->setting('has_s3');
        if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
        {
            uploadToS3(catalog_upload_path().$new_file, catalog_upload_path().$new_file);
            uploadToS3(catalog_upload_path().$new_file_medium, catalog_upload_path().$new_file_medium);
            uploadToS3(catalog_upload_path().$new_file_small, catalog_upload_path().$new_file_small);
        }
        
        echo json_encode(array('catalog_image' => catalog_path().$new_file_medium));
    }
     
    function crop_image($config)
    {
        $uploaded_data  = isset($config['uploaded_data'])?$config['uploaded_data']:array();
        $width          = isset($config['width'])?$config['width']:360;
        $height         = isset($config['height'])?$config['height']:160;
        $orginal_name   = isset($config['orginal_name'])?$config['orginal_name']:false;

        $source_path    = $uploaded_data['full_path'];
        
        $DESIRED_IMAGE_WIDTH  = $width;
        $DESIRED_IMAGE_HEIGHT = $height;
        /*
         * Add file validation code here
         */

        list($source_width, $source_height, $source_type) = getimagesize($source_path);

        switch ($source_type)
        {
            case IMAGETYPE_GIF:
                $source_gdim = imagecreatefromgif($source_path);
                break;
            case IMAGETYPE_JPEG:
                $source_gdim = imagecreatefromjpeg($source_path);
                break;
            case IMAGETYPE_PNG:
                $source_gdim = imagecreatefrompng($source_path);
                break;
        }

        $source_aspect_ratio = $source_width / $source_height;
        $desired_aspect_ratio = $DESIRED_IMAGE_WIDTH / $DESIRED_IMAGE_HEIGHT;

        if ($source_aspect_ratio > $desired_aspect_ratio)
        {
            /*
             * Triggered when source image is wider
             */
            $temp_height = $DESIRED_IMAGE_HEIGHT;
            $temp_width = ( int ) ($DESIRED_IMAGE_HEIGHT * $source_aspect_ratio);
        } else {
            /*
             * Triggered otherwise (i.e. source image is similar or taller)
             */
            $temp_width = $DESIRED_IMAGE_WIDTH;
            $temp_height = ( int ) ($DESIRED_IMAGE_WIDTH / $source_aspect_ratio);
        }

        /*
         * Resize the image into a temporary GD image
         */

        $temp_gdim = imagecreatetruecolor($temp_width, $temp_height);
        imagecopyresampled(
            $temp_gdim,
            $source_gdim,
            0, 0,
            0, 0,
            $temp_width, $temp_height,
            $source_width, $source_height
        );

        /*
         * Copy cropped region from temporary image into the desired GD image
         */

        $x0 = ($temp_width - $DESIRED_IMAGE_WIDTH) / 2;
        $y0 = ($temp_height - $DESIRED_IMAGE_HEIGHT) / 2;
        $desired_gdim = imagecreatetruecolor($DESIRED_IMAGE_WIDTH, $DESIRED_IMAGE_HEIGHT);
        imagecopy(
            $desired_gdim,
            $temp_gdim,
            0, 0,
            $x0, $y0,
            $DESIRED_IMAGE_WIDTH, $DESIRED_IMAGE_HEIGHT
        );

        /*
         * Render the image
         * Alternatively, you can save the image in file-system or database
         */

        //header('Content-type: image/jpeg');
        $directory          = catalog_upload_path();
        $this->make_directory($directory);
        if($orginal_name)
        {
            $final_file = $uploaded_data['raw_name'].'.jpg';                    
        }
        else
        {
            $final_file = $uploaded_data['raw_name'].'_'.$width.'x'.$height.'.jpg';        
        }
        imagejpeg($desired_gdim, $directory.$final_file);

        /*
         * Add clean-up code here
         */
        //return $uploaded_data['raw_name'].'.jpg';
        return $final_file;
    }
    
    private function make_directory($path=false)
    {
        if(!$path )
        {
            return false;
        }
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }
    
    function save_s3_responseURL()
    {
        $updated_date           = date('Y-m-d H:i:s');
        $catalog_id             = $this->input->post('id');
        $catalog_img_url        = $this->input->post('filepath');
        
        $encode_url             = urldecode($catalog_img_url);
        $url_keys               = parse_url($encode_url); // parse the url
        $url_path               = explode("/", $url_keys['path']); // splitting the path
        $catalog_img_name        = end($url_path); // get the value of the last element 
        
        $save['id']             = $catalog_id;
        $save['c_image']        = $catalog_img_name."?v=".rand(10,1000);;
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['action_id']      = $this->actions['create'];
        $save['updated_date']   = $updated_date;
        
        
        $this->Catalog_model->save($save);
        $this->session->set_flashdata('message', lang('catalog_image_saved')); 
    }
    
    function get_courses() {
        $data = array();
        $data['courses'] = $this->Course_model->courses(array('direction'=>'DESC', 'status' => '1'));
        echo json_encode($data);
    }
    
    function get_course_details() {
        $data               = array();
        $data['course_ids'] = $this->input->post('course');
        $data['courses']    = array();
        foreach ($data['course_ids'] as $course_id){
            $course             = $this->Course_model->course(array( 'status' => '1', 'id' => $course_id));
            if(!empty($course))
            {
                $data['courses'][]  = $course;
            }
        }
        echo json_encode($data);
    }
    
    function s3objetcs()
    {
        $response               = array();
      
        $response['error']      = true;
        $response['s3_object']  = array();
        $s3_setting             = $this->settings->setting('has_s3');
        if( $s3_setting['as_superadmin_value'] && $s3_setting['as_siteadmin_value'] )
        {
            $response['error']      = false;
            $s3_keys                = $s3_setting['as_setting_value']['setting_value'];

            define('S3_BUCKET', $s3_keys->s3_bucket);
            define('S3_KEY',    $s3_keys->s3_access);
            define('S3_SECRET', $s3_keys->s3_secret);
            define('S3_ACL',    'private');

            $now            = time() + (12 * 60 * 60 * 1000);
            $expire         = gmdate('Y-m-d\TH:i:s\Z', $now);
            $url            = 'https://' . S3_BUCKET . '.s3.amazonaws.com'; 
            $policy         = json_encode(  
                                    array(
                                        'expiration' => '2020-01-01T00:00:00Z',
                                        'conditions' => array(
                                            array( 'bucket' => S3_BUCKET ),
                                            array( 'acl' => S3_ACL ), 
                                            array( 'starts-with', '$key', '',),
                                            array( "success_action_status" => "201" )
                                        )
                                    )
                                  );  
            $base64Policy   = base64_encode($policy);
            $signature      = base64_encode(hash_hmac("sha1", $base64Policy, S3_SECRET, $raw_output = true));
            
            $upload_path    =  catalog_upload_path();
            if(!file_exists($upload_path)){
                mkdir($upload_path, 0777, true);
            }
            
            $response['s3_object']['upload_path']               = $upload_path;
            $response['s3_object']['url']                       = $url;
            $response['s3_object']['access_key']                = S3_KEY;
            $response['s3_object']['acl']                       = S3_ACL;
            $response['s3_object']['success_action_status']     = '201';
            $response['s3_object']['policy']                    = $base64Policy;
            $response['s3_object']['signature']                 = $signature;
            $response['catalog_path']                           = catalog_upload_path();
            
        }
        echo json_encode($response);
    }
    
    function basics($id = false)
    {
        $updated_date       = date('Y-m-d H:i:s'); 
        $data               = array();
        $data['title']      = lang('catalog_bar_trainings');
        $data['catalogs']   = $this->Catalog_model->catalogs(array('direction'=>'DESC'));
        
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['id']                    = '';
        $data['c_title']               = $this->input->post('c_title');
        $data['c_description']         = $this->input->post('c_description');
        $data['c_position']            = $this->input->post('c_position');
        $data['c_category']            = $this->input->post('c_category');
        $data['c_is_free']             = $this->input->post('c_is_free');
        $data['c_price']               = $this->input->post('c_price');
        $data['c_discount']            = $this->input->post('c_discount');
        if($id)
        {
            $catalog_basic             = $this->Catalog_model->catalog(array('id' => $id));
            if(!$catalog_basic){
                redirect($this->config->item('admin_folder').'/catalog');
            }
            
            if($catalog_basic['c_deleted'] == 1){
                redirect($this->config->item('admin_folder').'/catalog');
            }
            
            $data['id']                 = $catalog_basic['id'];
            $data['c_title']            = $catalog_basic['c_title'];
            $data['c_description']      = $catalog_basic['c_description'];
            $data['c_position']         = $catalog_basic['c_position'];
            $category_name              = $this->Category_model->category(array('id'=>$catalog_basic['c_category']));
            $data['c_category']         = $category_name['ct_name'];
            $data['c_image']            = $catalog_basic['c_image'];
            $data['c_is_free']          = $catalog_basic['c_is_free'];
            $data['c_courses']          = $catalog_basic['c_courses'];
            $data['c_price']            = $catalog_basic['c_price'];
            $data['c_discount']         = $catalog_basic['c_discount'];
            $data['c_status']           = $catalog_basic['c_status'];
        }else{
            redirect($this->config->item('admin_folder').'/catalog');
        }
        
        $breadcrumb         = array();
        $breadcrumb[]       = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]       = array( 'label' => lang('catalog_bar_trainings'), 'link' => admin_url('catalog'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]       = array( 'label' => $catalog_basic['c_title'], 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb'] = $breadcrumb;
        
        $this->form_validation->set_rules('c_title', 'Catalog Title','required');
        $this->form_validation->set_rules('c_description', 'Catalog Description','required');
        
        if ($this->form_validation->run() == FALSE)
        {
            $data['errors'] = validation_errors(); 
            $this->load->view($this->config->item('admin_folder').'/catalog_basics',$data);
        }
       
        else
        {
            $save['id']                     = $id;

            $save['c_title']               = str_replace(array("\"", "&quot;", "<", ">", "{", "}"), "", htmlspecialchars($this->input->post('c_title')));
            $save['c_title']               = ltrim($save['c_title']," ");

            //$save['c_title']                = ltrim($this->input->post('c_title')," ");

            $save['c_description']          = $this->input->post('c_description');
            $save['c_position']             = $this->input->post('c_position');
            
            $category                       = $this->Category_model->category(array('category_name'=>$this->input->post('c_category')));
            if(!$category)
            {                
                //configuring the slug for new category
                $this->load->helper('text');
                $slug   = $this->input->post('c_category');;
                $slug	= url_title(convert_accented_characters($slug), 'dash', TRUE);
                $this->load->model('Routes_model');

                $slug           = $this->Routes_model->validate_slug($slug);
                $route['slug']	= $slug;	
                $route_id       = $this->Routes_model->save($route);
                //End
                
                $category                   = array();
                $category['id']             = false;
                $category['ct_name']        = $this->input->post('c_category');
                $category['ct_status']      = '1';
                $category['ct_account_id']  = $this->config->item('id');
                $category['action_by']      = $this->auth->get_current_admin('id');
                $category['action_id']      = $this->actions['create'];
                $category['ct_route_id']    = $route_id;
                $category['ct_slug']        = $slug;
                $category['id']             = $this->Category_model->save($category);
                
                $route['id']            = $route_id;
                $route['slug']          = $slug;
                $route['route']         = 'category/view/'.$category['id'];
                $route['r_account_id']	= $this->config->item('id');
                $route['r_item_type']   = 'category';
                $route['r_item_id']     = $category['id'];
                $this->Routes_model->save($route);
            }
            $save['c_category']             = $category['id'];
            $save['c_is_free']              = $this->input->post('c_is_free');
            $save['c_courses']              = implode(',',json_decode($this->input->post('c_courses')));
            $save['c_price']                = ($this->input->post('c_is_free') == '1')?'0':$this->input->post('c_price'); 
            $save['c_discount']             = ($this->input->post('c_is_free') == '1')?'0':$this->input->post('c_discount');
            $save['action_by']              = $this->auth->get_current_admin('id');
            $save['action_id']              = $this->actions['update'];
            $save['updated_date']           = $updated_date;
            $save['c_account_id']           = config_item('id');


            $this->Catalog_model->save($save);
            $this->session->set_flashdata('message', lang('catalog_basics_saved'));
            redirect($this->config->item('admin_folder').'/catalog_settings/basics/'.$id);
        }
    }
    
    function advanced($id = false)
    {
        $updated_date       = date('Y-m-d H:i:s');
        $data               = array();
        $data['title']      = lang('catalog_bar_trainings');
        $data['catalogs']   = $this->Catalog_model->catalogs(array('direction'=>'DESC'));
        
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['id']                     = '';
        $data['c_promo']               = $this->input->post('c_promo');
        if($id)
        {
            $catalog_basic               = $this->Catalog_model->catalog(array('id' => $id));
            if(!$catalog_basic){
                redirect($this->config->item('admin_folder').'/catalog');
            }
            
            if($catalog_basic['c_deleted'] == 1){
                redirect($this->config->item('admin_folder').'/catalog');
            }
            
            $data['id']                 = $catalog_basic['id'];
            $data['c_image']            = $catalog_basic['c_image'];
            $courses_list               = $catalog_basic['c_courses'];
            $catalog_courses            = explode(',', $courses_list);
            $data['c_course_count']     = count($catalog_courses);
            $data['c_promo']            = $catalog_basic['c_promo'];
            $data['c_title']            = $catalog_basic['c_title'];
            $data['c_price']            = $catalog_basic['c_price'];
            $data['c_discount']         = $catalog_basic['c_discount'];
        }else{
            redirect($this->config->item('admin_folder').'/catalog');
        }
        
        $breadcrumb         = array();
        $breadcrumb[]       = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]       = array( 'label' => lang('catalog_bar_trainings'), 'link' => admin_url('catalog'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]       = array( 'label' => $catalog_basic['c_title'], 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb'] = $breadcrumb;
        
        $this->form_validation->set_rules('c_promo', 'Course Promo','required');

        if ($this->form_validation->run() == FALSE)
        {
            $data['errors'] = validation_errors(); 
            $this->load->view($this->config->item('admin_folder').'/catalog_advanced',$data);
        }
        else
        {
            $save['id']                     = $id;
            $save['c_promo']                = $this->input->post('c_promo');
            $save['action_by']              = $this->auth->get_current_admin('id');
            $save['action_id']              = $this->actions['update'];
            $save['updated_date']           = $updated_date;
            $save['c_account_id']           = config_item('id');
           
            
            $this->Catalog_model->save($save);
            $this->session->set_flashdata('message', lang('catalog_advanced_saved'));
            redirect($this->config->item('admin_folder').'/catalog_settings/advanced/'.$id);
        }
    }
    
    function seo($id = false)
    {
        $updated_date       = date('Y-m-d H:i:s');
        $data               = array();
        $data['title']      = lang('catalog_bar_trainings');
        $data['courses']    = $this->Catalog_model->catalogs(array('direction'=>'DESC'));
        
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['id']                     = '';
        $data['c_slug']                 = $this->input->post('c_slug');
        $data['c_meta']                 = $this->input->post('c_meta');
        $data['c_title']                = $this->input->post('c_title');
        $data['c_meta_description']     = $this->input->post('c_meta_description');
        if($id)
        {
            $catalog_basic               = $this->Catalog_model->catalog(array('id' => $id));
            if(!$catalog_basic){
                redirect($this->config->item('admin_folder').'/catalog');
            }
            
            $data['id']                 = $catalog_basic['id'];
            $data['c_slug']             = $catalog_basic['c_slug'];
            $courses_list               = $catalog_basic['c_courses'];
            $catalog_courses            = explode(',', $courses_list);
            $data['c_course_count']     = count($catalog_courses);
            $data['c_meta']             = $catalog_basic['c_meta'];
            $data['c_title']            = $catalog_basic['c_title'];
            $data['c_image']            = $catalog_basic['c_image'];
            $data['c_price']            = $catalog_basic['c_price'];
            $data['c_discount']         = $catalog_basic['c_discount'];
            $data['c_meta_description'] = $catalog_basic['c_meta_description'];
        }else{
            redirect($this->config->item('admin_folder').'/catalog');
        }
        $breadcrumb         = array();
        $breadcrumb[]       = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]       = array( 'label' => lang('catalog_bar_trainings'), 'link' => admin_url('catalog'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]       = array( 'label' => $catalog_basic['c_title'], 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb'] = $breadcrumb;
        $this->form_validation->set_rules('c_slug', 'Friendly URL','required');

        if ($this->form_validation->run() == FALSE)
        {
            $data['errors'] = validation_errors(); 
            $this->load->view($this->config->item('admin_folder').'/catalog_seo',$data);
        }
        else
        {
            
            $this->load->helper('text');
            //first check the slug field
            $slug = $this->input->post('c_slug');

            //if it's empty assign the name field
            if(empty($slug) || $slug=='')
            {
                $slug = $this->input->post('c_title');
            }
            $slug	= url_title(convert_accented_characters($slug), 'dash', TRUE);
            $this->load->model('Routes_model');
            $route_id	= $catalog_basic['c_route_id'];
            $slug	= $this->Routes_model->validate_slug($slug, $route_id);

            $save['id']                     = $id;
            $save['c_slug']                 = $slug;
            $save['c_meta']                 = $this->input->post('c_meta');
            //$save['c_title']                = $this->input->post('c_title');
            $save['c_meta_description']     = $this->input->post('c_meta_description');
            $save['c_route_id']             = $route_id;
            $save['action_by']              = $this->auth->get_current_admin('id');
            $save['action_id']              = $this->actions['update'];
            $save['updated_date']           = $updated_date;
            $save['c_account_id']           = config_item('id');
            $this->Catalog_model->save($save);
            
            $route['id']	        = $route_id;
            $route['slug']	        = $slug;
            $route['route']	        = 'catalog/view/'.$id;
            $route['r_item_type']   = 'bundle';
            $route['r_item_id']     = $id;
            $this->Routes_model->save($route);
            
            $this->session->set_flashdata('message', lang('catalog_seo_saved'));
            redirect($this->config->item('admin_folder').'/catalog_settings/seo/'.$id);
        }
    }
    
    function change_status()
    {
        $response               = array();
        $response['error']      = false;
        $message                = '';
        $catalog_id             = $this->input->post('catalog_id');
        $catalog                = $this->Catalog_model->catalog(array('id' => $catalog_id));
        
        if($catalog['c_image'] == 'default.jpg' && $catalog['c_status'] == '0')
        {
            $response['error']   = true;
            $message .= lang('upload_image_to_publish').'<br>';
            
        }
        if($catalog['c_description'] == '')
        {
            $response['error']   = true;
            $message .= 'Please enter catalog description<br>';
        }
        if($catalog['c_category'] == 0)
        {
            $response['error']   = true;
            $message .= 'Please enter catalog category<br>';
        }
        if($catalog['c_promo'] == '')
        {
            $response['error']   = true;
            $message .= 'Please enter Youtube video URL<br>';
        }
        
        if($response['error'] == true){
            $response['message'] = $message;
            echo json_encode($response);exit;
        }

        $save                   = array();
        $save['id']             = $catalog_id;
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['updated_date']   = date('Y-m-d H:i:s');
        $save['c_status']       = '1';
        
        $save['action_id']      = $this->actions['activate'];
        $response['message']    = lang('activated');
                     
        $action_label   = $this->actions[$this->actions['activate']]['label'];
        $button_text    = lang('deactivate');

        $action_list    = '<a href="javascript:void(0);" data-target="#publish-course" class="btn pull-right label label-warning" data-toggle="modal" onclick="changeCatalogStatus(\''.$catalog['id'].'\', \''.lang('deactivate').' '.$catalog['c_title'].' '.  lang('catalog').'\', \''.lang('change_status_message').' '.lang('deactivate').'\')">'.strtoupper(lang('deactivate')).'</a>';
        if($catalog['c_status'])
        {
            $action_list    = '<a href="javascript:void(0);" data-target="#publish-course" class="btn pull-right label label-success" data-toggle="modal" onclick="changeCatalogStatus(\''.$catalog['id'].'\', \''.lang('activate').' '.$catalog['c_title'].' '.  lang('catalog').'\', \''.lang('change_status_message').' '.lang('activate').'\')">'.strtoupper(lang('activate')).'</a>';
            $action_label   = $this->actions[$this->actions['deactivate']]['label'];
            $button_text    = lang('activate');
            
            $save['c_status']       = '0';
            $save['action_id']      = $this->actions['deactivate'];
            $response['message']    = lang('deactivated');
        }
        
        //set the database value
        $action_date    = date("d M Y", strtotime($save['updated_date']));
        $action_author  = $this->auth->get_current_admin('us_name');
        $action_author  = ($action_author)?$action_author:'Admin';

        //consider the record is deleted and set the value if record deleted
        $label_class    = 'spn-delete';
        $action_class   = 'label-danger';
        $action         = lang('deleted');
        //case if record is not deleted
        if($catalog['c_deleted'] == 0)
        {
            if($catalog['c_status'] == 0)
            {
                $action_class   = 'label-success';                                                                
                $label_class    = 'spn-active';                                        
                $action         = lang('active');
            }
            else
            {
                $action_class   = 'label-warning';                                                                
                $label_class    = 'spn-inactive';                                        
                $action         = lang('inactive');
            }
        }
        else
        {
            $action_label = $this->actions[$this->actions['delete']]['label'];
            unset($save['action_id']);
            unset($save['updated_date']);
            unset($save['action_by']);
        }
        
        if(!$this->Catalog_model->save($save))
        {
            $response['error']   = true;
            $response['message'] = lang('error_change_status');
        }

        $response['actions']                   = array();
        $response['actions']['action_label']   = $action_label;
        $response['actions']['action']         = $action;
        $response['actions']['action_date']    = $action_date;
        $response['actions']['action_author']  = $action_author;
        $response['actions']['label_class']    = $label_class;
        $response['actions']['action_class']   = $action_class;
        $response['actions']['button_text']    = $button_text;
        $response['actions']['label_text']     = $action_label.' by- '.$action_author.' on '.$action_date;
        
        $response['action_list'] = $action_list;        
        echo json_encode($response);
    }
    
    function get_category_list()
    { 
        $data 	 		= array();
        $keyword 		= $this->input->post('c_category');
        $categories		= $this->Category_model->categories(array('name'=>$keyword));
        $data['tags']           = array();
        if( sizeof($categories))
        {
            foreach( $categories as $category)
            {
                $category['name'] = $category['ct_name'];
                $data['tags'][]   = $category;
            }
        }
        echo json_encode($data);
    }
    
    function language()
    {
        $response = array();
        $response['language'] = array();
        $response['language'] = get_instance()->lang->language;
        echo json_encode($response);
    }

}