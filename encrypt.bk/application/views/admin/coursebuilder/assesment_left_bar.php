<?php include_once 'lecture_header.php'; ?>
    <!-- ADDING REDACTOR PLUGIN INSIDE -->
    <!-- ############################# --> <!-- START -->
        <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/redactor.css" />
        <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/alignment.css" />
    <!-- ############################# --> <!-- END -->
    <!-- ADDING REDACTOR PLUGIN INSIDE -->
<?php 
    $checked       = 'checked="checked"';

    $status_button = 'btn-green';
    $status_label  = 'activate';
    $status_mark   = 'Inactive';
    $status_lang   = 'inactive';
    if($lecture['cl_status'] == 1)
    {
        $status_button = 'btn-orange';
        $status_label  = 'deactivate';
        $status_mark   = 'active';
        $status_lang   = 'active';
    }
?>

    <div class="col-sm-6 builder-left-inner"><!-- !.Left side bar section -->

                <!-- top Header with drop down and action buttons -->
                <div class="buldr-header inner-buldr-header clearfix row">
                        <div class="pull-left">
                            <!-- Header left items -->
                            <div class="lecture-icon-big <?php echo $lecture_icons[$lecture['cl_lecture_type']]['parent'] ?>"><i class="icon <?php echo $lecture_icons[$lecture['cl_lecture_type']]['child'] ?>"></i></div>
                            <h3><?php echo $lecture['cl_lecture_name']; ?></h3>
                            <!-- Drop down -->
                            <div class="btn-group section-control">
                                <span class="dropdown-tigger" data-toggle="dropdown">
                                <span class="label-text">
                                  <i class="icon icon-down-arrow"></i>
                                </span>
                                <span class="tilder"></span>
                                </span>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <li>
                                        <a href="#" data-target="#deleteSection" onclick="deleteLecture('<?php echo htmlentities($lecture['cl_lecture_name']) ?>', '<?php echo $lecture['id'] ?>')" data-toggle="modal"><?php echo lang('delete') ?></a>
                                    </li>
                                </ul>
                            </div>
                            <!-- !.Drop down -->
                        </div>
                    <!-- !.Header left items -->
                    <div class="pull-right rite-side">
                        <!-- Header right side items with buttons -->
                    <a id="lecture_action_status_<?php echo $lecture['id'] ?>" class="btn <?php echo $status_button ?>"  data-toggle="modal" data-target="#active-lecture" onclick="changeLectureStatus('<?php echo $lecture['cl_status'] ?>', '<?php echo base64_encode(htmlentities($lecture['cl_lecture_name'])); ?>', '<?php echo $lecture['id'] ?>')"><?php echo strtoupper(lang($status_label)) ?></a>
                    <a href="<?php echo admin_url('coursebuilder/home/'.$lecture['cl_course_id']) ?>"><button class="btn btn-red" id="back_button"><i class="icon icon-left"></i><?php echo lang('back') ?></button></a>
                    </div>
                </div>
                <!-- !.top Header with drop down and action buttons -->

        <!-- Form elements Left side :: lecture Documents-->

        <div class="builder-inner-from" id="survey_form">
                <div class="form-group clearfix">
                    <label><?php echo lang('assesment_title') ?> *:</label>
                    <div id="lecture_status_wraper_<?php echo $lecture['id'] ?>" class="<?php echo $status_mark ?>-section"><i class="icon icon-ok-circled"></i><span class="ap_cont" id="lecture_status_text_<?php echo $lecture['id'] ?>"><?php echo ucfirst(lang($status_lang)) ?></span></div>
                <input type="text" maxlength="80" placeholder="eg: Mathematical Calculations" id="lecture_name" name="lecture_name" value="<?php echo isset($lecture['cl_lecture_name'])?htmlentities($lecture['cl_lecture_name']):''; ?>" class="form-control">
                </div>

                <!-- Description area -->
                <div class="form-group clearfix">
                    <label><?php echo lang('assesment_description') ?> :</label>
                    <textarea class="form-control" id="lecture_description" onkeyup="validateMaxLength(this.id)" maxlength="1000" name="lecture_description" ><?php echo isset($lecture['cl_lecture_description'])?$lecture['cl_lecture_description']:''; ?></textarea>
                    <label class="pull-right" id="lecture_description_char_left"> 
                        <?php echo (1000 -strlen($lecture['cl_lecture_description'])).' '.lang('characters_left') ?></label>
                </div>
                <!-- !.Description area -->

                 <!-- Lecture Content area -->
                <div class="form-group clearfix">
                    <label><?php echo lang('instructions') ?> *:</label>
                    <textarea class="form-control h235" id="lecture_instruction" onkeyup="validateMaxLength(this.id)" maxlength="1000" name="lecture_description" ><?php echo (isset($lecture['assesment']['a_instructions'])&&$lecture['assesment']['a_instructions']!='')?$lecture['assesment']['a_instructions']:get_instruction(); ?></textarea>
                    <?php /* ?><label class="pull-right" id="lecture_instruction_char_left"> 
                        <?php 
                        $intruction_char_length = isset($lecture['assesment']['a_instructions'])?strlen($lecture['assesment']['a_instructions']):0;
                        ?>
                        <?php echo (1000 - $intruction_char_length).' '.lang('characters_left') ?>
                    </label><?php */ ?>
                </div>
                <!-- !.Lecture Content area -->

                <!-- Access and maximum access count -->
                <div class="form-group clearfix row correct-lbl">
                    <div class="col-sm-4">
                        <label class="mb10"><?php echo lang('attempt') ?>* :</label>
                        <br>
                        <label class="pad-top10" id="cl_limited_radio">
                            <input type="radio" name="cl_limited" <?php echo (($lecture['cl_limited_access']==0)?$checked:''); ?> value="0"> <?php echo lang('unlimited') ?>
                        </label>
                        <label>
                            <input type="radio" name="cl_limited" <?php echo (($lecture['cl_limited_access'] > 0)?$checked:''); ?>  value="1" > <?php echo lang('limited') ?>
                        </label>
                    </div>

                    <div class="col-sm-4" id="cl_limited_access_wrapper" style="display:<?php echo ($lecture['cl_limited_access']>0)?'block':'none'; ?>">
                        <div class="form-group">
                            <label class="mb10"><?php echo lang('maximum_attempt') ?>* :</label>
                            <input type="text" class="form-control" id="cl_limited_access" maxlength="3" name="cl_limited_access" placeholder="Eg: 5" value="<?php echo $lecture['cl_limited_access'] ?>">
                        </div>
                    </div>
                     <div class="col-sm-4">
                        <div class="form-group">
                            <label class="mb10"><?php echo lang('assesment_duration') ?>* :</label>
                            <div class="input-group">
                                <input type="text" class="form-control" maxlength="3" id="assesment_duration" value="<?php echo isset($lecture['assesment']['a_duration'])?$lecture['assesment']['a_duration']:''; ?>" placeholder="Eg: 160">
                                <div class="input-group-addon">MIN</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- !.Access and maximum access count -->
                
                <?php //echo '<pre>'; print_r($lecture);die; ?>
                <!-- Access and maximum access count -->
                <div class="form-group clearfix row correct-lbl">
                     <div class="col-sm-4">
                        <div class="form-group">
                            <label class="mb10">Pass Percentage *:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" maxlength="3" id="pass_percentage" value="<?php echo isset($lecture['assesment']['a_pass_percentage'])?$lecture['assesment']['a_pass_percentage']:''; ?>" placeholder="Eg: 77">
                                <div class="input-group-addon">MIN</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- !.Access and maximum access count -->




                <div class="form-group clearfix">
                    <div class="checkbox"><label><input type="checkbox" id="cl_sent_mail_on_lecture_creation" value="1" <?php echo (($lecture['cl_sent_mail_on_lecture_creation'] == 1)?$checked:''); ?>><span class="ap_cont chk-box"><?php echo lang('send_mail_on_completed') ?>.</span></label></div>
                </div>
                
                <div class="form-group clearfix">
                    <div class="checkbox"><label><input type="checkbox" id="a_show_categories" name="a_show_categories" value="1" <?php echo (isset($lecture['assesment']['a_show_categories'])&&$lecture['assesment']['a_show_categories']=='1')?$checked:''; ?> ><span class="ap_cont chk-box">Show Categories</span><br /><small>(If you select this option, categories tab will display in assessment page)</small></label></div>
                </div>

            <div class="pull-right">
                <input type="hidden" id="lecture_id" value="<?php echo $lecture['id'] ?>">
                <input type="hidden" id="course_id" value="<?php echo $lecture['cl_course_id'] ?>">
                <button type="button" onclick="saveAssesmentLecture()" class="btn btn-green"><?php echo lang('save') ?></button>
                <a href="<?php echo admin_url('coursebuilder/lecture/'.$lecture_id) ?>" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></a>
            </div>

        </div>
        <!-- !.Form elements Left side :: lecture Documents-->


    </div>  <!-- !.Left side bar section -->
    
