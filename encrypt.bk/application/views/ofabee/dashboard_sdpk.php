<?php include 'header.php'; ?>
<style media="screen">
@media (max-width:560px){
    .dashboard-empty-message{padding:0px 0px;}
    .btn.explore-btn{margin: 10px 0;}
}
.dash-user-icon{
    width: 20px;
    display: inline-block;
    vertical-align: sub;
}
.dash-mail-icon{
    display: inline-block;
    width: 20px;
    height: 16px;
    vertical-align: middle;
}
.dash-phone-icon{
    display: inline-block;
    width: 20px;
    height: 18px;
}
</style>
<section id="nav-group">
    <?php include_once "dashboard_header.php"; ?>
</section>
<link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/zabuto_calendar.min.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/sdpk/dashboard.css"> 
<section>
    <div class="all-challenges">
        <div class="container container-altr">        
            <div class="tab-content">
                <?php include_once('messages.php'); ?>
                <!-- student profile starts-->
                <div class="dash-stud-profile-wrapper">
                    <div class="col-md-8 profile-left-wrapper">
                        <!-- profile area starts -->
                        <div class="stud-profile-col">
                            <div class="col-md-5 avatar-info">
                                <div class="profile-avatar">
                                    <img src="<?php echo (($session['us_image'] == 'default.jpg') ? default_user_path() : user_path()) . $session['us_image'] ?>" class="img-responsive" alt="" width="250">
                                </div>
                                <div>
                                    <div class="joined-info text-center">
                                        <span>Joined On<br><?php echo date('d - m - Y',strtotime($session['created_date'])); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7 profile-info-wrapper">
                                <div class="stud-name"><h4><?php echo $session['us_name']; ?></h4></div>
                                <div class="profile-info">
                                    <p>
                                        <span class="dash-mail-icon">
                                            <svg height="331.46899" id="svg2439" version="1.0" width="439.371" viewBox="0 0 331.46899 439.371" xmlns="http://www.w3.org/2000/svg" xmlns:cc="http://creativecommons.org/ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" xmlns:svg="http://www.w3.org/2000/svg" style="width: 19px;height: 14px;margin-left: -4px;"><defs id="defs2441"><marker id="ArrowEnd" markerHeight="3" markerUnits="strokeWidth" markerWidth="4" orient="auto" refX="0" refY="5" viewBox="0 0 10 10"><path d="M 0,0 L 10,5 L 0,10 L 0,0 z" id="path2444"></path></marker><marker id="ArrowStart" markerHeight="3" markerUnits="strokeWidth" markerWidth="4" orient="auto" refX="10" refY="5" viewBox="0 0 10 10"><path d="M 10,0 L 0,5 L 10,10 L 10,0 z" id="path2447"></path></marker></defs><g id="g2449" transform="translate(-145.3305,-145.3305)"><path d="M 569.374,461.472 L 569.374,160.658 L 160.658,160.658 L 160.658,461.472 L 569.374,461.472 z" id="path2451" style="fill:none;stroke:#000000;stroke-width:30.65500069"></path><path id="path2453" style="fill:none;stroke:#000000;stroke-width:30.65500069"></path><path d="M 164.46,164.49 L 340.78,343.158 C 353.849,356.328 377.63,356.172 390.423,343.278 L 566.622,165.928" id="path2455" style="fill:none;stroke:#000000;stroke-width:30.65500069"></path><path d="M 170.515,451.566 L 305.61,313.46" id="path2457" style="fill:none;stroke:#000000;stroke-width:30.65500069"></path><path d="M 557.968,449.974 L 426.515,315.375" id="path2459" style="fill:none;stroke:#000000;stroke-width:30.65500069"></path></g></svg>
                                        </span>
                                        <?php echo $session['us_email']; ?>
                                    </p>
                                    <p>
                                        <span class="dash-phone-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" xmlns:xlink="http://www.w3.org/1999/xlink" height="18px" version="1.1" viewBox="0 0 18 18" width="18px" style="vertical-align: baseline;width: 12px;height: 13px;"><title></title><desc></desc><defs></defs><g fill="none" fill-rule="evenodd" id="Page-1" stroke="none" stroke-width="1"><g fill="#000000" id="Icons-Communication" transform="translate(-85.000000, -126.000000)"><g id="phone" transform="translate(85.000000, 126.000000)"><path d="M3.6,7.8 C5,10.6 7.4,12.9 10.2,14.4 L12.4,12.2 C12.7,11.9 13.1,11.8 13.4,12 C14.5,12.4 15.7,12.6 17,12.6 C17.6,12.6 18,13 18,13.6 L18,17 C18,17.6 17.6,18 17,18 C7.6,18 0,10.4 0,1 C0,0.4 0.4,0 1,0 L4.5,0 C5.1,0 5.5,0.4 5.5,1 C5.5,2.2 5.7,3.4 6.1,4.6 C6.2,4.9 6.1,5.3 5.9,5.6 L3.6,7.8 L3.6,7.8 Z" id="Shape"></path></g></g></g></svg>
                                        </span>
                                        <?php echo $session['us_phone']; ?>
                                    </p>
                                </div>
                                <div class="score-col">
                                    <h5 class="score-col-title">Report Score</h5>
                                    <div class="score-wrapper">
                                        <div class="score-points-col text-center col-md-12">
                                            <span class="score-count"><?php echo $score!=0?$score:'-'; ?></span>
                                            <p>Points</p>
                                        </div>
                                        <?php /* ?>
                                        <div class="grade-col col-md-5 text-center">
                                            <span class="grade"><?php echo $grade; ?></span>
                                            <p>Grade</p>
                                        </div>
                                        <?php */ ?>
                                    </div>
                                </div>
                                <?php /* ?>
                                <div class="view-grade" style="visibility:hidden;">
                                    <a href="<?php echo site_url('dashboard/grade'); ?>">View Grade Report</a>
                                </div>
                                <?php */ ?>
                            </div>
                        </div>
                        <!-- profile area ends -->
                        
                        <?php if(!empty($courses)): $count = 0; ?>
                        <?php foreach($courses as $course_id => $course): ?>
                            <?php if($count < 2):$count++;else:break;endif; ?>
                            <?php
                            $course_rate = "width:0%";
                            if(isset($course['ratting']) && $course['ratting'] != 0){
                                $percentage  = 20*$course['ratting'];
                                $course_rate = 'width:'.$percentage.'%';
                            }
                            $image_first_name   = substr($course['cb_image'],0,-4);
                            $image_dimension    = '_300x160.jpg';
                            $image_new_name     = $image_first_name.$image_dimension;
                            $image              = (($course['cb_image'] == 'default.jpg')?default_course_path():  course_path(array('course_id' => $course_id))).$image_new_name;
                            $tutors             = array();
                            foreach($courses[$course_id]['tutors'] as $tutor){
                                $tutors[] = $tutor['us_name'];
                            }
                            ?>
                            <?php
                                $course_slug     = isset($course['cb_slug'])? $course['cb_slug'] : '';
                                $header_url = site_url($course_slug);
                                $footer_url = site_url($course_slug);
                                $onclick = 'javascript:void(0)';                                  
                                if($course['enrolled']){
                                    switch($course['cs_approved']){
                                        case 1:
                                        $header_url = site_url('materials/course/'.$course['course_id'].'/'.$course['cs_last_played_lecture']);
                                        $footer_url = site_url('course/dashboard/'.$course['course_id']);
                                        break;
                                        case 2:
                                        $header_url = 'javascript:void(0)';
                                        $footer_url = 'javascript:void(0)';
                                        $onclick = 'showCommonModal(\'\',\'Subscription is waiting for approval by admin.\',\'\')';
                                        break;
                                        default:
                                        $header_url = 'javascript:void(0)';
                                        $footer_url = 'javascript:void(0)';
                                        $onclick = 'showCommonModal(\'\',\'Your subscription is suspended by admin.\',\'\')';
                                        break;
                                    }
                                }
                            ?>
                            <div onclick="<?php echo $onclick; ?>" class="course-card-horizontal">
                                <div class="course-card-wrapper">
                                    <div class="course-block-left col-md-4 col-xs-4 course-top-half rounded-corner">
                                        <a href="<?php echo $course['expired']?"javascript:void(0)":$header_url; ?>">                        
                                            <img src="<?php echo $image ?>" class="img-responsive card-img-fit" width=""> 
                                            <?php if(!$course['expired']): ?>  
                                            <div class="play-btn"></div>
                                            <?php endif; ?>             
                                        </a> 
                                    </div>
                                    <div class="course-block-right col-md-6 col-xs-6">
                                        <a href="<?php echo $footer_url; ?>">
                                            <label class="course-card-title"><?php echo $course['cb_title'] ?></label>
                                            <p class="sub-head-des"><?php echo implode(', ',$tutors) ?></p>
                                            <!-- progress bar starts -->
                                            <div class="progress_main">            
                                                <div class="progress">                
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo round($course['course_completion']) ?>%">
                                                    </div>            
                                                </div> 
                                                <span class="progress-precentage"><?php echo round($course['course_completion']) ?>%</span>           
                                                <!-- <span class="sr-only">0% Complete</span>        --> 
                                            </div>
                                            <!-- progress bar ends -->
                                            <div class="card-status-col">
                                                <div style="visibility:hidden!important;" class="star-ratings-sprite star-ratings-sprite-block">
                                                    <span style="<?php echo $course_rate; ?>" class="star-ratings-sprite-rating"></span>
                                                </div>
                                                <?php 
                                                    switch($course['cs_approved']){
                                                        case 0: ?>
                                                        <label class="course-status red-text">Suspended</label>
                                                    <?php break; ?>
                                                    <?php case 2: ?>
                                                        <label class="course-status red-text">Pending Approval</label>
                                                    <?php break; ?>
                                                    <?php default:
                                                                if($course['cs_course_validity_status'] == 0): ?>
                                                                <label class="course-status">Lifetime Validity</label>
                                                        <?php   else: ?>
                                                                    <?php if(!$course['expired']): ?>
                                                                        <?php switch($course['expire_in_days']){
                                                                                    case 0: ?>
                                                                                    <label class="course-status">Expires today</label>
                                                                            <?php break; ?>
                                                                            <?php case 1: ?>
                                                                                    <label class="course-status">Expires tomorrow</label>
                                                                            <?php break; ?>
                                                                            <?php default: ?>
                                                                                    <label class="course-status">Expires in <?php echo $course['expire_in_days']; ?> days</label>
                                                                            <?php break; ?>
                                                                        <?php } ?>
                                                                    <?php else : ?>
                                                                        <label class="course-status red-text">Expired on <?php echo $course['validity_format_date']; ?></label>
                                                                    <?php endif; ?>
                                                        <?php   endif; 
                                                            break; 
                                                    } ?>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="card-grade-col col-md-2 col-xs-2">
                                        <span class="grade-on-card"><?php echo $course['cs_manual_grade']=='-'  || $course['cs_manual_grade']=='' ? $course['cs_auto_grade']:$course['cs_manual_grade']; ?></span>
                                        <p>Grade</p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                            <div class="dashboard-empty-message text-center">
                                <h4>Seems No Course Enrolled yet !</h4>
                                <p>To find more interesting courses click below button</p>
                                <a class="btn btn-default explore-btn" href="<?php echo site_url('course/listing'); ?>" type="">Explore courses</a>
                            </div>
                        <?php endif; ?>
                        
                        <!-- load more btn -->
                        <?php if(isset($courses) && count($courses) > 2):?>
                            <div class="btn-center-div loadmore-dash-btn">
                                <a href="<?php echo site_url();?>dashboard/courses" class="btn  orange-flat-btn  orange-course-btn inline-blk">View all Courses </a>
                            </div>    
                        <?php endif;?>        
                        <!-- load more btn ends --> 
   
                    </div>
                    <!-- left wrap ends -->

                    <!-- right venets starts -->
                    <div class="col-md-4 event-right-wrapper">
                    <?php function render_upcomming($param = array(),$count = 1){
                            $return = '<div class="event-tag-wrapper"><div class="dash-event-tags">';
                            $date   = '';
                            $stat   = 'join'; //: 'join';
                            $count_class = $count%2==0?' red-bg':'';
                            switch ($param['cl_lecture_type']) {
                                case 8:
                                    $date = strtotime($param["event_date"]);
                                    $link = $param['event_link']; //site_url('materials/course').'/'.$param['cl_course_id'].'/0/0/'.$param['id'];
                                    $return .= '<div class="event-col'.$count_class.'">
                                                    <h4 class="date">'.date('d',$date).'</h4>
                                                    <span class="month">'.date('M',$date).'</span>
                                                </div>';
                                    $return .= '<div class="event-info ">
                                                    <a href="'.$link.'">Submission of '.$param["clt_name"].' named <b>"'.$param["event_name"].'</b>.</a>
                                                </div>';
                                break;
                                case 7:
                                $date = strtotime($param["event_date"]);
                                $link = $param['event_link']; 
                                $return .= '<div class="event-col'.$count_class.'">
                                                    <h4 class="date">'.date('d',$date).'</h4>
                                                    <span class="month">'.date('M',$date).'</span>
                                                </div>';
                                    $return .= '<div class="event-info">
                                                    <a href="'.$link.'">Live session <b>"'.$param["event_name"].'"</b> scheduled.</a>
                                                </div>';
                                break;
                                case 0:
                                    $date = strtotime($param["event_date"]);
                                    $link = $param['event_link']; //site_url('events').'/event/'.base64_encode($param['id']);
                                    $return .= '<div class="event-col'.$count_class.'">
                                                    <h4 class="date">'.date('d',$date).'</h4>
                                                    <span class="month">'.date('M',$date).'</span>
                                                </div>';
                                    $return .= '<div class="event-info">
                                                    <a href="'.$link.'">Event named <b>"'.$param["event_name"].'"</b> has been scheduled.</a>
                                                </div>';
                                break;
                            }
                            return $return.'</div></div>';
                        } ?>
                        <?php
                            if(!empty($upcomming))
                            {
                                $e_count = 0;
                                echo '<div class="event-wrapper">';	
                                foreach($upcomming as $upcomming_event)
                                {
                                    echo render_upcomming($upcomming_event,$e_count);
                                    $e_count++;
                                    if($e_count > 3)
                                    {
                                        break;
                                    }
                                }
                                echo '</div>';	                            
                            }
                        ?>
                        <div id="my-calendar" data-day-format="DDD"></div>
                    </div>
                </div>
                <!-- student profile ends-->
            </div>         
        </div><!--container altr-->  

        

    </div><!--all-challenges-->
