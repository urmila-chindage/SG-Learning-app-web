<?php
class Test extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model(array('Test_model'));
    }
    
    function index()
    {
         $source =  FCPATH.cisco_upload_path().'9e293046-d970-49e1-8820-ea3423ffd07c/20170804062730+0000.mp4';
         $destination =  FCPATH.video_upload_path().'20170804062730+0000.mp4';
         if(copy($source, $destination))
         {
             echo $source .'====='.$destination;
         }
    }

    public function roles_modules()
    {
        $roles_modules_ids                      = array();
        $module_ids                             = $this->Test_model->get_records('id', 'modules');
        $role_ids                               = $this->Test_model->get_records('id', 'roles');
        $roles_modules                          = $this->Test_model->get_records('role_id, module_id', 'roles_modules_meta');
        if(!empty($roles_modules))
        {
            foreach($roles_modules as $role_module)
            {
                $roles_modules_ids[]            = $role_module['module_id'].'_'.$role_module['role_id'];
            }
        }
        if(!empty($module_ids))
        {
            foreach($module_ids as $module)
            {
                $module_id                      = $module['id'];
                if(!empty($role_ids))
                {
                    foreach($role_ids as $role)
                    {
                        $role_id                = $role['id'];
                        $role_module_id         = $module_id.'_'.$role_id;
                        if(!in_array($role_module_id, $roles_modules_ids))
                        {
                            $roles_modules_meta = array(
                                                        'id'        => false,
                                                        'role_id'   => $role_id,
                                                        'module_id' => $module_id,
                                                        'permissions' => ''
                                                        );
                            $this->Test_model->save_module_previlages($roles_modules_meta);
                        }
                    }
                }
            }
            echo 'Modules previlages for Roles created successfully!';
        }
    }
}
?>