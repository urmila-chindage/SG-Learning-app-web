<?php include_once 'material_header.php'; ?>
<style>
.custom-header{ overflow: hidden;}
.scrom-content{ width: 90% !important;}
</style>
<div class="wrapper">
    <div class="left custom-header">
         <div class="maincontent">
                <?php if(!$subscription){
                if($course['cb_preview'] == '1'){
                //if($course['cb_preview_time'] <= $user_preview_time) { ?>
                    <div class="timer-count">
                        <span class="counter-text" id="timer_free_preview_course"></span>
                    </div><!--timer-count-->
                    <?php  }}?>
                    <div class="innercontent video-container" id="limit_reached_container">
                        <style>
                            .limit-reached-informer h1 {
                                color: #922318;
                                font-size: 2em;
                                padding-top: 60px;
                                text-align: center;
                            }
                            .limit-reached-informer h3 {
                                color: #922318;
                                padding-top: 12px;
                                text-align: center;
                            }
                        </style>
                        <div class="limit-reached-informer">
                            <h1>You have completed maximum allowed attempts.</h1>
                        </div>
                    </div>
                    <div class="innercontent video-container" id="videocontent">
                        <?php include_once 'video_player.php'; ?>
                    </div>
                    <div class="innercontent live-video-container" id="livevideocontent">
                        <?php include_once 'live-video-player.php'; ?>
                    </div>
        
        
                    <div class="innercontent textcontainer" id="descriptivecontent">
                    </div>     
                   
                    <div class="innercontent textcontainer container-fluid" id="scromcontent">
                    </div>     
              
                    <div class="innercontent pdfloader" id="pdfcontent">
                    </div>
        
                    <div class="innercontent pdfloader redactor-styles lecture-html-content redactor-editor" id="textcontent">
                    </div>
        
                    <div class="innercontent pdfloader" id="lecture_iframe">
                        <iframe width="100%" height="100%" src="" frameborder="0" allowfullscreen></iframe>
                    </div>
                    
                    <div class="innercontent pdfloader" id="lecture_recorded_iframe">
                        <iframe width="100%" height="100%" src="" frameborder="0" allowfullscreen></iframe>
                    </div>
                    
                    <div class="innercontent messageloader" id="lecture_message">
                    </div>
                    
                    <div class="innercontent certificateLoader" id="certificateContent">
                        <div class="certificateGenerateBlock">
                        <h4 class="testtitle">Generate Certificate</h4>
                        <p class="certificateContent" id="status_certificate"></p>
                        <p></p>
                        <div class="cent-algn-txt">
                        <a href="javascrip:void(0)" id="show_percentage" class="link-style"></a>
                    </div>
                        <div class="course-progress">
                          <div class="course-progressBar" id="show_percentage_div"></div>
                        </div>
                        <div class="course-actions" id="show_button_div">
                           
                               <!--  <button class="green-btn" id="green_cert_button">Generate Your Certificate</button>
                            
                                <button class="offwhite-btn" id="white_cert_button">Generate Your Certificate</button> -->
                            
                        </div>
                        
                    </div>
            </div>  <!--end of maincontent-->
       
        <div class="ts-md-12 static-foot footbox">
        	<div class="ts-md-2 us-hide"><a href="javascript:void(0)" id="previous_button" class="orangeicon icon-left-open notp btn btn-default btn-xs sdpk-btn"><span class="dark-text">Previous</span></a></div>
        	<div class="ts-md-4 ts-us-4"> <span class="text-left center-block dark-text" id="lecture_title"></span></div>


            <div class="pull-right course-foot-btn us-hide"><a href="javascript:void(0)" id="next_button" class="end-next"><span class="dark-text btn btn-default btn-xs sdpk-btn">Next</span><span class="orangeicon icon-right-open"></span></a> </div>
  
            
                <div class="pull-right course-foot-btn"><span class="text-center center-block"><a href="javascript:void(0)" id="browse_curriculum"  class="browse-curiculum dark-text btn btn-default btn-xs sdpk-btn">Curriculum</a></span></div>        
            
                <div class="pull-right course-foot-btn"><span class="text-center center-block"><a href="javascript:void(0)" id="browse_discussion" class="browse-questions dark-text btn btn-default btn-xs sdpk-btn">Ask Question</a></span></div>            
            
        </div>    
    </div> 
    </div>
    
    <!--curiculums section starts here-->
	<div class="curiculums" id="curriculum">
        <div class="question-head bighead">
            <span class="heading">Curriculum</span>
            <span class="action-btns"><a href="javascript:void(0)" class="close-curiculum"><span class="icon-cancel"></span></a></span>
        </div>
        <ul class="curriculumsections">
        <?php if(!empty($materials)): ?>
        <?php $section = 1; ?>
        <?php foreach($materials as $material): ?>
        <?php  
            if(empty($material['lectures']))
            {
               continue;
            }   
        ?>
        <li>
            <h4><b><?php echo lang('section') ?> <?php echo $section++; ?>:</b> <?php echo $material['s_name'] ?></h4>
            <ul>
                <?php if(!empty($material['lectures'])): ?>
                <?php $item_lecture = 1; $item_quiz = 1; ?>
                <?php foreach ( $material['lectures'] as $lecture):?>
                <li class="link lectures-custom" id="lecture_<?php echo $lecture['id'] ?>" data-prev="<?php echo $lecture['previous_lecture'] ?>" data-next="<?php echo $lecture['next_lecture'] ?>" data-ID="#pdfcontent" onClick="loadLecture('<?php echo $lecture['id'] ?>')">
                <span class="ts-md-12">
                        <span class="curriculumtype"></span>

                        <?php /* ?><span class="curriculumtype <?php echo $lecture_type_icon[$lecture['cl_lecture_type']] ?>"></span><?php */ ?>

                        <?php if($lecture['cl_lecture_type'] != 3){ ?>
                        <span class="curriculumdetails"><?php echo $item_lecture++ ?>: <?php echo $lecture['cl_lecture_name'] ?></span>
                        <?php } else{ ?>
                        <span class="curriculumdetails"><?php echo lang('quiz').' '.$item_quiz++ ?>: <?php echo $lecture['cl_lecture_name'] ?></span>
                        <?php } ?>
                        <?php if($lecture['cl_lecture_type'] == 1){ ?><span class="video-duration"><?php if($lecture['cl_duration'] >= '3600') { echo gmdate("H:i:s", $lecture['cl_duration']); } else { echo gmdate("i:s", $lecture['cl_duration']); } ?></span><?php } ?> 
                        <?php if($lecture['cl_lecture_type'] == 2){ ?><span class="video-duration"><?php echo $lecture['cl_total_page'].' '.'Pages';?></span><?php } ?> 
                        <?php /* if($lecture['cl_lecture_type'] == 3){ ?><span class="video-duration"><?php $lecture['cl_duration'] = $lecture['cl_duration'] * 60; if($lecture['cl_duration'] >= '3600') { echo gmdate("H:i:s", $lecture['cl_duration']); } else { echo gmdate("i:s", $lecture['cl_duration']); } ?></span><?php }*/ ?> 
                        <?php if($lecture['cl_lecture_type'] == 4){?>
                            <span class="video-duration">
                        <?php if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $lecture['cl_filename'], $id)) {
                                  $values = $id[1];
                                } else if (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $lecture['cl_filename'], $id)) {
                                  $values = $id[1];
                                } else if (preg_match('/youtube\.com\/v\/([^\&\?\/]+)/', $lecture['cl_filename'], $id)) {
                                  $values = $id[1];
                                } else if (preg_match('/youtu\.be\/([^\&\?\/]+)/', $lecture['cl_filename'], $id)) {
                                  $values = $id[1];
                                }
                                else if (preg_match('/youtube\.com\/verify_age\?next_url=\/watch%3Fv%3D([^\&\?\/]+)/', $lecture['cl_filename'], $id)) {
                                    $values = $id[1];
                                } 
                                $key          = config_item('youtube_api');
                                $url         = "https://www.googleapis.com/youtube/v3/videos?part=contentDetails&id=".$values."&key=".$key;
                               
                                $dur          = file_get_contents($url);
                                $duration     = json_decode($dur, true);
                                foreach ($duration['items'] as $vidTime) {
                                     $vTime= $vidTime['contentDetails']['duration'];
                                }
                                $duration = new DateInterval($vTime);
                                //print_r($duration->h); die;
                                if($duration->h > 0){
                                    print $duration->format('%H:%I:%S');
                                }
                                else{
                                    print $duration->format('%I:%S');
                                }
                            ?>
                            </span>
                           <?php } ?>  
                           <?php if($lecture['cl_lecture_type'] == 7){ ?><span class="video-duration"><?php $lecture['cl_duration'] = $lecture['cl_duration'] * 60; if($lecture['cl_duration'] >= '3600') { echo gmdate("H:i:s", $lecture['cl_duration']); } else { echo gmdate("i:s", $lecture['cl_duration']); } ?></span><?php } ?> 
                        <span class="curriculumtype icon-ok-circled2 greenicon"></span>
                 </span>
        
                 </li>
                <?php endforeach; ?>
                <?php endif;?>
            </ul>
        </li>
        <?php endforeach; ?>
        <?php endif; ?>
        <?php if($show_certificate=='1'){ ?>
            <li class="orange-btn certificate-btn"><a href="#" class="certificate-generate" onclick="javascript:generateCertificate()">Generate Certificate</a></li>
         <!--<li class="orange-btn"><a href="#" class="certificate-generate" id="genereateCertificate">Generate Certificate</a></li>-->
         <?php } ?>
        </ul>
		
    </div>
    <!--curiculums section ends here-->    
    
    
    <!--discussions section starts here-->
	<div class="discussions">
    	<div class="discussionContent">
        
            <div class="question-head bighead">
                <span class="heading">Questions</span>
                <span class="action-btns"><a href="javascript:void(0)" class="close-questions"><span class="icon-cancel"></span></a></span>
            </div>
            <div class="question-content">
            	<div class="search-question">
                	<div class="question-box sptb"><input type="text" name="question-search" placeholder="Search for a question" id="search_text"></div>
                    <div class="question-tools">
                    	<div class="half lefty sptb" id="search_count"><span id="q_count"><?php echo count($course_comments);?></span> Questions in this section</div>
                        <div class="half righty"><button class="green-btn righty ask-question">Ask a new question</button></div>
                    </div>
                </div>    
               	<span class="solidline"></span> 
                <div class="question-archives">
                    <div class="loader" style="display:none;"></div>
                    <ul id="show_parent" class="show-parent">
                      <?php if(!empty($course_comments)) { 
                            foreach($course_comments as $course_comment) { ?>  
                                <li class="individual-question" id="<?php echo $course_comment['id']?>">
                                    <span class="question-avatar"><img src="<?php echo (($course_comment['us_image'] == 'default.jpg')?default_user_path():user_path()).$course_comment['us_image']; ?>" alt="<?php echo $course_comment['us_name'] ?>" width="50" height="50"></span>
                                    <span class="question-description">
                                        <div class="archive-question"><?php echo $course_comment['comment_title'];?></div>
                                        <div class="archive-answer"><?php echo $course_comment['comment'];?></div>
                                    </span>
                                </li> 
                            <?php } } else { ?>     
                            <div class="fx-c p20" style=""> 
                                <div class="fx tac"> 
                                    <div class="bold pt10" translate="" style="">
                                        <span>No questions yet</span>
                                            </div> 
                                            <div class="bold pt10">
                                        <span style="display:none;">No related questions found</span>
                                    </div> 
                                    <div translate="">
                                        <span>Be the first to ask your question! You’ll be able to add details in the next step.</span>
                                    </div> 
                                    <img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/raise_your_hand.png" alt="img">
                                </div> </div>
                            <?php } ?>
                    </ul>    
            	</div>
            </div>
        </div>
    </div>
    <!--discussions section ends here-->
    
     <!-- add new question starts here-->
    
     	<div class="add-new-questions">
    	<div class="discussionContent">
        
            <div class="question-head">
                <span class="action-btns lefty head-icons"><a href="#" class="back-main"><span class="big-icon icon-left-open-narrow"></span></a></span>
                <span class="heading">My Question</span>
                <span class="action-btns righty head-icons"><a href="#" class="close-detail"><span class="icon-cancel"></span></a></span>
            </div>
            
            <div class="question-content">
            	<div class="search-question">
                	<div class="question-box spt"><input type="text" placeholder="Question title" id="add_discussion_title"/></div>
                	<div class="question-box sptb"><textarea placeholder="Describe what you're trying to achieve and where you're getting stuck" class="add-qstn" id="add_discussion_input" maxlength="1000"></textarea></div>
                    <div class="question-tools">
                        <div class="half righty"><button class="green-btn righty" id="add_discussion">Post question</button></div>
                    </div>
                </div>    

                
            </div>
        </div>
    </div>   

    <!-- add new question starts here-->
    
    <!--question detailed view starts here-->
	<div class="question-detail">
    	<div class="discussionContent details-content">
        
            <div class="question-head">
                <span class="action-btns lefty head-icons"><a href="#" class="back-question"><span class="big-icon icon-left-open-narrow"></span></a></span>
                <span class="heading">Question Details</span>
                <span class="action-btns righty head-icons"><a href="#" class="close-detail"><span class="icon-cancel"></span></a></span>
            </div>
			<div class="question-content full-questions">
                <div  class="question-archives">
                    <ul id="show_discussion_div">

                    </ul>    
                </div>
            </div>
        </div>
    </div>    
     <!--question detailed view ends here-->
    
