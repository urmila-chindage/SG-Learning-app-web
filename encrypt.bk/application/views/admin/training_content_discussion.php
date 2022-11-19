<?php include_once 'training_header.php'; ?>
        <!-- MAIN TAB --> <!-- END -->
<section class="content-wrap content-wrap-align forum-wrap settings-top" style="overflow-y: hidden;">
    <div class="course-cont-wrap discussion-wrap height-100"> 
        <div class="table course-cont rTable question-archives height-100" style="" id="left_discussion_content">
            <iframe src="<?php echo base_url('/forum_service/'.$course['id'].'/0/main'); ?>" frameborder="0" height="100%" width="100%" style="position:absolute;"></iframe>
        </div>
    </div>
</section>

    <script src="<?php echo assets_url() ?>js/jquery.timeago.js"></script>
    <!--course discussion JS-->
    <!-- <script type="text/javascript" src="<?php echo assets_url() ?>js/course_discussion.js"></script> -->
    <script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.slimscroll.min.js"></script>
    <!-- initialising the tag plugin using tokenize  -->
    <script type="text/javascript" src="<?php echo assets_url() ?>js/multi-select/jquery.tokenize.js"></script>
    <!-- END -->
    <!-- ADDING REDACTOR PLUGIN INSIDE -->
    <!-- ############################# --> <!-- START -->
        <!-- JS -->
        <script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
        <script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
        <script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script> 
        <script src="<?php echo assets_url() ?>js/discussion-ui.js"></script>        
<?php include_once 'training_footer.php'; ?>
<script>
	// jquery script for showing and hiding content based on facebook checkbox ends here
$( document ).ready(function() {			
	$(function(){
		$('.full-questions').slimScroll({
			height: '100%',
			wheelStep : 3,
			distance : '10px'
		});
	});
})	
</script>	

<!-- Modal pop up contents:: Delete Section discussion popup-->
        <div class="modal fade alert-modal-new" id="delete_comment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-group">
                            <b id="delete_confirmation_header"><?php echo lang('alert')?></b>
                            <input type="hidden" id="modal_parent_id">
                            <input type="hidden" id="modal_child_id">
                            <p class="m0" id="confirm_box_content_1">Are you sure you want to delete this discussion ?</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-green" data-dismiss="modal">CANCEL</button>
                        <button type="button" class="btn btn-red" id="dlete_confirm" onclick="deleteCommentUser()">DELETE</button>
                    </div>
                </div>
            </div>
        </div>
    <!-- !.Modal pop up contents :: Delete Section discussion popup-->

<!-- Modal pop up contents:: Ask a new question popup-->
        <div class="modal fade alert-modal-new" data-backdrop="static" data-keyboard="false" id="ask_question" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <div class="modal-body">
						<div class="row">
							<div class="col-sm-12">
                                Question Title * :
                                <div class="form-group">
                                    <input  type="text" class="form-control" placeholder="Question title" id="add_discussion_title">
                                </div>
                            </div>
							<div class="col-sm-12">
                                Describe what you're trying to achieve and where you're getting stuck *:
                                <textarea maxlength="1000" id="add_discussion_input" class="add-qstn" name="content-0"></textarea>
                            </div>
                                                        
                                                    
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-green" id="add_discussion">POST</button>
                        <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
                    </div>
                </div>
            </div>
        </div>
    <!-- !.Modal pop up contents :: ask a new question  popup-->