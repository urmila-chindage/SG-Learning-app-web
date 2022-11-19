<?php include_once 'header.php'; ?>
<section class="courses-tab base-cont-top-nosidebar">
    <ol class="nav nav-tabs offa-tab">
        <!-- active tab start -->
        <li class="active">
            <a href="#!."> <?php echo lang('descriptive_test'); ?> Report</a>
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
            <div class="rTable content-nav-tbl" style="">
                <div class="rTableRow">
                    <div class="rTableCell dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="course_name"> <?php echo $current_course; ?> <span class="caret"></span></a>
                            <ul class="dropdown-menu white">
                                <?php foreach($courses as $course){ ?>
                                    <li><a href="#" onclick="select_course(<?php echo $course['id']; ?>, this)"><?php echo $course['cb_title']; ?></a></li>
                                <?php } ?>
                            </ul>
                    </div>
                    <div class="rTableCell dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="test_name"> <?php echo $current_lecture; ?> <span class="caret"></span></a>
                            <ul class="dropdown-menu white" id="test_list">
                            <?php foreach($tests as $test){ ?>
                                <li><a href="#" onclick="select_test(<?php echo $test['id']; ?>, this)"><?php echo $test['cl_lecture_name']; ?></a></li>
                            <?php } ?>
                            </ul>
                    </div>
                    <input type="hidden" id="hidden_lecture_id" value="<?php if($lecture_id){ echo $lecture_id;} ?>" />
                    <div class="rTableCell" >
                        <!-- lecture-control start -->
                        <a href="<?php echo admin_url().'coursebuilder/export_descriptive_test/'.$lecture_id; ?>" id="export_results" class="btn btn-green" >Export</a>
                        <!-- lecture-control end -->
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <div class="left-wrap col-sm-12 profile-wrap report-wrap">
    	<div class="container-fluid">
    		
    			<table class="rTable table-with-border width-100p" id="tblcontent">
                <?php foreach($users as $user){ ?>
                    <tr class="rTableRow">
                        <td class="rTableCell">
                            <a href="<?php echo admin_url().'user/profile/'.$user['dtua_user_id']; ?>">
                                <span class="icon-wrap-round img">
                                    <img src="<?php echo (($user['us_image'] == 'default.jpg')?default_user_path():  user_path()).$user['us_image']; ?>">
                                </span>

                                <span class="line-h36"><?php echo $user['us_name']; ?></span>
                            </a>
                        </td>
                        <td class="rTableCell">
                        	<?php
                        		$dt = new DateTime($user['updated_date']);
                        		echo strtoupper($dt->format('M d Y'));
                        	?>
                        </td>
                        <td class="rTableCell font-green">
                        	<?php
                        		if($user['mark'] == -1){
                        			echo 'Not evaluated yet';
                        		}
                        		else{
                        			echo $user['mark'].' Marks';
                        		}
                        	?>
                        </td>
                        <td class="rTableCell"><a href="<?php echo admin_url().'coursebuilder/evaluate_descriptive/'.$user['dtua_lecture_id'].'/'.$user['dtua_user_id'].'/'.$user['attempted_id']; ?>" class="btn btn-green" > EVALUATE</a></td>
                    </tr>
                    <?php } ?>
                </table>
    		
    	</div>
    </div>
</section>
<script type="text/javascript" src="<?php echo assets_url() ?>js/descriptive_evaluate.js"></script>
<?php include_once 'footer.php'; ?>