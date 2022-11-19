                    
<div class="row">
    <?php if(isset($assessment_courses) && !empty($assessment_courses)): ?>
    <div class="col-xs-6 col-sm-6 col-md-6"><span class="text-left center-block my-dash-result"><h3><?php echo lang('assesment_results'); ?></h3></span></div>
    <div class="col-xs-6 col-sm-6 col-md-6"><span class="text-right center-block my-dash-result"><h4 id="total_assessments"></h4></span></div>
    <?php endif; ?>
    <div class="col-xs-12 col-sm-12 col-md-12">
        
        <?php $marks = array(); $no_data_found = true; ?>
        <?php if(isset($assessment_courses) && !empty($assessment_courses)): ?>
            <?php foreach($assessment_courses as $assessment_course): ?>
                <ul class="discussion-forum-parent my-dshbord-mar-top">
                    <li class="discussion-forum-white-lists my-dshbord-head">
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <span class="my-dashbord-head"><?php echo $assessment_course['cb_title'] ?></span>
                            </div><!--columns-->
                            <div style="visibility:hidden;" class="col-md-3 col-md-offset-1 col-sm-3 col-sm-offset-1 col-xs-12 text-center">
                                <span class="my-dashbord-point">102</span>
                                <span class="my-dashbord-eng-points">Engagement Points</span>
                            </div><!--columns-->
                            <div class="col-md-2 col-sm-2 col-xs-12 text-center">
                                <span class="my-dashbord-point" id="total_assessment_mark_<?php echo $assessment_course['a_course_id'] ?>"></span>
                                <span class="my-dashbord-eng-points">Total Marks</span>
                            </div><!--columns-->
                        </div><!--row-->
                    </li>
                    <?php $marks[$assessment_course['a_course_id']] = array(); ?>
                    <?php if(isset($assessment_course['attempts']) && !empty($assessment_course['attempts'])): ?>
                    <?php $no_data_found = false; ?>
                        <?php foreach($assessment_course['attempts'] as $attemp): ?>
                            <li class="discussion-forum-white-lists dashboard-courses">
                                <span class="forum-title-wrap result-title-wrap forum-title-wrap-alter-index">
                                    <span class="dashboard-result-icon"></span>
                                    <span class="forum-des result-des"><?php echo $attemp['cl_lecture_name'] ?></span>
                                </span><!--forum-title-wrap-->
                                <?php $marks[$assessment_course['a_course_id']][] = $attemp['total_mark']; ?>
                                <span class="topic-xs result-mid-box">
                                    <span class="topic-form-text quarter-result date-time-hide"><strong><?php echo date("F j, Y", strtotime($attemp['aa_attempted_date'])) ?></strong> <br/>Date Attended</span>
                                    <span class="topic-form-text quarter-result date-time-hide"><strong><?php echo secondsToTime($attemp['aa_duration']) ?></strong> <br/>Time Taken</span>
                                    <span class="topic-form-text quarter-result"><strong><?php echo ($attemp['total_mark'])?round($attemp['total_mark'], 2):'0' ?></strong><br/>Marks Scored</span>
                                    <?php /* ?><span class="topic-form-text quarter-result"><strong>120</strong><br/>Your Rank</span><?php */ ?>
                                </span>   <!--topic-xs-->  
                                <span class="last-post-forum-text last-col-result t-dash-details">
                                    <span class="by-name"><a href="<?php echo site_url('material/assesment_report_item/'.$attemp['a_attempt_id']) ?>" class="name-orange dash-result-link">View Details</a></span><!--by-name-->
                                </span><!--last-post-forum-text-->
                            </li><!--discussion-forum-white-lists"-->
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php /* ?><div class="my-dashbord-load-more">
                        <Span>View More Results</Span>
                    </div><!--my-dashbord-load-more-->   <?php */ ?>               
                </ul>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <?php if(isset($challenge_zone_attempts) && !empty($challenge_zone_attempts)): ?>
    <div class="col-xs-6 col-sm-6 col-md-6"><span class="text-left center-block my-dash-result"><h3>Online Test Results</h3></span></div>
    <div class="col-xs-6 col-sm-6 col-md-6"><span class="text-right center-block my-dash-result"><h4><?php echo sizeof($challenge_zone_attempts) ?> Online <?php echo sizeof($challenge_zone_attempts)>1?'Tests':'Test'; ?></h4></span></div>
    <?php endif; ?>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <ul class="discussion-forum-parent">
        <?php if(isset($challenge_zone_attempts) && !empty($challenge_zone_attempts)): ?>
                    <?php $no_data_found = false; ?>
            <?php foreach($challenge_zone_attempts as $challenge_zone_attempt): ?>
                <li class="discussion-forum-white-lists dashboard-challenges mt10">
                    <span class="forum-title-wrap result-title-wrap forum-title-wrap-alter-index">
                        <span class="dashboard-result-icon"></span>
                        <span class="forum-des result-des"><?php echo $challenge_zone_attempt['cz_title'] ?></span>
                    </span><!--forum-title-wrap-->
                    <span class="topic-xs result-mid-box">
                        <span class="topic-form-text quarter-result date-time-hide"><strong><?php echo date("F j, Y", strtotime($challenge_zone_attempt['cza_attempted_date'])) ?></strong> <br/>Date Attended</span>
                        <span class="topic-form-text quarter-result date-time-hide"><strong><?php echo secondsToTime($challenge_zone_attempt['cza_duration']) ?></strong> <br/>Time Taken</span>
                        <span class="topic-form-text quarter-result"><strong><?php echo ($challenge_zone_attempt['total_mark'])?$challenge_zone_attempt['total_mark']:'0' ?></strong><br/>Marks Scored</span>
                    </span>   <!--topic-xs-->  
                    <span class="last-post-forum-text last-col-result t-dash-details">
                        <span class="by-name"><a href="<?php echo site_url('material/challenge_zone_report_item/'.$challenge_zone_attempt['czr_attempt_id']) ?>" class="name-orange dash-result-link">View Details</a></span><!--by-name-->
                    </span><!--last-post-forum-text-->
                </li><!--discussion-forum-white-lists"-->
            <?php endforeach; ?>
        <?php endif; ?>

        </ul>                         
    </div>
