<?php
class Cisco extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }
    
    function index()
    {
        $this->__coSpaceId = '9e293046-d970-49e1-8820-ea3423ffd07c';
        
        $directory  = FCPATH.'cisco/spaces/'.$this->__coSpaceId;;
        $files      = scandir($directory);
        unset($files[0]);
        unset($files[1]);
        if(sizeof($files))
        {
            foreach($files as $file)
            {
                $file_date = substr($file, 0, 8);
                //echo cisco_path().$this->__coSpaceId.'/'.$file.'<br />';
                echo $file_date.'<br />';
            }
        }
    }
    
}
?>