</div>
<?php include_once 'material_footer.php'; ?>

<!-- Modal pop up contents:: Rating Section popup-->
        
    <!-- !.Modal pop up contents :: Rating Section popup-->
    
<!-- Modal pop up contents:: Delete Section popup-->
        <div class="modal fade alert-modal-new" data-backdrop="static" data-keyboard="false" id="delete_comment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 id="confirm_box_title"><?php echo lang('alert')?></h4>
                      </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="hidden" id="modal_parent_id">
                            <input type="hidden" id="modal_child_id">
                            <p class="m0" id="confirm_box_content_1"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-green" data-dismiss="modal">CANCEL</button>
                        <button type="button" class="btn btn-red" id="dlete_confirm" onclick="deleteCommentUser()">DELETE</button>
                    </div>
                </div>
            </div>
        </div>
    <!-- !.Modal pop up contents :: Delete Section popup-->

    <!-- Modal pop up contents:: Delete Section popup-->
        <div class="modal fade alert-modal-new" data-backdrop="static" data-keyboard="false" data-ba id="report_comment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 id="confirm_box_title"><?php echo lang('report_alert')?></h4>
                      </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="hidden" id="modal_parent_id_report">
                            <input type="hidden" id="modal_child_id_report">
                            <p class="m0" id="confirm_box_content_report"><?php echo lang('report_text')?></p>
                            <p id="show_error_report"></p>
                            <textarea class="form-control" rows="5" cols="20" id="report_reason" placeholder="Please enter reason for reporting (required)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-green" data-dismiss="modal">CANCEL</button>
                        <button type="button" class="btn btn-red" id="report_confirm" onclick="reportCommentConfirm()">REPORT</button>
                    </div>
                </div>
            </div>
        </div>
    <!-- !.Modal pop up contents :: Delete Section popup-->
    <script>
        <?php $browse = $this->uri->segment(4); ?>
        <?php if($browse == 'browse_discussion'): ?>
            $(document).ready(function(){
               $('#browse_discussion').trigger('click');
            });
        <?php endif; ?>
    </script>