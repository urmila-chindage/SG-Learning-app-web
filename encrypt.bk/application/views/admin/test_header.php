<style type="text/css">
    .marign-top {
        margin: 14px 0px 0px 0px;
    }
    .nav-tabs.offa-tab{
        border-bottom: 1px solid #b4b5b9;
    }
    .courses-tab ol.nav li{border-bottom: none;}
    .rightwrap-top-update {top: 125px !important;}
</style>
<?php
$tcurr_meth             = $this->router->fetch_method();
$tsteps                 = array();
$tsteps[0]['name']      = 'Create New Quiz';
$tsteps[0]['method']    = 'test_basics';
$tsteps[1]['name']      = 'Quiz Setting';
$tsteps[1]['method']    = 'test_settings';
$tsteps[2]['name']      = 'Quiz Questions';
$tsteps[2]['method']    = 'test_questions';
$tsteps[3]['name']      = 'Quiz Availability';
$tsteps[3]['method']    = 'test_publishing';
$tsteps[4]['name']      = 'Quiz Batch Override';
$tsteps[4]['method']    = 'test_assign';
//print_r($test['course_id']);

?>


    <!-- top Header starts-->
    <div class="buldr-header inner-buldr-header custom-coursebuilder-header clearfix">
        <div class="pull-left">
            <div class="lecture-icon-big text-purple"><i class="course-icon quiz-icon-gray"></i></div>
            <?php foreach($tsteps as $key => $steps): 
            if($tcurr_meth==$steps['method']):
            ?>
            <h3 style="text-transform:uppercase;"><?php echo $test['cl_lecture_name'].' : <span class="text-green">'.$steps['name'].'</span>'; ?></h3>
            <?php 
             endif;
             endforeach; 
             ?>
        </div>
        <div class="pull-right rite-side">
            <a href="<?php echo admin_url('coursebuilder/home/'.$test['course_id']) ?>">
                <button class="btn btn-red" id="back_button">
                    <i class="icon icon-left"></i>BACK
                </button>
            </a>
        </div>
    </div>
    <!-- !.top Header ends -->

    <section class="courses-tab base-cont-top-heading coursestab-top-update" style="background: transparent;">
        <ol class="nav nav-tabs offa-tab" style="padding-left: 40px;background: #e8e8e8;">
            <!-- active tab start -->
            <?php foreach($tsteps as $key => $steps): ?>
                <li <?php echo $tcurr_meth==$steps['method']?'class="active"':''; ?> data-div="step-two">
                    <a href="<?php echo $tcurr_meth!=$steps['method']?admin_url('test_manager/'.$steps['method'].'/'.base64_encode($test['id'])):'javascript:void(0)'; ?>" class="text-center">Step <?php echo $key+1; ?><br><h5 class="marign-top rt-7"><?php echo $steps['name']; ?></h5></a>
                    <span class="active-arrow"></span>
                </li>
            <?php endforeach; ?>
        </ol>
    </section>
