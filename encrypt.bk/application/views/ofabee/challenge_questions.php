<?php include('header.php') ?>

<section id="fundamentals">
    <div class="fundamentals">
        <div class="container">
        <div class="container-reduce-width">
            <span class="funda-date"><?php $date = new DateTime($challenge_zone_details['cz_end_date']); echo $date->format('d M y'); ?></span>
            <h2 class="funda-head"><?php echo $challenge_zone_details['cz_title']; ?></h2> 
                    
            </div><!--container-reduce-width-->
        </div><!--container-->    
    </div><!--fundamentals-->
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
                            
                            
                            <span class="your-answer-wrap-left margin-left-for-answer your-answer-wrap-modified">
                              <span class="your-answer-wrap-left-inside-text">Answer</span>
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
                                    <?php endif;  endforeach; ?>
                                   

                                <?php  endif; endif; endif;?>
                        
                            </span>
                            <span class="your-answer-wrap-right your-answer-wrap-right-modified">
                              <span class="small-device-border small-device-border-modified">
                                  
                              </span><!--small-device-border-->
                              <span class="answer-exp answer-exp-modified" data-internal-id="<?php echo $question['id'] ?>">Answer Explanation</span>
                            </span>
                    </div>
                  </div><!--single-choice-header-->
                  <div class="reveal-answer" id="reveal_answer_<?php echo $question['id'] ?>"><?php echo $question['q_explanation']; ?></div>
            </div><!--single-choice-wraper-->
            <br>
        <?php $sl_no++; endforeach;?>
        <?php else: ?> 
            <div class="row">    
  			<div class="col-sm-12 dashboard-no-course">
 				
                <div class="no-course-container">
                 	<img class="no-questions-svg" width="100" height="100" src="<?php echo assets_url(); ?>themes/<?php echo $this->config->item('theme') ?>/images/No_Notification_illustration.svg">
                 	<span class="no-discussion no-content-text">No questions to show</span>
                    <div class="text-center">
                    <span class="noquestion-btn-wrap mobile-btn"><a href="javascript:void(0)" class="orange-flat-btn noquestion-btn">Browse Courses</a></span>
                    </div><!--text-center-->
                 </div> 				
                
                </div>              
             </div>
        <?php endif; ?> 
        </div><!--container-reduce-width-->
    </div><!--container-->
</section>
<script type="text/javascript">
  $('.answer-exp-modified').click(function(){
    var id = $(this).attr('data-internal-id');
    $('#reveal_answer_'+id).slideToggle();
  });
</script>
<?php include_once 'footer.php'; ?>