</section>

<script type="text/javascript">
    var __site_url = '<?php echo site_url(); ?>';
    var __user_id  = '<?php echo $session['id']; ?>';
    var __busy     = false;
</script>

<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/zabuto_calendar.js"></script>
<script type="text/javascript">
    var __month_data;
    var __assets_url = '<?php echo assets_url(); ?>';
    $(document).ready(function () {
        $("#my-calendar").zabuto_calendar({
            ajax: {
                url: __site_url+"dashboard/calendar_events/1",
                modal: false
            },
            show_previous: false,
            today: true
            ,action: function() { myDateFunction(this.id); }
        });
        setInterval(function(){ $(".alert").remove(); }, 2000);
        
    });
    function myDateFunction(date){
        date = date.split('_');
        $.each(__month_data,function(key, m_data) {
            //console.log(m_data);
            if(m_data['date'] == date[3]){
                render_modal(m_data);
            }
        });
    }
    function render_modal(day_data){
        // //console.log(day_data);
        var date       = day_data['date'].split('-');
        var months     = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var monthsfull = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        var event_list = '';
        $('.sdpk-modal-title').html('<span class="badge lefty round-label main-label"><span class="big-modal-date">'+date[2]+'</span><br/>'+months[(date[1]-1)]+'</span>Events on '+monthsfull[date[1]-1]+' '+date[2]+', '+date[0]);
        $.each(day_data['events'],function(key, event) {
            event_list += '<li class="list-group-item single-event modal-event">';
            event_list += '<a target="_blank" href="'+event['link']+'"><span class="badge lefty round-label modal-badge"><img src="'+__assets_url+'themes/ofabee/img/date.svg" width="24"></span>';
            event_list += '<span class="event-title">'+event['message']+'</span><br>';
            event_list += '<span class="event-date modal-date">'+months[date[1]-1]+' '+date[2]+', '+date[0]+'</span>';
            event_list += '</a>';
            event_list += '</li>';
        });
        $('#modal_events').html(event_list);
        $('#EventModal').modal('toggle');
    }  
</script>    


<script type="text/javascript" src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/js/dashboard.js'; ?>"></script>



<div id="EventModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header sdpk-modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title sdpk-modal-title"><span class="badge lefty round-label main-label"><span class="big-modal-date">17</span><br/>Jul</span>Events on June 17, 2017</h4>
      </div>
      <div class="modal-body sdpk-modal-body">
        <ul class="list-group event-group" id="modal_events">                             
        </ul>          

      </div>
      <div class="modal-footer sdpk-modal-center">
        <!-- <button type="button" class="btn btn-orange flex-round-btn modal-btn">View More</button> -->
      </div>
    </div>

  </div>
</div>
<?php include_once 'modals.php'; ?>

<?php include 'footer.php'; ?>