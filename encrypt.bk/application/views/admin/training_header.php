<?php include_once 'header.php';?>
<?php $actions = config_item('actions');?>
<style>
    .report-dropdown{
    position: absolute;
    left: 250px;
    top: -100%;
    padding: 0;
    box-shadow: 2px 2px 3px 0px #9090905c;
    display: none;
    }
    .report-drop-toggle:hover .report-dropdown{display: block;}
    .report-dropdown li {
    padding: 0px !important;
    min-width: 250px;
    background: #e8e8e8 !important;
    }
    .report-dropdown li a {
    line-height: 38px;
    width: 100%;
    display: inline-block;
    padding: 5px 25px !important;
    margin: 0;
    }
    .report-dropdown li:hover {background: #f6f8fa !important;} 
</style>
<!-- MAIN TAB --> <!-- STARTS -->
<section class="courses-tab cont-course-big custom-sidenav" id="courses-tab">
<?php
$menu_active = array(
 'basic' => ''
 , 'users' => ''
 , 'groups' => ''
 , 'discussion' => ''
 , 'reviews' => ''
 , 'announcement' => ''
 , 'backups' => ''
);
$menu_active[$this->router->fetch_method()] = 'active';
$menu_active_cs                             = array(
 'course_settings_basics' => ''
 , 'course_settings_advanced' => ''
 , 'course_settings_seo' => ''
 , 'course_settings_backups' => '',
);
$menu_active_cs[$this->router->fetch_class() . '_' . $this->router->fetch_method()] = 'active';
?>
    <ol class="nav offa-tab custom-sidemenu">
        <!-- active tab start -->
        <li class="<?php echo $menu_active['basic'] ?>">
            <a href="<?php echo admin_url('course/basic/' . $course['id']); ?>"> <?php echo lang('statistics') ?></a>
        </li>
        <?php
        if(!empty($this->course_content_privilege)) {
            if( in_array($this->privilege['view'], $this->course_content_privilege) ) {
                ?>
                <li>
                    <a href="<?php echo admin_url('coursebuilder/home/' . $course['id']) ?>">
                        <?php echo lang('course_content_builder') ?>
                    </a>
                </li>
                <?php
            }
        }
        ?>
        <?php
        if (!empty($this->student_enroll_privilege)) {
            if (in_array($this->privilege['view'], $this->student_enroll_privilege)) {
        ?>
                <li class="<?php echo $menu_active['users'] ?>">
                    <a href="<?php echo admin_url('course/users/' . $course['id']); ?>"><?php echo lang('users') ?></a>
                </li>
        <?php
            }
        }
        ?>
        <?php
        if (!empty($this->batch_enroll_privilege)) {
            if (in_array($this->privilege['view'], $this->batch_enroll_privilege)) {
        ?>
                <li class="<?php echo $menu_active['groups'] ?>">
                    <a href="<?php echo admin_url('course/groups/' . $course['id']); ?>"><?php echo lang('groups') ?></a>
                </li>
        <?php
            }
        }
        ?>
        
        <?php if(!empty($this->forum_privilege)): ?>
        <?php if(in_array($this->privilege['view'], $this->forum_privilege)): ?>
            <li class="<?php echo $menu_active['discussion'] ?> report-drop-toggle">
            <a href="javascript:void(0)"><?php echo lang('discussion') ?></a>
                <ol class="report-dropdown">
                    <li class=""><a href="<?php echo admin_url('course/discussion_instruction/' . $course['id']); ?>" onclick="redirectReport(2)">Discussion Instruction</a></li>
                    <li class=""><a href="<?php echo admin_url('course/discussion/' . $course['id']); ?>">Go to Discussion</a></li>
                </ol>
            </li>

        <?php endif; ?>
        <?php endif; ?>
        <?php 
        if (!empty($this->report_privilege)) {
            if (in_array($this->privilege['view'], $this->report_privilege)) {
        ?>
        <!-- Report -->
            <li class="report-drop-toggle" >
            <a href="javascript:void(0)">Report</a>
                <ol class="report-dropdown">
                    <li class=""><a href="javascript:void(0)" onclick="redirectReport(1)">Grade Report</a></li>
                    <li class=""><a href="javascript:void(0)" onclick="redirectReport(2)">Quiz Report</a></li>
                    <li class=""><a href="javascript:void(0)" onclick="redirectReport(3)">Assignment Report</a></li>
                    <li class=""><a href="javascript:void(0)" onclick="redirectReport(4)">Course Performance Report</a></li>
                </ol>
            </li>
       <!-- Report ends -->
        <?php   
            }
        }
        ?>
        <?php if(isset($this->review_permission) && in_array($this->privilege['view'], $this->review_permission)):?> 
            
        <li class="<?php echo $menu_active['reviews'] ?>">
            <a href="<?php echo admin_url('course/reviews/' . $course['id']); ?>"><?php echo 'Reviews' //lang('reviews')                                        ?></a>
            <span class="active-arrow"></span>
        </li>
        <?php endif;?>
    <?php 
        if (!empty($this->userPrivilege)) {
            if (in_array($this->privilege['view'], $this->userPrivilege)) {
        ?>
            <li class="<?php echo $menu_active['announcement'] ?>">
                <a href="<?php echo admin_url('course/announcement/' . $course['id']); ?>"><?php echo 'announcement' //lang('reviews')                                        ?></a>
                <span class="active-arrow"></span>
            </li>
    <?php   }
        }
    ?>

        <?php 
        if (!empty($this->userPrivilege)) {
            if (in_array($this->privilege['view'], $this->course_privilege) && in_array($this->privilege['edit'], $this->course_privilege)) {
        ?>
            <li class="<?php echo $menu_active_cs['course_settings_basics'] ?>">
                <a href="<?php echo admin_url('course_settings/basics/' . $course['id']); ?>"><?php echo lang('settings') ?></a>
            </li>
        <?php   
            }
        }
        ?>
        <?php if (!empty($this->__loggedInUser['role_id']) && $this->__loggedInUser['role_id'] != '3' && $this->__loggedInUser['role_id'] != '8'): ?>
            <?php if (isset($this->assign_faculty_privilege) && in_array(1, $this->assign_faculty_privilege)): ?>
            <li class="<?php echo $menu_active_cs['course_settings_advanced'] ?>">
                <a href="<?php echo admin_url('course_settings/advanced/' . $course['id']); ?>">ASSIGN FACULTIES</a>
            </li>
            <?php endif;?>
        <?php endif;?>

        <?php 
        if (!empty($this->course_privilege)) {
            if (in_array($this->privilege['view'], $this->course_privilege) && in_array($this->privilege['edit'], $this->course_privilege)) {
        ?>
        <li class="<?php echo $menu_active_cs['course_settings_seo'] ?>">
            <a href="<?php echo admin_url('course_settings/seo/' . $course['id']); ?>">Advanced</a>
        </li>
        <?php 
            }
        }
        
    ?>

        <?php if (!empty($this->__loggedInUser['role_id']) && $this->__loggedInUser['role_id'] != '3'): ?>
            <?php if (!empty($this->backup_privilege)): ?>
                <?php if (in_array($this->privilege['view'], $this->backup_privilege)): ?>
                <li class="<?php echo $menu_active['backups'] ?>">
                    <a href="<?php echo admin_url('course/backups/' . $course['id']); ?>">Backup & Restore</a>
                </li>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>

        <!-- active tab end -->
    </ol>

</section>
<!-- MAIN TAB --> <!-- END -->

<script>
    var adminUrl    = '<?php echo admin_url(); ?>';
    __courseid      = '<?php echo $this->course_id; ?>';

// redirectReport(4);
    function redirectReport(option){

        switch(option){
            
            case 1 :    window.location = adminUrl+"report/grade_report?course="+__courseid;
                        break;
            case 2 :    window.location = adminUrl+"report/assessments/?course_id="+__courseid;
                        break;
            case 3 :    window.location = adminUrl+"report/assignment/?course_id="+__courseid;
                        break;
            case 4 :    window.location = adminUrl+"report/course_institute_performance/"+__courseid;
                        break;
        }
    }
</script>