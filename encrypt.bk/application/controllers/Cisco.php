<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cisco extends CI_Controller 
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        echo 'Cisco API';
    }
    public function recorded_video()
    {
        $input          = file_get_contents('php://input');
        $decoded_input  = json_decode($input, true);
        
        // //writing inout to text file
        // $myfile         = fopen("uploads/upload.txt", "w");
        // $txt            = json_encode($input);
        // fwrite($myfile, $txt);
        // fclose($myfile);
        // //end

        $token          = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkYXRhIjoiY2lzY28gZmlsZSB3YXRjaGVyIHNjcmlwdCIsImlhdCI6MTU0OTAwNzkzMiwiYXVkIjoiU0RQSyIsImlzcyI6IkVuZmluIn0.0ZTIWJL2OkW7_lVbnjtDR_sGnDnmTo0O3tc4WlJ-3pY';
        $cisco_root     = cisco_upload_path();

        $filename_with_path    = str_replace('\\', '/', $decoded_input['filename']);
        $final_path            = $cisco_root.$filename_with_path;//assume we have only file name
        
        //check we have folders
        $folders            = explode('/', $final_path);
        $folder_depth       = sizeof($folders);
        $filename           = $folders[$folder_depth-1];
        unset($folders[$folder_depth-1]);//deleting the index that contins file name
        $directory_to_create = implode('/', $folders);
        if(!is_dir($directory_to_create))
        {
            mkdir($directory_to_create, 0777, true);
        }

        $shell_command = 'curl -H "Authorization: Bearer '.$token.'" -o '.$final_path.' http://'.$decoded_input['sourceIp'].'/video/'.$decoded_input['filename'];
        shell_exec($shell_command);

        //save file destails to database
        $this->load->model('Cisco_model');
        $save                       = array();
        $save['id']                 = false;
        $save['cr_source_ip']       = $decoded_input['sourceIp'];
        $save['cr_destination_ip']  = $decoded_input['destinationIp'];
        $save['cr_filename']        = $filename_with_path."?v=".rand(10,1000);
        $save['cr_size']            = $decoded_input['size'];
        $save['cr_date']            = date('Y-m-d');
        $save['cr_modified']        = $decoded_input['modified'];
        $this->Cisco_model->save_recording($save);
        //End
        echo json_encode(array('error' => false, 'message' => 'File copied to server'));
    }
}
