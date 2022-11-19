<?php include 'header.php'; ?>
<div class="container">
	<div class="row">

	<div class="col-xs-12 col-sm-12 col-md-12"> 
         
			<div class="panel panel-default whitefullpanel assessment_report">
                <div class="panel-body greytextsmall spacer_btline">
                	<?php
                        if(!empty($users)){
                	$cnt = 1;
                	foreach($users as $user){ 
                	?>
                		
                		<div class="row">
                        	<div class="col-xs-12 col-sm-4 col-md-4 nopad">
                            	<div class="col-xs-1 col-sm-1 col-md-1 nopad ranktext"><?php echo $cnt; ?></div>
                            	
                            	<div class="col-xs-3 col-sm-3 col-md-3 nopad"><img src="<?php echo (($user['us_image'] == 'default.jpg')?default_course_path():course_path()).$user['us_image']; ?>" class="img-circle reportimage"></div>
                            	
                            	<div class="col-xs-8 col-sm-8 col-md-8 nopad tabtext"><?php echo $user['us_name']; ?></div>
                            	
                            </div>
                            <div class="col-xs-12 col-sm-8 col-md-8 nopad">
                                <div class="col-xs-4 col-sm-2 col-md-2 datetxt">
                                <?php 
                                $dt = new DateTime($user['cza_attempted_date']);
                        		echo strtoupper($dt->format('M d Y'));
                                ?>
                                </div>
                                <?php $duration_in_minutes     = $user['cza_duration']/60;
                                      $cut_duration_in_minutes = substr($duration_in_minutes, 0, 4); ?>
                                <?php /* ?><div class="col-xs-4 col-sm-2 col-md-2 greentext"><?php echo $user['correct']; ?> Correct <i class="icon-ok hidden-xs"></i></div>
                                <div class="col-xs-4 col-sm-2 col-md-2 redtext"><?php echo $user['incorrect']; ?> Wrong <i class="icon-cancel hidden-xs"></i></div>
                                <div class="col-xs-4 col-sm-2 col-md-2 lightgreytext"><?php echo $user['count_not_tried']; ?> Not tried <i class="icon-attention-alt hidden-xs"></i></div><?php */ ?>
                                <div class="col-xs-4 col-sm-2 col-md-2 bluetext"><?php echo $user['percentage']; ?>% Success</div>
                                <div class="col-xs-4 col-sm-2 col-md-2 orangetext"><?php echo $cut_duration_in_minutes; ?> mins <i class="icon-clock hidden-xs"></i></div>
                            </div>
                		</div>
                		
                	<?php 
                	$cnt++;
                        } }else{
                	?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="course_description">
                                <h2>SORRY!!</h2>
                                No users have participated in this challenge.
                            </div>
                        </div>
                        
                    </div>
                        <?php } ?>
                </div>
            </div>
		</div>
	</div>
</div>
<?php include 'footer.php'; ?>