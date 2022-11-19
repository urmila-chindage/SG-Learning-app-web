<?php
class Live_service extends CI_Controller
{
    private $__response;
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
        $redirect               = $this->auth->is_logged_in(false, false, 'user');
        $this->__admin_index    = 'admin';
        $this->__loggedInUser   = $this->auth->get_current_user_session('admin');
        
        $this->__response   = array('message' => '', 'error' => false);
        $this->actions      = $this->config->item('actions');
        $this->load->model(array('Liveservice_model'));
    }
    
    function index()
    {
        //echo '<pre>'; print_r($this->__response);die;
    }
    function docConversion()
    {
    	$LIBREOFFICE_PATH = config_item('libre_office');  //"/usr/bin/libreoffice";
        $tempFile       =   $_FILES['Filedata']['tmp_name'];
        $fileName       =   $_FILES['Filedata']['name'];
        $fileSize       =   $_FILES['Filedata']['size'];
        $destination        = $this->config->item('upload_folder').'/'.$this->config->item('acct_domain').'/live_uploaded_docs/';

        $exp_arr       = explode('.', $fileName);
        $extension     =  $exp_arr[count($exp_arr)-1];
        $new_file_name =  uniqid();

        if(!file_exists($destination)){
            mkdir($destination, 0777, true);
        }
        move_uploaded_file($tempFile, $destination.$new_file_name.'.'.$extension);
        if($extension != 'pdf'){
            $convert_script = $LIBREOFFICE_PATH.' --headless --convert-to pdf --outdir '.$destination.' '.$destination.$new_file_name.'.'.$extension;
            $output         = shell_exec($convert_script);
        }
        
       $shell_swf = 'export HOME=/tmp && /usr/local/bin/pdf2swf -T9 --flatten -s jpegquality=80 -s subpixels=1 -s insertstop '.$destination.$new_file_name.'.pdf -o '.$destination.$new_file_name.'.swf';
        shell_exec($shell_swf);
       
        echo  site_url().$this->config->item('upload_folder').'/'.$this->config->item('acct_domain').'/live_uploaded_docs/'.$new_file_name.'.swf';
        $this->create_preview_images($new_file_name,$destination);
    }
    function create_preview_images($new_file_name,$destination) {
 
		mkdir($destination.$new_file_name);
		$density = 100;
		$quality = 90;
		shell_exec('convert -density '.$density.' -quality '.$quality.' -background white -alpha remove '.$destination.$new_file_name.'.pdf -thumbnail 140x80 '.$destination.$new_file_name.'/%d.jpg');
    }

    function UpdateOnlineStatus($liveid_status=false)
    {
        if(!$liveid_status)
        {
            $this->__response['message'] = 'LiveId and status missing<br />';
            $this->__response['error']   = true;
            $this->json_response_and_halt();
        }
        $liveid_status  = explode('_', $liveid_status);
        $live_id        = isset($liveid_status[0])?$liveid_status[0]:false;
        $status         = isset($liveid_status[1])?$liveid_status[1]:'';

        $error_message  = '';
        if(!$live_id)
        {
            $error_message .= 'Live id missing <br />';
        }
        if($status == '')
        {
            $error_message .= 'Status missing <br />';
        }
        if($error_message != '' )
        {
            $this->__response['message'] = $error_message;
            $this->__response['error']   = true;
            $this->json_response_and_halt();            
        }
        
        $save                   = array();
        $save['id']             = $live_id;
        $save['ll_is_online']   = $status;
        
        $this->Liveservice_model->save_live($save);
        $this->__response['message'] = 'Live status updated successfully';
        $this->__response['error']   = false;
        $this->json_response_and_halt();            
    }
    
    function SaveRecordDetails()
    {
        $title      = $this->input->post('tittle');
        $live_id    = $this->input->post('session_id');
        $course_id  = $this->input->post('course_id');
        $clip_id    = $this->input->post('video_name');
        $live_type  = $this->input->post('type');
        $live_type  = ($live_type == '1')?'1':'2';
        
        $error_message  = '';
        if(!$title)
        {
            $error_message .= 'Title missing <br />';
        }
        if(!$live_id)
        {
            $error_message .= 'Live Id missing <br />';
        }
        if(!$course_id)
        {
            $error_message .= 'Course Id missing <br />';
        }
        if(!$clip_id)
        {
            $error_message .= 'Clip Id missing <br />';
        }
        if(!$live_type)
        {
            $error_message .= 'Live Type missing <br />';
        }
        if($error_message != '' )
        {
            $this->__response['message'] = $error_message;
            $this->__response['error']   = true;
            $this->json_response_and_halt();            
        }
        
        $save                        = array();
        $save['id']                  = false;
        $save['llr_live_id']         = $live_id;
        $save['llr_course_id']       = $course_id;
        $save['llr_title']           = $title;
        $save['llr_clip_id']         = $clip_id;
        $save['llr_type']            = $live_type;
        $this->__response['message'] = 'Live recording created successfully';
        $recording                   = $this->Liveservice_model->live_lecture_recording(array('clip_id' => $clip_id, 'live_id' => $live_id));
        if($recording)
        {
            $save['id']                     = $recording['id'];
            $this->__response['message']    = 'Live recording details updated successfully';
        }
        //echo '<pre>';print_r($save);  die;
        $this->Liveservice_model->save_live_recording($save);
        $this->__response['error']   = false;
        $this->json_response_and_halt();            
    }
    
    function SavePresentation()
    {

        $user_name  = $this->input->post('user_name');
        $file_name  = $this->input->post('file_name');
        $file_type  = $this->input->post('file_type');
        $swf_url    = $this->input->post('swf_url');
        $live_id    = $this->input->post('live_id');
        
        $error_message  = '';
        if(!$user_name)
        {
            $error_message .= 'Username missing <br />';
        }
        if(!$file_name)
        {
            $error_message .= 'Filename missing <br />';
        }
        if(!$file_type)
        {
            $error_message .= 'File type missing <br />';
        }
        if(!$swf_url)
        {
            $error_message .= 'SWF Url missing <br />';
        }
        if(!$live_id)
        {
            $error_message .= 'Live Id missing <br />';
        }
        else
        {
            $live  = $this->Liveservice_model->live(array('id' => $live_id));
            if(!$live)
            {
                $error_message .= 'Invalid Live Id <br />';
            }
        }
        if($error_message != '' )
        {
            $this->__response['message'] = $error_message;
            $this->__response['error']   = true;
            $this->json_response_and_halt();            
        }
        
        $save                        = array();
        $save['id']                  = false;
        $save['lpd_user_name']       = $user_name;
        $save['lpd_file_name']       = $file_name."?v=".rand(10,1000);
        $save['lpd_swf_url']         = $swf_url;
        $save['lpd_live_id']         = $live_id;
        $save['lpd_course_id']       = $live['ll_course_id'];
        $this->Liveservice_model->save_live_presentation($save);
        $this->__response['error']   = false;
        $this->__response['message'] = 'Presentation saved successfully';
        $this->json_response_and_halt();            
    }
    
    function SaveLiveUsers()
    {
        $live_id    = $this->input->post('live_id');
        $user_id    = $this->input->post('user_id');
        
        $error_message  = '';
        if(!$user_id)
        {
            $error_message .= 'User ID missing <br />';
        }
        if(!$live_id)
        {
            $error_message .= 'Live Id missing <br />';
        }
        else
        {
            $live  = $this->Liveservice_model->live(array('id' => $live_id));
            if(!$live)
            {
                $error_message .= 'Invalid Live Id <br />';
            }
        }
        if($error_message != '' )
        {
            $this->__response['message'] = $error_message;
            $this->__response['error']   = true;
            $this->json_response_and_halt();            
        }
        
        $user_already_join = $this->Liveservice_model->get_live_user(array('user_id'=> $user_id, 'live_id' => $live_id));
        
        $save                        = array();
        $save['id']                  = ($user_already_join['id'])?$user_already_join['id']:false;
        $save['llu_user_id']       = $user_id;
        $save['llu_live_id']       = $live_id;
        $this->Liveservice_model->save_live_users($save);
        $this->__response['error']   = false;
        $this->__response['message'] = 'Users list saved successfully';
        $this->json_response_and_halt(); 
    }
    
    function GetPresentation()
    {
        $live_id        = $this->input->post('live_id');
        $error_message  = '';
        if(!$live_id)
        {
            $error_message .= 'Live id missing <br />';
        }
        if($error_message != '' )
        {
            $this->__response['message'] = $error_message;
            $this->__response['error']   = true;
            $this->json_response_and_halt();            
        }
        $result = $this->Liveservice_model->live_presentations(array('live_id' => $live_id));
        $resultsXML = "<root>";
        foreach ($result as &$value) {
		    //$value = $value['id'];
		    $value['type'] = 'pdf';
		    $resultsXML .= "<presentation><name>".$value['lpd_file_name']."</name><type>".$value['type']."</type><url>".$value['lpd_swf_url']."</url><date>".$value['created_date']."</date><id>".$value['id']."</id></presentation>";
		    
		}
        $resultsXML .= "</root>";
        echo $resultsXML;
        die;             
    }
    
    function DeletePresentation()
    {
        $presentation_id = $this->input->post('id');
        $error_message   = '';
        if(!$presentation_id)
        {
            $error_message .= 'Presentation id missing <br />';
        }
        if($error_message != '' )
        {
            $this->__response['message'] = $error_message;
            $this->__response['error']   = true;
            $this->json_response_and_halt();            
        }
        $this->Liveservice_model->delete_presentation($presentation_id);
        $this->__response['error']          = false;
        $this->__response['message']        = 'Presentation deleted successfully';
        $this->json_response_and_halt();                    
    }
    
    function updatetimejson()
    {
        //recieve input from client
        $record_id      = $this->input->post('record_Id');
        $live_id        = $this->input->post('live_id');
        $json_string    = $this->input->post('json_String');
        //validation
        $error_message   = '';
        if(!$record_id)
        {
            $error_message .= 'Record id missing <br />';
        }
        if(!$json_string)
        {
            $error_message .= 'Json String missing <br />';
        }
        if($error_message != '' )
        {
            $this->__response['message'] = $error_message;
            $this->__response['error']   = true;
            $this->json_response_and_halt();            
        }
        
        //configurations
        $destination        = $this->config->item('upload_folder').'/'.$this->config->item('acct_domain').'/live_recording_json/';
        $file_name          = 'time_'.$record_id.'.json';
        $file_with_path     = $destination.$file_name;
        
        //check folder exists. If not create folder
        if(!file_exists($destination)){
            mkdir($destination, 0777, true);
        }

        //create file if not exists
        if(!file_exists($file_with_path))
        {
            fopen($file_with_path, 'w');
            chmod($file_with_path, 0777); 
        }
        
        //Write the new json value in json file.
        $json_file = fopen($file_with_path, "w");
        $content = json_encode($json_string);
        fwrite($json_file, $content);
        fclose($json_file);
        $this->__response['error']          = false;
        $this->__response['message']        = 'Time json saved successfully';
        $this->json_response_and_halt();                    
    }
    
    function updateobjectjson()
    {
        //recieve input from client
        $record_id      = $this->input->post('record_Id');
        $live_id        = $this->input->post('live_id');
        $json_string    = $this->input->post('json_String');
        
        //validation
        $error_message   = '';
        if(!$record_id)
        {
            $error_message .= 'Record id missing <br />';
        }
        if(!$json_string)
        {
            $error_message .= 'Json String missing <br />';
        }
        if($error_message != '' )
        {
            $this->__response['message'] = $error_message;
            $this->__response['error']   = true;
            $this->json_response_and_halt();            
        }

        //configurations
        $destination        = $this->config->item('upload_folder').'/'.$this->config->item('acct_domain').'/live_recording_json/';
        $file_name          = 'object_'.$record_id.'.json';
        $file_with_path     = $destination.$file_name;
        
        //check folder exists. If not create folder
        if(!file_exists($destination)){
            mkdir($destination, 0777, true);
        }

        //create file if not exists
        if(!file_exists($file_with_path))
        {
            fopen($file_with_path, 'w');
            chmod($file_with_path, 0777); 
        }
        
        //Write the new json value in json file.
        $json_file = fopen($file_with_path, "w");
        $content = json_encode($json_string);
        fwrite($json_file, $content);
        fclose($json_file);
        $this->__response['error']          = false;
        $this->__response['message']        = 'Object json saved successfully';
        $this->json_response_and_halt();                    
    }
    
    function retrive()
    {
        $record_id      = $this->input->post('record_id');
        if(!$record_id)
        {
            $this->__response['error']    = true;
            $this->__response['message']  = 'Record id missing';
            $this->json_response_and_halt();                    
        }
        
        $destination        = $this->config->item('upload_folder').'/'.$this->config->item('acct_domain').'/live_recording_json/';
        $file_name          = 'time_'.$record_id.'.json';
        $file_with_path     = $destination.$file_name;
        if(!file_exists($file_with_path))
        {
            $this->__response['error']    = true;
            $this->__response['message']  = 'Invalid record Id';
            $this->json_response_and_halt();                    
        }
        $response      =  file_get_contents($file_with_path);
        $obj           =  json_decode($response);
        echo $obj;
        die();               
    }
    
    function retriveinitial()
    {
        $record_id      = $this->input->post('record_id');
        if(!$record_id)
        {
            $this->__response['error']    = true;
            $this->__response['message']  = 'Record id missing';
            $this->json_response_and_halt();                    
        }
        
        $destination        = $this->config->item('upload_folder').'/'.$this->config->item('acct_domain').'/live_recording_json/';
        $file_name          = 'object_'.$record_id.'.json';
        $file_with_path     = $destination.$file_name;
        if(!file_exists($file_with_path))
        {
            $this->__response['error']    = true;
            $this->__response['message']  = 'Invalid record Id';
            $this->json_response_and_halt();                    
        }
        $response      =  file_get_contents($file_with_path);
        $obj           =  json_decode($response);
        echo $obj;
        die();                     
    }
    
    function json_response_and_halt()
    {
        echo json_encode($this->__response);die;
    }
}