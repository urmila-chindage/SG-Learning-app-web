
<?php include_once 'header.php'; ?>
<script type="text/javascript">
	var lecture_id = '<?php echo $lecture_id; ?>';
	var user_id    = '<?php echo $user_id; ?>';
	var user_name  = '<?php echo $user["us_name"]; ?>';
    var admin_name = '<?php echo $admin_name; ?>';
    var next_id    = '<?php echo $next_id; ?>';
    var attempt_id = '<?php echo $attempt_id; ?>';   
</script>
<section class="content-wrap minus-level-top pad0 pad-top10">
	<div class="col-sm-6 ">
	    <a href="<?php echo admin_url().'report/assignment'; ?>" class="btn btn-red bck-btn selected"><i class="icon icon-left"></i> BACK<ripples></ripples></a> <h2 class="dsp-inline bold-heading"><?php echo strtoupper($course['cb_title']); ?> - <?php echo strtoupper($lecture['cl_lecture_name']); ?></h2>
	</div>

	<div class="col-sm-6 user-slide">
	    <div class="pull-right dsp-inline pad-top5 font15">
	        <a href="<?php echo admin_url().'user/profile/'.$user['id']; ?>">
	            <span class="icon-wrap-round sm-img img">
	                <img src="<?php echo (($user['us_image'] == 'default.jpg')?default_user_path():  user_path()).$user['us_image']; ?>">
	            </span>

	            <span class="line-h36"><?php echo $user['us_name']; ?></span>
	        </a>
	    </div>
	</div>
	<div class="container-fluid">
        <div class="col-sm-12 table-data-bdr marg-top10 off-border-bottom">
        </div>
    </div>

    <section>
    	<div style="width: 66%;" class="left-wrap container">
            <div class="row discussion-container listing-discuss course-cont-wrap pad0">   
                <div class="col-sm-12 bg-white">         
                    <div class="">
                		<div class="row">
              
                    		<div class="col-sm-12 quest-descr">
                    			<h2 class="bold-heading pad0 marg-top10"><?php echo strtoupper($lecture['cl_lecture_name']); ?> <?php echo isset($answer_details['created_date'])?' [Submitted : '.date('d-M-Y',strtotime($answer_details['created_date'])).']':''; ?></h2>
                        		<?php echo $lecture['cl_lecture_description']; ?>
                    		</div>

                </div>
                <div class="row table-data-bdr marg-top10 padding-left-right off-border-bottom">
                    <div class="">
                        <div class="rTable width-100p">
                            <div class="rTableRow">
                                <div class="rTableCell">
                                <?php
                                $dt = new DateTime($answer_details['updated_date']);
                                echo $dt->format('M d Y');
                                ?>
                                </div>
                                <div class="rTableCell font-green">
                                <?php
                                	if($answer_details['mark'] == -1){
                                		echo 'Not evaluated yet';
                                	}
                                	else{
                                		echo 'Mark '.$answer_details['mark'].'/'.$question['dt_total_mark'];
                                	}
                                ?>
                                </div>
                                <div class="rTableCell pad-vert"><a class="btn btn-green selected pull-right small-font" href="<?php echo admin_url().'coursebuilder/download_descriptive_test/'.$lecture_id; ?>">DOWNLOAD QUESTION PAPER<ripples></ripples><ripples></ripples></a></div>
                                
                                
                                <div class="rTableCell pad-vert dsp-inline"><span class="pull-right dsp-inline">Enter Mark 
                                        <input type="number" min="0" max="<?php echo $question['dt_total_mark'] ?>" id="txtmrk" class="form-control dsp-inline" style="width: 80px;">
                                        <input type="hidden" id="descriptive_max_mark" value="<?php echo $question['dt_total_mark'] ?>" />
                                    </span> </div>
                            </div>

                        </div>
                    </div>
  </div>
<div></div>
            </div>
                    </div>
                    <hr>
                    <div class="col-sm-12" id="cmtsection"> 
                    <?php foreach($comments as $comment) { ?>
                        <div class="right-group-wrap old-chat clearfix" id="comment_id_<?php echo $comment['comment_id']; ?>">
                            <span class="">
                                <img class="box-style" src="<?php echo (($comment['us_image'] == 'default.jpg')?default_user_path():  user_path()).$comment['us_image']; ?>">
                            </span>
                            <span class="user-date">
                                <a>
                                    <span class="user"><?php echo $comment['us_name']; ?></span>
                                    <span class="pull-right comment-delete-btn" onclick="deleteComment(<?php echo $comment['comment_id']; ?>, '<?php echo $comment['file']; ?>')">X</span>
                                    <span class="pull-right date"><?php
	                                $dt = new DateTime($comment['updated_date']);
	                                echo $dt->format('M d Y');
	                                ?>
                                </span>
                                </a>
                                <div class="content-text"><?php echo $comment['comment']; ?></div>
                                <?php if($comment['file'] != ""){ ?>
                                <div class="attachment"><a href="<?php echo assignment_path().$comment['file']; ?>" download><i class="icon icon-attach-1"></i>Attachment</a></div>
                                <?php } ?>
                            </span>
                        </div>       
                	<?php } ?>
                    </div>
                    <div class="col-sm-12"> 
                        <div class="right-group-wrap old-chat clearfix">
                            <span class=""><img class="box-style" src="<?php echo (($admin_image == 'default.jpg')?default_user_path():  user_path()).$admin_image; ?>"></span>
                            <span class="user-date">
                                <div class="form-group">
                                    <div class="pad0">
                                        <input id="cmtbox" type="text" class="form-control">
                                    </div>
                                </div>
                            </span>
                        </div>      
                    </div>
                </div>     
            </div>
    </section>

    <div class="container-fluid marg-bot10 pad-top30">
        <div class="col-sm-12">
            <a class="btn btn-green selected pull-right" id="saveandcontinue" >SAVE &amp; CONTINUE<ripples></ripples></a>
            <a class="btn btn-green selected pull-right" id="saveandexit">SAVE &amp; EXIT<ripples></ripples></a>
        </div>
    </div>

</section>
<script type="text/javascript" src="<?php echo assets_url() ?>js/descriptive_evaluate.js?v=3"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery-dateFormat.min.js"></script>

<?php include_once 'footer.php'; ?>