<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/source.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
<script src="<?php echo assets_url() ?>js/jquery.form-validator.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/html.js"></script>
<script>
    $(document).ready(function(e) {
    $('#redactor,#redactor_invite, #explanation, #question, .question-text-input, #directions, #lecture_instruction').redactor({
        minHeight: 250,
        maxHeight: 250,
        imageUpload: admin_url+'configuration/redactore_image_upload',
        alignment:true,
        formattingAdd: {
  "align-left": {
    "title": "Align left",
    "args": ["p","class","align-left"],
  },
  "align-right": {
    "title": "Align right",
    "args": ["p","class","align-right"],
  },
  "align-center": {
    "title": "Align center",
    "args": ["p","class","align-center"],
  },
  "align-justify": {
    "title": "Justify",
    "args": ["p","class","align-justify"],
  },
},
        plugins: ['table', 'alignment', 'source'],
        callbacks: {
            imageUploadError: function(json, xhr)
            {
                 var erorFileMsg = "This file type is not allowed. upload a valid image.";
                 $('survey_form').prepend(renderPopUpMessage('error', erorFileMsg));
                 scrollToTopOfPage();
                 return false;
            }
        }  
    });
});
</script>
<?php 
function get_instruction()
{
    return '<div id="dvInstruction">
            <p class="headings-altr" style="text-align:left;"><strong>General Instructions:</strong></p>
            <ol class="header-child-alt">
            <li>The clock has been set at the server and the countdown timer at the top right corner of your screen will display the time remaining for you to complete the exam. When the clock runs out the exam ends by default - you are not required to end or submit your exam.</li>
            <li>The question palette at the right of screen shows one of the following statuses of each of the questions numbered:
            <table style="margin-bottom: 3px;"><tbody>
            <tr><td>You have not visited the question yet. ( In White Color )</td>
            <td style="padding-left: 7px;"><div class="gray" style="width: 20px;height: 20px;border-radius: 4px;"></div></td></tr>
            </tbody>
            </table>
            <table style="margin-bottom: 3px;"><tbody>
            <tr><td>You have not answered the question. ( In Red Color )</td>
            <td style="padding-left: 7px;"><div class="red" style="width: 20px;height: 20px;border-radius: 4px;"></div></td>
            </tr>
            </tbody>
            </table>
            <table style="margin-bottom: 3px;"><tbody>
            <tr><td>You have answered the question. ( In Green Color )</td><td style="padding-left: 7px;"><div class="green" style="width: 20px;height: 20px;border-radius: 4px;"></div></td>
            </tr>
            </tbody>
            </table>
            <table style="margin-bottom: 3px;"><tbody>
            <tr><td>You have marked the for review.( In Pink Color ) </td><td style="padding-left: 7px;"><div class="purpal" style="width: 20px;height: 20px;border-radius: 4px;"></div></td>
            </tr>
            </tbody>
            </table>
            </li>
            <li>&nbsp;</li>
            <li>The Marked for Review status simply acts as a reminder that you have set to look at the question again. <em>If an answer is selected for a question that is Marked for Review, the answer will be considered in the final evaluation.</em></li>
            </ol>
            <p class="headings-altr"><strong>Navigating to a question :</strong></p>
            <ol start="5" class="header-child-alt">
            <li>To select a question to answer, you can do one of the following:
            <ol type="a">
            <li>Click on the question number on the question palette at the right of your screen to go to that numbered question directly. Note that using this option does NOT save your answer to the current question.</li>
            <li>Click on Save and Next to save answer to current question and to go to the next question in sequence.</li>
            <li>Click on Mark for Review and Next to save answer to current question, mark it for review, and to go to the next question in sequence.</li>
            </ol>
            </li>
            <li>You can view the entire paper by clicking on the <strong>Question Paper</strong> button.</li>
            </ol>
            <p class="headings-altr"><strong>Answering questions :</strong></p>
            <ol start="7"  class="header-child-alt">
            <li>For multiple choice type question :
            <ol type="a">
            <li>To select your answer, click on one of the option buttons</li>
            <li>To change your answer, click the another desired option button</li>
            <li>To save your answer, you MUST click on <strong>Save & Next</strong></li>
            <li>To deselect a chosen answer, click on the chosen option again or click on the <strong>Clear Response</strong> button.</li>
            <li>To mark a question for review click on <strong>Mark for Review & Next</strong>.&nbsp;</li>
            </ol>
            </li>
            <li>For a numerical answer type question
            <ol type="a">
            <li>To enter a number as your answer, use the virtual numerical keypad</li>
            <li>A fraction (eg. 0.4 or -0.4) can be entered as an answer ONLY with \'0\' before the decimal point</li>
            <li>To save your answer, you MUST click on <strong>Save & Next</strong></li>
            <li>To clear your answer, click on the<strong> Clear Response </strong>button</li>
            </ol>
            </li>
            <li>To change an answer to a question, first select the question and then click on the new answer option followed by a click on the <strong>Save & Next</strong> button.</li>
            <li>Questions that are saved or marked for review after answering will ONLY be considered for evaluation.</li>
            </ol>
            <p class="headings-altr"><strong>Navigating through sections :</strong></p>
            <ol start="11" class="header-child-alt">
            <li>Sections in this question paper are displayed on the top bar of the screen. Questions in a section can be viewed by clicking on the section name. The section you are currently viewing is highlighted.</li>
            <li>After clicking the <strong>Save & Next</strong> button on the last question for a section, you will automatically be taken to the first question of the next section.</li>
            <li>You can move the mouse cursor over the section names to view the status of the questions for that section.</li>
            <li>You can shuffle between sections and questions anytime during the examination as per your convenience.</li>
            </ol></div>';
}
?>