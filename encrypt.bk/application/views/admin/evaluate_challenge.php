<?php include_once 'header.php'; ?>
<script type="text/javascript">
	var challenge_id = '<?php echo $challenge_id; ?>';
	var user_id    = '<?php echo $user_id; ?>';
	var user_name  = '<?php echo $users[0]["us_name"]; ?>';
    var next_id    = '<?php echo $next_id; ?>';
</script>
<section class="content-wrap minus-level-top pad0 pad-top10">
	<div class="col-sm-6 ">
	    <!--<a href="<?php echo admin_url().'challenge_zone/report/'.$challenge_id; ?>" class="btn btn-red bck-btn selected"><i class="icon icon-left"></i> BACK<ripples></ripples></a> --> <h2 class="dsp-inline bold-heading"><?php echo strtoupper($current_category['ct_name']); ?> - <?php echo strtoupper($current_challenge['cz_title']); ?></h2>
	</div>
	<div class="col-sm-6 user-slide">
	    <div class="pull-right dsp-inline">
	        <a href="<?php 
        if($next_id != ''){
        echo admin_url().'challenge_zone/evaluate_challenge/'.$challenge_id.'/'.$next_id;
        }
        else{
            echo '#';
        }
             ?>">
	            <h2 class="font14 font-bold nxt">
	                NEXT <i class="icon font-bold icon-right-open-big"></i>
	            </h2>
	        </a>
	    </div>
	    <div class="pull-right dsp-inline pad-top5 font15">
	        <a href="<?php echo admin_url().'user/profile/'.$user_id; ?>">
	            <span class="icon-wrap-round sm-img img">
	                <img src="<?php echo (($users[0]['us_image'] == 'default.jpg')?default_user_path():  user_path()).$users[0]['us_image']; ?>">
	            </span>

	            <span class="line-h36"><?php echo $users[0]['us_name']; ?></span>
	        </a>
	    </div>
	    <div class="pull-right dsp-inline">
	        <a href="<?php 
                if($prev_id != ''){
                echo admin_url().'challenge_zone/evaluate_challenge/'.$challenge_id.'/'.$prev_id;
                }
                else{
                    echo '#';
                }
            ?>">
	            <h2 class="font14 font-bold prev">
	                <i class="icon font-bold  icon-left-open-big"></i>PREV
	            </h2>
	        </a>
	    </div>
	</div>
	<div class="container-fluid">
        <div class="col-sm-12 table-data-bdr marg-top10">
            <div class="col-sm-12">
                <div class="rTable width-100p">
                    <div class="rTableRow">
                        <div class="rTableCell"><?php
                        	$dt = new DateTime($users[0]['cza_attempted_date']);
                        	echo strtoupper($dt->format('M d Y'));
                        ?></div>
                        <div class="rTableCell font-green"><?php echo $users[0]['correct']; ?> Correct</div>
                        <div class="rTableCell font-red"><?php echo $users[0]['incorrect']; ?> Wrong</div>
                        <div class="rTableCell font-lgt-grey"><?php echo $users[0]['count_not_tried']; ?> Not Tried</div>
                        <div class="rTableCell font-green"><?php echo round($users[0]['percentage'], 2); ?>% success</div>
                        <div class="rTableCell"><?php echo $users[0]['cz_duration']; ?> min</div>
                        <div class="rTableCell pad-vert"><a class="btn btn-green selected pull-right mrgin-rightM30 small-font" onclick="printreport()" >PRINT<ripples></ripples></a></div>
                    </div>

                </div>
            </div>
        </div>
    </div>
<?php 
$cnt = 1;
foreach($users[0]['assessment_report'] as $report)
{ 
?>
    <div class="container-fluid">
    	<div class="col-sm-12 question-cont marg-top10">
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
            		for($i =0; $i < count($report['qo_options_value']);$i = $i + 1) 
            		{ 
            		?>
                    <?php if($i % 2 == 0){ ?>
                    <div class="rTableRow">
                    <?php } ?>
                        <?php $tags = array("<p>", "</p>"); ?>
                        <div class="rTableCell">
                            <?php echo chr($chr++).'. '.str_replace($tags, "", $report['qo_options_value'][$i]['qo_options']); ?>
                        </div>
                    <?php if($i % 2 != 0){ ?>    
                    </div>
                    <?php } ?>
                    <?php 
                	} 
                	?>
                </div>
            </div>
    <?php } else if($report['q_type'] == 3){ ?>
    		<div class="col-sm-12 quest-descr">
    			<?php echo $report['q_question']; ?>
    			<div class="choice-question width-100p">
                    <span class="font-bold600 font-lgt-grey" >Evaluate  users answer</span>
                    <span class="form-control"><?php echo $report['czr_answer']; ?></span>
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
                                <select class="form-control dsp-inline descriptiveval" id="<?php echo $report['czr_id']; ?>">
                                    <?php for($j = $report['q_negative_mark'] * -1;$j <= $report['q_positive_mark']; $j++){ ?>
                                    	<option <?php if( intval($report['czr_mark']) == $j){ echo 'selected ';}?> value="<?php echo $j ?>" ><?php echo $j ?></option>
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

</section>
<script type="text/javascript" src="<?php echo assets_url() ?>js/challenge_zone_report.js"></script>
<?php include_once 'footer.php'; ?>