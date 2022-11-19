<?php
function toAlpha($number)
{
    $alphabet = range('A','Z');
    //$number--;
    $count = count($alphabet);
    if($number <= $count)
        return $alphabet[$number-1];
    while($number > 0){
        $modulo     = ($number - 1) % $count;
        $alpha      = $alphabet[$modulo].$alpha;
        $number     = floor((($number - $modulo) / $count));
    }
    return $alpha;
}
//echo $url= $_SERVER['HTTP_REFERER'];
?>
<!DOCTYPE html>
<html>
    <!-- head start-->

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
        <title><?php echo isset($title) ? $title : config_item('site_name'); ?></title>
        <link rel="icon" href="<?php echo base_url('favicon.png') ?>">
        <!-- ADDING REDACTOR PLUGIN INSIDE -->
        <!-- ############################# --> <!-- START -->

        <!-- ############################# --> <!-- END -->
        <!-- ADDING REDACTOR PLUGIN INSIDE -->    
        <!-- Customized bootstrap css library -->
        <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/adminstyle.css">
        <link rel="stylesheet" href="<?php echo assets_url() ?>css/autocomplete.css" />
        <style>
        #report-card .text-qus {
            display: inherit;
            font-weight: 400;
        } 
        .preview-header{
            padding: 15px;
            border-bottom: 1px solid #e5e5e5;
            min-height: 16.42857143px;
            background: #7f8284;
            color: #fff;
        }
        .preview-footer{
            bottom: 0;
            width: 100%;
            padding: 0 200px 25px 0;
            text-align: right;
        }
        .close {
            color: #fff!important;
            opacity: unset!important;
        }
        </style>
    
    </head>
    <!-- head end-->
    <!-- body start-->
    <body>
        <?php 
        $df_values = $this->session->userdata('default_field_values');
        $df_values = ($df_values)?$df_values:array();
        $explanation = json_decode(($q_explanation),true);
       // echo '<pre>'; print_r($explanation); die('----');
    
       // echo '<pre>'; print_r($df_values); die('----');
        ?>
         <?php 
        $q_question_temp = json_decode($q_question);
        if(!(json_last_error() == JSON_ERROR_NONE))
        {
            $q_question = '';
        }
        else
        {
            $q_question =  isset($q_question_temp->$active_web_language)?stripslashes($q_question_temp->$active_web_language):'';
        }

        if(($q_type==1) || ($q_type==2)){
            $qus_options = explode(",",$qus_options);
            $qus_answer  = explode(",",$qus_answer);
            $answer_key = array();
            foreach($qus_answer as $question_answer){
                $question_answer;
                $answer_key[] = toAlpha((array_search($question_answer, $qus_options)+1));
                $answer       = implode(",",$answer_key);
            } 
        } else {
                 $answer = $qus_answer;
        }
      //echo $answer;
        ?>
        <div class="container">
          <!-- Trigger the modal with a button -->
  


    </div>
        <!-- Manin Iner container start -->
        <div class='bulder-content-inner add-question-block'>
           

                      <!-- Modal -->
          <div class="" id="report-card" role="dialog">
            <div class="">
              <!-- Modal content-->
              <div class="">
                <div class="preview-header">
                  <button type="button" class="close"  onclick="location.href='<?php echo admin_url('generate_test') ?>'" >&times;</button>
                  <h4 style="margin:0" >Question ID &nbsp;&nbsp;-&nbsp;&nbsp;<span id="question_preview_count"><?php if($id): ?><strong>#<?php echo $q_code ?></strong><?php endif; ?></span></h4>
                </div>
                <div class=""> 
                    <!-- card content -->
                    <div class="container-reduce-width" id="review-question">
                        <div class="single-choice-wraper right" >
                            <div class="single-choice-header">
                                <div class="row">
                                    <div class="col-md-6">
                                        <b><span class="single-choice-label"><?php echo $question_types[$q_type];?></span></b>
                                    </div>
                                   
                                </div>
                                <div class="question-master-parent">
                                    <div class="col-md-6 no-padding">
                                        <div class="margin-top-bottom"><span class="text-blue">Subject : </span><span><?php echo $q_subject; ?></span></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="single-choice-label  margin-top-bottom"><span class="text-blue">Topic : </span> <?php echo $q_topic; ?></div>
                                    </div>
                                </div>
                                <div class="question-master-parent">
                                    <span class="what-are-some-para"><p><?php echo $q_question;?></p></span>
                                    <span class="question-wrap">
                                    <?php if(!empty($q_options)): ?>
                                    <?php $count = 1; ?>
                                    <?php foreach ($q_options as $option):?>
                                    <?php 
                                    $qo_options_temp = json_decode($option['qo_options']);
                                    if(!(json_last_error() == JSON_ERROR_NONE))
                                    {
                                        $option['qo_options'] = '';
                                    }
                                    else
                                    {
                                        $option['qo_options'] = isset($qo_options_temp->$active_web_language)?stripslashes($qo_options_temp->$active_web_language):'';
                                    }
                                    ?>
                               
                                     
                                           <span class="series-of-question text-qus-padding-right"><span class="a-b-c"><?php echo toAlpha($count); $count++; ?>)</span><p class="text-qus"><?php echo $option['qo_options'] ?></p></span> 
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                    </span>
                                    <!--series-of-question-->
                                    </span>
                                </div>
                                <!--question-master-parent-->
                                <hr class="hr-alter">
                                <div class="choice-footer-wrap clearfix">
                                    <div class="choice-footer-wrap clearfix">
                                        <span class="your-answer-wrap-left">
                                                        <span class="your-answer-wrap-left-inside-text">Correct answer : &nbsp; <span class="text-blue"><?php echo $answer; ?></span></span>
                                        
                                        <!-- <span class="right-text-green">Right</span> -->
                                        </span>
                                        <!--your-answer-wrap-->
                                        <span class="your-answer-wrap-right">
                                        <span class="small-device-border">
                                            <strong>+ve marks : <span class="green-status"><?php echo $q_positive_mark; ?></span></strong>
                                            &nbsp;&nbsp;&nbsp;
                                            <strong>-ve marks : <span class="red-status"><?php echo $q_negative_mark; ?></span></strong>
                                            &nbsp;&nbsp;&nbsp;
                                        </span>
                                        <!--your-answer-wrap-->
                                    </div>
                                </div>
                                 <div class="choice-footer-wrap margin-top clearfix">
                           <?php if(isset($explanation)){
                            
                            ?>
                            <span class="answer-exp">Answer Explanation</span>
                               <p><?php $explanation[1]; ?></p>
                            <?php
                           }
                            ?>
                     
                                </div>
                            
                                <div class="reveal-answer" id="reveal_answer_14"></div>
                            </div>
                            <!--single-choice-header-->
                            <div class="reveal-answer" id="reveal_answer_14"></div>
                        </div>
                                               
                    </div>
                    <!-- card content ends -->
                </div>
                <div class="preview-footer">
                  <button type="button" class="btn btn-danger" onclick="location.href='<?php echo admin_url('generate_test') ?>'">Close</button>
                </div>
              </div>
            </div>
          </div>
            <!-- modal ends -->
        </div>
        <!-- Manin Iner container end -->
    </body>
    <!-- body end-->
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/layout.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/redactor/js/source.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/bootstrap-tagsinput.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/system.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/app.js"></script>
<script>

</html>
