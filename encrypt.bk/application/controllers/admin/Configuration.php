<?php
class Configuration extends CI_Controller
{
    public function index()
    {
        $response                       = array();
        $response['default_user_path']  = default_user_path();
        $response['admin_url']          = admin_url();
        $response['uploads_url']        = uploads_url();
        $response['site_url']           = site_url();
        $response['assets_url']         = assets_url();

        $response['video_upload_path']      = video_upload_path();
        $response['document_upload_path']   = document_upload_path();
        $response['question_upload_path']   = question_upload_path();
        $response['course_upload_path']     = course_upload_path();
        $response['catalog_upload_path']    = catalog_upload_path();
        $response['user_upload_path']       = user_upload_path();
        $response['question_path']          = question_path();
        $response['video_path']             = video_path();
        $response['document_path']          = document_path();

        $response['course_path']            = course_path();
        $response['catalog_path']           = catalog_path();
        $response['user_path']              = user_path();
        $response['assignment_path']        = assignment_path();
        $response['default_video_path']     = default_video_path();
        $response['default_document_path']  = default_document_path();
        $response['default_question_path']  = default_question_path();
        $response['default_course_path']    = default_course_path();
        $response['default_catalog_path']   = default_catalog_path();
        $response['cisco_upload_path']      = cisco_upload_path();
        $response['cisco_path']             = cisco_path();
        $response['audio_upload_path']      = audio_upload_path();
        $response['audio_path']             = audio_path();

        $response['default_institute_path'] = default_institute_path();
        $response['institute_path']         = institute_path();
        echo json_encode($response);
    }
    
    function redactore_image_upload()
    {
        $directory                  = redactor_upload_path();
        $this->make_directory($directory);
        $files = array();
        $types = ['image/png', 'image/jpg', 'image/gif', 'image/jpeg', 'image/png'];

        $has_s3     = $this->settings->setting('has_s3');
        if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
        {
            if (isset($_FILES['file']))
            {
                foreach ($_FILES['file']['name'] as $key => $name)
                {
                    $type = strtolower($_FILES['file']['type'][$key]);
                    if (in_array($type, $types))
                    {
                        // setting file's mysterious name
                        $filename = md5(date('YmdHis')).'.jpg';
                        $path = $directory.$filename;

                        // copying
                        uploadToS3($_FILES['file']['tmp_name'][$key], $path);

                        $files['file-'.$key] = array(
                            'url' => redactor_path().$filename
                        );
                    }
                }
            }
            if(empty($files))
            {
                echo stripslashes(json_encode(array('error' => true, 'message' => 'This file type is not allowed. upload a jpg or png image.')));
            }
            else
            {
                echo stripslashes(json_encode($files));
            }
        }
        else
        {
            if (isset($_FILES['file']))
            {
                foreach ($_FILES['file']['name'] as $key => $name)
                {
                    $type = strtolower($_FILES['file']['type'][$key]);
                    if (in_array($type, $types))
                    {
                        // setting file's mysterious name
                        $filename = md5(date('YmdHis')).'.jpg';
                        $path = $directory.$filename;

                        // copying
                        move_uploaded_file($_FILES['file']['tmp_name'][$key], $path);

                        $files['file-'.$key] = array(
                            'url' => redactor_path().$filename
                        );
                    }
                }
            }
            if(empty($files))
            {
                echo stripslashes(json_encode(array('error' => true, 'message' => 'This file type is not allowed. upload JPG, JPEG or PNG file.')));
            }
            else
            {
                echo stripslashes(json_encode($files));
            }
        }
    }


    function redactore_file_upload()
    {
        $directory                  = redactor_upload_path();
        $this->make_directory($directory);
        $files = array();
        $types = ['xls', 'xlsx', 'doc', 'docx', 'odt', 'ods', 'odp','txt','pdf','pptx','ppt','rtf','avi','mov','mpg','mpeg','mp4','wmv','flv','f4v','mp3','wma','wav','acc','m4a','flac'];

        $has_s3     = $this->settings->setting('has_s3');
        if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
        {
            if (isset($_FILES['file']))
            {
                foreach ($_FILES['file']['name'] as $key => $name)
                {
                    $type = strtolower($_FILES['file']['type'][$key]);
                    if (in_array($type, $types))
                    {
                        // setting file's mysterious name
                        $filename = md5(date('YmdHis')).'.jpg';
                        $path = $directory.$filename;

                        // copying
                        uploadToS3($_FILES['file']['tmp_name'][$key], $path);

                        $files['filekey'] = array(
                            'url' => redactor_path().$filename,
                            'name' => $name, // optional
                            'id' => $key 
                        );
                    }
                }
            }
            echo stripslashes(json_encode($files));
        }
        else
        {
            if (isset($_FILES['file']))
            {
                foreach ($_FILES['file']['name'] as $key => $name)
                {
                    $type = pathinfo($name, PATHINFO_EXTENSION);
                    if (in_array($type, $types))
                    {
                        // setting file's mysterious name
                        $filename = md5(date('YmdHis')).'.'.$type;
                        $path = $directory.$filename;
    
                        // copying
                        move_uploaded_file($_FILES['file']['tmp_name'][$key], $path);
    
                        $files['filekey'] = array(
                            'url' => redactor_path().$filename,
                            'name' => $name, // optional
                            'id' => $key 
                        );
                        
                    }
                }
            }    
            echo stripslashes(json_encode($files));
        }
    }

