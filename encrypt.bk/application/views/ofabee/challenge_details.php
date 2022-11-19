<?php include 'header.php'; 
//echo "<pre>";print_r($users);die;
?>

<script type="text/javascript">
	var challenge_id = '<?php echo $challenge_id; ?>';
	var user_id    = '<?php echo $user_id; ?>';
    var attempted_id   = '<?php echo $attempted_id; ?>';
</script>
<div class="container darkbg">
    <div class="row">
    	<div class="col-xs-12 col-sm-12 spacer">
        	<div class="col-xs-12 col-sm-12">
        	<a href="<?php echo site_url().'/dashboard'; ?>"><button class="btn btn-danger mybutton"><?php echo lang('back'); ?></button></a>
            <span class="maintitle"><?php echo strtoupper($current_category['ct_name']); ?> - <?php echo strtoupper($current_challenge['cz_title']); ?></span></div>
        </div>
    </div>
    <div class="row borderrow">
    	<div class="col-xs-12 col-sm-12">
        	<div class="row lh">
        	<div class="col-xs-6 col-sm-6 sdfw">
            	<div class="col-xs-3 col-sm-3">
            		<?php
            		$dt = new DateTime($users[0]['cza_attempted_date']);
                    echo strtoupper($dt->format('M d Y'));
                    
                    $duration_in_minutes     = $users[0]['cza_duration']/60;
                                                    $cut_duration_in_minutes = substr($duration_in_minutes, 0, 4);
            		?>
            	</div>
                <div class="col-xs-3 col-sm-3 eval_greentext"><?php echo $users[0]['correct']; ?>  correct</div>
                <div class="col-xs-3 col-sm-3 eval_redtext"><?php echo $users[0]['incorrect']; ?> wrong</div>
                <div class="col-xs-3 col-sm-3 eval_lightgreytext"><?php echo $users[0]['count_not_tried']; ?> not tried</div>
            </div>
            <div class="col-xs-6 col-sm-6 sdfw">
            	<div class="col-xs-4 col-sm-4 eval_greentext"><?php echo round($users[0]['percentage'], 2); ?>% success</div>
                <div class="col-xs-4 col-sm-4"><?php echo $cut_duration_in_minutes; ?> min</div>
                <div class="col-xs-4 col-sm-4"><button class="btn btn-success mybutton pull-right" onclick="printreport()" >print</button></div>
            </div>
            </div>
        </div>
    </div>

