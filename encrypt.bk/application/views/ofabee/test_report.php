<?php    //echo '<pre>'; print_r($lecture);  ?>

<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8">
        <title><?php echo isset($title) ? $title : config_item('site_name'); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php if (!isset($meta_original_title)) {
            $meta_original_title = config_item('site_name');
        } ?>
        <meta name="title" content="<?php echo isset($meta_title) ? $meta_title : $meta_original_title; ?>">
        <meta name="description" content="<?php echo isset($meta_description) ? $meta_description : config_item('meta_description'); ?>">
            
<?php if (isset($page) && $page == 'coursedescription') { ?>
            <meta property="og:url"           content="<?php echo $current_url; ?>" />
            <meta property="og:type"          content="website" />
            <meta property="og:title"         content="<?php echo $meta_original_title; ?>" />
            <meta property="og:description"   content="<?php echo $meta_description; ?>" />
<?php } ?>
        <?php $logo = $this->config->item('site_logo');
        $logo = ($logo == 'default.png')?base_url('uploads/site/logo/default.png'):base_url().logo_path().$logo;
        ?>
        <meta property="og:image" content="<?php echo $logo; ?>" />
        <meta property="og:image:width" content="400" />
        <meta property="og:image:height" content="300" />
        
        <link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/discussion-customized.css">
        <link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/custom_beta.css">
        <link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/addon.css">
        <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/font-awesome.min.css" rel="stylesheet">
        <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/bootstrap-select.css" rel="stylesheet">
        <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/owl.carousel.css" rel="stylesheet">
        <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/flexslider.css" rel="stylesheet">
        <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/starability-all.min.css" rel="stylesheet">
        <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/toastr.min.css" rel="stylesheet">
        <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/bootstrap-multiselect.css" rel="stylesheet">
        <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/fontello.css" rel="stylesheet">
        <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/forum/css/chrome-css.css" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/bootstrap-tokenfield.css">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
        

        <?php /* ?><link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/bootstrap.css" rel="stylesheet">
          <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/font-awesome.min.css" rel="stylesheet">
          <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/bootstrap-select.css" rel="stylesheet">
          <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/owl.carousel.css" rel="stylesheet">
          <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/flexslider.css" rel="stylesheet">
          <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/starability-all.min.css" rel="stylesheet">
          <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/styles.css" rel="stylesheet"> <?php
          <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/custom.css" rel="stylesheet">
          <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/toastr.min.css" rel="stylesheet"> */ ?>

        <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/developer.css" rel="stylesheet">
        <style>
            .fixed {
                position: fixed;
                top:0; left:0;
                width: 100%; 
                z-index:10;
                background: rgba(46, 51, 56, 0.2)
            }
            .absolute{
                width: 100%;
                position: absolute;
                z-index:10;
                background: rgba(46, 51, 56, 0.2)
            }
            .sticky p {
                margin-top: 3px;
                font-size: 16px;
                color: #fe8000;
                text-align: center;
                font-family: 'Open Sans', sans-serif;
            }
            .go-live{
                background: #fe8000 none repeat scroll 0 0;
                border: 1px solid #fe8000;
                border-radius: 4px;
                color: #fff;
                padding: 2px 5px;
                cursor: pointer;
                font-size:15px;
            }
            .question-master-parent img, .what-are-some-para img{
              min-width:0;
              min-height:0;
            }
            .no-in-round{ 
              background: none;
              color: #3B4EA9;
              width: auto;
              padding: 0px;
              font-size: 16px;
              font-weight: 600;
              height: 38px;
              display: inline-block;
            }
            span.no-in-round:after {content: '.';}
            .single-choice-label{margin-left:0px;}
            .overflow-x-auto{overflow-x:auto;}
            .reveal-answer{word-break:break-word !important;}
            .reveal-answer li{word-break:break-word !important;}

            .reveal-answer p img{
              float: unset!important;
              height: unset!important;
              width: unset!important;
            }
            .what-are-some-para p img{
            float: unset!important;
            height: unset!important;
            width: unset!important;
            }
        </style>

        <script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/jquery.min.js"></script>

        <script type="text/javascript">

            var base_url = '<?php echo site_url('/'); ?>';
            var admin_url = '<?php echo admin_url(); ?>';
            var assets_url = '<?php echo assets_url(); ?>';
            var current_category = "<?php if (isset($category_id)) {
            echo $category_id;
        } else {
            echo '0';
        } ?>";
            //$('#cat_text').html('');
            $(document).ready(function () {
                if (current_category != '0') {
                    $('#category_heading').html($('#curr_category_' + current_category).text().charAt(0).toUpperCase() + $('#curr_category_' + current_category).text().slice(1));
                    $('#cat_text').contents().first()[0].textContent = $('#curr_category_' + current_category).text().charAt(0).toUpperCase() + $('#curr_category_' + current_category).text().slice(1);
                }
            });
            $(document).on('click', '#basic li', function () {
                var category_id = $(this).attr('id');
                var category_slug = $(this).attr('data-link');
                window.location.href = base_url + "" + category_slug;
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
<section id="fundamentals">
	<div class="fundamentals">

          <?php 
          $assessment_attempt_details['aa_assessment_detail'] = json_decode($assessment_attempt_details['aa_assessment_detail'], true);
          //echo '<pre>'; print_r($assessment_attempt_details);die;
            $assesment_date     = $assessment_attempt_details['aa_attempted_date'];
            $attempted_date     = date("M j Y", strtotime($assesment_date));
            
            //$attended_time      = $assessment_attempt_details['aa_duration']/60;
            //$duration_in_minutes= substr($attended_time, 0, 4); 
            // <div class="report-close" onclick="self.close()">&times;</div> 
            $duration_in_minutes= secondsToTime($assessment_attempt_details['aa_duration']);
          ?>

      <div class="d-flex align-center test-report-title">
          <div class="d-flex align-center">
              <span class="report-date"><?php echo $attempted_date; ?></span>
              <span class="report-title"><?php echo (isset($lecture_name['cl_lecture_name'])? $lecture_name['cl_lecture_name'] : '');?></span>
          </div>
          <?php if($user_token != '' && $quick_report == 'true'): ?>
          
          <div class="report-close" onclick="close_detail_report()">&times;</div>
          <?php endif; ?>
          <?php if($user_token != '' && $quick_report == 'false'): ?>
          <div class="report-close" onclick="history.back()">&times;</div>
          <?php endif; ?>
      </div>

    	<div class="container">
        <div class="container-reduce-width">
         
        	<span class="funda-date hidden-xs"><?php echo $attempted_date; ?></span>
            <h2 class="funda-head hidden-xs"><?php echo (isset($lecture_name['cl_lecture_name'])? $lecture_name['cl_lecture_name'] : '');?></h2>
            <ul class="funda-strip clearfix">
            <?php if(!empty($lecture['questions'])):?>
            <?php $full_questions = 0; $right_answer_full = 0; $wrong_answer_full = 0; $unattended_answer_full = 0; $full_accuracy = 0; $full_time_taken = 0; $full_marks_scored = 0; ?>
            <?php foreach ($lecture['questions'] as $key => $full_question): ?>
                <?php switch($lecture['questions'][$key]['q_type']){
                        case 1:
                          if(!isset($lecture['answers'][$key]['ar_answer'])||$lecture['answers'][$key]['ar_answer'] == ''){
                            $unattended_answer_full++;
                          }else if($lecture['questions'][$key]['q_answer'] == $lecture['answers'][$key]['ar_answer']){
                            $right_answer_full++;
                          }else{
                            $wrong_answer_full++;
                          }
                        break;
                        case 2:
                          if(!isset($lecture['answers'][$key]['ar_answer'])||$lecture['answers'][$key]['ar_answer'] == ''){
                            $unattended_answer_full++;
                          }else if(isset($lecture['answers'][$key]['ar_answer'])&&$lecture['answers'][$key]['ar_answer'] != ''){
                            $right_answers = explode(',',$lecture['questions'][$key]['q_answer']);
                            $my_answers = explode(',',$lecture['answers'][$key]['ar_answer']);
                            $results = array_diff($right_answers,$my_answers);
                              if(empty($results) && (count($right_answers) == count($my_answers))){
                                $right_answer_full++;
                              }else{
                                $wrong_answer_full++;
                              }
                          }
                        break;
                        case 3:
                          if(!isset($lecture['answers'][$key]['ar_answer'])||$lecture['answers'][$key]['ar_answer'] == ''){ 
                            $unattended_answer_full++;
                          }else{
                            if($lecture['answers'][$key]['ar_mark'] > 0){
                              $right_answer_full++;
                            }else{
                              $wrong_answer_full++;
                            }
                          }
                        break;
                        case 4:
                        if(!isset($lecture['answers'][$key]['ar_answer'])||$lecture['answers'][$key]['ar_answer'] == ''){ 
                          $unattended_answer_full++;
                        }else{
                          if($lecture['answers'][$key]['ar_mark'] > 0){
                            $right_answer_full++;
                          }else{
                            $wrong_answer_full++;
                          }
                        }
                      break;
                        } ?>

            <?php if(isset($lecture['answers'][$key])) :?>
            <?php $full_time_taken = $full_time_taken + $lecture['answers'][$key]['ar_duration']; endif;?>

            <?php if(isset($lecture['answers'][$key])) :?>
            <?php $full_marks_scored = $full_marks_scored + $lecture['answers'][$key]['ar_mark']; endif;?>

            <?php if(!isset($lecture['answers'][$key])) :?>
            <?php $full_marks_scored = $full_marks_scored + 0; endif;?>

            <?php endforeach; endif; ?>
            
            	<li>
                	<div class="funda-space">
                    	<span class="funda-strip-number"><?php if(!empty($lecture['questions'])): $full_questions = count($lecture['questions']); ?><?php echo $full_questions;?><?php else: echo 0; endif; ?></span>
                        <span class="fund-strip-text">Questions</span>
                    </div>
                </li>
            	<li>
                	<div class="funda-space">
                    	<span class="funda-strip-number fund-number-green"><?php echo isset($right_answer_full)?$right_answer_full:0; ?></span>
                        <span class="fund-strip-text">Right</span>
                    </div>
                </li>
                
            	<li>
                	<div class="funda-space">
                    	<span class="funda-strip-number fund-number-red"><?php echo isset($wrong_answer_full)?$wrong_answer_full:0; ?></span>
                        <span class="fund-strip-text">Wrong</span>
                    </div>
                </li>
                
            	<li>
                	<div class="funda-space">
                    	<span class="funda-strip-number fund-number-blue"><?php echo isset($unattended_answer_full)?$unattended_answer_full:0; ?></span>
                        <span class="fund-strip-text">Unattended</span>
                    </div>
                </li>
                
            	<li>
                	<div class="funda-space">
                    <?php if(!empty($lecture['questions'])): ?><?php $right_answer_full = isset($right_answer_full)?$right_answer_full:0; $full_accuracy = round(($right_answer_full*100)/count($lecture['questions'])); ?><?php endif; ?>
                    	<span class="funda-strip-number"><?php echo isset($full_accuracy)?$full_accuracy:0; ?>%</span>
                        <span class="fund-strip-text">Accuracy</span>
                    </div>
                </li>
                
            	<li>
                	<div class="funda-space">
                    
                    	<span class="funda-strip-number"><?php echo $duration_in_minutes//$full_time_taken; ?></span> 
                        <span class="fund-strip-text">Time Taken</span>
                    </div>
                </li>
            	<li class="mark-scored-green">
                	<div class="funda-space funda-space-bg-rm">
                    	<span class="funda-strip-number color-white"><?php echo isset($assessment_attempt_details['aa_mark_scored'])?$assessment_attempt_details['aa_mark_scored']:0; ?></span>
                        <span class="fund-strip-text color-white">Marks Scored</span>
                    </div>
                </li>  
                <?php if($user_token == ''): ?>
                <li class="view-rank-list-wraper">
                	<ul class="view-mark-list-ul">
                      <li><a href="<?php echo site_url('dashboard')?>" class="orange-flat-btn">Back to Dashboard</a></li>
                      <?php /* ?><li><a href="<?php echo site_url('report/assessment/'.$assessment_attempt_details['aa_assessment_id'])?>" class="orange-flat-btn">View rank list</a></li><?php */ ?>
                    </ul>
                </li> 
                <?php endif; ?>
            </ul>            
            </div><!--container-reduce-width-->
        </div><!--container-->    
    </div><!--fundamentals-->
</section>

<section>
    <div class="bottom-contents">
    	<div class="container">
         <div class="container-reduce-width">
         <div class="table-wrapper">
        	<div class="table-responsive">          
              <table class="table">
                <thead>
                  <tr class="table-black-head">
                  <!--<th class="table-th">Topics</th>-->
                    <th class="table-th">Subjects</th>
                    <th class="table-th">No.of Qns</th>
                    <th class="table-th">Right</th>
                    <th class="table-th">Wrong</th>
                    <th class="table-th">Unattended</th>
                    <th class="table-th accuracy-highlight">Accuracy</th>
                    <th class="table-th">Remarks</th>
                  </tr>
                </thead>
                <tbody class="tbody-border">
                <?php $number = 0; if(!empty($lecture['category_questions'])):?>
                <?php foreach($lecture['category_questions'] as $key => $category_question):?>
                  <tr class="tabel-white-strip">
                    <td class="table-sub-rows"><?php echo $lecture['categories'][$key]['qs_subject_name']; ?></td>
                    <td class="table-sub-rows"><?php echo count($lecture['sl_no'][$key]); ?></td>
                <?php $right_answer = 0; $wrong_answer = 0; $unattended_answer = 0; $remarks = ''; $accuracy = '0'; ?>
                <?php if(!empty($category_question)) : ?>
                <?php foreach($category_question as $question_category): ?>
                    <?php switch($lecture['questions'][$question_category]['q_type']){
                            case 1:
                              if(isset($lecture['answers'][$question_category]['ar_answer'])&&$lecture['answers'][$question_category]['ar_answer']!=''){
                                if($lecture['answers'][$question_category]['ar_answer'] == $lecture['questions'][$question_category]['q_answer']):
                                  $right_answer++;
                                else:
                                  $wrong_answer++;
                                endif;
                              }else{
                                $unattended_answer++;      
                              }
                            break;
                            case 2:
                              if(isset($lecture['answers'][$question_category]['ar_answer'])&&$lecture['answers'][$question_category]['ar_answer']!=''){
                                $right_answers = explode(',',$lecture['questions'][$question_category]['q_answer']);
                                $my_answers = explode(',',$lecture['answers'][$question_category]['ar_answer']);
                                $results = array_diff($right_answers,$my_answers);
                                if(empty($results) && (count($right_answers) == count($my_answers))):
                                  $right_answer++;
                                else:
                                  $wrong_answer++;
                                endif;
                              }else{
                                $unattended_answer++;
                              }
                            break;
                            case 3:
                              if(isset($lecture['answers'][$question_category]['ar_answer'])&&$lecture['answers'][$question_category]['ar_answer']!=''){
                                if($lecture['answers'][$question_category]['ar_mark']>0):
                                  $right_answer++;
                                else:
                                  $wrong_answer++;
                                endif;
                              }else{
                                $unattended_answer++;
                              }
                            break;
                            case 4:
                              if(isset($lecture['answers'][$question_category]['ar_answer'])&&$lecture['answers'][$question_category]['ar_answer']!=''){
                                if($lecture['answers'][$question_category]['ar_mark']>0):
                                  $right_answer++;
                                else:
                                  $wrong_answer++;
                                endif;
                              }else{
                                $unattended_answer++;
                              }
                            break;
                          } ?>

                <?php endforeach; endif; ?>

                	<td class="table-sub-rows"><span class="fund-number-green"><?php echo $right_answer; ?></span></td>
                    <td class="table-sub-rows"><span class="fund-number-red"><?php echo $wrong_answer; ?></span></td>
                    <td class="table-sub-rows"><span class="fund-number-blue"><?php echo $unattended_answer; ?></span></td>
                    <?php //echo '<pre>';print_r($lecture);die; ?>
                <?php $accuracy = ($right_answer*100)/count($lecture['sl_no'][$key]); ?>
                    <td class="table-sub-rows high-light-cell"><?php echo round($accuracy,2); ?>%</td>
                <?php 
                switch ($accuracy) {

                  case ($accuracy>='90'):
                      $remarks = 'Excellent';
                      break;

                  case ($accuracy>'70' && $accuracy<='89'):
                      $remarks = 'Good';
                      break;

                  case ($accuracy>'60' && $accuracy<='70'):
                      $remarks = 'Average';
                      break;

                  default:
                      $remarks = 'Needs Improvement';
                      break;
                 } if($accuracy=='0') { $remarks = 'Needs Improvement'; }
                ?>
                    <td class="table-sub-rows"><span class="fund-orange"><?php echo $remarks; ?></span></td>
                  </tr>    
                <?php $number++; endforeach; endif; ?>
                </tbody>
              </table>
 			 </div><!--table-responsive-->
            </div><!--table-wrapper-->
            </div>	<!--container-reduce-width-->
        </div><!--container-->       
    </div><!--bottom-contents-->
</section>

<section>

    <div class="do-nuts-diagram">
    	<div class="container">
        	<div class="container-reduce-width">
        	<div class="row">

  <?php $question_difficulty = array(1=>"Easy</br>Questions",2=>"Medium</br>Questions",3=>"Hard</br>Questions"); ?>
    <?php 
    foreach($question_difficulty as $difficulty_type => $difficulty_text)
    {
        if(!isset($lecture['question_difficulty_type'][$difficulty_type]))
        {
            $lecture['question_difficulty_type'][$difficulty_type] = array();
        }        
    }
    ?>
<?php //echo '<pre>'; print_r($lecture['question_difficulty_type']);die; ?>
  <?php if(!empty($lecture['question_difficulty_type'])):?>
  <?php foreach($lecture['question_difficulty_type'] as $key=>$difficulty_question):?>

        <?php $right_answer_difficulty = 0; $wrong_answer_difficulty = 0; $unattended_answer_difficulty = 0; ?>
        <?php if(!empty($difficulty_question)) : ?>
        <?php foreach($difficulty_question as $question_each): 
          ?>
          <?php switch($lecture['questions'][$question_each]['q_type']){
                            case 1:
                              if(isset($lecture['answers'][$question_each]['ar_answer'])&&$lecture['answers'][$question_each]['ar_answer']!=''){
                                if($lecture['answers'][$question_each]['ar_answer'] == $lecture['questions'][$question_each]['q_answer']):
                                  $right_answer_difficulty++;
                                else:
                                  $wrong_answer_difficulty++;
                                endif;
                              }else{
                                $unattended_answer_difficulty++;      
                              }
                            break;
                            case 2:
                              if(isset($lecture['answers'][$question_each]['ar_answer'])&&$lecture['answers'][$question_each]['ar_answer']!=''){
                                $right_answers = explode(',',$lecture['questions'][$question_each]['q_answer']);
                                $my_answers = explode(',',$lecture['answers'][$question_each]['ar_answer']);
                                $results = array_diff($right_answers,$my_answers);
                                if(empty($results) && (count($right_answers) == count($my_answers))):
                                  $right_answer_difficulty++;
                                else:
                                  $wrong_answer_difficulty++;
                                endif;
                              }else{
                                $unattended_answer_difficulty++;
                              }
                            break;
                            case 3:
                              if(isset($lecture['answers'][$question_each]['ar_answer'])&&$lecture['answers'][$question_each]['ar_answer']!=''){
                                if($lecture['answers'][$question_each]['ar_mark']>0):
                                  $right_answer_difficulty++;
                                else:
                                  $wrong_answer_difficulty++;
                                endif;
                              }else{
                                $unattended_answer_difficulty++;
                              }
                            break;
                            case 4:
                              if(isset($lecture['answers'][$question_each]['ar_answer'])&&$lecture['answers'][$question_each]['ar_answer']!=''){
                                if($lecture['answers'][$question_each]['ar_mark']>0):
                                  $right_answer_difficulty++;
                                else:
                                  $wrong_answer_difficulty++;
                                endif;
                              }else{
                                $unattended_answer_difficulty++;
                              }
                            break;
                          } ?>
    <?php endforeach; endif; ?>

      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                  <div class="donuts-wrapper">
                      <div id="doughnutChart_<?php echo $key;?>" class="chart">
                          <div class="doughnutSummary">
                              <label><?php echo (isset($question_each)&&$question_each!='')?$question_difficulty[$lecture['questions'][$question_each]['q_difficulty']]:$question_difficulty[$key];?></label>
                            </div><!--doughnutSummary-->
                        </div><!--chart-->
                       <div class="diagram-bottom-detail-holder clearfix">
                          <span class="diagram-color-code-holder">
                            <span class="diagram-left-items ">
                              <span class="green-bar"></span>
                                <span class="diagram-bar-text">Right Answers</span>
                            </span><!--diagram-left-items-->
                            
                            <span class="diagram-left-right clearfix">
                                <span class="diagram-bar-text" id="right_graph_<?php echo $key;?>"><?php echo $right_answer_difficulty; ?></span>
                            </span><!--diagram-left-items-->
                            
                          </span><!--diagram-color-code-holder-->  
                       </div><!--diagram-bottom-detail-holder-->
                       
                       <div class="diagram-bottom-detail-holder clearfix">
                          <span class="diagram-color-code-holder">
                            <span class="diagram-left-items ">
                              <span class="green-bar red-bar"></span>
                                <span class="diagram-bar-text">Wrong Answers</span>
                            </span><!--diagram-left-items-->
                            
                            <span class="diagram-left-right clearfix">
                                <span class="diagram-bar-text" id="wrong_graph_<?php echo $key;?>"><?php echo $wrong_answer_difficulty; ?></span>
                            </span><!--diagram-left-items-->
                            
                          </span><!--diagram-color-code-holder-->  
                       </div><!--diagram-bottom-detail-holder-->
                       
                       <div class="diagram-bottom-detail-holder clearfix">
                          <span class="diagram-color-code-holder">
                            <span class="diagram-left-items ">
                              <span class="green-bar blue-bar"></span>
                                <span class="diagram-bar-text">Unattended</span>
                            </span><!--diagram-left-items-->
                            
                            <span class="diagram-left-right clearfix">
                                <span class="diagram-bar-text" id="unattended_graph_<?php echo $key;?>"><?php echo $unattended_answer_difficulty; ?></span>
                            </span><!--diagram-left-items-->
                            
                          </span><!--diagram-color-code-holder-->  
                       </div><!--diagram-bottom-detail-holder-->

                    </div><!--donuts-wrapper-->
                </div><!--columns-->
<?php $question_each = ''; ?>
  <?php endforeach; endif; ?>  
               
            </div><!--row-->
           </div><!--container-reduce-width--> 
        </div><!--container-->	    
    </div><!--do-nuts-diagram-->
</section>

<section>
	<div class="all-question-above">
    	<div class="container">
        	<div class="container-reduce-width">
            	<div class="row">
                	<div class="col-lg-6 col-md-7 col-sm-6">
                    	<span class="show-label hidden-xs hidden-sm">Show</span>
                        <span class="all-questions-wrapper">
                        	<div class="dropdown">
                              <button class="btn btn-outline dropdown-toggle" type="button" data-toggle="dropdown" id="filter_button">All questions
                              <span class="dropdown-arrow-down">
                              <svg version="1.1" x="0px" y="0px"width="21px" height="17px" viewBox="0 0 21 17" enable-background="new 0 0 21 17" xml:space="preserve">
                            <g>
                                <g>
                                    <path fill="#808080" d="M16.2,4.2L10.5,10L4.8,4.2L3,6l7.5,7.5L18,6L16.2,4.2z"/>
                                    <path fill="none" d="M-4.5-6.5h30v30h-30V-6.5z"/>
                                </g>
                            </g>
							</svg>
                            </span></button>
                              <ul class="dropdown-menu dropdown-menu-width" id="filter_answer">
                                <li id="all"><a>All questions</a></li>
                                <li id="right"><a>Right</a></li>
                                <li id="wrong"><a>Wrong</a></li>
                                <li id="unattended"><a>Unattended</a></li>
                              </ul>
                           </div>
                        </span><!--all-questions-wrapper-->
                        <span class="showing-questions-wraper hidden-sm hidden-xs">
                        	Showing <b class="filter_count">&nbsp;<?php if(!empty($lecture['questions'])):?> <?php echo count($lecture['questions']); endif;?>&nbsp;</b> of <b class="all_count">&nbsp;<?php if(!empty($lecture['questions'])):?> <?php echo count($lecture['questions']); endif;?>&nbsp;</b> questions
                        </span><!--showing-questions-wraper-->
                    </div><!--columns-->
                    <?php /* ?>
                    <div class="col-lg-6 col-md-5 col-sm-6 clearfix">
                    	<span class="to-right">
                    		<span class="compare-score-text-wrap hidden-sm hidden-xs">Compare your score with others</span>
                        <span class="black-btn-wraper">
                        	<a href="<?php echo site_url('report/assessment/'.$assessment_attempt_details['aa_assessment_id'])?>" class="btn btn-black">Compare</a>
                        </span><!--black-btn-wraper-->
                        </span><!--pull-right-->
                    </div><!--columns--> <?php */ ?>
                </div><!--row-->
                <span class="hidden-lg hidden-md- hidden-sm showing-questions-xs">Showing <b class="filter_count">&nbsp;<?php if(!empty($lecture['questions'])):?> <?php echo count($lecture['questions']); endif;?>&nbsp;</b> of <b class="all_count">&nbsp;<?php if(!empty($lecture['questions'])):?> <?php echo count($lecture['questions']); endif;?>&nbsp;</b> questions</span>
            </div><!--container-reduce-width-->
        </div><!--container-->
    </div><!--all-question-above-->
</section>

<?php
function get_answer_stat($param = array()){
  $return = '';
  switch ($param['question_type']) {
    case 1:
      if($param['right_answer'] == $param['my_answer']){
        $return = get_type($param['type'],1);
      }else{
        $return = get_type($param['type'],2);
      }
    break;

    case 2:
      $q_ans = explode(',',$param['right_answer']);
      $my_ans  = explode(',',$param['my_answer']);
      $rslt_arr = array_diff($q_ans,$my_ans);
      if(empty($rslt_arr) && (count($q_ans) == count($my_ans))){
        $return = get_type($param['type'],1);
      }else{
        $return = get_type($param['type'],2);
      }
    break;

    case 3:
      if($param['my_answer']!=""){
        if($param['mark'] > 0){
          $return = get_type($param['type'],1);
        }else{
          $return = get_type($param['type'],2);
        }
      }else{
        $return = get_type($param['type'],3);
      }
    break;
    
    case 4:
      if($param['my_answer']!=""){
        if($param['mark'] > 0){
          $return = get_type($param['type'],1);
        }else{
          $return = get_type($param['type'],2);
        }
      }else{
        $return = get_type($param['type'],3);
      }
    break;

  }

  return $return;
}

function get_type($type,$rtype){
    $return = '';
    switch ($type){
      case 1:
        switch($rtype){
          case 1:
            $return = 'right';
          break;
          case 2:
            $return = 'wrong';
          break;
          case 3:
            $return = 'unattended';
          break;
        }
        break;
      
      case 2:
        switch($rtype){
          case 1:
            $return = '';
          break;
          case 2:
            $return = 'no-in-round-red';
          break;
          case 3:
            $return = 'round-blue';
          break;
        }
        break;

      case 3:
        switch($rtype){
          case 1:
            $return = 1;
          break;
          case 2:
            $return = 2;
          break;
          case 3:
            $return = 3;
          break;
        }
        break;

      case 4:
        switch($rtype){
          case 1:
            $return = 1;
          break;
          case 2:
            $return = 2;
          break;
          case 3:
            $return = 3;
          break;
        }
        break;
    }
    return $return;
  }
?>

<section>
	<div class="container">
    	<div class="container-reduce-width">
        <?php $character = array(); if(!empty($lecture['questions'])):?>
          <?php 
              $sl_no          = 1;
          ?>
          <?php foreach ($lecture['questions'] as $key => $question):?>
            <?php //echo '<pre>';print_r($question);//die; ?>
            <?php $alphabet_count = 65; ?>
        	   <div class="single-choice-wraper <?php echo (!isset($lecture['answers'][$key])||$lecture['answers'][$key]['ar_answer'] == '')?'unattended':get_answer_stat(array('type'=>1,'question_type'=>$lecture['questions'][$key]['q_type'],'right_answer'=>$lecture['questions'][$key]['q_answer'],'my_answer'=>$lecture['answers'][$key]['ar_answer'],'mark'=>$lecture['answers'][$key]['ar_mark'])); ?>" id="<?php echo $key.'_';?><?php echo !isset($lecture['answers'][$key])?'unattended':get_answer_stat(array('type'=>1,'question_type'=>$lecture['questions'][$key]['q_type'],'right_answer'=>$lecture['questions'][$key]['q_answer'],'my_answer'=>$lecture['answers'][$key]['ar_answer'],'mark'=>$lecture['answers'][$key]['ar_mark'])); ?>">
            	<div class="single-choice-header">
              	<span class="no-in-round <?php echo (!isset($lecture['answers'][$key])||$lecture['answers'][$key]['ar_answer'] == '')?'round-blue':get_answer_stat(array('type'=>2,'question_type'=>$lecture['questions'][$key]['q_type'],'right_answer'=>$lecture['questions'][$key]['q_answer'],'my_answer'=>$lecture['answers'][$key]['ar_answer'],'mark'=>$lecture['answers'][$key]['ar_mark'])); ?>"><?php echo $sl_no; ?></span><!--no-in-round-->
                  <span class="single-choice-label">
                    
                  <?php 
                    if(isset($lecture['question_type_wise']['1'])) :
                      if(in_array($question['id'],$lecture['question_type_wise']['1'])):?>
                        Single choice
                  <?php endif; endif; ?>
                  <?php 
                    if(isset($lecture['question_type_wise']['2'])) :
                      if(in_array($question['id'],$lecture['question_type_wise']['2'])):?>
                        Multiple choice
                  <?php endif; endif; ?>
                  <?php 
                    if(isset($lecture['question_type_wise']['3'])) :
                      if(in_array($question['id'],$lecture['question_type_wise']['3'])):?>
                        Explanatory
                  <?php endif; endif; ?>
                  <?php 
                    if(isset($lecture['question_type_wise']['4'])) :
                      if(in_array($question['id'],$lecture['question_type_wise']['4'])):?>
                        Fill in the blanks
                  <?php endif; endif; ?>
                  <?php if(isset($lecture['answers'][$key]['ar_mark'])){$lecture['answers'][$key]['ar_mark'] = $lecture['answers'][$key]['ar_mark']== -0?0:$lecture['answers'][$key]['ar_mark'];} ?>
                  </span>
                  <span class="what-are-some-para"><?php $question_text = json_decode($question['q_question'],true); echo stripslashes(strip_tags($question_text[1])); ?></span>
                  <div class="question-master-parent">
                    <?php if(!empty($question['options'])): ?>  
                      <span class="question-wrap">
                        <?php $option_count = 0; ?>
                        <?php foreach($question['options'] as $options): ?>
                          <?php 
                            $character[$options['id'].$question['q_question']] = chr($alphabet_count);
                            $option_count++; 
                            $option_position_class = ($option_count%2 == 0)?'text-qus-padding-lefts':'text-qus-padding-rights';
                          ?>


                          <?php if($option_count>2 && ($option_count%2 != 0)):?>
                            </span><span class="question-wrap">
                          <?php endif;?>

                          	<span class="series-of-question <?php echo $option_position_class ?>">
                              	<span class="a-b-c"><?php echo strtolower(chr($alphabet_count++)); ?>)</span>
                            		<span class="text-qus"><?php $option = json_decode($options['qo_options'],true); echo stripslashes(html_entity_decode($option[1])); ?></span><!--text-qus-->
                            </span><!--series-of-question-->
                        <?php endforeach;?>
                      </span><!--question-wrap-->
                    <?php endif;?>
                  </div><!--question-master-parent-->




                    <hr class="hr-alter">
                    <div class="choice-footer-wrap clearfix">
                    <?php $explanation = json_decode($question['q_explanation'],true); ?>
                    <?php switch($lecture['questions'][$key]['q_type']){
                      case 1:
                        if(!isset($lecture['answers'][$key]['ar_answer'])||$lecture['answers'][$key]['ar_answer'] == ''){
                          ?>
                            <div class="choice-footer-wrap clearfix">
                                <span class="unattended-border-xs">
                                    <span class="unattend">Unattended</span> 
                                </span><!--unattended-border-xs-->
                                <span class="your-answer-wrap-left margin-left-for-answer your-answer-wrap-modified">
                                <span class="your-answer-wrap-left-inside-text">Right answer</span>
                                <?php $option_value = 65;
                                  foreach($lecture['questions'][$key]['options'] as $ans_key => $option){
                                    if($lecture['questions'][$key]['q_answer'] == $option['id']){
                                      break;
                                    }else{
                                      $option_value++;
                                    }
                                  }
                                ?>
                                <span class="your-answer-wrap-left-inside-circle"><?php echo strtolower(chr($option_value));?></span>
                              </span>
                                <div class="detail-answer-missed-mark"> 
                                    <span class="mark-missed">Marks you missed <?php echo $lecture['questions'][$key]['q_positive_mark']; ?></span> 
                                    <?php if(isset($explanation[1])&&$explanation[1]!=''): ?>
                                      <span class="answer-exp answer-exp-modified anser-exp-alterd" id="reveal-ans-<?php echo $key; ?>">Answer Explanation</span>  
                                    <?php endif; ?>
                              </div>
                            </div>
                          <?php
                        }else if($lecture['questions'][$key]['q_answer'] == $lecture['answers'][$key]['ar_answer']){
                          ?>
                            <div class="choice-footer-wrap clearfix">
                                <span class="your-answer-wrap-left">
                                    <span class="your-answer-wrap-left-inside-text">Your answer</span>
                                    <?php $option_value = 65;
                                        foreach($lecture['questions'][$key]['options'] as $ans_key => $option){
                                          if($lecture['answers'][$key]['ar_answer'] == $option['id']){
                                            break;
                                          }else{
                                            $option_value++;
                                          }
                                        }
                                      ?>
                                <span class="your-answer-wrap-left-inside-circle"><?php echo strtolower(chr($option_value));?></span>
                                    <span class="right-text-green">Right</span>
                                </span><!--your-answer-wrap-->
                                <span class="your-answer-wrap-right">
                                    <span class="small-device-border">
                                  <span class="time-taken">Time Taken<strong>&nbsp;&nbsp;<?php echo gmdate("i:s", $lecture['answers'][$key]['ar_duration']);?>&nbsp;mm:ss</strong></span>
                                        <span class="marks">Marks <span class="green"><?php echo $lecture['answers'][$key]['ar_mark'];?></span></span>
                                    </span><!--small-device-border-->
                                    <?php if(isset($explanation[1])&&$explanation[1]!=''): ?>
                                      <span class="answer-exp" id="reveal-ans-<?php echo $key; ?>">Answer Explanation</span>
                                    <?php endif; ?>
                                </span><!--your-answer-wrap-->
                            </div>
                          <?php

                        }else{
                          ?>
                            <div class="choice-footer-wrap clearfix">
                                <span class="your-answer-wrap-left your-answer-wrap-left-modified">
                                    <span class="your-answer-wrap-left-inside-text">Your answer</span>
                                    <?php $option_value = 65;
                                        foreach($lecture['questions'][$key]['options'] as $ans_key => $option){
                                          if($lecture['answers'][$key]['ar_answer'] == $option['id']){
                                            break;
                                          }else{
                                            $option_value++;
                                          }
                                        }
                                      ?>
                                <span class="your-answer-wrap-left-inside-circle"><?php echo strtolower(chr($option_value));?></span>
                                    <span class="right-text-green right-text-red margin-right-left wrong-text-modified">Wrong</span>
                              </span><!--your-answer-wrap-->
                                <span class="your-answer-wrap-left margin-left-for-answer your-answer-wrap-modified">
                                    <span class="your-answer-wrap-left-inside-text">Right answer</span>
                                    <?php $option_value = 65;
                                      foreach($lecture['questions'][$key]['options'] as $ans_key => $option){
                                          if($lecture['questions'][$key]['q_answer'] == $option['id']){
                                            break;
                                          }else{
                                            $option_value++;
                                          }
                                        }
                                      ?>
                                <span class="your-answer-wrap-left-inside-circle"><?php echo strtolower(chr($option_value));?></span>
                                </span>
                              <span class="your-answer-wrap-right your-answer-wrap-right-modified">
                                  <span class="small-device-border small-device-border-modified">
                                  <span class="time-taken">Time Taken<strong>&nbsp;&nbsp;<?php echo gmdate("i:s", $lecture['answers'][$key]['ar_duration']);?>&nbsp;mm:ss</strong></span>
                                      <span class="marks marks-right">Marks <span class="green right-text-red"><?php echo $lecture['answers'][$key]['ar_mark'];?></span></span>
                                  </span><!--small-device-border-->
                                  <?php if(isset($explanation[1])&&$explanation[1]!=''): ?>
                                  <span class="answer-exp answer-exp-modified" id="reveal-ans-<?php echo $key; ?>">Answer Explanation</span>
                                  <?php endif; ?>
                              </span><!--your-answer-wrap-->
                            </div>
                          <?php

                        }
                      break;
                      case 2:
                        if(!isset($lecture['answers'][$key]['ar_answer'])||$lecture['answers'][$key]['ar_answer'] == ''){
                          ?>
                            <div class="choice-footer-wrap clearfix">
                                <span class="unattended-border-xs">
                                    <span class="unattend">Unattended</span> 
                                                           
                                </span>
                              <span class="your-answer-wrap-left margin-left-for-answer your-answer-wrap-modified">
                                    <span class="your-answer-wrap-left-inside-text">Right answer</span>
                                    <?php
                                      $right_answers = explode(',',$lecture['questions'][$key]['q_answer']);
                                      foreach($right_answers as $answer){
                                        $option_value = 65;
                                        foreach($lecture['questions'][$key]['options'] as $optn){
                                          if($answer == $optn['id']){
                                            ?>
                                            <span class="your-answer-wrap-left-inside-circle"><?php echo strtolower(chr($option_value));?></span>
                                            <?php
                                            break;
                                          }else{
                                            $option_value++;
                                          }
                                        }
                                      }
                                    ?>
                                </span><!--unattended-border-xs--> 
                                <div class="detail-answer-missed-mark"> 
                                  <span class="mark-missed">Marks you missed <?php echo $lecture['questions'][$key]['q_positive_mark']; ?></span> 
                                  <?php if(isset($explanation[1])&&$explanation[1]!=''): ?>
                                    <span class="answer-exp answer-exp-modified anser-exp-alterd" id="reveal-ans-<?php echo $key; ?>">Answer Explanation</span>  
                                  <?php endif; ?>
                                </div>
                            </div>
                          <?php
                        }else if(isset($lecture['answers'][$key]['ar_answer'])&&$lecture['answers'][$key]['ar_answer'] != ''){
                          $right_answers = explode(',',$lecture['questions'][$key]['q_answer']);
                          $my_answers = explode(',',$lecture['answers'][$key]['ar_answer']);
                          $results = array_diff($right_answers,$my_answers);
                            if(empty($results) && (count($right_answers) == count($my_answers))){ ?>
                              <div class="choice-footer-wrap clearfix">
                                  <span class="your-answer-wrap-left">
                                      <span class="your-answer-wrap-left-inside-text">Your answer</span>
                                      <?php
                                        $my_answers = explode(',',$lecture['answers'][$key]['ar_answer']);
                                        foreach($my_answers as $answer){
                                          $option_value = 65;
                                          foreach($lecture['questions'][$key]['options'] as $optn){
                                            if($answer == $optn['id']){
                                              ?>
                                              <span class="your-answer-wrap-left-inside-circle"><?php echo strtolower(chr($option_value));?></span>
                                              <?php
                                              break;
                                            }else{
                                              $option_value++;
                                            }
                                          }
                                        }
                                      ?>
                                  <span class="right-text-green">Right</span>
                                  </span><!--your-answer-wrap-->
                                  <span class="your-answer-wrap-right">
                                      <span class="small-device-border">
                                      <span class="time-taken">Time Taken<strong>&nbsp;&nbsp;<?php echo gmdate("i:s", $lecture['answers'][$key]['ar_duration']);?>&nbsp;mm:ss</strong></span>
                                          <span class="marks">Marks <span class="green"><?php echo $lecture['answers'][$key]['ar_mark'];?></span></span>
                                      </span><!--small-device-border-->
                                      <?php if(isset($explanation[1])&&$explanation[1]!=''): ?>
                                        <span class="answer-exp" id="reveal-ans-<?php echo $key; ?>">Answer Explanation</span>
                                      <?php endif; ?>
                                  </span><!--your-answer-wrap-->
                              </div>
                            <?php
                            }else{ ?>
                              <div class="choice-footer-wrap clearfix">
                                <span class="your-answer-wrap-left your-answer-wrap-left-modified">
                                  <span class="your-answer-wrap-left-inside-text">Your answer</span>
                                  <?php
                                    $my_answers = explode(',',$lecture['answers'][$key]['ar_answer']);
                                    foreach($my_answers as $answer){
                                      $option_value = 65;
                                      foreach($lecture['questions'][$key]['options'] as $optn){
                                        if($answer == $optn['id']){
                                          ?>
                                          <span class="your-answer-wrap-left-inside-circle"><?php echo strtolower(chr($option_value));?></span>
                                          <?php
                                          break;
                                        }else{
                                          $option_value++;
                                        }
                                      }
                                    }
                                  ?>
                                  <span class="right-text-green right-text-red margin-right-left wrong-text-modified">Wrong</span>
                                </span><!--your-answer-wrap-->
                                <span class="your-answer-wrap-left margin-left-for-answer your-answer-wrap-modified">
                                  <span class="your-answer-wrap-left-inside-text">Right answer</span>
                                <?php
                                    $right_answers = explode(',',$lecture['questions'][$key]['q_answer']);
                                    foreach($right_answers as $answer){
                                      $option_value = 65;
                                      foreach($lecture['questions'][$key]['options'] as $optn){
                                        if($answer == $optn['id']){
                                          ?>
                                          <span class="your-answer-wrap-left-inside-circle"><?php echo strtolower(chr($option_value));?></span>
                                          <?php
                                          break;
                                        }else{
                                          $option_value++;
                                        }
                                      }
                                    }
                                  ?>
                                </span>
                                <span class="your-answer-wrap-right your-answer-wrap-right-modified">
                                  <span class="small-device-border small-device-border-modified">
                                  <span class="time-taken">Time Taken<strong>&nbsp;&nbsp;<?php echo gmdate("i:s", $lecture['answers'][$key]['ar_duration']);?>&nbsp;mm:ss</strong></span>
                                        <span class="marks marks-right">Marks <span class="green right-text-red"><?php echo $lecture['answers'][$key]['ar_mark'];?></span></span>
                                    </span><!--small-device-border-->
                                    <?php if(isset($explanation[1])&&$explanation[1]!=''): ?>
                                      <span class="answer-exp answer-exp-modified" id="reveal-ans-<?php echo $key; ?>">Answer Explanation</span>
                                    <?php endif; ?>
                                </span><!--your-answer-wrap-->
                            </div>
                            <?php
                            }
                        }
                      break;
                      case 3:
                        if(!isset($lecture['answers'][$key]['ar_answer'])||$lecture['answers'][$key]['ar_answer'] == ''){ ?>
                          <div class="choice-footer-wrap clearfix">
                              <span class="unattended-border-xs">
                                <span class="unattend">Unattended</span> 
                              </span>
                            <div class="detail-answer-missed-mark"> 
                                <span class="mark-missed">Marks you missed <?php echo $lecture['questions'][$key]['q_positive_mark']; ?></span> 
                                <?php if(isset($explanation[1])&&$explanation[1]!=''): ?>
                                  <span class="answer-exp answer-exp-modified anser-exp-alterd" id="reveal-ans-<?php echo $key; ?>">Answer Explanation</span>  
                                <?php endif; ?>
                              </div>
                          </div>
                        <?php
                        }else{
                          if($lecture['answers'][$key]['ar_mark'] > 0){ ?>
                            <div class="choice-footer-wrap clearfix">
                                <span class="your-answer-wrap-left">
                                    <span class="your-answer-wrap-left-inside-text">Your answer</span>
                                <span class="right-text-green">Right</span>
                                </span><!--your-answer-wrap-->
                                <span class="your-answer-wrap-right">
                                  <span class="small-device-border">
                                  <span class="time-taken">Time Taken<strong>&nbsp;&nbsp;<?php echo gmdate("i:s", $lecture['answers'][$key]['ar_duration']);?>&nbsp;mm:ss</strong></span>
                                        <span class="marks">Marks <span class="green"><span class="green right-text-red"><?php echo $lecture['answers'][$key]['ar_mark'];?></span></span>
                                    </span><!--small-device-border-->
                                    <?php if(isset($explanation[1])&&$explanation[1]!=''): ?>
                                      <span class="answer-exp" id="reveal-ans-<?php echo $key; ?>">Answer Explanation</span>
                                    <?php endif; ?>
                                </span><!--your-answer-wrap-->
                            </div>
                          <?php
                          }else{ ?>
                            <div class="choice-footer-wrap clearfix">
                                <span class="your-answer-wrap-left your-answer-wrap-left-modified">
                                    <span class="your-answer-wrap-left-inside-text">Your answer</span>
                                <span class="right-text-green right-text-red margin-right-left wrong-text-modified">Wrong</span>
                                </span><!--your-answer-wrap-->
                                <span class="your-answer-wrap-right your-answer-wrap-right-modified">
                                  <span class="small-device-border small-device-border-modified">
                                  <span class="time-taken">Time Taken<strong>&nbsp;&nbsp;<?php echo gmdate("i:s", $lecture['answers'][$key]['ar_duration']);?>&nbsp;mm:ss</strong></span>
                                        <span class="marks marks-right">Marks <span class="green right-text-red"><?php echo $lecture['answers'][$key]['ar_mark'];?></span></span>
                                    </span><!--small-device-border-->
                                    <?php if(isset($explanation[1])&&$explanation[1]!=''): ?>
                                      <span class="answer-exp answer-exp-modified" id="reveal-ans-<?php echo $key; ?>">Answer Explanation</span>
                                    <?php endif; ?>
                                </span><!--your-answer-wrap-->
                            </div>
                          <?php
                          }
                        }
                      break;
                      case 4:
                        if(!isset($lecture['answers'][$key]['ar_answer'])||$lecture['answers'][$key]['ar_answer'] == ''){ ?>
                          <div class="choice-footer-wrap clearfix">
                              <span class="unattended-border-xs">
                                <span class="unattend">Unattended</span> 
                              </span>
                            <div class="detail-answer-missed-mark"> 
                                <span class="mark-missed">Marks you missed <?php echo $lecture['questions'][$key]['q_positive_mark']; ?></span> 
                                <?php if(isset($explanation[1])&&$explanation[1]!=''): ?>
                                  <span class="answer-exp answer-exp-modified anser-exp-alterd" id="reveal-ans-<?php echo $key; ?>">Answer Explanation</span>  
                                <?php endif; ?>
                              </div>
                          </div>
                        <?php
                        }else{
                          if($lecture['answers'][$key]['ar_mark'] > 0){ ?>
                            <div class="choice-footer-wrap clearfix">
                                <span class="your-answer-wrap-left">
                                    <span class="your-answer-wrap-left-inside-text">Your answer</span>
                                <span class="right-text-green">Right</span>
                                </span><!--your-answer-wrap-->
                                <span class="your-answer-wrap-right">
                                  <span class="small-device-border">
                                  <span class="time-taken">Time Taken<strong>&nbsp;&nbsp;<?php echo gmdate("i:s", $lecture['answers'][$key]['ar_duration']);?>&nbsp;mm:ss</strong></span>
                                        <span class="marks">Marks <span class="green"><span class="green right-text-red"><?php echo $lecture['answers'][$key]['ar_mark'];?></span></span>
                                    </span><!--small-device-border-->
                                    <?php if(isset($explanation[1])&&$explanation[1]!=''): ?>
                                      <span class="answer-exp" id="reveal-ans-<?php echo $key; ?>">Answer Explanation</span>
                                    <?php endif; ?>
                                </span><!--your-answer-wrap-->
                            </div>
                          <?php
                          }else{ ?>
                            <div class="choice-footer-wrap clearfix">
                                <span class="your-answer-wrap-left your-answer-wrap-left-modified">
                                    <span class="your-answer-wrap-left-inside-text">Your answer</span>
                                <span class="right-text-green right-text-red margin-right-left wrong-text-modified">Wrong</span>
                                </span><!--your-answer-wrap-->
                                <span class="your-answer-wrap-right your-answer-wrap-right-modified">
                                  <span class="small-device-border small-device-border-modified">
                                  <span class="time-taken">Time Taken<strong>&nbsp;&nbsp;<?php echo gmdate("i:s", $lecture['answers'][$key]['ar_duration']);?>&nbsp;mm:ss</strong></span>
                                        <span class="marks marks-right">Marks <span class="green right-text-red"><?php echo $lecture['answers'][$key]['ar_mark'];?></span></span>
                                    </span><!--small-device-border-->
                                    <?php if(isset($explanation[1])&&$explanation[1]!=''): ?>
                                      <span class="answer-exp answer-exp-modified" id="reveal-ans-<?php echo $key; ?>">Answer Explanation</span>
                                    <?php endif; ?>
                                </span><!--your-answer-wrap-->
                            </div>
                          <?php
                          }
                        }
                      break;
                      } ?>
                    </div>
                    <?php //echo '<pre>';print_r($question); ?>  
                    <div class="reveal-answer" id="reveal_answer_<?php echo $question['id'] ?>"><?php $explanation = json_decode($question['q_explanation'],true); echo isset($explanation[1])&&$explanation[1]!=''?stripslashes(html_entity_decode($explanation[1])):''; ?></div>
                  </div><!--single-choice-header-->
                  <div class="reveal-answer" id="reveal_answer_<?php echo $question['id'] ?>">
                    <?php 
                      $explanation = json_decode($question['q_explanation'],true); 
                      $explanation = (isset($explanation[1])&&$explanation[1]!=''?($explanation[1]):'');
                      echo html_entity_decode($explanation); 
                    ?>                   
                  </div>
            </div><!--single-choice-wraper-->
            <script type="text/javascript">
                $(document).on("click", "#reveal-ans-<?php echo $question['id'] ?>", function(){
                    $("#reveal_answer_<?php echo $question['id'] ?>").slideToggle();
                });
            </script>
        <?php $sl_no++; endforeach; endif; ?> 
        </div><!--container-reduce-width-->
    </div><!--container-->
</section>
<script type="text/javascript" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/jquery.drawDoughnutChart_1.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/test_report.js"></script>
<script>

  var chartArray = new Array();
  var chartArray = <?php if(!empty($lecture['question_difficulty_type'])):?><?php echo json_encode($lecture['question_difficulty_type']); ?><?php endif; ?>;
//console.log(chartArray);
$(document).ready(function(){
    generateGraph();
});
function close_detail_report(){
  parent.postMessage("quiz_close", "*");
    var iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    if(iOS == true) {
        window.location.href = 'inapp://closewindow';
    }
    window.close();
}
function generateGraph(){
  for (var key in chartArray){

      if (chartArray.hasOwnProperty(key)) {
        //console.log(chartArray);
        var right_answer = parseInt($('#right_graph_'+key).html());
        var wrong_answer = parseInt($('#wrong_graph_'+key).html());
        var unatt_answer = parseInt($('#unattended_graph_'+key).html());

        var total_questions  = parseFloat(right_answer) + parseFloat(wrong_answer) + parseFloat(unatt_answer);
        
        var right_percentage = (right_answer*100)/total_questions;
        var wrong_percentage = (wrong_answer*100)/total_questions;
        var unatt_percentage = (unatt_answer*100)/total_questions;
         
        $("#doughnutChart_"+key).drawDoughnutChart([
          { 
            title: right_percentage.toFixed(2)+'%',
            value : right_answer,  
            color: "#00C853"
          },
          { 
            title: wrong_percentage.toFixed(2)+'%',
            value:  wrong_answer,   
            color: "#FF5252" 
          },
          { 
            title: unatt_percentage.toFixed(2)+'%',
            value:  unatt_answer,   
            color: "#3B4EA9" 
          }
        ]);

    }
  }
}

</script>



<?php 
switch ($this->router->fetch_class()) {
    case 'dashboard':
        include_once 'dashboard_modals.php';
        break;
    case 'material':
        include_once 'material_modals.php';
        break;
    }
    
        
?>

<?php
//convert to mins

function convert_to_minutes($seconds)
{
    $duration_in_minutes     = $seconds/60;
    $cut_duration_in_minutes = substr($duration_in_minutes, 0, 4);
    
    return $cut_duration_in_minutes;
}
?>

<?php
//usleep(5000000);
/*
$survey_status = check_survey_status();
$today         = date('d-m-Y');
if (strtotime($today) >= strtotime($survey_status['s_start_date']) && strtotime($today) <= strtotime($survey_status['s_end_date']) && $this->router->fetch_class()!='survey') {
    if(!isset($_COOKIE['surveycookie']) && !isset($_COOKIE['surveytakencookie'])){
        echo '<script>setTimeout(function(){  var surveyModal = $("#survey_modal").modal({backdrop: "static", keyboard: false});  }, 10000);</script>';
    }
}
else{
    //echo 'Passed';
}
*/
?>
<style type="text/css">
.callus .right a.site-mail {
    color: #fff;
    text-decoration: none;
}
</style>


<!-- Bootstrap core JavaScript -->
<!-- <?php if(($this->router->fetch_class() == 'login') || ($this->router->fetch_class() == 'register')){  ?>
    <script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/jquery.min.js"></script>
<?php } ?> -->
<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/bootstrap.min.js"></script>

<?php if(($this->router->fetch_class() == 'homepage') || ($this->router->fetch_class() == 'expert_lectures')){  ?>
    <script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/ekko-lightbox.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function ($) {
                // delegate calls to data-toggle="lightbox"
                $(document).delegate('*[data-toggle="lightbox"]:not([data-gallery="navigateTo"])', 'click', function(event) {
                    event.preventDefault();
                    return $(this).ekkoLightbox({
                        onShown: function() {
                                if (window.console) {
                                        return //console.log('onShown event fired');
                                }
                        },
                        onContentLoaded: function() {
                                if (window.console) {
                                        return //console.log('onContentLoaded event fired');
                                }
                        },
                        onNavigate: function(direction, itemIndex) {
                                if (window.console) {
                                        return //console.log('Navigating '+direction+'. Current item: '+itemIndex);
                                }
                        }
                    });
                });

        });
    </script>
<?php } ?>
<script type="text/javascript" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/underscore.js"></script>
<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/bootstrap-multiselect.js"></script>
<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/responsive-tabs.js"></script>
<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/jquery.vticker.min.js"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/ie10-viewport-bug-workaround.js"></script>
<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/bootstrap-select.js"></script>
<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/owl.carousel.js"></script>
<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/jquery.flexslider.js"></script>
<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/toastr.min.js"></script>
<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/jquery.events.input.js"></script>
<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/jquery.elastic.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/prefixfree.min.js"></script>

