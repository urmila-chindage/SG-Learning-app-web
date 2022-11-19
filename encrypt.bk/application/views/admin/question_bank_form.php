<?php
// die;
function toAlpha($number)
{
    $alphabet = range('A','Z');
    $number--;
    $count = count($alphabet);
    if($number <= $count)
        return $alphabet[$number-1];
    while($number > 0){
        $modulo     = ($number - 1) % $count;
        $alpha      = $alphabet[$modulo].$alpha;
        $number     = floor((($number - $modulo) / $count));
    }
    return $alpha;
}

function get_video_content($data)
{
    $content = '';
    $content = str_replace("<","&lt;",$data);
    $content = str_replace(">","&gt;",$data);
    if (strpos($content, '<iframe') !== true)
    {
        $iframe         = get_string_between_tags($content,'iframe','</iframe');
        $video_id       = get_string_between_tags($content,'src="https://www.youtube.com/embed/','" frameborder');
        $video_url      = 'https://www.youtube.com/watch?v='.$video_id;           
        $video_string   = '[youtube]'.$video_url.'[/youtube]';
        $iframe         = str_replace("&lt;","<",$iframe);
        $iframe         = str_replace("&gt;",">",$iframe);
        $iframe_content = '<iframe'.$iframe.'</iframe>';
        $content        = str_replace($iframe_content,$video_string,$data);
    }
    return $content;
}

