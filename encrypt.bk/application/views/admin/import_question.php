<?php include_once "header.php"; ?>  
<style>
    .question-bank-bulk{
        padding-bottom: 0px !important;
    }
    .question-count{
        overflow: visible !important;
    }
</style>
<?php //include_once "cms_tab.php"; ?>
<section class="courses-tab base-cont-top"> 
    <ol class="nav nav-tabs offa-tab">
        <li class="active">
            <a href="javascript:void(0)"> Question Bank</a>
            <span class="active-arrow" style="background: rgb(255, 255, 255) none repeat scroll 0% 0%;"></span>
        </li>
    </ol>
</section>


<!-- SIDEBAR RIGHT SIDE FIXED --> <!-- START -->
<div class="right-wrap base-cont-top container-fluid pull-right">

    <a href="<?php echo admin_url('coursebuilder/lecture/'.$lecture_id) ?>" class="btn btn-big btn-blue selected full-width-btn">
        Back to Assessment
    </a>
    
    <a href="javascript:void(0)" id="import_question_confirmed" class="btn btn-big btn-green disabled selected full-width-btn" onclick="importQuestionToAssessment()">
        Import Questions
    </a>


    <!--  Adding list group  --> <!-- END  -->
</div>
<!-- SIDEBAR RIGHT SIDE FIXED --> <!-- END -->



<section class="content-wrap base-cont-top">

    <!-- Nav section inside this wrap  --> <!-- START -->
    <!-- =========================== -->
