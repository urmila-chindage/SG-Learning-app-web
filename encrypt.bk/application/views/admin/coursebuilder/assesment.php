<link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>font/Chanakya/fonts.css">

<?php include_once 'lecture_header.php'; ?>

    <!-- Manin Iner container start -->
    <div class='course-bulder-content-inner'>
   
        <?php include_once 'assesment_left_bar.php'; ?>


    <div class="col-sm-6 builder-right-inner"> <!-- right side bar section -->

       <!-- top Header with drop down and action buttons -->
        <div class="buldr-header inner-buldr-header clearfix row">
                <div class="pull-left">
                    <!-- Header left items -->
                    <h3 class="right-top-header" id="questions_count_assessment"><?php echo sizeof($lecture['questions']); ?> <?php echo (sizeof($lecture['questions'])>1)?' Questions':' Question'; ?></h3>
                </div>
            <!-- !.Header left items -->

            <!-- !.Header right items -->
            <div class="pull-right rite-side">
            <!-- Questions buttons section, Add , Upload, import -->
            <a href="<?php echo admin_url('coursebuilder/question/0/'.$lecture_id); ?>" class="btn btn-blue" ><?php echo lang('add_question') ?></a>
            <a href="<?php echo admin_url('generate_test/import/'.$lecture_id); ?>" class="btn btn-blue" >IMPORT FROM QUESTION BANK</a>
            <button class="btn btn-green" data-toggle="modal" data-target="#addquestion"><?php echo lang('upload_question') ?></button>
            <!-- !.Questions buttons section, Add , Upload, import -->
            </div>
        </div>
        <!-- !.top Header with drop down and action buttons --> 
        
        <!-- Preivew of  test content will show here -->
        <div class="preivew-area test-content">

            <?php if($lecture['cl_status'] == 1) { ?>
                <a href="<?php echo admin_url('coursebuilder/report/'.$lecture['id']) ?>" class="link-style u-line view-reprt"><em><?php echo lang('view_report') ?></em></a>
            <?php } ?>
            <!-- test folder root or parent section begins here -->
            <?php if(!empty($lecture['questions'])): ?>
            <?php foreach ($lecture['questions'] as $question) :?>
            <div class="default-view-txt m0 mb10 test-folder" id="question_wrapper_<?php echo $question['id'] ?>">
                <a href="<?php echo admin_url('coursebuilder/question/'.$question['id'].'/'.$lecture['id']) ?>" title="<?php echo lang('edit') ?>" class="test-folder-row" data-toggle="tooltip" class="test-folder-delte" data-placement="top" data-original-title="<?php echo lang('edit') ?>">
                     <i class="icon icon-pencil"></i>
                 </a>
                <?php 
                    $question_stripped = strip_tags($question['q_question']);
                    echo (strlen($question_stripped) > 80)?(substr($question_stripped, 0, 77).'...'):$question_stripped;
                    ?>
                <a href="javascript:void(0)" title="<?php echo lang('delete') ?>" onclick="deleteQuestion('<?php echo $question['id'] ?>', '<?php echo $lecture['assesment']['assesment_id'] ?>')" data-toggle="tooltip" class="test-folder-delte" data-placement="top" data-original-title="<?php echo lang('delete') ?>">
                     <i class="icon icon-trash-empty"></i>
                </a>
             </div>
            <?php endforeach; ?>
            <?php endif; ?>
            <!-- !.test folder root or parent section begins here -->
        </div>
            
    </div><!-- right side bar section -->

</div>
    <script>
        var __lecture_id = '<?php echo $lecture_id ?>';
    </script>

<script type="text/javascript" src="<?php echo assets_url() ?>js/assesment.js"></script>
<?php include_once 'lecture_footer.php'; ?>