function get_string_between_tags($string, $start, $end)
{
    $string     = ' ' . $string;
    $ini        = strpos($string, $start);
    if ($ini == 0) return '';
    $ini       += strlen($start);
    $len        = strpos($string, $end, $ini) - $ini;
    $return     = substr($string, $ini, $len);
    return $return;
}
?>
<!DOCTYPE html>
<html>
<!-- head start-->

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
        <title><?php echo isset($title) ? $title : config_item('site_name'); ?></title>
        <link rel="icon" href="<?php echo base_url('favicon.png') ?>">
        <!-- ADDING REDACTOR PLUGIN INSIDE -->
        <!-- ############################# --> <!-- START -->
        <?php /* ?><link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/redactor.css" />
        <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/alignment.css" /><?php */ ?>
        <!-- ############################# --> <!-- END -->
        <!-- ADDING REDACTOR PLUGIN INSIDE -->    
        <!-- Customized bootstrap css library -->
        <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/bootstrap.css">
        <script src="<?php echo assets_url() ?>js/flowchart/go.js"></script>
        <script src="<?php echo assets_url() ?>js/flowchart/flowchart_init.js"></script>
    </head>
    <!-- head end-->
    <!-- body start-->
    <body >
        <?php 
        $df_values = $this->session->userdata('default_field_values');
        $df_values = ($df_values)?$df_values:array();
        //echo '<pre>'; print_r($df_values); die('----');
        ?>
        <!-- Manin Iner container start -->
        <div class='bulder-content-inner add-question-block'>
            <div class="col-sm-12 bottom-line question-head">
                <h3 class="question-title">add question

                </h3>
      
                <?php 
                $history_url = admin_url('generate_test');
                if(isset($assessment['a_lecture_id']))
                {
                    $history_url = admin_url('coursebuilder/lecture/'.$assessment['a_lecture_id']);
                }
                if($from_test_manager)
                {
                    $history_url = $from_test_manager;
                }
                ?>
                <span class="cb-close-qstn"><i class="icon icon-cancel-1" onclick="location.href='<?php echo $history_url ?>'"></i></span>
            </div>
            <div class="col-sm-12 question-block">
                <form action="<?php echo admin_url('generate_test/question/'.$id.'/'.$assessment_id) ?>" method="POST" id="question_form">
                <input type="hidden" name="removed_options" id="removed_options" value="">
                <input type="hidden" name="redirect" id="redirect_type" value="">
                <input type="hidden" name="from_test_manager" value="<?php echo $from_test_manager ?>">
               <?php
               //print_r($df_values);
               //die();
               ?>
                <div class="question-leftbox">
                    <div class="row">
                        <?php if($id): ?>
                        <div class="col-sm-12">
                            <p>Question Id: <strong>#<?php echo $q_code ?></strong></p>
                        </div>
                        <?php endif; ?>
                        <div class="col-sm-12">
                            Question Type
                            <div class="form-group">
                                <select class="form-control" id="q_type" name="q_type">
                                <?php foreach ($question_types as $qus_type => $qus_value): ?>
                                    <option <?php echo (($q_type == $qus_value)?$selected:((!$id&&isset($df_values['q_type'])&&$df_values['q_type']==$qus_value)?$selected:'')) ?> value="<?php echo $qus_value ?>"><?php echo $qus_type ?></option>
                                <?php endforeach; ?>
                                </select>
                            </div>
                        </div> 
                        <div class="col-sm-12">
                            Difficulty
                            <div class="form-group">
                                <select class="form-control" id="q_difficulty" name="q_difficulty">
                                <?php foreach ($difficulty as $d_type => $d_value): ?>
                                    <option <?php echo (($q_difficulty == $d_value)?$selected:((!$id&&isset($df_values['q_difficulty'])&&$df_values['q_difficulty']==$d_value)?$selected:'')) ?> value="<?php echo $d_value ?>"><?php echo $d_type ?></option>
                                <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 dropdown-lister-wrapper">
                            Category

                            <div class="form-group">
                                    <select class="form-control" name="q_category" id="q_category" onchange="generateSubjectList()">
                                <option value="0">Choose Course Category</option>
                                <?php 
                                if(!empty($df_values) && !$id){
                                    $q_category = ($df_values['q_category']!='')?$df_values['q_category']:0;
                                }
                             
                                foreach($course_categories as $course_category): ?>
                                    <option value="<?php echo $course_category['id'] ?>" <?php echo ($q_category==$course_category['id'])? "selected" : ""; ?>><?php echo $course_category['ct_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                                <!--input autocomplete="off" type="text" class="form-control filter-field" placeholder="Electronics & Communication" id="q_category" name="q_category" value="<?php //echo ($q_category)?$q_category:((!$id&&isset($df_values['q_category']))?$df_values['q_category']:'') ?>"-->
                            </div>
                            <!--ul class="auto-search-lister" id="listing_question_category" style="display: none;">
                            </ul-->
                        </div> 
                        <div class="col-sm-12 dropdown-lister-wrapper">
                            Subject
                            <div class="form-group">
                                <select class="form-control" id="q_subject" name="q_subject" onchange="generateTopicList()" >
                                    <option value="0">Choose Subject</option>
                        <?php 
                            if((!empty($df_values)) && (isset($df_values['question_subjects'])) && !$id){
                                $question_subjects = $df_values['question_subjects'];
                                $q_subject = ($df_values['q_subject']!='')?$df_values['q_subject']:0;
                            } 
                            foreach($question_subjects as $question_subject): 
                            ?>
                                <option value="<?php echo $question_subject['id'] ?>" <?php echo ($q_subject==$question_subject['id'])? "selected" : ""; ?>> <?php echo $question_subject['qs_subject_name'] ?></option>
                            <?php endforeach; 
                            
                            
                        ?>
                                </select>
                                <!--input autocomplete="off" type="text" class="form-control filter-field" placeholder="Digital Electronics" id="q_subject" name="q_subject" value="<?php //echo ($q_subject)?$q_subject:((!$id&&isset($df_values['q_subject']))?$df_values['q_subject']:'') ?>"-->
                            </div>
                            <!--ul class="auto-search-lister" id="listing_question_subject" style="display: none;">
                            </ul-->
                        </div>                    
                        <div class="col-sm-12 dropdown-lister-wrapper">
                            Topic
                            <div class="form-group">
                                <select class="form-control" id="q_topic" name="q_topic" >
                                    <option value="0">Choose Topic</option>
                                    <?php 
                                     if((!empty($df_values)) && (isset($df_values['question_topics'])) && !$id){
                                        $question_topics = $df_values['question_topics'];
                                        $q_topic = ($df_values['q_topic']!='')?$df_values['q_topic']:0;
                                     }
                                        foreach($question_topics as $question_topic): ?>
                                            <option value="<?php echo $question_topic['id'] ?>" <?php echo ($q_topic==$question_topic['id'])? "selected" : ""; ?>><?php echo $question_topic['qt_topic_name'] ?></option>
                                        <?php endforeach; ?>
                                </select>
                                <!--input autocomplete="off" type="text" class="form-control filter-field" placeholder="Logic Gates" id="q_topic" name="q_topic" value="<?php //echo ($q_topic)?$q_topic:((!$id&&isset($df_values['q_topic']))?$df_values['q_topic']:'') ?>"-->
                            </div>
                            <!--ul class="auto-search-lister" id="listing_question_topic" style="display: none;">
                            </ul-->
                        </div>   
                        <div class="col-sm-5">
                            +ve Mark
                            <div class="form-group">
                                <input type="number" oninput="this.value = Math.abs(this.value)" min="0" class="form-control" placeholder="1" id="q_positive_mark" onkeypress="return preventCharector(event)" name="q_positive_mark" value="<?php echo $q_positive_mark ? $q_positive_mark : '';?>">
                            </div>
                        </div>                     
                        <div class="col-sm-6 pull-right">
                            -ve Mark
                            <div class="form-group">
                                <input type="number" class="form-control" placeholder="0" id="q_negative_mark" min="0" name="q_negative_mark"  onkeypress="return preventCharector(event)" value="<?php echo $q_negative_mark!='' ? $q_negative_mark : '';?>">
                            </div>
                        </div>                     

                        <div class="col-sm-12">
                        Tag
                        <div class="form-group">
                            <input type="text" class="form-control" value="<?php echo ($q_tags)?$q_tags:((!$id&&isset($df_values['q_tags']))?$df_values['q_tags']:'') ?>" id="q_tags" name="q_tags">
                        </div>
                    </div>                     
                    </div>
                </div>
                <div class="question-rightbox">
                    <div class="language-selector">
                        <ol class="nav nav-tabs offa-tab" style="visibility:hidden;">
                            <?php if(!empty($web_languages)): ?>
                            <?php foreach($web_languages as $web_language): ?>
                                <li  <?php echo ((isset($active_web_language) && $active_web_language == $web_language['id'])?'class="active"':'') ?> >
                                    <a href="javascript:void(0)" onclick="changeLanguage(<?php echo $web_language['id'] ?>)"> <?php echo $web_language['wl_name'] ?></a>
                                    <span class="active-arrow"></span>
                                </li>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </ol> 
                        <?php 
                            $q_question_temp = json_decode($q_question);
                            if(!(json_last_error() == JSON_ERROR_NONE))
                            {
                                $q_question = '';
                            }
                            else
                            {
                                $q_question =  isset($q_question_temp->$active_web_language)?stripslashes($q_question_temp->$active_web_language):'';
                            }
                        $ckeditor     = array();
                        $ckeditor[]   =((strpos($q_question, '<pre>') !== false) || ($q_question != strip_tags($q_question)))? 1 :0;    
                        ?>  
                        <span class="cb-drop-down-enable ">
                        <!-- <label>Text</label> -->
                        <select class="form-control" id="" name="q_pending_status">
                            <option <?php if(!empty( $q_pending_status ) && $q_pending_status == '0'){ echo "selected"; } ?> value="0">Not completed</option>        
                            <option <?php if(!empty( $q_pending_status ) && $q_pending_status == '1'){ echo "selected"; } ?> value="1">Partially completed</option>        
                            <option <?php if(!empty( $q_pending_status ) && $q_pending_status == '2'){ echo "selected"; } ?> value="2">Completed</option>        
                        </select>
                        
                        </span>               
                        <span class="cb-editor-enable">
                            <label>Text Editor <input type="checkbox"  onchange="toggleRedactor(this)" id="check_redactor" ></label>
                        </span>
                    </div>
                    <div class="question-content" id="question_content">
                        <?php include 'messages.php'; ?>
                        <div class="add-question">
                            
                            <div class="form-group row single-question-block">
                                <div class="col-sm-12">
                                    <span>Type your question * :</span>
                                  
                                    <textarea id="q_question" name="q_question" class="form-control" ><?php echo $q_question ?></textarea>
                                </div>
                            </div>
                            <div class="form-group row" id="copy_blank_code_block" <?php echo ($q_type == '4' || (isset($df_values['q_type']) && $df_values['q_type'] == '4'))?'style="display: block;"':'style="display: none;"' ?>>
                                <div class="col-sm-12">
                                    <p><span id="copy">[_____]  </span><a href="#" onclick="copyToClipboard('#copy')"><b>Click here</b></a> to copy the dash in clipboard</p>
                                </div>
                            </div>
                            <div class="answer-block">
                                <ul class="answer-ul" id="question_option_wrapper">
                                    <?php if(!$id): ?>
                                        <?php for($field = 1; $field<=4;$field++): ?>
                                        <?php 
                                        $input_type = 'checkbox';
                                        $answer     = '['.$field.']';                  
                                        if(isset($df_values['q_type']) && $df_values['q_type'] == 1)
                                        {
                                            $input_type = 'radio';
                                            $answer     = '';
                                        }
                                        ?>
                                        <li id="new_option_<?php echo $field ?>" class="option-element" data-id="<?php echo $field ?>">
                                            <span class="cb-answer">
                                                <span class="cb-alphabet"><?php echo toAlpha($field+1); ?></span>
                                                <span class="cb-radio <?php echo $input_type ?>-btn">
                                                    <input class="rdobtn question-option-input-new" type="<?php echo $input_type ?>" value="<?php echo $field ?>" name="answer_new<?php echo $answer ?>" >
                                                    <label class="rdoinr rdc">
                                                        <span class="inrrclr"></span>
                                                    </label>                                        
                                                </span>
                                            </span> 
                                            <span  class="cb-textbox">
                                                <textarea name="option_new[<?php echo $field ?>]" id="new_option_textarea_<?php echo $field ?>" class="form-control question-text-input" rows="4" ></textarea>
                                            </span>
                                        </li>
                                        <?php endfor; ?>
                                    <?php endif; ?>
                                    <?php if(!empty($q_options)): ?>
                                    <?php $count = 2; $checked = 'checked="checked"'; $single_choice = 1; ?>
                                    <?php foreach ($q_options as $option):?>
                                    <?php 
                                    $input_type = 'checkbox';
                                    $answer     = '['.$option['id'].']';                                     
                                    if($q_type == $single_choice)
                                    {
                                        $input_type = 'radio';
                                        $answer     = '';
                                    }
                                    ?>
                                    <?php 
                                    $qo_options_temp = json_decode($option['qo_options']);
                                    if(!(json_last_error() == JSON_ERROR_NONE))
                                    {
                                        $option['qo_options'] = '';
                                    }
                                    else
                                    {
                                        $option['qo_options'] = isset($qo_options_temp->$active_web_language)?stripslashes($qo_options_temp->$active_web_language):'';
                                    }

                                    $ckeditor[] = ((strpos($option['qo_options'], '<pre>') !== false) || ($option['qo_options'] != strip_tags($option['qo_options'])))? 1 :0;
                                    ?>
                                    <li id="option_wrapper_<?php echo $option['id'] ?>" class="option-element" data-id="<?php echo $option['id'] ?>">
                                        <span class="cb-answer">
                                            <span class="cb-alphabet"><?php echo toAlpha($count); $count++; ?></span>
                                            <span class="cb-radio <?php echo $input_type ?>-btn">
                                                <input class="rdobtn question-option-input" type="<?php echo $input_type ?>" <?php echo (in_array($option['id'], $q_answer))?$checked:'';  ?> value="<?php echo $option['id'] ?>" name="answer<?php echo $answer ?>" >
                                                <label class="rdoinr rdc">
                                                    <span class="inrrclr"></span>
                                                </label>                                        
                                            </span>
                                        </span>
                                        <span class="cb-textbox">
                                            <textarea id="option_textarea_<?php echo $option['id'] ?>" name="option[<?php echo $option['id'] ?>]" class="form-control question-text-input" rows="4" ><?php echo $option['qo_options'] ?></textarea>
                                        </span>
                                        <?php if($count>4): ?>
                                        <span class="cb-delete-option" onclick="deleteOldOption('option_wrapper_<?php echo $option['id'] ?>')"><i class="icon icon-trash-empty"></i></span>
                                        <?php endif; ?>
                                    </li>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                               
                                <div class="col-sm-12" id="add_option_button" <?php echo (($q_type == '3' || (isset($df_values['q_type']) && $df_values['q_type'] == '3')) || ($q_type == '4' || (isset($df_values['q_type']) && $df_values['q_type'] == '4')))?'style="display: none;"':'' ?>><button type="button" id="add-more-option" class="btn btn-green pull-right custom-btn"><i class="icon icon-plus"></i>Add New Option<ripples></ripples></button></div>

                            </div>
                            <div class="row">
                                <div class="col-sm-12 add-question-panel">

                                    <div class="panel-group">
                                        <div class="panel panel-default aq-panel">
                                            <div class="panel-heading">
                                                <h4 class="panel-title aq-title">
                                                    <a data-toggle="collapse" id="q_explanationbox" href="#collapse1" class="text-uppercase"><i class="icon icon-plus"></i>Add Explanation</a>
                                                </h4>
                                            </div>
                                            <?php 
                                            $q_explanation_temp = json_decode($q_explanation);
                                            if(!(json_last_error() == JSON_ERROR_NONE))
                                            {
                                                $question_explanation = '';
                                                $q_explanation        = '';
                                            }
                                            else
                                            {
                                                $question_explanation =  isset($q_explanation_temp->$active_web_language)?$q_explanation_temp->$active_web_language:'';
                                                $q_explanation        =  isset($q_explanation_temp->$active_web_language)?stripslashes($q_explanation_temp->$active_web_language):'';
                                            }
                                            //echo get_video_content($question_explanation);
                                       
                                            $ckeditor[] = ((strpos($q_explanation, '<pre>') !== false) || ($q_explanation != strip_tags($q_explanation)))? 1 :0;
                                            ?>
                                            <div id="collapse1" class="panel-collapse collapse <?php if($q_explanation!=''){ echo 'in'; } ?> ">
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <textarea class="form-control" id="q_explanation" name="q_explanation"><?php echo get_video_content($question_explanation); ?></textarea>
                                                    </div>                                        
                                                </div>
                                            </div>
                                        </div>
                                    </div>                            
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 text-center">
                                    <button type="button" onclick="resetQuestionConfirm();" class="btn btn-blue text-uppercase">reset<ripples></ripples></button>
                                    <button type="button" onclick="saveQuestionConfirm();" class="btn btn-green text-uppercase">save<ripples></ripples></button>
                                    <button type="button" onclick="saveAndNewQuestionConfirm();"class="btn btn-green text-uppercase">save &amp; new<ripples></ripples></button>
                                    <?php if( $next_id != 0 ):?>
                                        <button type="button" onclick="saveAndNextQuestionConfirm();"class="btn btn-green text-uppercase">save &amp; Next<ripples></ripples></button>
                                    <?php endif;?>
                                    <button type="button" onclick="cancelQuestionConfirm();" class="btn btn-red text-uppercase">cancel<ripples></ripples></button>
                                </div>
                            </div>
                        </div>
                    </div>    
                </div>
                                    
                </form>
            </div>

   
        </div>
        <!-- Manin Iner container end -->
<div class="modal fade alert-modal-new" id="test_basic_modal" role="dialog">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content" style="width:800px;">
                <div class="modal-header" style="float: left;color:#444;margin: -15px 1px 15px 0;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel">Flowchart</h4>
                </div>
                <div class="modal-body">
                          <div style="width: 100%; display: flex; justify-content: space-between">
                            <div id="myPaletteDiv" style="width: 120px; background-color: gray;"></div>
                            <div id="myDiagramDiv" style="flex-grow: 1; height: 490px; "></div>
                          </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-blue text-uppercase selected" id="test_pop_clear">Clear</button>
                    <button type="button" class="btn btn-red" id="test_pop_cancel" data-dismiss="modal">Cancel</button>
                    <button type="button" data-dismiss="modal" onclick="generateHtmlCode()" class="btn btn-green" id="test_pop_continue">Continue</button>
                </div>
            </div>
        </div>
    </div>
</body>
    <!-- body end-->
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/layout.min.js"></script>

<?php  /* ?><script type="text/javascript" src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/redactor/js/source.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script><?php */ ?>
<script type="text/javascript" src="<?php echo assets_url() ?>js/bootstrap-tagsinput.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/system.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/app.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>/assets/js/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>/assets/js/ckeditor/config.js"></script>
<script>
    var __redactorEnabled = false;
    $(document).ready(function () {
        setTimeout(function()
        {
            $('.message_container').html('');
        }, 2000);
        $('#q_tags').tagsinput();
        $('.bootstrap-tagsinput').removeClass('form-control');
        <?php if (in_array("1",$ckeditor)): ?>
        $("#check_redactor").prop("checked", true);
            startTextToolbar();
        <?php endif; ?>
        
        <?php if(!$id): ?>
            $('#q_type').trigger('change');
        <?php endif; ?>
       // startTextToolbar();
        $('body').click(function(){
            if(event.target.className != 'form-control filter-field')
            {
                $('.auto-search-lister').hide();
            }
        });
        App.initEqualizrHeight(".builder-left-inner", ".builder-right-inner");
    });

    
    function flowchartPopUp(element)
    {
        setElement(element);
        var diagramdiv = go.Diagram.fromDiv("myDiagramDiv");
        if(!diagramdiv)
        {
            init();
        }
        $('#test_basic_modal').modal('show');
    }

    function generateHtmlCode()
    {
        CKEDITOR.instances[getElementId()].insertHtml(generateImageHtmlCode().outerHTML);
        flowchartClear();
    }

    $( "#test_pop_clear" ).click(function() {
        flowchartClear();
    });
    function flowchartClear(){
        myDiagram.div = null;
        var diagramdiv = go.Diagram.fromDiv("myDiagramDiv");
        if(!diagramdiv)
        {
            init();
        }
    }
    function startTextToolbar()
    {
        /*$('#q_question, #q_explanation, .question-text-input').redactor({
            minHeight: 100,
            maxHeight: 100,
            imageUpload: __admin_url+'configuration/redactore_image_upload',
            plugins: ['table', 'alignment', 'source'],
            callbacks: {
                imageUploadError: function(json, xhr)
                {
                     var erorFileMsg = "This file type is not allowed. upload a valid image.";
                     $('#question_content').prepend(renderPopUpMessage('error', erorFileMsg));
                      $(".question-content").animate({ scrollTop: 0 }, "slow");
                     return false;
                }
            }  
        });*/
        CKEDITOR.replace('q_question', { height: 100 });
        CKEDITOR.replace('q_explanation', { height: 100 });
        $('.question-text-input').each(function(){
            CKEDITOR.replace($(this).attr('id'), { height: 100 });                 
        });
    }
    function changeLanguage(language_id)
    {
        $.ajax({
            url: '<?php echo admin_url() ?>coursebuilder/change_language/'+language_id,
            type: "POST",
            data:{ "is_ajax":true},
            success: function(response) {
                var data  = $.parseJSON(response);
                if(data['error'] != false)
                {   
                    lauch_common_message('Error in switching language', data['message']);
                }
                else
                {
                    location.reload();
                }
            }
        });
    }
    
    var __redactorEnablingInProgress = false;
    var __admin_url = '<?php echo admin_url() ?>';
    function toggleRedactor(option)
    {
        if(__redactorEnablingInProgress == true)
        {
            return false;
        }
        __redactorEnablingInProgress = true;
        __redactorEnabled = option.checked;
        if( __redactorEnabled == true)
        {
            /*$('#q_question, #q_explanation, .question-text-input').redactor({
                minHeight: 100,
                maxHeight: 100,
                imageUpload: __admin_url+'configuration/redactore_image_upload',
                plugins: ['table', 'alignment', 'source'],
                callbacks: {
                    imageUploadError: function(json, xhr)
                    {
                         var erorFileMsg = "This file type is not allowed. upload a valid image.";
                         $('#question_content').prepend(renderPopUpMessage('error', erorFileMsg));
                          $(".question-content").animate({ scrollTop: 0 }, "slow");
                         return false;
                    },
                    init: function()
                    {
                        __redactorEnablingInProgress = false;
                    }
                }  
            });*/
            CKEDITOR.replace('q_question', { height: 100 });
            CKEDITOR.replace('q_explanation', { height: 100 });
            $('.question-text-input').each(function(){
                CKEDITOR.replace($(this).attr('id'), { height: 100 });            
            });
            __redactorEnablingInProgress = false;

        }
        else
        {
            $('#q_question').val(CKEDITOR.instances[$('#q_question').attr('name')].getData());
                CKEDITOR.instances[$('#q_question').attr('name')].destroy(true);
            $('#q_explanation').val(CKEDITOR.instances[$('#q_explanation').attr('name')].getData());
                CKEDITOR.instances[$('#q_explanation').attr('name')].destroy(true);
            $('.question-text-input').each(function(){
                $(this).val(CKEDITOR.instances[$(this).attr('id')].getData());
                CKEDITOR.instances[$(this).attr('id')].destroy(true);
            });
            //$('#q_question, #q_explanation, .question-text-input').redactor('core.destroy');        
            __redactorEnablingInProgress = false;
        }
    }


    var __single_choice        = 1;
    var __removed_options      = new Array();
    $(document).on('click', '#add-more-option', function(){
        if($('#check_redactor').prop("checked") == true){
                __redactorEnabled = true;
        }
        var total_options = $('#question_option_wrapper .option-element').length;
            total_options++;
            if(__redactorEnabled == true){
                var new_option_textarea_counts = $('#new_option_textarea_'+total_options).length;
                if(new_option_textarea_counts){
                    $('#new_option_textarea_'+total_options).remove();
                }
            }
        //console.log(total_options, 'total_options', new_option_textarea_counts);
        var question_type = $('#q_type').val(); 
        if( question_type == '' )
        {
            $('#question_content').prepend(renderPopUpMessage('error', 'Choose question type'));
             $(".question-content").animate({ scrollTop: 0 }, "slow");
            return false;
        }
        var optionHtml  = '';
        var input_type  = 'checkbox';
        var answer      = '['+total_options+']';                                     
            if( question_type == __single_choice )
            {
                input_type = 'radio';
                answer     = '';
            }
            optionHtml += '<li class="option-element" id="new_option_'+total_options+'">';
            optionHtml += '    <span class="cb-answer">';
            optionHtml += '        <span class="cb-alphabet">'+toAlpha(total_options+1)+'</span>';
            optionHtml += '        <span class="cb-radio '+input_type+'-btn">';
            optionHtml += '            <input class="rdobtn question-option-input-new" type="'+input_type+'" value="'+total_options+'" name="answer_new'+answer+'">';
            optionHtml += '            <label class="rdoinr rdc">';
            optionHtml += '                <span class="inrrclr"></span>';
            optionHtml += '            </label>';
            optionHtml += '        </span>';
            optionHtml += '    </span>';
            optionHtml += '    <span class="cb-textbox">';
            optionHtml += '        <textarea id="new_option_textarea_'+total_options+'" name="option_new['+total_options+']" class="form-control question-text-input" rows="4"></textarea>';
            optionHtml += '    </span>';
            optionHtml += '    <span class="cb-delete-option" onclick="deleteNewOption(\'new_option_'+total_options+'\')"><i class="icon icon-trash-empty"></i></span>';
            optionHtml += '</li>';
            $('#question_option_wrapper').append(optionHtml);  
                                                                        
            if(__redactorEnabled == true)
            {
                /*$('#new_option_textarea_'+total_options).redactor({
                    minHeight: 100,
                    maxHeight: 100,
                    imageUpload: __admin_url+'configuration/redactore_image_upload',
                        plugins: ['table', 'alignment', 'source']
                });*/
                CKEDITOR.replace('new_option_textarea_'+total_options, { height: 100 });
            }
    });
    
    $(document).on('change', '.question-option-input, .question-option-input-new', function(){
        var question_type = $('#q_type').val();
        if( question_type == __single_choice )
        {
            $('.question-option-input, .question-option-input-new').prop('checked', false);
            $(this).prop('checked', true);
        }
    });

    function deleteNewOption(optionId)
    {
        $('#'+optionId).remove();
        $('.cb-alphabet').text(function (i) {
            return toAlpha(i+2);
        });
    }

    function deleteOldOption(optionId)
    {
        var question_id = $('#'+optionId).attr('data-id');
        __removed_options.push(question_id);
        $('#'+optionId).remove();
        $('.cb-alphabet').text(function (i) {
            return toAlpha(i+2);
        });
    }

    if($('#q_type').val() != '4'){
        $('#copy_blank_code_block').hide();
    }
    
    $(document).on('change', '#q_type', function(){
        var question_type = $('#q_type').val();
        $('.question-option-input, .question-option-input-new').prop('checked', false);
        $('.option-element, #add_option_button').show();
        switch (question_type)
        {
            case '1':
                $('.question-option-input').each(function(){
                    $(this).attr('type', 'radio').attr('name', "answer".substr(0, 6));
                });
                $('.question-option-input-new').each(function(){
                    $(this).attr('type', 'radio').attr('name', "answer_new".substr(0, 10));
                });
                $('.cb-radio').removeClass('checkbox-btn').removeClass('radio-btn').addClass('radio-btn');
                $('#copy_blank_code_block').hide();
                if($('.option-element').length == 0) {
                    $('#add-more-option').trigger('click');
                    $('#add-more-option').trigger('click');
                }
                $('.cb-delete-option').hide();
            break;
            case '2':
                $('.question-option-input').each(function(){
                    $(this).attr('type', 'checkbox').attr('name', "answer["+$(this).val()+"]");        
                });
                $('.question-option-input-new').each(function(){
                    $(this).attr('type', 'checkbox').attr('name', "answer_new["+$(this).val()+"]");        
                });
                $('.cb-radio').removeClass('checkbox-btn').removeClass('radio-btn').addClass('checkbox-btn');
                $('#copy_blank_code_block').hide();
                if($('.option-element').length == 0) {
                    $('#add-more-option').trigger('click');
                    $('#add-more-option').trigger('click');
                }
                if($('.option-element').length == 2) {
                    $('.cb-delete-option').hide();
                }
            break;
            case '3':
                $('#copy_blank_code_block').hide();
                $('.option-element, #add_option_button').hide();
            break;
            case '4':
                $('.option-element, #add_option_button').hide();
                $('#copy_blank_code_block').show();
            break;
        }
    });


    function saveQuestion()
    {
        var errCount      = 0;
        var errMessage    = '';
        var question = $('#q_question').val();//q_explanation
        if(__redactorEnabled == true)
        {
            question      = CKEDITOR.instances[$('#q_question').attr('id')].getData();
        }
        var question_type = $('#q_type').val().trim();
        var total_options = $('#question_option_wrapper .option-element').length;
        var category      = $('#q_category').val().trim();
        var subject       = $('#q_subject').val().trim();
        var topic         = $('#q_topic').val().trim();
        var positive_mark = $('#q_positive_mark').val().trim();
        var negative_mark = $('#q_negative_mark').val().trim();
        var q_explanationbox = $('#q_explanationbox').attr('aria-expanded');
        var q_explanation = $('#q_explanation').val();
        
        //console.log(question_type,'-',question,'-',total_options,'-',category,'-',subject,'-',topic,'-',positive_mark,'-',negative_mark);
        //return;
        //if( category.replace(" ", "") == '' )
        if(category == 0)
        {
            errMessage += 'Category cannot be empty<br />';   
            errCount++;        
        }
        if(subject == 0)
        {
            errMessage += 'Subject cannot be empty<br />';   
            errCount++;        
        }
        if(topic == 0)
        {
            errMessage += 'Topic cannot be empty<br />';   
            errCount++;        
        }
        if( positive_mark == '' || isNaN(positive_mark) || positive_mark < 1)
        {
            errMessage += 'Invalid mark entered for positive mark<br />';   
            errCount++;        
        }
        if( negative_mark == '' || isNaN(negative_mark))
        {
            errMessage += 'Invalid mark entered for negative mark<br />';   
            errCount++;        
        }
        var regex = /(&nbsp;|<([^>]+)>)/ig;
        if( question.replace(regex, "").trim() == '' )
        {
            errMessage += 'Question cannot be empty<br />';   
            errCount++;        
        }

        if(typeof q_explanationbox !== "undefined" && q_explanationbox === 'true'){

                if( (q_explanation.replace(regex, "").trim()) == '' )
                {
                    $('#q_explanation').val('');
                    //errMessage += 'Explanation cannot be empty<br />';
                    //errCount++;        
                }
                
        // }else{
        //     $('#q_explanation').val('');
        }

        if(total_options < 2 && question_type != 3 && question_type != 4)
        {
            errCount++;
            errMessage += 'Please add atleast two options<br />';
        }

        if( question_type != 3 && question_type != 4)
        {
            var OptionErrMessage = '';
            var page_content = '';
            $('.question-text-input').each(function(){ 
                
                if(__redactorEnabled == true)
                {
                    page_content    = CKEDITOR.instances[$(this).attr('id')].getData().trim();
                }
                else
                {
                    page_content    = $(this).val().trim();
                }
                page_content        = page_content.replace(regex, "").trim();
                //console.log(page_content,'page_content');
                if((page_content) == '')
                {
                    OptionErrMessage = 'Options cannot be empty <br />';   
                    errCount++;
                } 
                
            });
            
            if(errCount){
                errMessage += OptionErrMessage;
            }
        }
        var checked = 0;
        $('.question-option-input, .question-option-input-new').each(function(){
            if( $(this).prop('checked') == true )
            {
                checked++;
            }
        });
        if( checked == 0 && (question_type != 3 && question_type != 4))
        {
            errMessage += 'Choose the answer<br />';   
            errCount++;        
        }
        cleanPopUpMessage();
        if( errCount > 0 )
        {
            $('#question_content').prepend(renderPopUpMessage('error', errMessage));
             $(".question-content").animate({ scrollTop: 0 }, "slow");
            return false;        
        }

        $('#removed_options').val(JSON.stringify(__removed_options));
        $('#question_form').submit();
    }

    function toAlpha(number)
    {
        var alphabet = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        number--;
        var count = alphabet.length;
        if(number <= count)
            return alphabet[number-1];
        var modulo = 0, alpha = '';
        while(number > 0){
            modulo     = (number - 1) % count;
            alpha      = alphabet[modulo]+alpha;
            number     = Math.floor(((number - modulo) / count));
        }
        return alpha;
    }
    
    
    var categorySearchTimeOut = '';
    $(document).on('keyup', '#q_category', function(){
        clearTimeout(categorySearchTimeOut);
        categorySearchTimeOut = setTimeout(function(){ 
            var keyword         = $('#q_category').val();
            var url             = __admin_url+'generate_test/question_category';
            var questionHtml    = '';
            if( keyword.replace(' ', '') != '' )
            {
                $.ajax({
                        url: url,
                        type: "POST",
                        data:{ 'category':keyword, 'is_ajax':true},
                        success: function (response){
                            var data    = $.parseJSON(response);
                            if(Object.keys(data['category']).length > 0 )
                            {
                                for( var i = 0; i < data['category'].length; i++)
                                {
                                    questionHtml += '<li id="'+data['category'][i]['id']+'">'+data['category'][i]['ct_name']+'</li>';
                                }
                                $("#listing_question_category").html(questionHtml).show();       
                            }
                        }
                });
            }
        }, 600);
    });
    $(document).on('click', '#listing_question_category li', function(){
        $('#q_category').val($(this).text());
        $(this).parent().html('').hide();
    });
    
    var subjectSearchTimeOut = '';
    $(document).on('keyup', '#q_subject', function(){
        clearTimeout(subjectSearchTimeOut);
        subjectSearchTimeOut = setTimeout(function(){ 
            var keyword         = $('#q_subject').val();
            var category      = $('#q_category').val();
            var url             = __admin_url+'generate_test/question_subject';
            var subjectHtml     = '';
            if( keyword.replace(' ', '') != '' && category.replace(' ', '') != '')
            {
                $.ajax({
                        url: url,
                        type: "POST",
                        data:{ 'subject':keyword, 'category':category, 'is_ajax':true},
                        success: function (response){
                            var data    = $.parseJSON(response);
                            if(Object.keys(data['subject']).length > 0 )
                            {
                                for( var i = 0; i < data['subject'].length; i++)
                                {
                                    subjectHtml += '<li id="'+data['subject'][i]['id']+'">'+data['subject'][i]['qs_subject_name']+'</li>';
                                }
                                $("#listing_question_subject").html(subjectHtml).show();       
                            }
                        }
                });
            }
        }, 600);
    });
    $(document).on('click', '#listing_question_subject li', function(){
        $('#q_subject').val($(this).text());
        $(this).parent().html('').hide();
    });
    
    var topicSearchTimeOut = '';
    $(document).on('keyup', '#q_topic', function(){
        clearTimeout(topicSearchTimeOut);
        topicSearchTimeOut = setTimeout(function(){ 
            var keyword         = $('#q_topic').val();
            var category      = $('#q_category').val();
            var subject       = $('#q_subject').val();
            var url             = __admin_url+'generate_test/question_topic';
            var topicHtml       = '';
            if( keyword.replace(' ', '') != '' && category.replace(' ', '') != '' && subject.replace(' ', '') != '')
            {
                $.ajax({
                        url: url,
                        type: "POST",
                        data:{ 'category':category, 'subject':subject, 'topic':keyword, 'is_ajax':true},
                        success: function (response){
                            var data    = $.parseJSON(response);
                            if(Object.keys(data['topic']).length > 0 )
                            {
                                for( var i = 0; i < data['topic'].length; i++)
                                {
                                    topicHtml += '<li id="'+data['topic'][i]['id']+'">'+data['topic'][i]['qt_topic_name']+'</li>';
                                }
                                $("#listing_question_topic").html(topicHtml).show();       
                            }
                        }
                });
            }
        }, 600);
    });
    $(document).on('click', '#listing_question_topic li', function(){
        $('#q_topic').val($(this).text());
        $(this).parent().html('').hide();
    });
function copyToClipboard(element) {
  var $temp = $("<input>");
  $("body").append($temp);
  $temp.val($(element).text()).select();
  document.execCommand("copy");
  $temp.remove();
}
function saveQuestionConfirm()
{
    $('#redirect_type').val('save');
    saveQuestion();
}
function saveAndNewQuestionConfirm()
{
    $('#redirect_type').val('save_and_new');
    saveQuestion();
}
function saveAndNextQuestionConfirm()
{
    $('#redirect_type').val('save_and_next');
    saveQuestion();
}
function resetQuestionConfirm()
{
    $('#q_type, #q_difficulty').val('1');
    $('#q_positive_mark, #q_negative_mark').val('');
    $('#q_category, #q_subject, #q_topic').val(0);
    $('#q_tags').tagsinput('removeAll');
    $('#q_question, #q_explanation, .question-text-input').val('');
    $('.rdobtn').attr('checked', false);
    $('#copy_blank_code_block').hide();
    // if($('#checkArray:checkbox:checked').length > 0)
    // {      
            
    //     $('#q_question, #q_explanation, .question-text-input').redactor('code.set', '');       
    //     CKEDITOR.instances.q_question.setData('');
    //     CKEDITOR.instances.q_explanation.setData('');
    //     $('.question-text-input').each(function(){
    //         CKEDITOR.instances.$(this).attr('id').setData('');
    //     });
    // }
   // $('#q_question, #q_explanation, .question-text-input').redactor('code.set', ''); 
    if($('#check_redactor').prop("checked") == true){
        __redactorEnabled = true;
        CKEDITOR.instances.q_question.setData('');
        CKEDITOR.instances.q_explanation.setData('');
    } 
    $('#question_option_wrapper').html('');
    if($('.option-element').length == 0) {
                    $('#add-more-option').trigger('click');
                    $('#add-more-option').trigger('click');
    }
    
    $('.cb-delete-option').hide();
    $('#add_option_button').show();
}
function cancelQuestionConfirm()
{
    $('.cb-close-qstn i').trigger('click');
}
function generateSubjectList()
{
        $('#q_subject') .html('');
        var categoryId        = $('#q_category').val();
        var renderSubjectsHtml = '';
        renderSubjectsHtml    += '<option value="0">Loading...</option>';
        $('#q_subject').html(renderSubjectsHtml);
        var renderTopicHtml = '';
        renderTopicHtml += '<option value="0">Choose Topic</option>';
        $('#q_topic').html(renderTopicHtml);
        __ajaxInProgress = 1;
        $.ajax({
            url:  __admin_url+'question_manager/get_subjects',
            type: "POST",
            data: {"is_ajax": true, 'course_category': categoryId},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                //console.log(data);
                if(data['error']==false)
                {
                    $("#q_subject").html(" ");
                       // subject_data      = [];
                    var renderSubjectHtml = '';
                    renderSubjectHtml += '<option value="0">Choose Subject</option>';
                    if(data['subject'].length!=0){
                       for (i = 0; i < data['subject'].length; i++) { 
                        var subject_data   = data['subject'][i];
                        renderSubjectHtml += '<option value="'+subject_data['id']+'">'+subject_data['qs_subject_name']+'</option>';
                       }
                    }   
                    $('#q_subject').prepend(renderSubjectHtml);
                }
            }
        });
}
function generateTopicList()
{
        $("#q_topic").html(" ");
        var categoryId   = $('#q_category').val();
        var subjectId   = $('#q_subject').val();
        var renderTopicsHtml = '';
        renderTopicsHtml    += '<option value="0">Loading...</option>';
        $('#q_topic').html(renderTopicsHtml);
        __ajaxInProgress = 1;
        $.ajax({
            url: __admin_url+ 'question_manager/get_topics',
            type: "POST",
            data: {"is_ajax": true, 'course_category': categoryId, 'question_subject': subjectId},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                //console.log(data);
                if(data['error']==false)
                {
                    $("#q_topic").html(" ");
                    var renderTopicHtml = '';
                    renderTopicHtml += '<option value="0">Choose Topic</option>';
                    if(data['topic'].length!=0){
                        for (i = 0; i < data['topic'].length; i++) { 
                        var topic_data   = data['topic'][i];
                        renderTopicHtml += '<option value="'+topic_data['id']+'">'+topic_data['qt_topic_name']+'</option>';
                       }
                    } 
                    $('#q_topic').prepend(renderTopicHtml);
                }
            }
        });

}
</script>
</html>
<!-- Modal pop up contents:: Delete Section popup-->
    
<!-- !.Modal pop up contents :: Delete Section popup-->