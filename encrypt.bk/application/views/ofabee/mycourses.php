<?php include 'header.php'; $my_course_categories = array(); $my_wishlist_categories =array() ?>
<style type="text/css" media="screen">
    @media (min-width:481px){
        #dashboard-my-courses{padding-top: 25px;}
    }
    #alert_info{
        color: #3a0c0c;
        background-color: #59c7e0;
        width: 50%;
        margin: 0 auto;
        margin-top: 15px;
    }
    .mycourse_container{
        position: relative;
        height: auto;
    }
    .footer-group{
        /* position: absolute;
        bottom: 0px; */
    }
    #dashboard-my-courses{margin-bottom: 30px;}
    .bundle-label{
    background: #ff327a;
        width: 36px;
        height: 42px;
        border-radius: 0px 6px 6px 6px;
        position: absolute;
        top: -5px;
        right: 25px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .bundle-label:before{
        content: ' ';
        position: absolute;
        left: -4px;
        top: 0px;
        border-style: solid;
        border-width: 0 0 6px 4px;
        border-color: transparent transparent #aa28ac transparent;
    }
    .bundle-label .bundle-icon{height: 20px;margin: 0 auto;}
    .bundle-label .bundle-count{
        font-size: 12px;
        color: #fff;
        text-align: center;
        line-height: 15px;
    }
    .bundle-label .bundle-count span{font-weight: 600;}
    .bundle-label .bundle-count span.in{
        font-size: 10px;
        display: inline-block;
    }
</style>


<section id="nav-group">
    <?php include_once "dashboard_header.php"; ?>
</section>


<section class="mycourse_container xs-minheight-vh">
<div class="all-challenges">
    <?php 
    $error = $this->session->flashdata('error');
    if(!empty($error)){
    ?>
        <div class="alert alert-error" id="alert_info">
            <a class="close" data-dismiss="alert" id="dismiss_pass_pop">×</a>
            <?php echo $error; ?>
        </div>
    <?php
    }
    ?>
    <div class="container container-altr no-padding-xs">
        <div class="container-reduce-width">
        
              <div class="tab-content">
            <div id="dashboard-my-courses" class="tab-pane active">
                <?php $course_url = ''; ?>
                <?php if( (isset($course_details)&&count($course_details)>0) || (isset($subscription)&&count($subscription)>0) ){ ?>
                    <div class="row course-cards-row">
                    <ul class="ex-course-container">
                    <?php if(isset($course_details)&&count($course_details)>0){
                        foreach ($course_details as $course){
                            if(empty($course['cs_bundle_id']))
                            {
                                course_card($course,'subscribedCourses');
                            }
                            //$my_course_categories[$course['cb_category']]['title'] = $course['ct_name'];
                        }
                    }
                    if(isset($subscription)&&count($subscription)>0){
                        foreach ($subscription as $course){
                            course_card($course,'subscribedCourses');
                        }
                    }
                    
                    ?>
                    </ul>
                    
                    </div>
                <?php }else{ ?>
                    <div class="row">    
                        <div class="col-sm-12 dashboard-no-course">
                            <div class="no-course-container">
                                <img class="no-questions-svg" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme'); ?>/img/no-courses.svg">
                                <?php /* ?><span class="no-discussion no-content-text"><span>Add Courses </span>From Top Institutes and Renowned Teachers</span><?php */ ?>
                                <div class="text-center">
                                <span class="noquestion-btn-wrap "><a href="<?php echo site_url('course/listing') ?>" class="orange-flat-btn noquestion-btn browse-course-btn">Browse Courses</a></span>
                                </div><!--text-center-->
                            </div>                 
                        </div>              
                    </div>

                <?php } ?>
                </div>

        
            </div>  <!--container-reduce-width-->
        </div><!--container altr-->       
    </div><!--all-challenges-->
</section>

<?php include 'footer.php'; ?>

<?php include_once 'modals.php'; ?>


<!-- <script>
//console.log('< ?php echo config_item('youtube_api')?>');
</script> -->