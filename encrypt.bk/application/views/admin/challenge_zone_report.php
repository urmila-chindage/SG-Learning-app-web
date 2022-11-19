<?php include_once 'header.php'; ?>

<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.en.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.timepicker.js"></script>

<?php //echo "<pre>";print_r($current_challenges);die; ?>
<section class="courses-tab base-cont-top-nosidebar">
    <ol class="nav nav-tabs offa-tab">
        <!-- active tab start -->
        <li class="active">
            <a href="#!."> <?php echo lang('challenge_zones'); ?> Report</a>
            <span class="active-arrow"></span>
        </li>
        <!-- active tab end -->
        <!-- <li >
            <a href="#!.">Game Report</a>
            <span class="active-arrow"></span>
        </li> -->
    </ol>
</section>
<section class="content-wrap base-cont-top-nosidebar ">
	<div class="container-fluid nav-content nav-cntnt100">
		<div class="row">
			<div class="rTable content-nav-tbl">
				<div class="rTableRow">
					<div class="rTableCell challenge-zone-drop dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="course_name"> <?php if(isset($current_category['ct_name'])){echo $current_category['ct_name'];} ?> <span class="caret"></span></a>
                            <ul class="dropdown-menu white">
                                <?php foreach($categories as $category){ ?>
                                    <li><a href="#" onclick="select_course(<?php echo $category['id']; ?>, this)"><?php echo $category['ct_name']; ?></a></li>
                                <?php } ?>
                            </ul>
					</div>
					<div class="rTableCell challenge-zone-drop dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="test_name"> 
                            <?php 

                            if(isset($current_challenge['cz_title']))
                            {
                                echo $current_challenge['cz_title'];
                            } 
                            ?>
                                 <span class="caret"></span></a>
                            <ul class="dropdown-menu white" id="test_list">
                            <?php foreach($current_challenges as $challenge){ ?>
                                <li><a href="#" <?php echo ($current_challenge['id'] == $challenge['id'])?'id="current_challenge_id_select"':'' ?> onclick="select_test(<?php echo $challenge['id']; ?>, this)"><?php echo $challenge['cz_title']; ?></a></li>
                            <?php } ?>
                            </ul>
                    </div>
                    <div class="rTableCell" >
                        <!-- lecture-control start -->
                        <a href="<?php echo admin_url().'challenge_zone/export_challange_report/'.$challenge_id; ?>" class="btn btn-green" >Export</a>
                        <!-- lecture-control end -->
                    </div>
				</div>
			</div>
		</div>
	</div>
    <?php if(isset($current_challenge['id'])): ?>
        <script type="text/javascript">
            $(document).ready(function(){
                $('#current_challenge_id_select').trigger('click');
            });
        </script>
    <?php endif; ?>
	<div class="left-wrap col-sm-12 profile-wrap report-wrap">

    	<div class="container-fluid">
    	<div class="col-sm-12 marg-top10">
    		<table class="rTable table-with-border width-100p" id="tblcontent">
            <?php if(isset($users) && !empty($users)): ?>
    			<?php foreach($users as $user){ ?>

                    <tr class="rTableRow">
                        <td class="rTableCell">
                            <a target="_blank"  href="<?php echo admin_url().'challenge_zone/evaluate_challenge/'.$challenge_id.'/'.$user['uid']; ?>">
                                <span class="icon-wrap-round img">
                                    <img src="<?php echo (($user['us_image'] == 'default.jpg')?default_user_path():  user_path()).$user['us_image']; ?>">
                                </span>
                                <span class="line-h36"><?php echo $user['us_name']; ?></span>
                            </a>
                        </td>
                        <td class="rTableCell">
                        	<?php
                        		$dt = new DateTime($user['cza_attempted_date']);
                        		echo strtoupper($dt->format('M d Y'));
                        	?>
                        </td>
                        <?php if($user['q_type'] == 3){ ?>
                        	<td class="rTableCell">
                        		Explanatory question found, Need Manual evaluation
                        	</td>
                        	<td class="rTableCell"><a target="_blank"  href="<?php echo admin_url().'challenge_zone/evaluate_challenge/'.$challenge_id.'/'.$user['uid']; ?>" class="btn btn-green" > EVALUATE</a></td>
                        <?php }else{ ?>
                                <?php $duration_in_minutes     = $user['cza_duration']/60;
                                                    $cut_duration_in_minutes = substr($duration_in_minutes, 0, 4); ?>
                        	<td class="rTableCell font-green"><?php echo $user['correct']; ?> Correct</td>
                            <td class="rTableCell font-red"><?php echo $user['incorrect']; ?> Wrong</td>
                            <td class="rTableCell font-lgt-grey"><?php echo $user['count_not_tried']; ?> Not Tried</td>
                            <td class="rTableCell font-green"><?php echo $user['percentage']; ?>% success</td>
                            <td class="rTableCell"><?php echo $cut_duration_in_minutes; ?> min</td>
                        <?php } ?>
                        
                    </tr>
                    <?php } ?>
                <?php endif; ?>
    		</table>
    	</div>
    	</div>
    </div>
</section>
<script type="text/javascript" src="<?php echo assets_url() ?>js/challenge_zone_report.js"></script>
<?php include_once 'footer.php'; ?>