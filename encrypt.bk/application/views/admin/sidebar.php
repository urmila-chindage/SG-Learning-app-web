<?php 
$admin = $this->auth->get_current_user_session('admin');

$module_menu                = array();
$module_menu['dashboard']   = array(
                                'label' => 'Dashboard',
                                'link' => admin_url('dashboard'),
                                'sidebar' => array( 'icon' => 'icon-gauge' )
                            );

$module_course = $this->accesspermission->get_permission(array('role_id' => $admin['role_id'], 'module' => 'course'));
if(in_array(1, $module_course))
{
    $module_menu['course']  = array(
                                'label' => 'Course',
                                'link' => admin_url('course'),
                                'sidebar' => array( 'icon' => 'icon-graduation-cap' ),
                                'dashboard' => array('icon' => 'dash-mc')
                            );
}

$module = $this->accesspermission->get_permission(array('role_id' => $admin['role_id'], 'module' => 'user'));
//echo '<pre>'; print_r($module);die;
if(in_array(1, $module))
{
    $module_menu['user']            = array(
                                        'label' => 'Student',
                                        'link' => admin_url('user'),
                                        'sidebar' => array( 'icon' => 'icon-user' ),
                                        'dashboard' => array('icon' => 'dash-mu')
                                    );
}
if($admin['us_role_id'] != '8'){
    $module = $this->accesspermission->get_permission(array('role_id' => $admin['role_id'], 'module' => 'institutes'));
    if(in_array(1, $module))
    {
        $module_menu['institutes']      = array(
                                            'label' => 'Institutes',
                                            'link' => admin_url('institutes'),
                                            'sidebar' => array( 'icon' => 'icon-bank' ),
                                            'dashboard' => array('icon' => 'dash-ins')
                                        );
    }
}

if($admin['us_role_id'] != '3' && $admin['us_role_id'] != '8'){
    $module = $this->accesspermission->get_permission(array('role_id' => $admin['role_id'], 'module' => 'faculty'));
    if(in_array(1, $module))
    {
        $module_menu['faculties']       = array(
            'label' => 'Faculties',
            'link' => admin_url('faculties'),
            'dashboard' => array('icon' => 'dash-mf')
            );
    }
}

$module = $this->accesspermission->get_permission(array('role_id' => $admin['role_id'], 'module' => 'batch'));
if(in_array(1, $module))
{
    $module_menu['groups']          = array(
                                        'label' => 'Batches',
                                        'link' => admin_url('groups'),
                                        'sidebar' => array( 'icon' => 'icon-users' ),
                                        'dashboard' => array('icon' => 'dash-grp')
                                    );
}

$module = $this->accesspermission->get_permission(array('role_id' => $admin['role_id'], 'module' => 'question'));
if(in_array(1, $module))
{
    $module_menu['generate_test']   = array(
                                        'label' => 'Question Bank',
                                        'link' => admin_url('generate_test'),
                                        'dashboard' => array('icon' => 'dash-question-bank')
                                    );
}   
$module = $this->accesspermission->get_permission(array('role_id' => $admin['role_id'], 'module' => 'event'));
if(in_array(1, $module))
{
    $module_menu['event']           = array(
                                        'label' => 'Events',
                                        'link' => admin_url('event'),
                                        'sidebar' => array( 'icon' => 'icon-calendar-1' ),
                                    );
}   

$module = $this->accesspermission->get_permission(array('role_id' => $admin['role_id'], 'module' => 'report'));  
if(in_array(1, $module_course) && in_array(1, $module))
{ 
    $module_menu['report']          = array(
                                        'label' => 'Report',
                                        'link' => admin_url('report/course'),
                                        'sidebar' => array( 'icon' => 'icon-chart-bar' ),
                                        'dashboard' => array('icon' => 'dash-r')
                                    );
}
if(isset($admin)&&$admin['us_role_id'] == 1)
{
    $module_menu['settings']        = array(
                                        'label' => 'Settings',
                                        'link' => admin_url('environment'),
                                        'sidebar' => array( 'icon' => 'icon-cog-alt' ),
                                        'dashboard' => array('icon' => 'dash-s')
                                    );
}


$module = $this->accesspermission->get_permission(array('role_id' => $admin['role_id'], 'module' => 'page'));  
//echo '<br><br><br><br><pre>'.$admin['role_id']; print_r($module); die;
if(isset($module) && (in_array(1, $module)))
{
    $module_menu['pages']        = array(
                                        'label'     => 'Pages',
                                        'link'      => admin_url('page'),
                                        'dashboard' => array('icon' => 'dash-cms')
                                    );                            
}

?>
<aside class="menu-block">
    <ol class="sidebar-menu">
    <?php foreach($module_menu as $key => $menu_obj): ?>
        <?php if(isset($menu_obj['sidebar'])): ?>
            <li data-toggle="tooltip" title="<?php echo $menu_obj['label']?>" data-placement="right">
                <a href="<?php echo $menu_obj['link']?>">
                    <i class="icon <?php echo $menu_obj['sidebar']['icon']?>"></i>
                </a>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
        <?php /* ?><li data-toggle="tooltip" title="Dashboard" data-placement="right">
            <a href="<?php echo admin_url('dashboard') ?>">
                <i class="icon icon-gauge"></i>
            </a>
        </li>
        <li data-toggle="tooltip" title="Courses" data-placement="right">
            <a href="<?php echo admin_url('course') ?>">
                <i class="icon icon-graduation-cap"></i>
            </a>
        </li>
        <li data-toggle="tooltip" title="Students" data-placement="right">
            <a href="<?php echo admin_url('user') ?>">
                <i class="icon icon-user"></i>
            </a>
        </li>
        <?php if(isset($admin)&&$admin['us_role_id'] == 1): ?>
        <li data-toggle="tooltip" title="Institutes" data-placement="right">
            <a href="<?php echo admin_url('institutes') ?>">
                <i class="icon icon-bank"></i>
            </a>
        </li>
        <?php endif; ?>

       <li data-toggle="tooltip" title="Batches" data-placement="right">
            <a href="<?php echo admin_url('groups') ?>">
                <i class="icon icon-users"></i>
            </a>
        </li>

        <li data-toggle="tooltip" title="Events" data-placement="right">
            <a href="<?php echo admin_url('event'); ?>">
                <i class="icon icon-calendar-1"></i>
            </a>
        </li>
        
        <li data-toggle="tooltip" title="Report" data-placement="right">
            <a href="<?php echo admin_url('report/course') ?>">
                <i class="icon icon-chart-bar"></i>
            </a>
        </li>
        <li data-toggle="tooltip" title="Settings" data-placement="right">
            <a href="<?php echo admin_url('environment') ?>">
                <i class="icon icon-cog-alt"></i>
            </a>
        </li><?php */ ?>
    </ol>
</aside>
