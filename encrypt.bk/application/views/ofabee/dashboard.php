<?php include 'header.php';?>
<section id="nav-group">
    <?php include_once "dashboard_header.php"; ?>
</section>
<link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/zabuto_calendar.min.css">
 
<section>
<div class="all-challenges">
    <div class="container container-altr">
        <div class="container-reduce-width">
              <div class="tab-content">
                <?php include_once('messages.php'); ?>
                <div id="dashboard-my-dashboard" class="tab-pane active">
                        <div class="row dash-row">    
                            <div class="col-sm-3">
                            <h4>Upcoming Events</h4>
                                <div class="flex-card course-block-1">
                                    <?php if(empty($upcomming)): ?>
                                        <br/>
                                        <img src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>images/sad_face.svg" alt="image" class="img-responsive wink-align"><center><h4 style="opacity: 0.7">No upcoming events</h4></center>
                                    <?php else: ?>
                                        <ul class="list-group event-group">
                                            <?php $GLOBALS['session'] = $session; ?>
                                            <?php function render_upcomming($param = array()){ 
                                                        $return = '';
                                                        $date   = '';
                                                        $session= $GLOBALS['session'];
                                                        $stat   = 'join'; //: 'join';
                                                        switch ($param['cl_lecture_type']) {
                                                            case 8:
                                                            $date = strtotime($param["dt_last_date"]);
                                                                $return .= '<li class="list-group-item single-event">';
                                                                $return .= '<a target="_blank" href="'.site_url('materials/course').'/'.$param['cl_course_id'].'/0/0#'.$param['id'].'"><span class="badge lefty round-label">'.date('d',$date).'<br>'.date('M',$date).'</span>';
                                                                $return .= '<span class="event-title">Submission of '.$param["clt_name"].' named <b>"'.$param["cl_lecture_name"].'"</b>.</span><br/>';
                                                                $return .= '<span class="event-date">'.date('M d, Y',$date).'</span></a></li>';
                                                            break;

                                                            case 7:
                                                            $date = strtotime($param["ll_date"]);
                                                                $return .= '<li class="list-group-item single-event">';
                                                                //$return .= $param['ll_mode'] == 2?'<a target="_blank" href="'.base_url('conference').'/?name='.$session['us_name'].'&userid='.$session['id'].'&room='.$param['live_id'].'&type=viewer&app=web"><span class="badge lefty round-label">'.date('d',$date).'<br>'.date('M',$date).'</span>':'<a target="_blank" href="'.site_url('/live').'/'.$stat . '/' . $param['live_id'].'"><span class="badge lefty round-label">'.date('d',$date).'<br>'.date('M',$date).'</span>';
                                                                $return .= $param['ll_mode'] == 2?'<a target="_blank" href="https://join.dcloud.cisco.com/"><span class="badge lefty round-label">'.date('d',$date).'<br>'.date('M',$date).'</span>':'<a target="_blank" href="'.site_url('/live').'/'.$stat . '/' . $param['live_id'].'"><span class="badge lefty round-label">'.date('d',$date).'<br>'.date('M',$date).'</span>';
                                                                $return .= '<span class="event-title">Live session <b>"'.$param["cl_lecture_name"].'"</b> scheduled.</span><br/>';
                                                                $return .= '<span class="event-date">'.date('M d, Y  ',$date).'&nbsp;&nbsp;&nbsp;'.date("g:i A", strtotime($param['ll_time']));'</span></a></li>';
                                                            break;

                                                            case 0:
                                                            $date = strtotime($param["ev_date"]);
                                                                $return .= '<li class="list-group-item single-event">';
                                                                $return .= '<a target="_blank" href="'.site_url('events').'/event/'.base64_encode($param['id']).'"><span class="badge lefty round-label">'.date('d',$date).'<br>'.date('M',$date).'</span>';
                                                                $return .= '<span class="event-title">Event named <b>"'.$param["ev_name"].'"</b> has been scheduled.</span><br/>';
                                                                $return .= '<span class="event-date">'.date('M d, Y  ',$date).'&nbsp;&nbsp;&nbsp;'.date('g:i A',strtotime($param['ev_time'])).'</span></a></li>';
                                                            break;

                                                        }
                                                    return $return;
                                                  } ?>

                                                  <?php foreach($upcomming as $upcomming_event): 
                                                            echo render_upcomming($upcomming_event);
                                                        endforeach;
                                                  ?>                               
                                        </ul>
                                    <?php endif; ?>
                                </div>
                               

                                
                                <div id="my-calendar"></div>


                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-xs-8 col-sm-8 col-md-8">
                                        <h4 class="sd-course-title">My Subscriptions</h4>
                                    </div>  
                                    <?php if(!empty($courses)):  ?>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <a href="<?php echo site_url().'dashboard/courses'?>"  class="more-challenges sd-challenge">View All</a>
                                    </div>   
                                    <?php endif; ?>

                                </div>

                                <?php if(!empty($courses)): $count = 0; ?>
                                <?php foreach($courses as $course_id => $course): ?>
                                <?php if($count < 2):$count++;else:break;endif;
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
                                            $header_url = site_url('materials/course/'.$course['course_id'].'#'.$course['cs_last_played_lecture']);
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
                                    <a class="no-under-line" onclick="<?php echo $onclick; ?>" href="<?php echo $footer_url; ?>">
                                        <div class="my-course course-block-1">
                                            <div class="course-img"><img style="border-radius: 4px 0 0 4px;" src="<?php echo $image ?>"></div>
                                            <div class="course-cont">
                                                <div class="block-head"><?php echo $course['cb_title'] ?></div>
                                                <p class="sub-head-des">By <?php echo implode($tutors,', ') ?></p>
                                                <p><span class="text-left spanblock"><?php echo $courses[$course_id]['cl_lecture_name']; ?> <?php //echo ($course['total_lectures']>0)?'Lectures :'.$course['total_lectures']:'' ?></span><span class="text-right spanblock"><?php echo round($course['percentage']) ?>%</span></p>
                                                <div class="progress course-progress">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo round($course['percentage']) ?>%">
                                                        <span class="sr-only"><?php echo round($course['percentage']) ?>% Complete</span>
                                                    </div>
                                                </div>                                
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="row">    
                                        <div class="col-sm-12 dashboard-no-course">
                                            <div class="no-course-container">
                                                <img class="no-questions-svg" src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/no-courses.svg">
                                                <?php /* ?><span class="no-discussion no-content-text"><span>Add Courses </span>From Top Institutes and Renowned Teachers</span> <?php */ ?>
                                                <div class="text-center">
                                                <span class="noquestion-btn-wrap"><a href="<?php echo site_url('course/listing') ?>" class="orange-flat-btn noquestion-btn">Explore Courses</a></span>
                                                </div><!--text-center-->
                                            </div>                 
                                        </div>              
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-sm-6">
                            <?php if($dashboard_notification_html): ?>
                                <div class="row">
                                    <div class="col-xs-8 col-sm-8 col-md-8">
                                        <h4 class="sd-course-title">Notifications</h4>
                                    </div>                        
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <?php /* ?><a href="#" class="more-challenges sd-challenge">View All</a> <?php */ ?>
                                    </div>                        

                                </div>
                                <div class="my-notify course-block-1">
                                    <ul class="list-group notification" id="notification_ul_dash">
                                    </ul> 
                                </div>                        
                                <?php endif; ?>
                                    
                            </div>                    
                        </div>   
                    </div>
           
            </div>  <!--container-reduce-width-->
        </div><!--container altr-->       
    </div><!--all-challenges-->