<?php if(isset($session['id'])&&!isset($hide_live)) :
if($data_lectures && $this->router->fetch_class()!='live') {    ?>
<script type="text/javascript">
    $('#live-notification-slide-up').vTicker('init', {speed: 400, 
    pause: 2500,
    showItems: 1,
    padding:1
    });
</script>
<script>
$(document).ready(function(){
$('.close-live').click(function(){
        $.ajax({
            url: base_url+'/homepage/set_hide_live',
            type: "POST",
            data:{"hide_live":"yes"}, 
            success: function(response) {
                $('.live-notification-wrap').fadeOut();
            }
        });
}); 

/*$('.show-live').click(function(){
    $('.single-notification').hide();
    $('.list-notifications').fadeIn();
    $('.live-notification-wrap').animate({
    height: "200px",
    padding:"10px"
    }, 200);
    
}); */

$('.show-live').click(function(e){
    e.preventDefault();
    $('.single-notification').hide();
    $('.list-notifications').slideToggle();
    $('.show-live').hide();
    $('.hide-live').show();
});

 $('.hide-live').click(function(e){
    e.preventDefault();
    $('.list-notifications').slideToggle();
    $('.single-notification').show(400);
    $('.hide-live').hide();
    $('.show-live').show(); 
});
    
    
  $('.in,.open').removeClass('in open');    
  $('.dropdown-submenu a.test').on("click", function(e){

    $(this).next('ul').toggle();
    e.stopPropagation();
    e.preventDefault();
  });
  
  // for footer select box
     $('#cat-select').multiselect({
            includeSelectAllOption: true,
            maxHeight: 400,
            dropUp: true
        });
    
    
 
 
 
  
// jquery script for showing and hiding arrows on live box 
$('.show-live').click(function(){
    if($(this).hasClass("live-down")){
     $(this).removeClass("live-down").addClass("live-up");
    }else{
     $(this).removeClass("live-up").addClass("live-down");
    }
    

    
    
});

});
</script>
<script type="text/javascript">
    //if(session_id){
        setInterval(function () {
            $.ajax({
                url: base_url+'/homepage/get_live_lectures',
                type: "POST",
                data:{"is_ajax":true}, 
                success: function(response) {

                    var data = $.parseJSON(response);
                    //console.log(data);

                    var live_html = '';
                    var live_html1 = '';
                    if( data['live_lectures'].length > 0 )
                    {
                        for (var i=0; i < data['live_lectures'].length; i++)
                        {
                            if(data['live_lectures'][i]['ll_is_online'] == 2){
                                live_html += '<li>LIVE CLASS FOR '+data['live_lectures'][i]['cl_lecture_name'].toUpperCase()+' IS GOING ON<a href="'+base_url+'live/join/'+data['live_lectures'][i]['id']+'" class="btn live-notifications-btn pull-right">Join Live</a></li>';
                                live_html1 +='<li><span class="live-pic"><img src="'+assets_url+'images/live-icon.svg" width="24"></span><span class="live-cont">LIVE CLASS FOR '+data['live_lectures'][i]['cl_lecture_name'].toUpperCase()+' IS GOING ON</span><span class="live-actn"><a href="'+base_url+'live/join/'+data['live_lectures'][i]['id']+'" class="btn live-notifications-btn">Join Live</a></span></li>';
                            }else if(data['live_lectures'][i]['ll_is_online'] == 1){
                                live_html += '<li>LIVE CLASS FOR '+data['live_lectures'][i]['cl_lecture_name'].toUpperCase()+' IS GOING ON<a href="'+base_url+'live/golive/'+data['live_lectures'][i]['id']+'" class="btn live-notifications-btn pull-right">Join Live</a></li>';
                                live_html1 +='<li><span class="live-pic"><img src="'+assets_url+'images/live-icon.svg" width="24"></span><span class="live-cont">LIVE CLASS FOR '+data['live_lectures'][i]['cl_lecture_name'].toUpperCase()+' IS GOING ON</span><span class="live-actn"><a href="'+base_url+'live/golive/'+data['live_lectures'][i]['id']+'" class="btn live-notifications-btn">Join Live</a></span></li>';
                            }

                        }
                    }else{
                        $('.live-notification-wrap').stop().show().animate({
                        height: "0px",
                        padding:"0px"
                        }, 400); 
                    }
                    $(".live-buttons-ul").html(live_html);
                    $(".list-notifications-ul").html(live_html1);
                }
            });
        },5000);
    //}
</script>

<?php } endif; ?>

