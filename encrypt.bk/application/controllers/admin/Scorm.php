<?php

class Scorm extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->__manifest_file = 'imsmanifest.xml';
    }
    
    function index()
    {
        /*echo scorm_upload_path();
        echo '<br >'.scorm_path();*/
        
        $response               = array();
        $response['error']      = false;
        $response['message']    = '';
        
        //upladed details
        $uploaded_file = 'scorm-sample-ess-science';
        $manifest_file = scorm_upload_path().$uploaded_file.'/'.$this->__manifest_file;
        if(!file_exists($manifest_file))
        {
            $response['error']      = true;
            $response['message']    = 'Invalid scorm package found.';
            echo json_encode($response);die;
        }
        $manifest_file_content = file_get_contents($manifest_file);
        //$xml    = simplexml_load_string($manifest_file_content);
        $xml    = simplexml_load_file($manifest_file);
        
        $resources = (array)$xml->resources->resource;
        $initiating_file = '';
        if(isset($resources))
        {
            foreach ($resources as $key => $value)
            {
                echo '<pre>' ; print_r($value);die;
                $initiating_file = $value['href'];
                break;
            }
        }
        
        echo scorm_upload_path().$uploaded_file.'/'.$initiating_file;die;
    }
}
?>