</section>

<script type="text/javascript">
    var __user_id  = '<?php echo $session['id']; ?>';
    var __busy     = false;
    var __site_url = '<?php echo site_url(); ?>';  
</script>

<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/zabuto_calendar.js"></script>
<script type="text/javascript">
    var __site_url = '<?php echo site_url(); ?>';
    var __month_data;
    var __assets_url = '<?php echo assets_url(); ?>';
    $(document).ready(function () {
        $("#my-calendar").zabuto_calendar({
            ajax: {
                url: __site_url+"dashboard/calendar_events",
                modal: false
            },
            show_previous: false,
            today: true
            ,action: function() { myDateFunction(this.id); }
        });
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
        var date       = day_data['date'].split('-');
        var months     = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var monthsfull = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        var event_list = '';
        $('.sdpk-modal-title').html('<span class="badge lefty round-label main-label"><span class="big-modal-date">'+date[2]+'</span><br/>'+months[(date[1]-1)]+'</span>Events on '+monthsfull[date[1]-1]+' '+date[2]+', '+date[0]);
        $.each(day_data['events'],function(key, event) {
            event_list += '<li class="list-group-item single-event modal-event">';
            event_list += '<a target="_blank" href="'+event['link']+'"><span style="display: inline-block;" class="badge lefty round-label modal-badge"><img src="'+__assets_url+'themes/ofabee/img/date.svg" width="24" style="position: relative; z-index: 999999;"></span>';
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