</div>

<div class="row">
    <?php if(isset($user_generated_attempts) && !empty($user_generated_attempts)): ?>
    <div class="col-xs-6 col-sm-6 col-md-6"><span class="text-left center-block my-dash-result"><h3>User Generated Test Results</h3></span></div>
    <div class="col-xs-6 col-sm-6 col-md-6"><span class="text-right center-block my-dash-result"><h4><?php echo sizeof($user_generated_attempts) ?> user generated tests</h4></span></div>
    <?php endif; ?>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <ul class="discussion-forum-parent">
        <?php if(isset($user_generated_attempts) && !empty($user_generated_attempts)): ?>
            <?php foreach($user_generated_attempts as $user_generated_attempt): ?>
                <li class="discussion-forum-white-lists dashboard-usertest mt10">
                    <span class="forum-title-wrap result-title-wrap forum-title-wrap-alter-index">
                        <span class="dashboard-result-icon"></span>
                        <span class="forum-des result-des"><?php echo $user_generated_attempt['uga_title'] ?></span>
                    </span><!--forum-title-wrap-->
                    <span class="topic-xs result-mid-box">
                        <span class="topic-form-text quarter-result date-time-hide"><strong><?php echo date("F j, Y", strtotime($user_generated_attempt['uga_attempted_date'])) ?></strong> <br/>Date Attended</span>
                        <span class="topic-form-text quarter-result date-time-hide"><strong><?php echo secondsToTime($user_generated_attempt['uga_duration']) ?></strong> <br/>Time Taken</span>
                        <span class="topic-form-text quarter-result"><strong><?php echo ($user_generated_attempt['total_mark'])?$user_generated_attempt['total_mark']:'0' ?></strong><br/>Marks Scored</span>
                    </span>   <!--topic-xs-->  
                    <span class="last-post-forum-text last-col-result t-dash-details">
                        <span class="by-name"><a href="<?php echo site_url('material/user_generated_test_report_item/'.$user_generated_attempt['id']) ?>" class="name-orange dash-result-link">View Details</a></span><!--by-name-->
                    </span><!--last-post-forum-text-->
                </li><!--discussion-forum-white-lists"-->
            <?php endforeach; ?>
        <?php endif; ?>
        </ul>                        
    </div>
</div>     
<?php if($no_data_found): ?>
<div class="row">    
    <div class="col-sm-12 dashboard-no-course">
        <div class="no-course-container">
            <img class="no-questions-svg" src="<?php echo assets_url('themes/' . $this->config->item('theme')); ?>img/no-results.svg">
            <span class="no-discussion no-content-text"><span>Oops! </span>No assessments, challenges or tests attended yet.</span>
            <div class="text-center">
                <span class="noquestion-btn-wrap mobile-btn"><a href="javascript:void(0)" class="orange-flat-btn noquestion-btn">Attend Now</a></span>
            </div><!--text-center-->
        </div>                 
    </div>              
</div>
<?php endif; ?>
<script>
var __assessmentResultTotalMark = $.parseJSON(atob('<?php echo base64_encode(json_encode($marks)) ?>'));
$(document).ready(function(){
        if(Object.keys(__assessmentResultTotalMark).length > 0 )
        {
            var totalAssessments = 0;
            $.each(__assessmentResultTotalMark, function(courseId, marks )
            {
                if(Object.keys(marks).length > 0 )
                {
                    var totalMark = 0;
                    $.each(marks, function(key, mark )
                    {
                        totalMark = totalMark+Number(mark);
                        totalAssessments++;
                    });
                    $('#total_assessment_mark_'+courseId).html(totalMark);
                    $('#total_assessments').html(totalAssessments+' Assessments');
                }

            });
        }
});
</script>
<?php 
function secondsToTime($seconds)
{

  // extract hours
  $hours = floor($seconds / (60 * 60));

  // extract minutes
  $divisor_for_minutes = $seconds % (60 * 60);
  $minutes = floor($divisor_for_minutes / 60);

  // extract the remaining seconds
  $divisor_for_seconds = $divisor_for_minutes % 60;
  $seconds = ceil($divisor_for_seconds);

  $return       = '';
  if($hours > 0)
  {
      //$return .= $hours.':'; 
      $minutes = $minutes+($hours*60);
  }
  if($minutes > 0)
  {
      if($minutes > 9)
      {
          $return .= $minutes.':';           
      }
      else
      {
          $return .= '0'.$minutes.':'; 
      }
  }
  else
  {
     $return .= '00:'; 
  }
  if($seconds > 0)
  {
      if($seconds > 9)
      {
          $return .= $seconds;       
      }
      else
      {
          $return .= '0'.$seconds;                 
      }
  }
  else
  {
     $return .= '00'; 
  }
  return $return;
}
?>