<div class="container-fluid nav-content">

        <div class="row">
            <div class="rTable content-nav-tbl" style="">
                <div class="rTableRow">

                    <div class="rTableCell dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_text_difficulty"> <?php echo lang('all_questions') ?> <span class="caret"></span></a>
                        <ul class="dropdown-menu white inner-scroll">
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_all" onclick="filter_generate_test_by('all')"><?php echo lang('all_questions') ?></a></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_easy" onclick="filter_generate_test_by('easy')"><?php echo lang('easy') ?></a></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_medium" onclick="filter_generate_test_by('medium')"><?php echo lang('medium') ?></a></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_hard" onclick="filter_generate_test_by('hard')"><?php echo lang('hard') ?></a></li>
                        </ul>
                    </div>

                    <div class="rTableCell dropdown">

                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_text_category"> <?php echo lang('all_categories') ?> <span class="caret"></span></a>
                        <ul class="dropdown-menu white inner-scroll">
                            <li><a href="javascript:void(0)" id="dropdown_list_all" onclick="filter_category('all')"><?php echo lang('all_categories') ?></a></li>
                            
                            <?php if(!empty($q_parent_category)): ?>
                            <?php foreach($q_parent_category as $category): ?>
                                <?php if(strip_tags($category['ct_name'])): ?>
                                    <li><a href="javascript:void(0)" id="dropdown_list_<?php echo $category['id'] ?>" onclick="filter_category(<?php echo $category['id'] ?>)"><?php echo $category['ct_name'] ?></a></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>

                        
                    </div>
                    <div class="rTableCell dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_text_topics"> <?php echo lang('all_topics') ?> <span class="caret"></span></a>
                        <ul class="dropdown-menu white inner-scroll">
                            <li><a href="javascript:void(0)" id="dropdown_topic_list_all" onclick="filter_topics('all')"><?php echo lang('all_topics') ?></a></li>
                            <?php if(!empty($question_topics)): ?>
                            <?php foreach($question_topics as $topics): ?>
                                <?php if(strip_tags($topics['qc_category_name'])): ?>
                                    <li><a href="javascript:void(0)" id="dropdown_topic_list_<?php echo $topics['id'] ?>" onclick="filter_topics(<?php echo $topics['id'] ?>)"><?php echo $topics['qc_category_name']." - ".$topics['ct_name'] ?></a></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <div class="rTableCell">
                        <div class="input-group">
                            <input type="text" class="form-control srch_txt" id="generate_questions_keyword" placeholder="<?php echo lang('search_by_name') ?>" />
                            <a class="input-group-addon" id="basic-addon2">
                                <i class="icon icon-search"> </i>
                            </a>
                        </div> 
                    </div>
                    
                    <div class="rTableCell" >
                        <!-- lecture-control start -->
                        <div class="btn-group lecture-control btn-right-align" style="margin-top: 0px; display:none;" id="course_bulk">
                            <span class="dropdown-tigger" data-toggle="dropdown">
                                <span class='label-text'>
                                   Bulk Action  <!-- <span class="icon icon-down-arrow"></span> -->
                                </span>
                                <span class="tilder"></span>
                            </span>
                            <ul class="dropdown-menu pull-right" role="menu">
                                <li><a href="javascript:void(0)" class="list-disabled list-button" onclick="changeStatusBulk('<?php echo lang('are_you_sure_to'). ' ' .lang('publish_bulk_course').' ?' ?>', '<?php echo ($admin)?1:2 ?>', '<?php echo lang('activate') ?>')"><?php echo lang('activate') ?> </a></li>
                                <li><a href="javascript:void(0)" class="list-disabled list-button" onclick="changeStatusBulk('<?php echo lang('are_you_sure_to'). ' ' .lang('unpublish_bulk_course').' ?' ?>', '0', '<?php echo lang('deactivate') ?>')"><?php echo lang('deactivate') ?> </a></li>
                                <li><a href="javascript:void(0)" class="list-disabled list-button" onclick="addToCatalogBulk()"><?php echo lang('add_to_catalog')?> </a></li>

                            </ul>
                        </div>
                        <!-- lecture-control end -->
                    </div>
                </div>
            </div>

        </div>
    </div>
    
    <!-- =========================== -->
    <!-- Nav section inside this wrap  --> <!-- END -->


    <!-- LEFT CONTENT --> <!-- STARTS -->
    <!-- ===========================  -->

    <div class='bulder-content-inner'>


    <div class="col-sm-12 builder-right-inner"> <!-- right side bar section -->

       <!-- top Header with drop down and action buttons -->
        <div>
                <div class="pull-right">
                    <!-- Header left items -->
                    <h4 class="right-top-header question-count">
                        <?php 
                        $question_html  = '';
                        $question_html .= sizeof($questions).' / '.$total_questions;
                        $question_html .= ($total_questions>1)?' Questions':' Question';
                        echo $question_html;
                        $remaining_question = $total_questions - sizeof($questions);
                        $remaining_question = ($remaining_question>0)?'('.$remaining_question.')':'';
                        ?>
                    </h4>
                </div>
            <!-- !.Header left items -->

            
        </div>
        <!-- !.top Header with drop down and action buttons --> 
        
        <!-- Preivew of  test content will show here -->
        <div class="preivew-area test-content generate-test-wrapper" id="generate_test_wrapper">

            <?php 
        	$question_types = array( '1' =>  'Single Choice', '2' =>  'Multiple Choice', '3' =>  'Subjective', );
            ?>
            <!-- test folder root or parent section begins here -->
            <?php if(!empty($questions)): 
                $sl_no = 1;?>
            
            <?php foreach ($questions as $question) :?>
            <div class="default-view-txt m0 test-folder"  style="float: none; padding: 7px;" id="question_wrapper_<?php echo $question['id'] ?>">
                
                <input type="checkbox" class="import-questions" value="<?php echo $question['id'] ?>">
                
                <span class="question-sl-no"><?php echo $sl_no++ ?> .</span>
                    <?php 
                    $question_stripped = strip_tags($question['q_question']);
                    echo (strlen($question_stripped) > 80)?(substr($question_stripped, 0, 77).'...'):$question_stripped;
                    ?> 
                <span class="question-type"><?php echo $question_types[$question['q_type']]; ?></span>
  
             </div>
            <?php endforeach; ?>
            <?php endif; ?>
            <!-- !.test folder root or parent section begins here -->
        </div>
        <div <?php echo ((!$show_load_button)?'style="display:none;"':'') ?> class="default-view-txt m0 mb10 text-center " onclick="getQuestions()">
            <a href="javascript:void(0)" class="btn btn-green" id="load_more_question" data-toggle="modal" data-target="">
                Load More Question <?php echo $remaining_question ?><ripples></ripples>
            </a>
        </div>
            
    </div><!-- right side bar section -->

</div>
    <!-- ==========================  -->
    <!--  LEFT CONTENT--> <!-- ENDS -->


</section>


<!-- Basic All Javascript -->
<script>
var __assessmentId  = '<?php echo $assessment_id ?>';
var __lectureId     = '<?php echo $lecture_id ?>';
</script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/import_question.js"></script>
<!-- END -->
<?php include_once 'footer.php'; ?>