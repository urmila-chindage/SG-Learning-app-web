<?php include_once 'header.php'; 
//$_SESSION['positive'] = $_POST['q_positive_mark'];
//$_SESSION['negative'] = $_POST['q_negative_mark'];
?>

  <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/redactor.css" />
<link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/alignment.css" />

<?php //include_once "cms_tab.php"; ?>
        <section class="courses-tab base-cont-top"> 
            <ol class="nav nav-tabs offa-tab">
                <li class="active">
                    <a href="javascript:void(0)"> Add Question</a>
                    <span class="active-arrow" style="background: rgb(255, 255, 255) none repeat scroll 0% 0%;"></span>
                </li>
            </ol>
        </section>
        
        <!-- SIDEBAR RIGHT SIDE FIXED --> <!-- START -->
<div class="right-wrap base-cont-top container-fluid pull-right">

    <a href="<?php echo admin_url('generate_test/question/0') ?>" class="btn btn-blue selected full-width-btn">
       
        <?php echo lang('add_question') ?>
    </a>
    
    <a href="javascript:void(0)" class="btn btn-green selected full-width-btn" data-toggle="modal" data-target="#addquestion">
        
        <?php echo lang('upload_question') ?>
    </a>


    <!--  Adding list group  --> <!-- END  -->
</div>
<!-- SIDEBAR RIGHT SIDE FIXED --> <!-- END -->



<section class="content-wrap zero-level-top">

    <!-- Nav section inside this wrap  --> <!-- START -->
    <!-- =========================== -->

    
        
        
    <!-- Manin Iner container start -->
    <div class='bulder-content-inner'>


    <div class="col-sm-12 builder-right-inner"> <!-- right side bar section -->

       <!-- top Header with drop down and action buttons -->
            
            <!-- !.top Header with drop down and action buttons --> 
            
            <!-- Preivew of  test content will show here -->
            <div class="preivew-area margin-top padding-top test-content">

                

                <div class="form-horizontal">

                    <form class="form-horizontal" id="question_form" method="POST" action="<?php echo admin_url('generate_test/question/'.$id) ?>">
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
                                $question_type  = array( '1' => lang('single_choice'), '2' => lang('multiple_choice'), '3' => 'Subjective');
                                $difficulty     = array( '1' => lang('easy'), '2' => lang('medium'), '3' => lang('hard'));
                                ?>
                                <select class="form-control" id="question_type" name="q_type">
                                     <?php foreach ($question_type as $key => $value):?>
                                        <option <?php echo ($q_type==$key)?$selected:''; ?> value="<?php echo $key ?>"><?php echo $value ?></option>
                                     <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <div class="">
                                    <div class=" no-style">
                                        <?php echo lang('difficulty') ?>:
                                    </div>
                                </div>
                                <select class="form-control" id="difficulty" name="q_difficulty">
                                     <?php foreach ($difficulty as $key => $value):?>
                                        <option <?php echo ($q_difficulty==$key)?$selected:''; ?> value="<?php echo $key ?>"><?php echo $value ?></option>
                                     <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-sm-4">
                                <div class="">
                                    <div class=" no-style">
                                        <?php echo lang('parent_category') ?>:
                                    </div>
                                </div>
                                <select class="form-control" id="q_parent_category" name="q_parent_category"> 
                                     <?php foreach ($q_parent_categories as $category):?>
                                        <option <?php echo ($category['id']==(isset($q_parent_category)?$q_parent_category:''))?$selected:''; ?> value="<?php echo $category['id'] ?>"><?php echo $category['ct_name'] ?></option>
                                     <?php endforeach; ?>
                                </select>
                            </div>
                            
                            
                        </div>
                        <div class="form-group">
