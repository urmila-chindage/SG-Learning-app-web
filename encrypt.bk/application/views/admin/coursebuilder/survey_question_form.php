<?php include_once 'lecture_header.php'; 
//$_SESSION['positive'] = $_POST['q_positive_mark'];
//$_SESSION['negative'] = $_POST['q_negative_mark'];

?>
        <link rel="stylesheet" href="<?php echo assets_url() ?>css/custom.css" />
    <!-- Manin Iner container start -->
    <div class='bulder-content-inner'>
   
        <?php include_once 'survey_left_bar.php'; ?>


    <div class="col-sm-6 builder-right-inner"> <!-- right side bar section -->

       <!-- top Header with drop down and action buttons -->
            <div class="buldr-header inner-buldr-header clearfix row">
                    <div class="pull-left">
                        <!-- Header left items -->
                        <h3 class="right-top-header"><?php echo ($q_question == "")?'New Question':'Update Question' ?></h3>
                    </div>
                <!-- !.Header left items -->

                <!-- !.Header right items -->
                <div class="pull-right rite-side">

                    <div class="pull-left" style="visibility: hidden;">
                        <!-- Header left items -->
                        <h3 class="right-top-header">30 Questions</h3>
                    </div>
                </div>
            </div>
            <!-- !.top Header with drop down and action buttons --> 
            
            <!-- Preivew of  test content will show here -->
            <div class="preivew-area test-content">

                <div class="form-horizontal">

                    <form class="form-horizontal" id="question_form" method="POST" action="<?php echo admin_url('coursebuilder/survey_question/'.$id.'/'.$lecture['id']) ?>">
                        <!-- Text Box  -->
                        <div class="form-group">
                            
                            <div class="col-sm-4">
                                <div class="">
                                    <div class=" no-style">
                                        <?php echo lang('question_type') ?>:
                                    </div>
                                </div>
                                <?php 
                                $selected = 'selected="selected"';
                                $question_type  = array( '1' => 'Radio', '2' => 'Checkbox', '3' => 'Text', '4' => 'Range', '5' => 'Dropdown');
                                
                                ?>
                                <select class="form-control" id="question_type" name="q_type">
                                     <?php foreach ($question_type as $key => $value):?>
                                        <option <?php echo ($q_type==$key)?$selected:''; ?> value="<?php echo $key ?>"><?php echo $value ?></option>
                                     <?php endforeach; ?>
                                </select>
                            </div>
                        </div>    

                        <div class="form-group">
                            <div class="col-sm-4">
                                <label>Required</label>
                                <?php
                                if($q_required == "1"):
                                    ?>
                                    <input type="checkbox" name="is_required" value="1" checked="checked">
                                    <?php
                                else:
                                    ?>
                                    <input type="checkbox" name="is_required" value="1">
                                    <?php
                                endif;
                                ?>
                            </div>
                        </div>                    

                        <!-- Collapsing Section goes here --><!-- START -->
                        <!-- ################################ -->
                        <!-- <div class="panel-group test-accord" id="accordion" role="tablist" aria-multiselectable="true">
                            

                        </div> -->

                        <!-- Text Box Addons  -->
                        <div class="form-group">
                            <div class="col-sm-12">
                                Question* : 
                                <textarea id="question" name="q_question" placeholder="Overall rating for the course" class="form-control" rows="4" ><?php echo $q_question ?></textarea>
                            </div>
                        </div>
                        
                        <!-- Select Box  -->
                        
                        <div class="form-group option-wrap" id="question_option_wrapper">
                            <?php if(!empty($q_options)): ?>
                            <?php $count = 1; $checked = 'checked="checked"'; $single_choice = 1; ?>
                            <?php foreach ($q_options as $option):?>
                            <div class="col-sm-6 option-element" id="option_wrapper_<?php echo $count ?>" data-id="<?php echo $count ?>" >
                                <span class="order-option"><?php echo lang('option').' '.$count; ?> :</span>
                                <div class="input-group full-width">
                                    <textarea name="option[<?php echo $count++?>]" class="form-control question-text-input" rows="1" ><?php echo $option ?></textarea>
                                    <!-- <span class="remove-cross remove-existing-option"> X </span> -->
                                    <span aria-hidden="true" class="remove-cross remove-existing-option icon icon-cancel-1"></span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            
                            <div class="col-sm-6 click-wrap" id="add_option_button" <?php echo ($q_type==3 || $q_type == 4)?'style="display:none;"':''; ?>>
                                <div class="input-group pad-top25">
                                    <a href="javascript:void(0)" class="my-italic u-line link-style" id="add-more-option"><?php echo lang('add_more_option') ?></a>
                                </div>
                            </div>
                        </div>

                        <div class="form-group range-input" style="<?php echo ($q_type == 4)? '':'display: none;' ?>">
                            <div class="col-sm-6">
                                <label>Lower limit:</label>
                                <select class="form-control" name="low_range" id="low_range">
                                <?php
                                $selected = 'selected="selected"';
                                $i = 1;
                                while($i<=10){
                                    ?>
                                    <option value="<?php echo $i ?>" <?php echo ($q_low_limit==$i)?$selected:''; ?>><?php echo $i ?></option>
                                    <?php
                                    $i++;
                                }
                                ?>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label>Lower limit value:</label>
                                <input class="form-control" type="text" name="low_range_label" value="<?php echo $q_low_limit_label ?>">
                            </div>
                            <div class="col-sm-6">
                                <label>Higher limit:</label>
                                <select class="form-control" name="high_range" id="high_range">
                                <?php
                                $i = 1;
                                while($i<=10){
                                    ?>
                                    <option value="<?php echo $i ?>" <?php echo ($q_high_limit==$i)?$selected:''; ?>><?php echo $i ?></option>
                                    <?php
                                    $i++;
                                }
                                ?>
                                </select>
                            </div>
                            <div class="col-sm-6">
                            <label>Higher limit value:</label>
                                <input class="form-control" type="text" name="high_range_label" value="<?php echo $q_high_limit_label ?>">
                            </div>
                                
                        </div>

                        

                        <!-- ################################ -->
                        <!-- Collapsing Section goes here --><!-- END -->

                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="pull-right">
                                    <input type="hidden" name="removed_options" id="removed_options" value="">
                                    <input type="hidden" name="survey_id" value="<?php echo $lecture['survey']['survey_id'] ?>">
                                    <button type="button" class="btn btn-green" onclick="saveSurveyQuestion();"><?php echo strtoupper(lang(($id)?'update':'add')) ?></button>
                                    <a href="<?php echo admin_url('coursebuilder/lecture/'.$lecture_id) ?>" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></a>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
                

    </div><!-- right side bar section -->

</div>
        <!-- JS -->
<script src="<?php echo assets_url() ?>js/jquery.form-validator.min.js" type="text/javascript"></script>
<script>
$(document).ready(function(e) {
});
    $.validate({
        modules : 'location, date, security',
        onModulesLoaded : function() {
        },
        errorMessagePosition : 'top' ,
        validateOnBlur : false
    });

  // Restrict presentation length

</script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/survey.js"></script>
<?php include_once 'lecture_footer.php'; ?>