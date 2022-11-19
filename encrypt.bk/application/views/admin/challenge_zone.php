<?php include_once 'challenge_zone_header.php'; ?>
<link rel="stylesheet" href="<?php echo assets_url() ?>css/datepicker.min.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>css/timepicker.css">


    <!-- Manin Iner container start -->
    <div class='bulder-content-inner'>
   
        <?php include_once 'challenge_left_bar.php'; ?>


    <div class="col-sm-6 builder-right-inner"> <!-- right side bar section -->

       <!-- top Header with drop down and action buttons -->
        <div class="buldr-header inner-buldr-header clearfix row">
                <div class="pull-left">
                    <!-- Header left items -->
                    <h3 class="right-top-header"><?php echo sizeof($questions);  ?> Questions</h3>
                </div>
            <!-- !.Header left items -->

            <!-- !.Header right items -->
            <div class="pull-right rite-side">
            <!-- Questions buttons section, Add , Upload, import -->
            <a href="<?php echo admin_url('challenge_zone/question/0/'.$id); ?>" class="btn btn-blue" ><?php echo lang('add_question') ?></a>
            <button class="btn btn-green" data-toggle="modal" data-target="#addquestion"><?php echo lang('upload_question') ?></button>
            
                
             <!-- !.Questions buttons section, Add , Upload, import -->

            </div>
        </div>
        <!-- !.top Header with drop down and action buttons --> 
        
        <!-- Preivew of  test content will show here -->
        <div class="preivew-area test-content">

            <a href="<?php echo admin_url('challenge_zone/report/'.$id) ?>" class="link-style u-line view-reprt"><em><?php echo lang('view_report') ?></em></a>
            <!-- test folder root or parent section begins here -->
            <?php if(!empty($questions)): ?>
            <?php foreach ($questions as $question) :?>
            <div class="default-view-txt m0 mb10 test-folder" id="question_wrapper_<?php echo $question['id'] ?>">
                <a href="<?php echo admin_url('challenge_zone/question/'.$question['id'].'/'.$id) ?>" title="<?php echo lang('edit') ?>" class="test-folder-row" data-toggle="tooltip" class="test-folder-delte" data-placement="top" data-original-title="<?php echo lang('edit') ?>">
                     <i class="icon icon-pencil"></i>
                 </a>
                <?php 
                    $question_stripped = strip_tags($question['q_question']);
                    echo (strlen($question_stripped) > 80)?(substr($question_stripped, 0, 77).'...'):$question_stripped;
                    ?>
                <a href="javascript:void(0)" title="<?php echo lang('delete') ?>" onclick="deleteQuestion('<?php echo $question['id'] ?>', '<?php echo $id ?>')" data-toggle="tooltip" class="test-folder-delte" data-placement="top" data-original-title="<?php echo lang('delete') ?>">
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
        var __challenge_id = '<?php echo $id ?>';
    </script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.en.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.timepicker.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/challenge_zone.js"></script>
    <script>
        var admin_url = '<?php echo admin_url() ?>';
        $(document).ready(function(){
            $('#challenge_zone_category').change(function(){
                __canSaveChallenge = true;
                var catid = $("#challenge_zone_category").val();
                if(catid != '')
                {
                    $.ajax({
                        url: admin_url+'challenge_zone/check_start_date',
                        type: "POST",
                        data: {'startdate': $("#challenge_start_date").val(), catid: catid, challenge_id: __challenge_id},
                        success: function(response){
                            var data = $.parseJSON(response);
                            if(data['msg'] != ''){
                                $('#popUpMessage').html(data['msg']);
                            }else{
                                $('#popUpMessage').remove();
                            }
                            if(data['stat'] == 0){
                                __canSaveChallenge = false;
                                cleanPopUpMessage();
                                errorMessage = data['msg'];
                                str          = renderPopUpMessage('error', errorMessage);
                                $('.challenge_zone_form').prepend(str);
                                scrollToTopOfPage();
                            }
                        }
                    });
                }
            });
        });
    </script>
<?php include_once 'challenge_zone_footer.php'; ?>