<script type="text/javascript">
    // Owl Slider
    $("#team").owlCarousel({
    items :9,
    lazyLoad : true,
    navigation : true,
    auto : true
    });
    //Tabs
    $('#myTab a').click(function (e) {
    e.preventDefault()
    $(this).tab('show')
    });
    <!-- flexslider -->
    $(window).on('load',function(){
    $('.flexslider').flexslider({
    animation: "slide",
    start: function(slider){
    $('body').removeClass('loading');
    }
    });
    });
    $(document).ready(function(){
       $('#course_listing_category').multiselect({
            includeSelectAllOption: true,
            maxHeight: 400,
            dropUp: false,
            nonSelectedText: "All Categories"
        }); 
    
        $('#course_listing_price').multiselect({
            includeSelectAllOption: true,
            maxHeight: 400,
            dropUp: false,
            nonSelectedText: "Price"
        }); 
        $('#course_listing_language').multiselect({
            includeSelectAllOption: true,
            maxHeight: 400,
            dropUp: false,
            nonSelectedText: "Language"
        });
        $('#course_search_price').multiselect({
            includeSelectAllOption: true,
            maxHeight: 400,
            dropUp: false,
            nonSelectedText: "Price"
        }); 
        $('#course_search_language').multiselect({
            includeSelectAllOption: true,
            maxHeight: 400,
            dropUp: false,
            nonSelectedText: "Language"
        });

        // Add wrapper 'div' to a Table for redactor
        $('.reveal-answer table').wrapAll('<div class="overflow-x-auto" />');
    });
