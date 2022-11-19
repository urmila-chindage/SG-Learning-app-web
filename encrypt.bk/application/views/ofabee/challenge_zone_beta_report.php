<?php include('header.php') ?>
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
    	<div class="container">
        <div class="container-reduce-width">
          <?php 
            $assesment_date     = $cz_attempt_details['cza_attempted_date'];
            $attempted_date     = date("M j Y", strtotime($assesment_date));
            
            //$attended_time      = $cz_attempt_details['cza_duration']/60;
            //$duration_in_minutes= substr($attended_time, 0, 4);
            $duration_in_minutes= secondsToTime($cz_attempt_details['cza_duration']);
          ?>
        	<span class="funda-date"><?php echo $attempted_date; ?></span>
            <h2 class="funda-head"><?php echo (isset($challenge_zone_details['cz_title'])? $challenge_zone_details['cz_title']: '');?></h2> 
            <ul class="funda-strip clearfix">
            <?php $full_questions = 0; $right_answer_full = 0; $wrong_answer_full = 0; $unattended_answer_full = 0; $full_accuracy = 0; $full_time_taken = 0; $full_marks_scored = 0; ?>
            <?php if(!empty($lecture['questions'])):?>
            
            <?php foreach ($lecture['questions'] as $key => $full_question): ?>

            <?php if(isset($lecture['answers'][$key])) : if($lecture['answers'][$key]['czr_mark'] > 0):?>
            <?php $right_answer_full++; endif; endif; ?>

            <?php if(isset($lecture['answers'][$key])) : if(($lecture['answers'][$key]['czr_answer']!="") && ($lecture['answers'][$key]['czr_mark'] <= 0)):?>

            <?php $wrong_answer_full++; endif; endif; ?>

            <?php if((!isset($lecture['answers'][$key]))):?>

            <?php $unattended_answer_full++; endif; ?>

            <?php if(isset($lecture['answers'][$key])) : if(($lecture['answers'][$key]['czr_answer']=="")):?>

            <?php $unattended_answer_full++; endif; endif; ?>

            <?php if(isset($lecture['answers'][$key])) :?>
            <?php $full_time_taken = $full_time_taken + $lecture['answers'][$key]['czr_duration']; endif;?>

            <?php if(isset($lecture['answers'][$key])) :?>
            <?php $full_marks_scored = $full_marks_scored + $lecture['answers'][$key]['czr_mark']; endif;?>

            <?php if(!isset($lecture['answers'][$key])) :?>
            <?php $full_marks_scored = $full_marks_scored + 0; endif;?>

            <?php endforeach; endif; ?>
            	<li>
                	<div class="funda-space">
                    	<span class="funda-strip-number"><?php if(!empty($lecture['questions'])): $full_questions = count($lecture['questions']); ?><?php echo $full_questions;?><?php endif; ?></span>
                        <span class="fund-strip-text">Questions</span>
                    </div>
                </li>
            	<li>
                	<div class="funda-space">
                    	<span class="funda-strip-number fund-number-green"><?php echo $right_answer_full; ?></span>
                        <span class="fund-strip-text">Right</span>
                    </div>
                </li>
                
            	<li>
                	<div class="funda-space">
                    	<span class="funda-strip-number fund-number-red"><?php echo $wrong_answer_full; ?></span>
                        <span class="fund-strip-text">Wrong</span>
                    </div>
                </li>
                
            	<li>
                	<div class="funda-space">
                    	<span class="funda-strip-number fund-number-blue"><?php echo $unattended_answer_full; ?></span>
                        <span class="fund-strip-text">Unattended</span>
                    </div>
                </li>
                
            	<li>
                	<div class="funda-space">
                    <?php if(!empty($lecture['questions'])): ?><?php $full_accuracy = round(($right_answer_full*100)/count($lecture['questions']),2); ?><?php endif; ?>
                    	<span class="funda-strip-number"><?php echo $full_accuracy; ?>%</span>
                        <span class="fund-strip-text">Accuracy</span>
                    </div>
                </li>
                
            	<li>
                	<div class="funda-space">
                    
                    	<span class="funda-strip-number"><?php echo $duration_in_minutes //$full_time_taken; ?></span>
                        <span class="fund-strip-text">Time Taken</span>
                    </div>
                </li>
            	<li class="mark-scored-green">
                	<div class="funda-space funda-space-bg-rm">
                    	<span class="funda-strip-number color-white"><?php echo $lecture['total_mark']; ?></span>
                        <span class="fund-strip-text color-white">Marks Scored</span>
                    </div>
                </li>  
                
                <li class="view-rank-list-wraper">
                	<ul class="view-mark-list-ul">
                    	<li><a href="<?php echo site_url('report/challenge_zone/'.$cz_attempt_details['cza_challenge_zone_id'])?>" class="orange-flat-btn">View rank list</a></li>
                    </ul>
                </li> 
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
                    <th class="table-th">Topics</th>
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
                    <td class="table-sub-rows"><?php echo $lecture['categories'][$key]['qc_category_name']; ?></td>
                    <td class="table-sub-rows"><?php echo count($lecture['sl_no'][$key]); ?></td>
                <?php $right_answer = 0; $wrong_answer = 0; $unattended_answer = 0; $remarks = ''; $accuracy = '0'; ?>
                <?php if(!empty($category_question)) : ?>
                <?php foreach($category_question as $question_category): ?>
                    <?php if(isset($lecture['answers'][$question_category])) : if($lecture['answers'][$question_category]['czr_mark'] > 0):?>

                    <?php $right_answer++; endif; endif; ?>

                    <?php if(isset($lecture['answers'][$question_category])) : if(($lecture['answers'][$question_category]['czr_answer']!="") && ($lecture['answers'][$question_category]['czr_mark'] <= 0)):?>

                    <?php $wrong_answer++; endif; endif; ?>

                    <?php if((!isset($lecture['answers'][$question_category]))):?>

                    <?php $unattended_answer++; endif; ?>

                    <?php if(isset($lecture['answers'][$question_category])) : if(($lecture['answers'][$question_category]['czr_answer']=="")):?>

                    <?php $unattended_answer++; endif; endif; ?>

                <?php endforeach; endif; ?>

                	<td class="table-sub-rows"><span class="fund-number-green"><?php echo $right_answer; ?></span></td>
                    <td class="table-sub-rows"><span class="fund-number-red"><?php echo $wrong_answer; ?></span></td>
                    <td class="table-sub-rows"><span class="fund-number-blue"><?php echo $unattended_answer; ?></span></td>
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

  <?php if(!empty($lecture['question_difficulty_type'])):?>
  <?php foreach($lecture['question_difficulty_type'] as $key=>$difficulty_question):?>

        <?php $right_answer_difficulty = 0; $wrong_answer_difficulty = 0; $unattended_answer_difficulty = 0; ?>
        <?php if(!empty($difficulty_question)) : ?>
        <?php foreach($difficulty_question as $question_each): ?>

           <?php if(isset($lecture['answers'][$question_each])) : if($lecture['answers'][$question_each]['czr_mark'] > 0):?>

                    <?php $right_answer_difficulty++; endif; endif; ?>

                    <?php if(isset($lecture['answers'][$question_each])) : if(($lecture['answers'][$question_each]['czr_answer']!="") && ($lecture['answers'][$question_each]['czr_mark'] <= 0)):?>

                    <?php $wrong_answer_difficulty++; endif; endif; ?>

                    <?php if((!isset($lecture['answers'][$question_each]))):?>

                    <?php $unattended_answer_difficulty++; endif; ?>

                    <?php if(isset($lecture['answers'][$question_each])) : if(($lecture['answers'][$question_each]['czr_answer']=="")):?>

                    <?php $unattended_answer_difficulty++; endif; endif; ?>

    <?php endforeach; endif; ?>

      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                  <div class="donuts-wrapper">
                      <div id="doughnutChart_<?php echo $key;?>" class="chart">
                          <div class="doughnutSummary">
                              <label><?php echo (isset($question_each) && $question_each!='')?$question_difficulty[$lecture['questions'][$question_each]['q_difficulty']]:$question_difficulty[$key];?></label>
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
                    <div class="col-lg-6 col-md-5 col-sm-6 clearfix">
                    	<span class="to-right">
                    		<span class="compare-score-text-wrap hidden-sm hidden-xs">Compare your score with others</span>
                        <span class="black-btn-wraper">
                        	<a href="<?php echo site_url('report/challenge_zone/'.$cz_attempt_details['cza_challenge_zone_id'])?>" class="btn btn-black">Compare</a>
                        </span><!--black-btn-wraper-->
                        </span><!--pull-right-->
                    </div><!--columns-->
                </div><!--row-->
                <span class="hidden-lg hidden-md- hidden-sm showing-questions-xs">Showing <b class="filter_count">&nbsp;<?php if(!empty($lecture['questions'])):?> <?php echo count($lecture['questions']); endif;?>&nbsp;</b> of <b class="all_count">&nbsp;<?php if(!empty($lecture['questions'])):?> <?php echo count($lecture['questions']); endif;?>&nbsp;</b> questions</span>
            </div><!--container-reduce-width-->
        </div><!--container-->
    </div><!--all-question-above-->