    function assignment_redactor_file_upload($course_id = 0, $purpose = 'assignment')
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, x-api-key ');

        $param          = array('course_id' => $course_id, 'purpose' => $purpose);
        $directory      = assignment_upload_path($param);
        $this->make_directory($directory);
        $files = array();
        $types = ['xls', 'xlsx', 'doc', 'docx', 'odt', 'ods', 'odp','txt','pdf','pptx','ppt','rtf','avi','mov','mpg','mpeg','mp4','wmv','flv','f4v','mp3','wma','wav','acc','m4a','flac'];
        
        $has_s3     = $this->settings->setting('has_s3');
        if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
        {
            if (isset($_FILES['file']))
            {
                foreach ($_FILES['file']['name'] as $key => $name)
                {
                    $type = pathinfo($name, PATHINFO_EXTENSION);
                    if (in_array($type, $types))
                    {
                        // setting file's mysterious name
                        $random = md5(date('YmdHis').rand(1000, 9999));
                        $filename = $random.'.'.$type;
                        $path = $directory.$filename;
                        // copying
                        uploadToS3($_FILES['file']['tmp_name'][$key], $path);
                        // processing file name
                        $exploded_name  = explode('.', $name);
                        unset($exploded_name[sizeof($exploded_name)-1]);
                        $raw_name       = implode('.', $exploded_name);
                        if(strlen($raw_name)>25)
                        {
                            $processed_name = substr($raw_name, 0, 25).'....'.$type;
                        }
                        else
                        {
                            $processed_name = $name;
                        }
                        //End
                        $files['filekey-'.$key] = array(
                            'error' =>false,
                            'url' => assignment_path($param).$filename,
                            'name' => $processed_name."?v=".rand(10,1000), // optional
                            'original_name' => $name."?v=".rand(10,1000), // optional
                            'file_name' => $filename."?v=".rand(10,1000),
                            'message' => 'File uploaded successfully'
                        );
                        // $files['file_names'][] = $filename;
                    } else {
                        $files = array('error' =>true,'message' =>'File type error</br>Allowed types :Documents, text, audio clips, video clips, excel, PPT, PPTX, XLS, XLSX, ODT, ODS, and ODP');
                    } 
                }
            }
        }
        else
        {
            if (isset($_FILES['file']))
            {
                foreach ($_FILES['file']['name'] as $key => $name)
                {
                    $type = pathinfo($name, PATHINFO_EXTENSION);
                    if (in_array($type, $types))
                    {
                        // setting file's mysterious name
                        $random = md5(date('YmdHis').rand(1000, 9999));
                        $filename = $random.'.'.$type;
                        $path = $directory.$filename;
                        // copying
                        move_uploaded_file($_FILES['file']['tmp_name'][$key], $path);
                        // processing file name
                        $exploded_name  = explode('.', $name);
                        unset($exploded_name[sizeof($exploded_name)-1]);
                        $raw_name       = implode('.', $exploded_name);
                        if(strlen($raw_name)>25)
                        {
                            $processed_name = substr($raw_name, 0, 25).'....'.$type;
                        }
                        else
                        {
                            $processed_name = $name;
                        }
                        //End
                        $files['filekey-'.$key] = array(
                            'error' =>false,
                            'url' => assignment_path($param).$filename,
                            'name' => $processed_name."?v=".rand(10,1000), // optional
                            'original_name' => $name."?v=".rand(10,1000), // optional
                            'file_name' => $filename."?v=".rand(10,1000),
                            'message' => 'File uploaded successfully'
                        );
                        // $files['file_names'][] = $filename;
                    } else {
                        $files = array('error' =>true,'message' =>'File type error</br>Allowed types :Documents, text, audio clips, video clips, excel, PPT, PPTX, XLS, XLSX, ODT, ODS, and ODP');
                    } 
                }
            }
        }
        
        echo stripslashes(json_encode($files));
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
}
?>