</script>
<script>
    var today      = "<?php echo strtotime(date('d-m-Y')) ?>";
    var start_date = "<?php echo strtotime($survey_status['s_start_date'])?>";
    var end_date   = "<?php echo strtotime($survey_status['s_end_date'])?>";
    var this_controller = "<?php echo $this->router->fetch_class() ?>";
</script>
<div class="modal fade" id="survey_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel"><?php if($survey_status['s_title'] && $survey_status['s_title'] != '') { echo $survey_status['s_title']; } ?></h4>
      </div>
      <div class="modal-body">
        <?php if($survey_status['s_description'] && $survey_status['s_description'] != '') { echo $survey_status['s_description']; } ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="createCookie('surveycookie','latercookie',3000);"><?php echo lang('later');?></button>
        <button type="button" class="btn btn-secondary orange" data-dismiss="modal" onclick="gotoSurvey()"><?php echo lang('take_now');?></button>
      </div>
    </div>
  </div>
</div>

<?php
$analytics_settings = $this->settings->setting('has_google_analytics');
if($analytics_settings['as_superadmin_value'] == '1' && $analytics_settings['as_siteadmin_value'] == '1' && $analytics_settings['as_setting_value']['setting_value']->script != ''){
    echo base64_decode($analytics_settings['as_setting_value']['setting_value']->script);
}
?>

<script>

function createCookie(name,value,days) {
        // alert(name+'/'+value+'/'+days);
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name,"",-1);
}

function gotoSurvey(){
    createCookie('surveytakencookie','takencookie',20*365*24*60*60);
    window.location.href='<?php echo site_url('/survey') ?>';   
}


</script>
</body>
</html>


<?php //include_once 'footer.php'; ?>