</section>


<section>
	<div class="container">
    	<div class="container-reduce-width">
        <?php $character = array(); if(!empty($lecture['questions'])):?>
          <?php 
              $sl_no          = 1;
          ?>
          <?php foreach ($lecture['questions'] as $key => $question):?>
            <?php $alphabet_count = 65; ?>
        	   <div class="single-choice-wraper <?php if(isset($lecture['answers'][$key]) && $lecture['answers'][$key]['czr_answer']!='' && $lecture['answers'][$key]['czr_mark'] <= 0){ echo 'wrong'; }?><?php if(!isset($lecture['answers'][$key]) ){ echo 'unattended'; }?><?php if(isset($lecture['answers'][$key]) && $lecture['answers'][$key]['czr_answer']=='' ){ echo 'unattended'; }?><?php if(isset($lecture['answers'][$key]) && $lecture['answers'][$key]['czr_answer']!='' && $lecture['answers'][$key]['czr_mark'] > 0){ echo 'right'; }?>" id="<?php echo $key.'_';?><?php if(isset($lecture['answers'][$key]) && $lecture['answers'][$key]['czr_answer']!='' && $lecture['answers'][$key]['czr_mark'] <= 0){ echo 'wrong'; }?><?php if(!isset($lecture['answers'][$key]) ){ echo 'unattended'; }?><?php if(isset($lecture['answers'][$key]) && $lecture['answers'][$key]['czr_answer']=='' ){ echo 'unattended'; }?><?php if(isset($lecture['answers'][$key]) && $lecture['answers'][$key]['czr_answer']!='' && $lecture['answers'][$key]['czr_mark'] > 0){ echo 'right'; }?>">
            	<div class="single-choice-header">
              	<span class="no-in-round <?php if(isset($lecture['answers'][$key]) && $lecture['answers'][$key]['czr_answer']!='' && $lecture['answers'][$key]['czr_mark'] <= 0){ echo 'no-in-round-red'; }?><?php if(isset($lecture['answers'][$key]) && $lecture['answers'][$key]['czr_answer']=='' ){ echo 'round-blue'; }?><?php if(!isset($lecture['answers'][$key]) ){ echo 'round-blue'; }?>"><?php echo $sl_no; ?></span><!--no-in-round-->
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

                  </span>
                  <span class="what-are-some-para"><?php echo $question['q_question']; ?></span>
                  <div class="question-master-parent">
                    <?php if(!empty($question['options'])): ?>  
                      <span class="question-wrap">
                        <?php $option_count = 0; ?>
                        <?php foreach($question['options'] as $options): ?>
                          <?php 
                            $character[$options['id'].$question['q_question']] = chr($alphabet_count);
                            $option_count++; 
                            $option_position_class = ($option_count%2 == 0)?'text-qus-padding-left':'text-qus-padding-right';
                          ?>

                          <?php if($option_count>2 && ($option_count%2 != 0)):?>
                            </span><span class="question-wrap">
                          <?php endif;?>

                          	<span class="series-of-question <?php echo $option_position_class ?>">
                              	<span class="a-b-c"><?php echo strtolower(chr($alphabet_count++)); ?>)</span>
                            		<span class="text-qus"><?php echo $options['qo_options']; ?></span><!--text-qus-->
                            </span><!--series-of-question-->
                        <?php endforeach;?>
                      </span><!--question-wrap-->
                    <?php endif;?>
                  </div><!--question-master-parent-->




                    <hr class="hr-alter">
                <div class="choice-footer-wrap clearfix">

                    <?php if(!isset($lecture['answers'][$key])) { ?>
                           <span class="unattended-border-xs">
                               <span class="unattend">Unattended</span> 
                              <span class="your-answer-wrap-left margin-left-for-answer your-answer-wrap-modified">
                              <span class="your-answer-wrap-left-inside-text">Right answer</span>

                                <?php 
                                  if(isset($lecture['question_type_wise']['1'])) :
                                    if(in_array($question['id'],$lecture['question_type_wise']['1'])):?>

                                    <?php if(!empty($question['options'])): ?> 
                                    <?php $option_wrong_right_value = 65; ?>
                                    <?php foreach($question['options'] as $options): ?>
                                    
                                    <?php if($options['id']==$question['q_answer']): ?>

                                      <span class="your-answer-wrap-left-inside-circle"><?php echo strtolower(chr($option_wrong_right_value));?></span>
                                      
                                <?php endif;  $option_wrong_right_value++; endforeach; endif; endif; endif; ?>


                                <?php 
                                  if(isset($lecture['question_type_wise']['2'])) :
                                    if(in_array($question['id'],$lecture['question_type_wise']['2'])):?>
                                  
                                    <?php if(!empty($question['options'])): ?> 
                                    <?php $option_wrong_your_full = 65; ?>
                                    
                                    <?php $ans_array = explode(',',$question['q_answer']); ?>
                                    <?php foreach($ans_array as $ans):?>
                                    <?php if(isset($character[$ans.$question['q_question']])):?>
                                      <span class="your-answer-wrap-left-inside-circle"><?php echo strtolower($character[$ans.$question['q_question']]);?></span>
                                    <?php endif; $option_wrong_your_full++; endforeach; ?>
                                   

                                <?php  endif; endif; endif;?>
                                

                            </span>
                               
                            </span><!--unattended-border-xs--> 
                            <div class="detail-answer-missed-mark">
                            <span class="mark-missed">Marks you missed <?php echo $question['q_positive_mark'];?></span>
                            <span class="answer-exp answer-exp-modified anser-exp-alterd" id="reveal-ans-<?php echo $question['id'] ?>">Answer Explanation</span>
                            </div>
                    <?php } ?> 

                    <?php if(isset($lecture['answers'][$key]) && $lecture['answers'][$key]['czr_answer']=='' ) { ?>

                            <span class="unattended-border-xs">
                               <span class="unattend">Unattended </span>
                               <?php //echo "<pre>";print_r($lecture); ?>
                               <?php if(!isset($lecture['question_type_wise']['3'])): ?>
                              <span class="your-answer-wrap-left margin-left-for-answer your-answer-wrap-modified">
                              <span class="your-answer-wrap-left-inside-text">Right answer</span>

                                <?php 
                                  if(isset($lecture['question_type_wise']['1'])) :
                                    if(in_array($question['id'],$lecture['question_type_wise']['1'])):?>

                                    <?php if(!empty($question['options'])): ?> 
                                    <?php $option_wrong_right_value = 65; ?>
                                    <?php foreach($question['options'] as $options): ?>
                                    
                                    <?php if($options['id']==$question['q_answer']): ?>

                                      <span class="your-answer-wrap-left-inside-circle"><?php echo strtolower(chr($option_wrong_right_value));?></span>
                                      
                                <?php endif;  $option_wrong_right_value++; endforeach; endif; endif; endif; ?>


                                <?php 
                                  if(isset($lecture['question_type_wise']['2'])) :
                                    if(in_array($question['id'],$lecture['question_type_wise']['2'])):?>
                                  
                                    <?php if(!empty($question['options'])): ?> 
                                    <?php $option_wrong_your_full = 65; ?>
                                    
                                    <?php $ans_array = explode(',',$question['q_answer']); ?>
                                    <?php foreach($ans_array as $ans):?>
                                    <?php if(isset($character[$ans.$question['q_question']])):?>
                                      <span class="your-answer-wrap-left-inside-circle"><?php echo strtolower($character[$ans.$question['q_question']]);?></span>
                                    <?php endif; $option_wrong_your_full++; endforeach; ?>
                                   

                                <?php  endif; endif; endif;?>
                                

                            </span>
                               <?php endif; ?>
                               
                            </span><!--unattended-border-xs--> 
                            <div class="detail-answer-missed-mark">
                                <span class="mark-missed">Marks you missed <?php echo $question['q_positive_mark'];?></span>
                                <span class="answer-exp answer-exp-modified anser-exp-alterd" id="reveal-ans-<?php echo $question['id'] ?>">Answer Explanation</span>  
                            </div>

                     <?php } ?>   

                     <?php if(isset($lecture['answers'][$key]) && $lecture['answers'][$key]['czr_answer']!='' && $lecture['answers'][$key]['czr_mark'] <= 0) { ?>
                        	
                            <span class="your-answer-wrap-left your-answer-wrap-left-modified">
                              <span class="your-answer-wrap-left-inside-text">Your answer</span>

                                <?php 
                                  if(isset($lecture['question_type_wise']['1'])) :
                                    if(in_array($question['id'],$lecture['question_type_wise']['1'])):?>

                                    <?php if(!empty($question['options'])): ?> 
                                    <?php $option_wrong_value = 65; ?>
                                    <?php foreach($question['options'] as $options): ?>
                                    
                                    <?php if($options['id']==$lecture['answers'][$key]['czr_answer']): ?>

                                      <span class="your-answer-wrap-left-inside-circle"><?php echo strtolower(chr($option_wrong_value));?></span>
                                      
                                <?php endif;  $option_wrong_value++; endforeach; endif; endif; endif; ?>

                                <?php 
                                  if(isset($lecture['question_type_wise']['2'])) :
                                      
                                    if(in_array($question['id'],$lecture['question_type_wise']['2'])): ?>
                                  
                                    <?php if(!empty($question['options'])):?> 
                                      
                                    <?php $option_wrong_mul_your  = 65; ?>
                                    <?php $option_wrong_your_full = 65; ?>
                                    <?php if (strpos($lecture['answers'][$key]['czr_answer'], ',') !== false) { ?>
                                    <?php $ans_array = explode(',',$lecture['answers'][$key]['czr_answer']); ?>
                                    <?php foreach($ans_array as $ans):?>
                                    <?php if(isset($character[$ans.$question['q_question']])):?>
                                      <span class="your-answer-wrap-left-inside-circle"><?php echo strtolower($character[$ans.$question['q_question']]);?></span>
                                    <?php endif; $option_wrong_your_full++; endforeach; ?>
                                    <?php } else { foreach($question['options'] as $options): ?>
                                    <?php if($options['id']==$lecture['answers'][$key]['czr_answer']): echo "test";?>
                                      <span class="your-answer-wrap-left-inside-circle"><?php echo strtolower(chr($option_wrong_mul_your));?></span>
                                    <?php endif; ?>
                                    <?php $option_wrong_mul_your++; endforeach; } ?>

                                <?php  endif; endif; endif; ?>

                                
                                <span class="right-text-green right-text-red margin-right-left wrong-text-modified">Wrong</span>
                            </span><!--your-answer-wrap-->
                            <?php //if(isset($lecture['question_type_wise']['3'])): ?>
                            <?php //else: ?>
                            <span class="your-answer-wrap-left margin-left-for-answer your-answer-wrap-modified">
                              <span class="your-answer-wrap-left-inside-text">Right answer</span>

                                <?php 
                                  if(isset($lecture['question_type_wise']['1'])) :
                                    if(in_array($question['id'],$lecture['question_type_wise']['1'])):?>

                                    <?php if(!empty($question['options'])): ?> 
                                    <?php $option_wrong_right_value = 65; ?>
                                    <?php foreach($question['options'] as $options): ?>
                                    
                                    <?php if($options['id']==$question['q_answer']): ?>

                                      <span class="your-answer-wrap-left-inside-circle"><?php echo strtolower(chr($option_wrong_right_value));?></span>
                                      
                                <?php endif;  $option_wrong_right_value++; endforeach; endif; endif; endif; ?>


                                <?php 
                                  if(isset($lecture['question_type_wise']['2'])) :
                                    if(in_array($question['id'],$lecture['question_type_wise']['2'])):?>
                                  
                                    <?php if(!empty($question['options'])): ?> 
                                    
                                    
                                    <?php $ans_array = explode(',',$question['q_answer']); ?>
                                    <?php foreach($ans_array as $ans):?>
                                    <?php if(isset($character[$ans.$question['q_question']])):?>
                                      <span class="your-answer-wrap-left-inside-circle"><?php echo strtolower($character[$ans.$question['q_question']]);?></span>
                                    <?php endif; $option_wrong_your_full++; endforeach; ?>
                                   

                                <?php  endif; endif; endif;?>
                                

                            </span>
                            <?php //endif; ?>
                            
                            <span class="your-answer-wrap-right your-answer-wrap-right-modified">
                              <span class="small-device-border small-device-border-modified">
                                  <span class="time-taken margin-left-remove">Time Taken<strong>&nbsp;&nbsp;<?php echo $lecture['answers'][$key]['czr_duration'];?>s</strong></span>
                                <span class="marks marks-right">Marks <span class="green right-text-red"><?php echo $lecture['answers'][$key]['czr_mark'];?></span></span>
                                </span><!--small-device-border-->
                              <span class="answer-exp answer-exp-modified" id="reveal-ans-<?php echo $question['id'] ?>">Answer Explanation</span>
                            </span><!--your-answer-wrap-->

                     <?php } ?>

                     <?php if(isset($lecture['answers'][$key]) && $lecture['answers'][$key]['czr_answer']!='' && $lecture['answers'][$key]['czr_mark'] > 0) { ?>

                          <span class="your-answer-wrap-left">
                            	<span class="your-answer-wrap-left-inside-text">Your answer</span>

                                <?php 
                                  if(isset($lecture['question_type_wise']['1'])) : 
                                    if(in_array($question['id'],$lecture['question_type_wise']['1'])):?>

                                    <?php if(!empty($question['options'])): ?> 
                                    <?php $option_count_value = 65; ?>
                                    <?php foreach($question['options'] as $options): ?>
                                    
                                    <?php if($options['id']==$lecture['answers'][$key]['czr_answer']): ?>

                                      <span class="your-answer-wrap-left-inside-circle"><?php echo strtolower(chr($option_count_value));?></span>
                                      
                                <?php endif;  $option_count_value++; endforeach; endif; endif; endif; ?>


                                <?php 
                                  if(isset($lecture['question_type_wise']['2'])) :
                                    if(in_array($question['id'],$lecture['question_type_wise']['2'])):?>
                                  
                                    <?php if(!empty($question['options'])): ?> 
                                    
                                    
                                    <?php $ans_array = explode(',',$lecture['answers'][$key]['czr_answer']); ?>
                                    <?php foreach($ans_array as $ans):?>
                                    <?php if(isset($character[$ans.$question['q_question']])):?>
                                      <span class="your-answer-wrap-left-inside-circle"><?php echo strtolower($character[$ans.$question['q_question']]);?></span>
                                    <?php endif; endforeach; ?>
                                   

                                <?php  endif; endif; endif;?>
                                

                                <span class="right-text-green">Right</span>
                            </span><!--your-answer-wrap-->
                            
                            <span class="your-answer-wrap-right">
                              <span class="small-device-border">
								                <span class="time-taken">Time Taken<strong>&nbsp;&nbsp;<?php echo $lecture['answers'][$key]['czr_duration'];?>s</strong></span>
                                <span class="marks">Marks <span class="green"><?php echo $lecture['answers'][$key]['czr_mark'];?></span></span>
                                </span><!--small-device-border-->
                              <span class="answer-exp" id="reveal-ans-<?php echo $question['id'] ?>">Answer Explanation</span>
                            </span><!--your-answer-wrap-->

                      <?php } ?>




                  </div><!--choice-footer-wrap-->
                        
                        
                  </div><!--single-choice-header-->
                  <div class="reveal-answer" id="reveal_answer_<?php echo $question['id'] ?>"><?php echo $question['q_explanation']; ?></div>
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
<?php include_once 'footer.php'; ?>