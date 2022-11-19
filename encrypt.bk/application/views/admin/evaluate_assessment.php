<?php include_once 'header.php'; ?>

<script type="text/javascript">
	var lecture_id = '<?php echo $lecture_id; ?>';
	var user_id    = '<?php echo $user_id; ?>';
	var user_name  = '<?php echo $user["us_name"]; ?>';
    var next_id    = '<?php echo $next_id; ?>';
    var attempt_id = '<?php echo $attempt_id; ?>';
</script>
<section class="content-wrap minus-level-top pad0 pad-top10">
	<div class="col-sm-6 ">
	    <a href="<?php echo admin_url().'coursebuilder/report/'.$lecture_id; ?>" class="btn btn-red bck-btn selected"><i class="icon icon-left"></i> BACK<ripples></ripples></a> <h2 class="dsp-inline bold-heading"><?php echo strtoupper($course['cb_title']); ?> - <?php echo strtoupper($lecture['cl_lecture_name']); ?></h2>
	</div>
	<div class="col-sm-6 user-slide">
	    <div class="pull-right dsp-inline pad-top5 font15">
	        <a href="<?php echo admin_url().'user/profile/'.$user_id ?>">
	            <span class="icon-wrap-round sm-img img">
	                <img src="<?php echo (($user['us_image'] == 'default.jpg')?default_user_path():  user_path()).$user['us_image']; ?>">
	            </span>

	            <span class="line-h36"><?php echo $user['us_name']; ?></span>
	        </a>
	    </div>
	</div>
	
	<div class="container-fluid">
        <div class="col-sm-12 table-data-bdr marg-top10">
            <div class="col-sm-12">
                <div class="rTable width-100p">
                    <div class="rTableRow">
                        <div class="rTableCell"><?php
                        if(isset($user['aa_attempted_date'])){
                            $dt = new DateTime($user['aa_attempted_date']); 
                            echo strtoupper($dt->format('M d Y'));
                        }
                        else{
                            echo '0';
                        }
                        ?></div>
                        <div class="rTableCell font-green">
                        <?php
                        if(isset($user['correct'])){
                            echo $user['correct']; 
                        }
                        else{
                            echo '0';
                        }
                        ?> Correct</div>
                        <div class="rTableCell font-red"><?php 
                        if(isset($user['incorrect'])){
                            echo $user['incorrect']; 
                        }
                        else{
                            echo '0';
                        }
                        ?> Wrong</div>
                        <div class="rTableCell font-lgt-grey">
                        <?php 
                        if(isset($user['count_not_tried'])){
                            echo $user['count_not_tried']; 
                        }
                        else{
                            echo '0';
                        }
                        ?> Not Tried</div>
                        <div class="rTableCell font-green"><?php 
                        if(isset($user['success_percent'])){
                            echo round($user['success_percent'], 2);
                        }
                        else
                        {
                            echo '0';
                        }
                        $user_duration = $user['aa_duration']/60;
                        $user_in_min   = substr($user_duration, 0, 4);
                        ?>% success</div>
                        <div class="rTableCell"><?php echo $user_in_min; ?> min</div>
                        <div class="rTableCell pad-vert"><a class="btn btn-green selected pull-right mrgin-rightM30 small-font" onclick="printreport()" >PRINT<ripples></ripples></a></div>
                    </div>

                </div>
            </div>
        </div>
    </div>
