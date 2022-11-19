<?php
$method              = $this->router->fetch_method();
$r_assessment        = '';
$r_course            = '';
$r_wishlist          = '';
$r_strength          = '';
$r_assignment        = '';
$r_cperformance      ='';
$r_tutor             = '';
$consolidated_tab    = '';
$r_preview           = '';
        

switch ($method) {
    case 'assessments':
        $r_assessment = 'active';
        break;
    case 'course':
        $r_course = 'active';
        break;
    case 'free_preview':
        $r_preview = 'active';
        break;
    case 'index':
        $r_wishlist = 'active';
        break;
    case 'strength_report':
        $r_strength = 'active';
        break;
    case 'assignment':
        $r_assignment = 'active';
        break;
    case 'course_performance':
        $r_cperformance = 'active';
        break;
    case 'tutor_performance_report':
        $r_tutor = 'active';
        break;
    case 'course_consolidated_report':
        $consolidated_tab = 'active';
    break;
}
?>

<section class="courses-tab cont-course-big custom-sidenav">
    <ol class="nav offa-tab custom-sidemenu">
        <li class="<?php echo $r_course; ?>">
            <a href="<?php echo admin_url('report/course') ?>">Grade Report</a>
            <span class="active-arrow"></span>
        </li>
        <?php if(in_array(1, $this->report_privilege)) { ?>
        <li class="<?php echo $r_assessment; ?>">
            <a href="<?php echo admin_url('report/assessments') ?>">Quiz Report</a>
            <span class="active-arrow"></span>
        </li>
        <?php } ?>
       <?php if(in_array(1, $this->report_privilege)) { ?>
       <li class="<?php echo $r_assignment; ?>">
            <a href="<?php echo admin_url('report/assignment') ?>">Assignment Report</a>
            <span class="active-arrow"></span>
        </li>
       <?php } ?>
       <?php if(in_array(1, $this->report_privilege)) { ?>
       <li class="<?php echo $r_preview; ?>">
            <a href="<?php echo admin_url('report/free_preview') ?>">Course Preview Report</a>
            <span class="active-arrow"></span>
        </li>
       <?php } ?>
        <li class="<?php echo $r_cperformance; ?>">
            <a href="<?php echo admin_url('report/course_performance') ?>">Course Performance Report</a>
            <span class="active-arrow"></span>
        </li>
        <li class="<?php echo $r_tutor; ?>">
            <a href="<?php echo admin_url('report/tutor_performance_report') ?>">Tutor Performance Report</a>
            <span class="active-arrow"></span>
        </li>
        <li>
            <a href="<?php echo admin_url('report/log_activity') ?>">Log Activity Report</a>
            <span class="active-arrow"></span>
        </li>
        <li>
            <a href="<?php echo admin_url('report/archive') ?>">Archive</a>
            <span class="active-arrow"></span>
        </li>
        <li class="<?php echo $consolidated_tab; ?>">
            <a href="<?php echo admin_url('report/course_consolidated_report') ?>">Consolidated Course Report</a>
            <span class="active-arrow"></span>
        </li>
       <!--  <li class="< ?php echo $r_strength; ?>">
           <a href="< ?php echo admin_url('report/strength_report') ?>">Strength Report</a>
           <span class="active-arrow"></span>
       </li> 
        <li class="advanced-report-tab">
            <a target="_blank" href="< ?php echo admin_url('advanced_report') ?>" onclick="locationToAdvancedReport()">Advanced Report</a>
            <span class="active-arrow"></span>
        </li>-->
        <?php // $adminn = $this->auth->get_current_user_session('admin'); ?>
        <?php /* if (in_array($adminn['id'], array(config_item('super_admin')))): ?>
            <li class="<?php echo $r_wishlist; ?>">
                <a href="<?php echo admin_url('wishlist') ?>">Wishlist Report</a>
                <span class="active-arrow"></span>
            </li>
		<?php  endif;  */?>
    </ol>
</section>

<script>
    function locationToAdvancedReport()
    {
        $('.report-tabs-custom li').removeClass('active');
        $('.advanced-report-tab').addClass('active');
    }
</script>