<!--                            <div class="col-sm-4">
                                <div class="">
                                    <div class=" no-style">
                                        <?php //echo "Sub Topic" ?>: 
                                    </div>
                                </div>
                                <input type="text" class="form-control" name="q_sub_category" id="q_sub_category" autocomplete="off" value="" />
                                <ul class="auto-search-lister" id="listing_question_sub_category" style="display: none;">
                                </ul>
                            </div>-->
                            <div class="col-sm-4">
                                <div class="">
                                    <div class=" no-style">
                                        <?php echo lang('topic') ?>:
                                    </div>
                                </div>
                                <input type="text" class="form-control" name="q_category" id="q_category" autocomplete="off" value="<?php echo isset($q_category)?$q_category:'' ;?>" />
                                <ul class="auto-search-lister" id="listing_question_category" style="display: none;">
                                </ul>
                            </div>
                            <div class="col-sm-4">
                                <div class="">
                                    <div class=" no-style">
                                        <?php echo lang('positive_mark') ?>
                                    </div>
                                </div>
                                <input type="text" class="form-control" name="q_positive_mark" id="positive_mark" autocomplete="off" value="<?php echo isset($q_positive_mark)?$q_positive_mark:'' ;?>" />
                                
                                
                            </div>
                            <div class="col-sm-4">
                                <div class="">
                                    <div class="no-style">
                                        <?php echo lang('negative_mark') ?>
                                    </div>
                                </div>
                                <input type="text" class="form-control" name="q_negative_mark" id="negative_mark" autocomplete="off" value="<?php echo isset($q_negative_mark)?$q_negative_mark:'' ;?>" />
                                
                            </div>
                        </div>

                        <!-- Collapsing Section goes here --><!-- START -->
                        <!-- ################################ -->
                        <div class="panel-group test-accord" id="accordion" role="tablist" aria-multiselectable="true">
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingOne">
                                  <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        <span class="plus-ico">+</span> <span class="ico-line"><?php echo lang('directions') ?>:</span> 
                                    </a>
                                  </h4>
                                </div>
                                <div id="collapseOne" class="panel-collapse collapse <?php echo ($q_directions)?'in':''; ?>" role="tabpanel" aria-labelledby="headingOne">
                                  <div class="panel-body">
                                    <!-- Text Area -->
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <textarea id="directions" name="q_directions" class="form-control" rows="4" ><?php echo $q_directions ?></textarea>
                                        </div>
                                    </div> 

                                  </div>
                                </div>
                              </div>
                              

                        </div>

                        <!-- Text Box Addons  -->
                        <div class="form-group">
                            <div class="col-sm-12">
                                <?php echo lang('questions') ?> * : 
                                <textarea id="question" name="q_question" class="form-control" rows="4" ><?php echo $q_question ?></textarea>
                            </div>
                        </div>
                        
                        <!-- Select Box  -->
                        
                        <div class="form-group option-wrap" id="question_option_wrapper">
                            <?php if(!empty($q_options)): ?>
                            <?php $count = 1; $checked = 'checked="checked"'; $single_choice = 1; ?>
                            <?php foreach ($q_options as $option):?>
                            <div class="col-sm-12 option-element" id="option_wrapper_<?php echo $option['id'] ?>" data-id="<?php echo $option['id'] ?>" >
                                <span class="order-option"><?php echo lang('option').' '.$count++; ?> :</span>
                                <div class="input-group">
                                    <?php 
                                    $input_type = 'checkbox';
                                    $answer     = '['.$option['id'].']';                                     
                                    if($q_type == $single_choice)
                                    {
                                        $input_type = 'radio';
                                        $answer     = '';
                                    }
                                    ?>
                                    <span class="input-group-addon option-type" >
                                        <input class="question-option-input" type="<?php echo $input_type ?>" <?php echo (in_array($option['id'], $q_answer))?$checked:'';  ?> value="<?php echo $option['id'] ?>" name="answer<?php echo $answer ?>" >
                                    </span>
                                    <textarea name="option[<?php echo $option['id'] ?>]" class="form-control question-text-input" rows="4" ><?php echo $option['qo_options'] ?></textarea>
                                    <span class="remove-cross remove-existing-option"> X </span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            
                            <div class="col-sm-6 click-wrap" id="add_option_button" <?php echo ($q_type==3)?'style="display:none;"':''; ?>>
                                <div class="input-group pad-top25">
                                    <a href="javascript:void(0)" class="my-italic u-line link-style" id="add-more-option"><?php echo lang('add_more_option') ?></a>
                                </div>
                            </div>


                        </div>


                        <div class="panel-group test-accord" id="accordion1" role="tablist" aria-multiselectable="true">
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingTwo">
                                  <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion1" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        <span class="plus-ico">+</span> <span class="ico-line"><?php echo lang('explanation') ?></span>
                                    </a>
                                  </h4>
                                </div>
                                <div id="collapseTwo" class="panel-collapse collapse <?php  echo ($q_explanation)?'in':''; ?>" role="tabpanel" aria-labelledby="headingTwo">
                                    <div class="panel-body">
                                        
                                        <!-- Text Area -->
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <textarea id="explanation" name="q_explanation" class="form-control" rows="4" ><?php echo $q_explanation ?></textarea>
                                            </div>
                                        </div>
                            
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ################################ -->
                        <!-- Collapsing Section goes here --><!-- END -->

                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="pull-right">
                                    <input type="hidden" name="removed_options" id="removed_options" value="">
                                    <button type="button" class="btn btn-green" onclick="saveQuestion();"><?php echo strtoupper(lang(($id)?'update':'add')) ?></button>
                                    <a href="<?php echo admin_url('generate_test') ?>" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></a>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
                

    </div><!-- right side bar section -->

</div>
</section>
        <!-- JS -->
        <script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/source.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
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
<script>
    $(document).ready(function(e) {
    $('#redactor, #explanation, #question, .question-text-input, #directions, #lecture_instruction').redactor({
        minHeight: 250,
        maxHeight: 250,
        imageUpload: admin_url+'configuration/redactore_image_upload',
        plugins: ['table', 'alignment', 'source'],
        callbacks: {
            imageUploadError: function(json, xhr)
            {
                 var erorFileMsg = "This file type is not allowed. upload a valid image.";
                 $('#assesment_form').prepend(renderPopUpMessage('error', erorFileMsg));
                 scrollToTopOfPage();
                 return false;
            }
        }  
    });
});
</script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/generate_test.js"></script>
<?php include_once 'footer.php'; ?>