<?php 
$cnt = 1;
foreach($users[0]['assessment_report'] as $report)
{ 
?>
	<?php 
	if($report['q_type'] == 1){
	?>
<div class="questionrow"> <!--start of question row-->
    <div class="row">

    	<div class="col-xs-12 col-sm-12">
    		<div class="col-xs-10 col-sm-10 spacer">
                <b>Question: <?php echo $cnt; ?></b> <span class="mainquestion">SINGLE CHOICE</span>
            </div>
	        <div class="col-xs-2 col-sm-2 spacer">
                <i class="redicon icon-cancel pull-right"></i>
            </div>
            <div class="col-xs-12 col-sm-12 spacer"><?php echo $report['q_question']; ?></div>
            	<div class="col-xs-12 col-sm-12 spacer">
            	<?php 
            		$chr  = 65; 
            		for($i =0; $i < count($report['qo_options_value']);$i = $i + 1) 
            		{
            	?>
                     <div class="col-xs-6 col-sm-6 spacer usdfw">
                        <div class="report-option-no col-xs-2 col-sm-1">
                            <?php echo chr($chr++).'. '; ?>
                        </div>
                        <div class="report-option col-xs-10 col-sm-11">
                         <?php echo $report['qo_options_value'][$i]['qo_options']; ?>
                        </div>
                    </div>
                <?php
            		}
                ?>
         		</div>
        </div>
    
    	</div>
        <div class="row darkborderrow">
    		<div class="col-xs-12 col-sm-12">
        		<div class="col-xs-4 col-sm-4 usnp"><span class="eval_blacktext">Correct Answers: 
        		<?php 
                    if(isset($report['correct_answer'])){
                    	echo $report['correct_answer'];
                    } 
                ?>
                </span></div>
                <div class="col-xs-4 col-sm-4 usnp"><span class="<?php echo ($report['correct'] == 1?'rightmarked':'wrongmarked'); ?>">Marked Answers: 
                <?php 
                    if(isset($report['user_answer'])){
                    	echo $report['user_answer'];
                    } 
                ?>
                </span></div>
                <div class="col-xs-4 col-sm-4 deskright usnp"><span class="<?php echo ($report['correct'] == 1?'positivemark':'negativemark'); ?>">Mark <?php echo ($report['correct'] == 1?'+':'-');echo $report['q_positive_mark']; ?></span></div>
            </div>
        </div>
</div>   
	<?php
	}
	else if($report['q_type'] == 2){
	?>
<div class="questionrow"> <!--start of question row-->
    <div class="row">
    	<div class="col-xs-12 col-sm-12">
    		<div class="col-xs-10 col-sm-10 spacer">
                <b>Question: <?php echo $cnt; ?></b> <span class="mainquestion">MULTIPLE CHOICE</span>
            </div>
	        <div class="col-xs-2 col-sm-2 spacer">
                <i class="greenicon icon-ok pull-right"></i>
            </div>
            <div class="col-xs-12 col-sm-12 spacer"><?php echo $report['q_question']; ?></div>
            	<div class="col-xs-12 col-sm-12 spacer">
                    <?php 
            		$chr  = 65; 
            		for($i =0; $i < count($report['qo_options_value']);$i = $i + 1) 
            		{
            		?>
                     <div class="col-xs-6 col-sm-6 spacer usdfw">
                        <div class="report-option-no col-xs-2 col-sm-1">
                            <?php echo chr($chr++).'. '; ?>
                        </div>
                        <div class="report-option col-xs-10 col-sm-11">
                         <?php echo $report['qo_options_value'][$i]['qo_options']; ?>
                        </div>
                    </div>
	                <?php
	            		}
	                ?>
         		</div>
            	          	
                
            </div>
    
    	</div>
        <div class="row darkborderrow">
    		<div class="col-xs-12 col-sm-12">
        		<div class="col-xs-4 col-sm-4 usnp"><span class="eval_blacktext">Correct Answers: <?php 
                    if(isset($report['correct_answer'])){
                    	echo $report['correct_answer'];
                    } 
                ?></span></div>
                <div class="col-xs-4 col-sm-4 usnp"><span class="<?php echo ($report['correct'] == 1?'rightmarked':'wrongmarked'); ?>">Marked Answers: <?php 
                    if(isset($report['user_answer'])){
                    	echo $report['user_answer'];
                    } 
                ?></span></div>
                <div class="col-xs-4 col-sm-4 deskright usnp"><span class="<?php echo ($report['correct'] == 1?'positivemark':'negativemark'); ?>">Mark <?php echo ($report['correct'] == 1?'+':'-');echo $report['q_positive_mark']; ?></span></div>
            </div>
        </div>
</div>   <!--end of question row-->
	<?php
	}
	else if($report['q_type'] == 3){
	?>
<div class="questionrow"> <!--start of explanatory question row-->
    <div class="row">
    	<div class="col-xs-12 col-sm-12">
    		<div class="col-xs-12 col-sm-12 spacer">
                <b>Question: <?php echo $cnt; ?></b> <span class="mainquestion">EXPLANATORY</span>
            </div>
            <div class="col-xs-12 col-sm-12 spacer"><?php echo $report['q_question']; ?></div>
            	<div class="col-xs-12 col-sm-12 spacer">
					<div class="t_whitebox"><?php echo $report['czr_answer']; ?></div>
         		</div>
            	          	
                
            </div>
    
    	</div>
        <div class="row darkborderrow">
    		<div class="col-xs-12 col-sm-12">
        		<div class="col-xs-4 col-sm-4 usnp"><span class="eval_blacktext"></span></div>
                <div class="col-xs-4 col-sm-4 usnp"><span class="rightmarked"></span></div>
                <div class="col-xs-4 col-sm-4 deskright usnp"><span class="positivemark">Mark <?php echo $report['czr_mark']; ?></span></div>
        </div>
    </div>
</div>   <!--end of explanatory question row-->
	<?php
	}
	?>
<?php
 $cnt++;
} 
 ?>
</div>
<script type="text/javascript" src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/js/challenge_report.js'; ?>" ></script>
<?php include 'footer.php'; ?>