<?php 
$cnt = 1;
if(isset($user['assessment_report'])){
foreach($user['assessment_report'] as $report)
{ 
?>
    <div class="container-fluid">
    	<div class="col-sm-12 question-cont marg-top10">
            <?php if($report['q_type'] != ''){ ?>
    		<div class="col-xs-6 font15 font-bold">
                Question: <?php echo $cnt; ?> <span class="single-pad-left">
                 
                 <?php 
                 	if($report['q_type'] == 1){
                 		echo 'SINGLE CHOICE';
                 	}
                 	else if($report['q_type'] == 2){
                 		echo 'MULTI CHOICE';
                 	}
                 	else if($report['q_type'] == 3){
                 		echo 'EXPLANATORY';
                 	}
                 ?>
                 </span>
            </div>
            <?php } ?>
	<?php if($report['q_type'] == 1 || $report['q_type'] == 2) { ?>
            <div class="col-xs-6 font15 font-bold">
            <?php if($report['correct'] == 1){ ?>
                <i class="font-green icon icon-ok pull-right"></i>
            <?php }else if($report['correct'] == 0){ ?>
            	<i class="font-red pull-right icon icon-cancel-1"></i>
            <?php } ?>
            </div>
            <div class="col-sm-12 quest-descr">
            	<?php echo $report['q_question']; ?>
            	<div class="rTable choice-question width-100p">
            		<?php 
            		$chr  = 65; 
            		for($i =0; $i < count($report['options']);$i = $i + 1) 
            		{ 
            		?>
                    <?php if($i % 2 == 0){ ?>
                    <div class="rTableRow">
                    <?php } ?>
                        <div class="rTableCell">
                            <div class="report-option-no col-xs-2 col-sm-1">
                                <?php echo chr($chr++).'.';?>
                            </div>
                            <div class="report-option col-xs-10 col-sm-11">
                                <?php echo $report['options'][$i]['qo_options']; ?>
                            </div>
                        </div>

                        
                    <?php if($i % 2 != 0){ ?>
                    </div>
                    <?php } 
                	} 
                	?>
                </div>
            </div>
    <?php } else if($report['q_type'] == 3){ ?>
    		<div class="col-sm-12 quest-descr">
    			<?php echo $report['q_question']; ?>
    			<div class="choice-question width-100p">
                    <span class="font-bold600 font-lgt-grey" >Evaluate  users answer</span>
                    <textarea class="form-control"><?php echo $report['ar_answer']; ?></textarea>
                </div>
    		</div>
    <?php } ?>

    	</div>

    	<?php if($report['q_type'] == 1 || $report['q_type'] == 2){ ?>
    	<div class="col-sm-12 result-sec table-data-bdr dark marg-top10">
            <div class="col-sm-12">
                <div class="rTable width-100p">
                    <div class="rTableRow font-bold">
                        <div class="rTableCell">Correct Answers: 
                        <?php 
                        if(isset($report['correct_answer'])){
                        	echo $report['correct_answer'];
                        } 
                        ?></div>
                        <?php if($report['correct'] == 1){ ?>
                        <div class="rTableCell font-green">Marked Answers: 
                        <?php
                        if(isset($report['user_answer'])){
                        	echo $report['user_answer'];
                        } 
                        ?>
                        </div>
                        <div class="rTableCell pad-vert"><span class = "pull-right font-green">Mark +<?php echo $report['q_positive_mark']; ?></span></div>
                        <?php }
                        	else if($report['correct'] == 0){
                        ?>
                        <div class="rTableCell font-red">Marked Answers: 
                        <?php
                        if(isset($report['user_answer'])){
                        	echo $report['user_answer'];
                        } 
                        ?>
                        </div>
                        <div class="rTableCell pad-vert"><span class = "pull-right font-red">Mark -<?php echo $report['q_positive_mark']; ?></span></div>
                        <?php
                        	}
                        	else if($report['correct'] == 2){
                        ?>
                        	<div class="rTableCell font-lgt-grey">Not Tried</div>
                        	<div class="rTableCell pad-vert"></div>
                        <?php
                        	}
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php } else if($report['q_type'] == 3){ ?>
        <div class="col-sm-12 result-sec explan-qst table-data-bdr dark marg-top10">
            <div class="col-sm-12">
                <div class="rTable width-100p">
                    <div class="rTableRow font-bold">
                        <div class="rTableCell">Explanatory question need manual evaluation </div>
                        <div class="rTableCell pad-vert">
                            <span class="pull-right">Mark 
                                <select class="form-control dsp-inline descriptiveval" id="<?php echo $report['ar_id']; ?>">
                                    <?php for($j = $report['q_negative_mark'] * -1;$j <= $report['q_positive_mark']; $j++){ ?>
                                    	<option <?php if( intval($report['ar_mark']) == $j){ echo 'selected ';}?> value="<?php echo $j ?>" ><?php echo $j ?></option>
                                    <?php  }  ?>
                                    
                                </select>
                            </span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <?php } ?>
    </div>
<?php
 $cnt++;
 } 
 ?>

<div class="container-fluid marg-bot10 pad-top30">
    <div class="col-sm-12">
        <a class="btn btn-green selected pull-right" onclick="savecontinue()" >SAVE &amp; CONTINUE<ripples></ripples></a>
        <a class="btn btn-green selected pull-right" onclick="saveexit()" >SAVE &amp; EXIT<ripples></ripples></a>
    </div>
</div>
<?php
}
?>

</section>

<script type="text/javascript" src="<?php echo assets_url() ?>js/assessment_report.js"></script>
<?php include_once 'footer.php'; ?>