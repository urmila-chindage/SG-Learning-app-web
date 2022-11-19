<!DOCTYPE html>
<html>
<!-- head start-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <title><?php echo isset($title) ? $title : config_item('site_name'); ?></title>

    <?php

        if(file_exists(favicon_upload_path().'/favicon.png'))
        {
            $favicon = base_url(favicon_upload_path().'favicon.png?v='.rand());
        }
        else
        {
            $favicon = base_url('favicon.png').'?v='.rand();
        }
    ?>
    <link rel="icon" href="<?php echo $favicon; ?>">
    <!-- Customized bootstrap css library -->
    <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/bootstrap.css">

    <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/adminstyle.css">

    <!-- DATE PICKER PLUGIN ADDED  --> <!-- START  -->
    <link rel="stylesheet" href="<?php echo assets_url() ?>css/datepicker.min.css">
    <link rel="stylesheet" href="<?php echo assets_url() ?>css/jquery-ui.css">
    <script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.min.js"></script>

    <link rel="stylesheet" href="<?php echo assets_url() ?>css/timepicker.css">
    <style>
        .bulder-content-noaccess{
                padding: 20px 20px 20px 20px;
        }
    </style>
    <script type="text/javascript" src="<?php echo assets_url() ?>js/system.js"></script>
    <script>
        var admin_url           = '<?php echo admin_url() ?>';
        var __uploads_url       = '<?php echo uploads_url() ?>';
        var __course_id         = '<?php echo $id ?>';
        var assets_url          = '<?php echo assets_url() ?>';
        var __total_sections    = parseInt('<?php echo $total_sections ?>');
        var __total_lecture     = parseInt('<?php echo $total_lecture ?>');
        var cb_has_lecture_image = '<?php echo $course['cb_has_lecture_image'] ?>';
    </script>
    <?php
$has_s3         = $this->settings->setting('has_s3');
$has_s3_ofabee  = $this->settings->setting('has_s3_ofabee');
$upload_js      = '<script type="text/javascript" src="' . assets_url() . 'js/file_server_upload.js"></script>';
if (  $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value']  ) {
    $upload_js  = '<script type="text/javascript" src="' . assets_url() . 'js/S3BlobUploader.js"></script>';
    $upload_js .= '<script type="text/javascript" src="' . assets_url() . 'js/file_s3_upload.js"></script>';
}
else
if( $has_s3_ofabee['as_superadmin_value'] && $has_s3_ofabee['as_siteadmin_value'] )
{
    $upload_js  = '<script type="text/javascript" src="' . assets_url() . 'js/S3BlobUploader.js"></script>';
    $upload_js .= '<script type="text/javascript" src="' . assets_url() . 'js/file_ofabee_s3_upload.js"></script>';  
}

if ($has_s3["as_superadmin_value"] && $has_s3["as_siteadmin_value"]) 
{
 $has_s3_flag = 1;
} else {
 $has_s3_flag = 0;
}


?>
    <?php
$has_dropbox = $this->settings->setting('has_dropbox');
$dropbox_js  = '';
if ($has_dropbox['as_superadmin_value'] && $has_dropbox['as_siteadmin_value']) {
 $secret_key = $has_dropbox['as_setting_value']['setting_value']->secret_key;
 $dropbox_js = '<script type="text/javascript" src="https://www.dropbox.com/static/api/2/dropins.js" id="dropboxjs" data-app-key="' . $secret_key . '"></script>';
 $dropbox_js .= '<script type="text/javascript" src="' . assets_url() . 'js/dropbox.js"></script>';
}
?>
<script type="text/javascript">
    var has_s3 = "<?php echo $has_s3_flag; ?>";
</script>
<style>
    .upload-file-name {
        background: rgb(255, 255, 255) none repeat scroll 0 0;
        border: 0 none;
        cursor: pointer;
        height: 100%;
        left: 0;
        margin: 0;
        padding: 8px 12px;
        position: absolute;
        width: calc(100% - 124px);
        z-index: 0;
    }
    .list-recorded_videos optgroup{ text-indent: 15px;}
    .noselect{user-select: none;}
    .active-section, .Inactive-section{float: unset;}
    .section-control, .lecture-control {float: unset;margin: 0px 10px;}
    .Inactive-section{height:unset;}
</style>
</head>
<!-- head end-->
<!-- body start-->

<body>
<?php 
    $accepted_files = array('mp3', 'mp4','mpeg','avi','doc', 'docx', 'pdf','pptx', 'xlsx', 'xls', 'zip');
    // $accepted_files = array('mp3', 'mp4', 'flv', 'avi', 'f4v', 'doc', 'docx', 'pdf', 'ppt', 'pptx', 'odt', 'zip','xlsx','xls','ods','odp');
    $recommended_files = array('mp4', 'mp3', 'doc', 'docx', 'pptx', 'pdf', 'xlsx', 'xls');
?>
    <!-- Humburger menu for ipad like devices -->

<div id="nav-icon1">
  <span></span>
  <span></span>
  <span></span>
</div>




<!--
        ###################################################################
            ********  Whole Modal popups Sections are here ************
        ###################################################################
  -->

    <!-- Modal pop up contents :: Upload a lecture -->
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="upload-lecture" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo strtoupper(lang('upload_lecture')) ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label id="attached_file_name"></label>
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('lecture_title') ?> *:</label>
                        <input type="text" maxlength="80" id="lecture_name" placeholder="eg: Become an Algebra Master" class="form-control">
                    </div>
                    <!-- Click to get add the cateogry to the select box -->
                     <div class="form-group add-category clearfix">
                            <div class="add-selectn alignment-order">
                                <label><?php echo lang('section') ?> *:</label>
                                <select class="form-control" id="section_id">
                                </select>
                                <input type="text" id="section_name" class="form-control" maxlength="60" placeholder="eg: Catalog" style="display:none;" aria-describedby="basic-addon2">
                            </div>
                            <div class="add-btn alignment-order">
                                <label>Or</label>
                                <a href="javascript:void(0)" class="btn btn-green" id="create_new_section"><?php echo lang('add_new_section') ?></a>
                                <a href="javascript:void(0)" style="display:none;" class="btn btn-danger" id="create_new_section_cancel"><?php echo lang('cancel') ?></a>
                            </div>
                    </div>
                     <!-- !.Click to get add the cateogry to the select box -->
                    <div class="form-group clearfix">
                        <label><?php echo lang('lecture_description') ?> :</label>
                        <textarea class="form-control" onkeyup="validateMaxLength(this.id)" maxlength="1000" id="lecture_description"></textarea>
                        <label class="pull-right" id="lecture_description_char_left">1000 <?php echo lang('charactors_left') ?></label>
                    </div>
                    <div class="clearfix progress-custom" id="percentage_bar">
                        <div class="progress width100">
                            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" id="percentage_width" style="width: 0%;">
                                  <span class="sr-only"></span>
                            </div>
                        </div>
                        <span class=""><?php echo lang('uploading') ?>...<b id="percentage_count" class="percentage-text">0%</b></span>
                    </div>
                    <div class="checkbox"><label><input type="checkbox" id="sent_mail_on_lecture_creation" value="1"><span class="ap_cont chk-box"><?php echo lang('send_mail_on_lecture_publish') ?></span></label></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo strtoupper(lang('cancel')) ?></button>
                    <button type="button" class="btn btn-green" id="save_lecture" ><?php echo strtoupper(lang('upload')) ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: Upload a lecture -->

<?php $is_first_view = $this->auth->get_current_admin('us_course_first_view');?>

<?php /* ?>
<!-- Modal for video help introduction -->
<div class="modal fade" id="help-intro" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
<div class="modal-dialog modal-lg" role="document">
<div class="col-sm-12">
<div class="pull-right"><h3 class="white">How to build a Course</h3></div>
<div class="pull-left"><h3 class="white">1 Minute Video Help.</h3></div>
</div>
<div class="modal-body">
<!-- https://www.youtube.com/embed/ufzghLhpcTU
<iframe width="100%" height="500px" src="https://www.youtube.com/embed/ufzghLhpcTU" frameborder="0" allowfullscreen></iframe>
-->
<div id="help_content"></div>
</div>
<div class="col-sm-12" id="help_text_close_btn">
<button type="button" class="btn btn-blue pull-right" data-dismiss="modal">Close</button>
<!-- <?php echo ($is_first_view==0)?'<button type="button" class="btn btn-blue pull-right" data-dismiss="modal">Close</button>':''; ?> -->
</div>

</div>
</div>
<!--   !.Modal for video help introduction   -->
<?php */?>


<!-- Modal pop up contents:: Delete Section popup-->
    <div class="modal fade alert-modal-new" id="deleteSection" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                <div class="modal-body">
                    <span><i class="icon icon-attention-alt"></i></span>
                    <div class="form-group">
                        <b id="delete_header_text"></b>
                        <p class="m0" id="delete_message"></p>
                        <p><?php echo lang('action_cannot_undone') ?></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-green" data-dismiss="modal"><?php echo strtoupper(lang('cancel')) ?></button>
                    <button type="button" class="btn btn-red" id="delete_section_ok"><?php echo strtoupper(lang('yes')) ?>, <?php echo strtoupper(lang('delete')) ?> !</button>
                </div>
            </div>
        </div>
    </div>
<!-- !.Modal pop up contents :: Delete Section popup-->


    <!-- Modal pop up contents :: Create Assesment -->
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="assesment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo lang('create') . ' ' . lang('assesment'); ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><?php echo lang('assesment') ?> <?php echo lang('title') ?> *:</label>
                        <input type="text" maxlength="80" placeholder="eg: Problems in Decimal equation" id="assesment_name" class="form-control">
                    </div>
                    <!-- Click to get add the cateogry to the select box -->
                     <div class="form-group add-category clearfix">
                            <div class="add-selectn alignment-order">
                                <label><?php echo lang('section') ?> *:</label>
                                <select class="form-control" id="section_id_assesment">
                                </select>
                                <input type="text" aria-describedby="basic-addon2" maxlength="60" placeholder="eg: Catalog" class="form-control" id="section_name_assesment">
                            </div>
                            <div class="add-btn alignment-order">
                                <label>Or</label>
                                <a href="javascript:void(0)" class="btn btn-green" id="create_new_section_assesment"><?php echo lang('add_new_section') ?></a>
                                <a href="javascript:void(0)" class="btn btn-danger" id="create_new_section_cancel_assesment"><?php echo lang('cancel') ?></a>
                            </div>
                    </div>
                     <!-- !.Click to get add the cateogry to the select box -->
                    <div class="form-group">
                        <label><?php echo lang('assesment') . ' ' . lang('description') ?> :</label>
                        <textarea onkeyup="validateMaxLength(this.id)" maxlength="1000" class="form-control" id="assesment_description" placeholder="eg : This is an assessment for practice in problems."></textarea>
                        <label class="pull-right" id="assesment_description_char_left">1000 <?php echo lang('characters_left') ?></label>
                    </div>
                    <!--div class="form-group">
                        <div class="checkbox"><label><input type="checkbox" checked="checked" id="a_show_categories" value="1"><span class="ap_cont chk-box">Show Categories</span><br /><small>(If you select this option, categories tab will display in assessment page)</small></label></div>
                    </div-->
                    <!--div class="form-group">
                        <div class="checkbox"><label><input type="checkbox" id="sent_mail_on_assesment_creation" value="1"><span class="ap_cont chk-box">< ?php echo lang('send_mail_on_completed') ?>;</span></label></div>
                    </div-->
                    <div class="checkbox"><label><input type="checkbox" id="sent_mail_on_assesment_creation" value="1"><span class="ap_cont chk-box"><?php echo lang('send_mail_on_lecture_publish') ?></span></label></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo strtoupper(lang('cancel')) ?></button>
                    <a type="button" class="btn btn-green" onclick="createAssesmentConfirmed()" href="javascript:void(0)" id="createAssesmentConfirmed"><?php echo strtoupper(lang('create')) ?></a>
                </div>
            </div> 
        </div>
    </div>
    <!-- Modal pop up contents :: Create Assesment -->

    <!-- Modal pop up contents :: Create Descriptive Test -->

    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="descriptive" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo lang('create') . ' ' . lang('descriptive_test'); ?></h4>
                </div>
                <div class="modal-body">
					<div class="descriptive_form form-group"></div>
                    <div class="form-group file-descriptive">
                        <label id="attached_file_descriptive"></label>
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('descriptive_test') ?> <?php echo lang('title') ?> *:</label>
                        <input type="text" maxlength="80" placeholder="eg: Practice for essay writing." id="descriptive_test_name" class="form-control">
                    </div>
                     <!-- Click to get add the cateogry to the select box -->
                     <div class="form-group add-category clearfix">
                        <div class="add-selectn alignment-order">
                            <label><?php echo lang('section') ?> *:</label>
                            <select class="form-control" id="section_id_descriptive">
                            </select>
                            <input type="text" aria-describedby="basic-addon2" placeholder="eg: Catalog" class="form-control" id="section_name_descriptive">
                        </div>
                        <div class="add-btn alignment-order">
                            <label>Or</label>
                            <a href="javascript:void(0)" class="btn btn-green" id="create_new_desciptive_test"><?php echo lang('add_new_section') ?></a>
                            <a href="javascript:void(0)" class="btn btn-danger" id="create_new_desciptive_test_cancel"><?php echo lang('cancel') ?></a>
                        </div>
                    </div>
                    <!-- !.Click to get add the cateogry to the select box -->
                    <div class="form-group clearfix">

                        <label><?php echo lang('descriptive_test') . ' ' . lang('description') ?> :</label>
                        <textarea onkeyup="validateMaxLength(this.id)" maxlength="1000" class="form-control" id="descriptive_description" placeholder="eg : This is an assessment to write essay."></textarea>
                        <label class="pull-right" id="descriptive_description_char_left">1000 <?php echo lang('characters_left') ?></label>

                    </div>
                    <div class="form-group clearfix">
                        <div class="row">
                            <div class="col-sm-4">
                                 <label><?php echo lang('total_mark') ?> *:</label>
                                <input type="number"  oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="4" onkeyup="return isNumber(event)" placeholder="eg: 100" min="1" max="999" id="total_mark" class="form-control">
                            </div>
                            <div class="col-sm-4">
                               <label>Submission date *:</label>
                                <input placeholder="dd-mm-yyyy" type="text" placeholder="" id="descriptive_submission_date" class="form-control" readonly="" style="background: #fff;">
                            </div>
                            <div class="col-sm-4">
                                <label>Words Limit:</label>
                                <input type="number"  oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="8" onkeypress="return isNumber(event)" min="1" maxlength="80" placeholder="eg: 1000" id="descriptive_words_limit" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Click to get add the cateogry to the select box -->
                    <div class="checkbox"><label><input type="checkbox" id="sent_mail_on_descriptive_creation" value="1"><span class="ap_cont chk-box"><?php echo lang('send_mail_on_lecture_publish') ?></span></label></div>

                </div>
                <div class="modal-footer">
                     <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo strtoupper(lang('cancel')) ?></button>
                     <a type="button" class="btn btn-green" onclick="createDescriptiveTest()" href="javascript:void(0)" id="createDescriptiveTest"><?php echo strtoupper(lang('create')) ?></a>
                </div>

            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: Create Descriptive Test -->

    <!-- Modal popup contents :: Create Scorm -->
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="scorm_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel">UPLOAD SCORM</h4>
                </div>
                <div class="modal-body">
					<div class="scorm_form form-group"></div>
                    <div class="form-group file-descriptive">
                        <label id="attached_file_scorm"></label>
                    </div>
                    <div class="form-group">
                        <label> Scorm Title *:</label>
                        <input type="text" maxlength="80" placeholder="eg: Practice for essay writing." id="scorm_name" class="form-control">
                    </div>
                    <!-- Click to get add the cateogry to the select box -->
                    <div class="form-group add-category clearfix">
                        <div class="add-selectn alignment-order">
                            <label><?php echo lang('section') ?> *:</label>
                            <select class="form-control" id="section_id_scorm">
                            </select>
                            <input type="text" aria-describedby="basic-addon2" placeholder="eg: Catalog" class="form-control" id="section_name_scorm">
                        </div>
                        <div class="add-btn alignment-order">
                            <label>Or</label>
                            <a href="javascript:void(0)" class="btn btn-green" id="create_new_section_scorm"><?php echo lang('add_new_section') ?></a>
                            <a href="javascript:void(0)" class="btn btn-danger" id="create_new_section_scorm_cancel"><?php echo lang('cancel') ?></a>
                        </div>
                    </div>
                    <div class="form-group clearfix">
                        <label>Lecture Description:</label>
                        <textarea class="form-control" onkeyup="validateMaxLength(this.id)" maxlength="1000" id="scorm_description"></textarea>
                        <label class="pull-right" id="scorm_description_char_left">1000 <?php echo lang('charactors_left') ?></label>
                    </div>
                    <div class="form-group clearfix">
                        <label>Upload File :</label>
                        <div class="fle-upload">
                            <label class="fle-lbl">BROWSE</label>
                            <input accept=".zip" type="file" name="file" id="scorm_file" class="form-control upload">
                            <input value="" readonly="" class="form-control upload-file-name" id="upload_scorm" type="text">
                            <!-- <span class="upload-clear">&times;</span> -->
                        </div>
                    </div>
                    <div class="clearfix progress-custom" id="scorm_percentage_bar">
                        <div class="progress width100">
                            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" id="percentage_width" style="width: 10%;">
                                  <span class="sr-only"></span>
                            </div>
                        </div>
                        <span class=""><?php echo lang('uploading') ?>...<b id="percentage_count" class="percentage-text">0%</b></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo strtoupper(lang('cancel')) ?></button>
                    <a type="button" class="btn btn-green" onclick="createScormConfirm()" href="javascript:void(0)"><?php echo strtoupper(lang('create')) ?></a>
                </div>

            </div>
        </div>
    </div>
    <!-- Modal popup contents :: Create Scorm -->
    <!-- Modal pop up contents :: Create html -->
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="htmlcode" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo lang('create_html_content') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><?php echo lang('lecture_title') ?> *:</label>
                        <input type="text" maxlength="80" placeholder="eg: Introdcution to inline css." id="html_name" class="form-control">
                    </div>
                    <!-- Click to get add the cateogry to the select box -->
                     <div class="form-group add-category clearfix">
                            <div class="add-selectn alignment-order">
                                <label><?php echo lang('section') ?> *:</label>
                                <select class="form-control" id="section_id_html">
                                </select>
                                <input type="text" aria-describedby="basic-addon2" maxlength="60" placeholder="eg: Catalog" class="form-control" id="section_name_html">
                            </div>
                            <div class="add-btn alignment-order">
                                <label>Or</label>
                                <a href="javascript:void(0)" class="btn btn-green" id="create_new_section_html"><?php echo lang('add_new_section') ?></a>
                                <a href="javascript:void(0)" class="btn btn-danger" id="create_new_section_cancel_html"><?php echo lang('cancel') ?></a>
                            </div>
                    </div>
                     <!-- !.Click to get add the cateogry to the select box -->
                    <div class="form-group">
                        <label><?php echo lang('html_description') ?> :</label>
                        <textarea onkeyup="validateMaxLength(this.id)" maxlength="1000" id="html_description" class="form-control" placeholder="eg : This is an html lecture to study inline css."></textarea>
                        <label class="pull-right" id="html_description_char_left">1000 <?php echo lang('characters_left') ?></label>
                    </div>
                    <div class="checkbox"><label><input type="checkbox" id="sent_mail_on_htmlcode_creation" value="1"><span class="ap_cont chk-box"><?php echo lang('send_mail_on_lecture_publish') ?></span></label></div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo strtoupper(lang('cancel')) ?></button>
                    <button type="button" class="btn btn-green" onclick="createHtmlConfirmed()" id="createHtmlConfirmed"><?php echo strtoupper(lang('create')) ?></button>

                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: Create html -->


    <!-- Modal pop up contents :: Create Live Lecture -->
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="livelecture" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo lang('schedule_live_lecture') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><?php echo lang('lecture_title') ?> *:</label>
                        <input type="text" maxlength="80" placeholder="eg: Live class for motivation." class="form-control" id="live_lecture_name">
                    </div>
                    <!-- Click to get add the cateogry to the select box -->
                     <div class="form-group add-category clearfix">
                            <div class="add-selectn alignment-order">
                                <label><?php echo lang('section') ?> *:</label>
                                <select class="form-control" id="section_id_live_lecture">
                                </select>
                                <input type="text" aria-describedby="basic-addon2" maxlength="60" placeholder="eg: Catalog" class="form-control" id="section_name_live_lecture">
                            </div>
                            <div class="add-btn alignment-order">
                                <label>Or</label>
                                <a href="javascript:void(0)" class="btn btn-green" id="create_new_section_live_lecture"><?php echo lang('add_new_section') ?></a>
                                <a href="javascript:void(0)" class="btn btn-danger" id="create_new_section_cancel_live_lecture"><?php echo lang('cancel') ?></a>
                            </div>
                    </div>
                     <!-- !.Click to get add the cateogry to the select box -->
                    <div class="form-group clearfix">
                        <label><?php echo lang('html_description') ?> :</label>
                        <textarea onkeyup="validateMaxLength(this.id)" id="live_lecture_description" maxlength="1000" class="form-control" placeholder="eg : A live class for motivate students going to face exam."></textarea>
                        <label class="pull-right" id="live_lecture_description_char_left">1000 <?php echo lang('characters_left') ?></label>
                    </div>

                    <div class="form-group clearfix">
                        <div class="alignment-order">
                            <label> Studio *:</label>
                            <?php if (!empty($studios)) {
 ?>
                                <select class="form-control" id="studio_list">
                                <option value="">SELECT</option>
                                <?php
foreach ($studios as $studio) {
  ?>
                                    <option value="<?php echo $studio['id'] ?>"> <?php echo $studio['st_dial_in_number'] . ' - ' . $studio['st_name'] ?> </option>
                                    <?php
}
 ?>
                                </select>
                                <?php
}?>
                        </div>
                    </div>

                    <div class="form-group clearfix">
                        <div class="row">
                            <div class="col-sm-4">
                                <label><?php echo lang('date') ?> *:</label>
                                <input placeholder="dd/mm/yyyy" readonly style="background-color:white" type="text" id="schedule_date" class="form-control">
                            </div>
                            <div class="col-sm-4">
                                <label><?php echo lang('start_time') ?> *:</label>
                                <div class="input-group" id="divtime">
                                    <input type="text" class="form-control" id="live_lecture_start_time" placeholder="eg: 10.45" onkeydown="event.preventDefault()">
                                        <div class="input-group-addon" id="live_lecture_start_time_noon">AM <!--<span class="my-italic font-blue">(IST)</span>--></div>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <label><?php echo lang('duration') ?> *:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" maxlength="3" id="live_lecture_duration" placeholder="eg: 160">
                                    <div class="input-group-addon">Min</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="checkbox"><label><input type="checkbox" id="sent_mail_on_live_lecture_creation" value="1"><span class="ap_cont chk-box"><?php echo lang('send_mail_on_lecture_publish') ?></span></label></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></button>
                    <button type="button" class="btn btn-green" onclick="scheduleLiveLectureConfirmed()" id="scheduleLiveLectureConfirmed"><?php echo lang('schedule') ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: Live Lecture -->

     <!-- Modal pop up contents :: Create Youtube -->
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="youtube" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo lang('import_from_youtube_or_vimeo') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><?php echo lang('lecture_title') ?> *:</label>
                        <input type="text" maxlength="80" placeholder="eg: Video lecture for Portfolio management." class="form-control" id="youtube_name">
                    </div>
                    <!-- Click to get add the cateogry to the select box -->
                     <div class="form-group add-category clearfix">
                            <div class="add-selectn alignment-order">
                                <label><?php echo lang('section') ?> *:</label>
                                <select class="form-control" id="section_id_youtube">
                                </select>
                                <input type="text" id="section_name_youtube" maxlength="60" class="form-control" placeholder="eg: Catalog" aria-describedby="basic-addon2">
                            </div>
                            <div class="add-btn alignment-order">
                                <label>Or</label>
                                <a class="btn btn-green" id="create_new_section_youtube"><?php echo lang('add_new_section') ?></a>
                                <a id="create_new_section_cancel_youtube" class="btn btn-danger" href="javascript:void(0)"><?php echo lang('cancel') ?><ripples></ripples></a>
                            </div>
                    </div>
                     <!-- !.Click to get add the cateogry to the select box -->
                    <div class="form-group clearfix">
                        <label><?php echo lang('lecture_description') ?> :</label>
                        <textarea onkeyup="validateMaxLength(this.id)" id="youtube_description" maxlength="1000" class="form-control" placeholder="eg : This is a video lecture describes how to manage portfolio."></textarea>
                        <label class="pull-right" id="youtube_description_char_left">1000 <?php echo lang('characters_left') ?></label>
                    </div>

                    <div class="form-group clearfix">
                        <label><?php echo ucfirst(lang('youtube')." / ".lang('vimeo')) ?> <?php echo strtoupper(lang('url')) ?> *:</label>
                        <input type="text" placeholder="https://www.youtube.com/" id="youtube_url" class="form-control">
                    </div>
                    <div class="checkbox"><label><input type="checkbox" id="sent_mail_on_youtube_creation" value="1"><span class="ap_cont chk-box"><?php echo lang('send_mail_on_lecture_publish') ?></span></label></div>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo strtoupper(lang('cancel')) ?></button>
                    <button type="button" id="create_btn" class="btn btn-green" onclick="createYoutubeConfirmed()" >CREATE</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: Create Youtube -->

    <!-- Modal pop up contents :: Create Import Content -->
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="importContent" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo lang('import_lecture') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Select Course *:</label>
                        <div class="add-selectn alignment-order">
                            <select class="form-control" id="course_id_import_content" onchange="loadSectionsAndLecures(this.value)">
                            </select>
                        </div>
                    </div>
                    <!-- Click to add the lecture to the select box -->
                     <div class="form-group add-category clearfix">
                         <label><?php echo lang('select_lectures') ?> *:</label>
                            <div class="inside-box">
                                <ul class="addlectre" id="import_section_list">
                                </ul>
                            </div>
                    </div>
                     <!-- Click to add the lecture to the select box -->

                     <!-- Click to get add the cateogry to the select box -->
                     <div class="form-group add-category clearfix">
                            <div class="add-selectn alignment-order">
                                <label><?php echo lang('import_destination_section') ?> *:</label>
                                <select class="form-control" id="section_id_import_content">
                                </select>
                                <input type="text" id="section_name_import_content" class="form-control" placeholder="eg: Catalog" aria-describedby="basic-addon2">
                            </div>
                            <div class="add-btn alignment-order">
                                <label>Or</label>
                                <a class="btn btn-green" id="create_new_section_import_content" href="javascript:void(0)"><?php echo lang('add_new_section') ?></a>
                                <a id="create_new_section_cancel_import_content" class="btn btn-danger" href="javascript:void(0)"><?php echo lang('cancel') ?><ripples></ripples></a>
                            </div>
                    </div>
                     <!-- !.Click to get add the cateogry to the select box -->


                    <!-- <div class="form-group">
                        <div class="checkbox"><label><input type="checkbox" value="1" id="sent_mail_on_import_creation"><span class="ap_cont chk-box"><?php echo lang('send_mail_on_completed') ?></span></label></div>
                    </div> -->
                    <p style="color: #F44336;">* Batch override & Access restriction will not be reflected in the imported content</p>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></button>
                    <button id="importcontent_btn" type="button" class="btn btn-green" onclick="importContentConfirm()"><?php echo lang('import') ?></button>
                    
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: Create Import Content -->


    <!-- Modal pop up contents :: Create Import Recorded Videos -->
    <?php /*
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="recordedVideos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel">Import Recorded Videos</h4>
                </div>
                <div class="modal-body" >
                    <div class="form-group" style="display:none;">
                        <label>Select Space *:</label>
                        <div class="add-selectn alignment-order">
                            <select class="form-control" id="cospace_id">
                                <option selected="selected" value="9e293046-d970-49e1-8820-ea3423ffd07c">SGlearningapp Scheduler Spaces</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Record Title *:</label>
                        <input maxlength="80" placeholder="eg: Introdcution to inline css." id="record_name" class="form-control" type="text">
                    </div>

                    <!-- Click to add the lecture to the select box -->
                    <div class="form-group add-category clearfix">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Filter by Recorded Date</label>
                                <select class="form-control" id="cisco_recorded_date">
                                    <option value="">Filter Recording by Date</option>
                                </select>
                            </div>
                            <div class="col-sm-8">
                                <label>Choose Recorded Video *:</label>
                                <select class="form-control list-recorded_videos" id="recorder_video_list">
                                </select>
                            </div>
                        </div>
                    </div>
                     <!-- Click to add the lecture to the select box -->

                     <!-- Click to get add the cateogry to the select box -->
                     <div class="form-group add-category clearfix">
                            <div class="add-selectn alignment-order">
                                <label><?php echo lang('import_destination_section') ?> *:</label>
                                <select class="form-control" id="section_id_recorded_video">
                                </select>
                                <input type="text" id="section_name_recorded_video" class="form-control" placeholder="eg: Catalog" aria-describedby="basic-addon2">
                            </div>
                            <div class="add-btn alignment-order">
                                <label>Or</label>
                                <a class="btn btn-green" id="create_new_section_recorded_video" href="javascript:void(0)"><?php echo lang('add_new_section') ?></a>
                                <a id="create_new_section_cancel_recorded_video" class="btn btn-danger" href="javascript:void(0)"><?php echo lang('cancel') ?><ripples></ripples></a>
                            </div>
                    </div>
                     <!-- !.Click to get add the cateogry to the select box -->


                     <div class="form-group" style="display: none;">
                        <div class="checkbox"><label><input type="checkbox" value="1" id="sent_mail_on_import_creation"><span class="ap_cont chk-box"><?php echo lang('send_mail_on_completed') ?></span></label></div>
                    </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></button>
                    <button type="button" class="btn btn-green" id="recorded_video_trigger_btn" onclick="recordedVideosConfirm()">CREATE</button>

                </div>
            </div>
        </div>
    </div>
    */ ?>
    <!-- Modal pop up contents :: Create Import Content -->


    <!-- Modal pop up contents :: Create new section popup-->
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="addsection" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo lang('create_new_section') ?></h4>
                </div>
                <div class="modal-body"> 
                    <div class="form-group">
                        <label><?php echo lang('section_title') ?>*:</label>
                        <input type="text" id="section_name_create" placeholder="eg: Operations in Algebra" class="form-control">
                    </div>

                    <div style="<?php echo ($course['cb_has_lecture_image'] == 1) ? "display:block" : "display:none" ?>" >
                        <form  id="save_section_image" method="POST" enctype="multipart/form-data" >
                            <div style="text-align: center;margin: 5px 0px;"><?php echo lang('section_image') ?></div>
                                <div class="section-create-wrapper text-center">
                                    <div class="section-card-container">
                                        <div class="section-card">
                                            <img id="section_logo" data-id="<?php ?>" class="img-responsive" src="<?php echo default_course_path().'default-section.jpg'?>">
                                            <label>
                                                <button class="btn btn-green section-img-upload-btn">CHANGE IMAGE</button>
                                                <input name="section_image" class="fileinput logo-image-upload-btn section-image-upload" id="site_logo_btn" accept="image/*" type="file">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div style="text-align: center;margin: 5px 0px;">Allowed Minimum File Size : 500px x 354px</div>
                            </div>
                        </form>

                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo strtoupper(lang('cancel')) ?></button>
                    <button type="button" class="btn btn-green" id="add_section_save_ok"><?php echo strtoupper(lang('create')) ?></button>

                </div>
            </div>
        </div>
    </div>
    <!-- !.Modal pop up contents :: create new section popup-->

    <!-- Modal pop up contents :: Create new section popup-->
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="addcertificate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel">New Certificate</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Certificate Title*:</label>
                        <input type="text" id="certificate_title" placeholder="eg: Completion Certificate" class="form-control">
                    </div>
                    <div class="form-group add-category clearfix">
                        <div class="add-selectn alignment-order">
                            <label><?php echo lang('section') ?> *:</label>
                            <select class="form-control" id="section_id_certificate">
                            </select>
                            <input type="text" aria-describedby="basic-addon2" maxlength="60" placeholder="eg: Catalog" class="form-control" id="section_name_certificate">
                        </div>
                        <div class="add-btn alignment-order">
                            <label>Or</label>
                            <a href="javascript:void(0)" class="btn btn-green" id="create_new_section_certificate"><?php echo lang('add_new_section') ?></a>
                            <a href="javascript:void(0)" class="btn btn-danger" id="create_new_section_cancel_certificate"><?php echo lang('cancel') ?></a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description:</label>
                        <textarea onkeyup="validateMaxLength(this.id)" maxlength="1000" class="form-control" id="certificate_description" placeholder="eg : This is an example certificate."></textarea>
                        <label class="pull-right" id="certificate_description_char_left">1000 <?php echo lang('characters_left') ?></label>
                    </div>
                    <div class="checkbox"><label><input type="checkbox" id="sent_mail_on_certificate_creation" value="1"><span class="ap_cont chk-box"><?php echo lang('send_mail_on_lecture_publish') ?></span></label></div>
                </div>
                <div class="modal-footer">
                   <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo strtoupper(lang('cancel')) ?></button>
                   <button type="button" class="btn btn-green" onclick="generateCertificateConfirmed()" id="add_section_save_ok">ADD</button>
                </div>
            </div>
        </div>
    </div>
    <!-- !.Modal pop up contents :: create new section popup-->

    <!-- Modal pop up contents :: Create new section popup-->
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="addsectiondraganddrop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo lang('create_new_section') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><?php echo lang('section_title') ?>*:</label>
                        <input type="text" id="section_name_create_on_drag_drop" placeholder="eg: Mathematical Calculations" class="form-control">
                    </div>
                    
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-red" id="cancel_section_drag_drop" data-dismiss="modal"><?php echo strtoupper(lang('cancel')) ?></button>
                    <button type="button" class="btn btn-green" id="save_section_drag_drop"><?php echo strtoupper(lang('create')) ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- !.Modal pop up contents :: create new section popup-->

    <!-- Modal pop up contents :: rename section popup-->
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="edit_section" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_section') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><?php echo lang('section_title') ?>*:</label>
                        <input type="text" placeholder="eg: Mathematical Calculations" class="form-control" id="section_name_rename">
                    </div>
                    <div style="<?php echo ($course['cb_has_lecture_image'] == 1) ? "display:block" : "display:none" ?>" >
                        <form  id="save_section_image" method="POST" enctype="multipart/form-data">
                            <div style="text-align: center;margin: 5px 0px;"><?php echo lang('section_image') ?></div>
                            <div class="section-create-wrapper text-center">
                                <div class="section-card-container">
                                    <div class="section-card">
                                        <img id="section_logo_Edit" data-id="<?php ?>" class="img-responsive" src="<?php //echo (($cb_image == 'default.jpg') ? default_course_path() : course_path(array('course_id' => $id))) . $cb_image; ?>">
                                        <label>
                                            <button class="btn btn-green section-img-upload-btn">CHANGE IMAGE</button>
                                            <input name="section_image" class="fileinput logo-image-upload-btn section-image-upload" id="section_logo_btn_edit" accept="image/*" type="file">
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div style="text-align: center;margin: 5px 0px;">Allowed Minimum File Size : 500px x 354px</div>
                        </form>

                    </div>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></button>
                    <button type="button" class="btn btn-green" id="section_save_ok"></button>
                </div>
            </div>
        </div>
    </div>
    <!-- !.Modal pop up contents :: rename section popup-->


<!-- Modal pop up contents:: Delete Section popup-->
        <div class="modal fade alert-modal-new" id="common_message" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-group">
                            <b id="common_message_header"></b>
                            <p class="m0" id="common_message_content"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-red" data-dismiss="modal">Ok</button>
                    </div>
                </div>
            </div>
        </div>
    <!-- !.Modal pop up contents :: Delete Section popup-->


    <!-- Modal pop up contents:: deactivate popup-->
    <div class="modal fade alert-modal-new" id="Deactivate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                <div class="modal-body">
                    <div class="form-group">
                        <b id="header_text"></b>
                        <p id="popup_message"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo strtoupper(lang('cancel')) ?></button>
                    <button type="button" class="btn btn-green" id="change_status_section"></button>
                </div>
            </div>
        </div>
    </div>
    <!-- !.Modal pop up contents :: deactivate popup-->


    <!-- Modal pop up contents :: Create Descriptive Test -->

    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="audioFiles" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel">Upload Audio file</h4>
                </div>
                <div class="modal-body">
                    <div class="descriptive_form form-group"></div>
                    <div class="form-group file-descriptive">
                        <label id="attached_file_descriptive"></label>
                    </div>
                    <div class="form-group">
                        <label>Audio Title *:</label>
                        <input type="text" maxlength="80" placeholder="eg: Practice for essay writing." id="audio_file_name" class="form-control">
                    </div>
                    <!-- Click to get add the cateogry to the select box -->
                    <div class="form-group add-category clearfix">
                        <div class="add-selectn alignment-order">
                            <label><?php echo lang('section') ?> *:</label>
                            <select class="form-control" id="section_id_audio_file">
                            </select>
                            <input type="text" aria-describedby="basic-addon2" placeholder="eg: Catalog" class="form-control" id="section_name_audio_file">
                        </div>
                        <div class="add-btn alignment-order">
                            <label>Or</label>
                            <a href="javascript:void(0)" class="btn btn-green" id="create_new_audio_file"><?php echo lang('add_new_section') ?></a>
                            <a href="javascript:void(0)" class="btn btn-danger" id="create_new_audio_file_cancel"><?php echo lang('cancel') ?></a>
                        </div>
                    </div>
                    <!-- !.Click to get add the cateogry to the select box -->
                    <div class="form-group">
                        <label><?php echo lang('descriptive_test') . ' ' . lang('description') ?> :</label>
                        <textarea onkeyup="validateMaxLength(this.id)" maxlength="1000" class="form-control" id="audio_file_description" placeholder="eg : This is an assessment to write essay."></textarea>
                        <label class="pull-right" id="audio_file_description_char_left">1000 <?php echo lang('characters_left') ?></label>
                    </div>
                    <div class="form-group clearfix">
                        <label>Upload Audio File(*mp3) :</label>
                        <div class="fle-upload">
                            <label class="fle-lbl">BROWSE</label>
                            <input accept=".pdf" type="file" name="file" id="audio_file" class="form-control upload">
                            <input value="" readonly="" class="form-control upload-file-name" id="upload_audio_file" type="text">
                        </div>
                    </div>
                    <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="sent_mail_on_audio_file_creation" value="1">
                                    <span class="ap_cont chk-box"><?php echo lang('send_mail_on_completed') ?>;</span>
                                </label>
                            </div>
                    </div>
                    <div class="clearfix progress-custom" id="audio_file_percentage_bar">
                        <div class="progress width100">
                            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="percentage_width" style="width: 0%;">
                                  <span class="sr-only"></span>
                            </div>
                        </div>
                        <span class=""><?php echo lang('uploading') ?>...<b id="percentage_count" class="percentage-text">0%</b></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <a type="button" class="btn btn-green" onclick="uploadAudioFileConfirm()" href="javascript:void(0)"><?php echo strtoupper(lang('create')) ?></a>
                    <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo strtoupper(lang('cancel')) ?></button>
                </div>

            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: Create Descriptive Test -->

    <!-- Modal pop up contents :: Create Survey -->
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="survey" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo lang('create') . ' Survey ' ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><?php echo 'Survey' ?> <?php echo lang('title') ?> *:</label>
                        <input type="text" maxlength="80" placeholder="eg: Course Survey" id="survey_name" class="form-control">
                    </div>
                    <!-- Click to get add the cateogry to the select box -->
                     <div class="form-group add-category clearfix">
                            <div class="add-selectn alignment-order">
                                <label><?php echo lang('section') ?> *:</label>
                                <select class="form-control" id="section_id_survey">
                                </select>
                                <input type="text" aria-describedby="basic-addon2" maxlength="60" placeholder="eg: Catalog" class="form-control" id="section_name_survey">
                            </div>
                            <div class="add-btn alignment-order">
                                <label>Or</label>
                                <a href="javascript:void(0)" class="btn btn-green" id="create_new_section_survey"><?php echo lang('add_new_section') ?></a>
                                <a href="javascript:void(0)" class="btn btn-danger" id="create_new_section_cancel_survey"><?php echo lang('cancel') ?></a>
                            </div>
                    </div>
                     <!-- !.Click to get add the cateogry to the select box -->
                    <div class="form-group clearfix">
                    <div class="col-sm-6 no-padding">
                            <label class="control-label">
                                <input type="radio" onchange="processSurveyType(this.value)" name="survey_type" checked="checked" value="regular" id="survey_regular">
                                <span>Regular Survey</span>
                            </label>
                            <label class="control-label">
                                <input type="radio" onchange="processSurveyType(this.value)" name="survey_type" value="tutor" id="survey_tutor">
                                <span>Tutor Feedback</span>
                            </label>
                        </div>
                        <div class="col-sm-6 no-padding" id="survey_tutor_list_wrapper" style="visibility:hidden;">
                            <select class="form-control" id="survey_tutor_list">
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><?php echo 'Survey ' . lang('description') ?> :</label>
                        <textarea onkeyup="validateMaxLength(this.id)" maxlength="1000" class="form-control" id="survey_description" placeholder="eg : This is a survey on lectures of the Course."></textarea>
                        <label class="pull-right" id="survey_description_char_left">1000 <?php echo lang('characters_left') ?></label>
                    </div>
                    <div class="checkbox"><label><input type="checkbox" id="sent_mail_on_survey_creation" value="1"><span class="ap_cont chk-box"><?php echo lang('send_mail_on_lecture_publish') ?></span></label></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo strtoupper(lang('cancel')) ?></button>
                    <a type="button" class="btn btn-green" onclick="createSurveyConfirmed()" href="javascript:void(0)" id="createSurveyConfirmed"><?php echo strtoupper(lang('create')) ?></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: Create Survey -->

<!--
        ###################################################################
            ********  Whole Modal popups Sections are here ************
        ###################################################################
  -->


        <?php 
        $wrapper_class = 'bulder-content-noaccess';
        $wrapper_display = '';
        if(sizeof($this->access_content_types)>0&& in_array($this->privilege['add'],$this->course_content_privilege))
        {
            $wrapper_class = '';
            $wrapper_display = 'style="display:block;"';    
        }
        ?>
    <!-- Manin Iner container start -->
    <div class='bulder-content <?php echo $wrapper_class ?>'  id="content_main_block">
        <!-- header with action buttons and titles including exit -->
        <div class="buldr-header clearfix">
            <!-- /added new-->
            <div class="course-builder-header">
                <!-- Header left items -->
                <h1 class="course-builder-title"><?php echo $course['cb_title'] ?></h1>
                <?php
                    $on_click = '';
                    if ($course['cb_status'] == 1) {
                        $status_class   = 'green';
                        $status_label   = 'active';
                        $cb_label_right = 'deactivate';
                        $cb_class_right = 'orange';

                    } else if ($course['cb_status'] == 2) {

                    $status_class   = 'yellow';
                    $status_label   = 'pending_approval';
                    $cb_label_right = 'activate';
                    $cb_class_right = 'green';

                    } else {
                        $status_class   = 'yellow';
                        $status_label   = 'inactive';
                        $cb_label_right = 'activate';
                        $cb_class_right = 'green';
                    }
                ?>
            </div>

            <!-- course-section-info starts -->
            <div class="pull-left course-section-info">
                <span id="section_lecture_count"><?php echo ($total_sections) ? $total_sections : 'No'; ?> Sections - <?php echo ($total_lecture) ? $total_lecture : 'No'; ?> Lectures</span>
                <label id="status_badge" class="badge bg-<?php echo $status_class ?> font-normal"><?php echo lang($status_label) ?></label>
                <!-- Drop down -->
                <?php
                if (!empty($this->course_content_privilege)) {
                    if (in_array($this->privilege['edit'], $this->course_content_privilege)) {
                ?>
                <div class="btn-group section-control">
                    <span class="dropdown-tigger" data-toggle="dropdown">
                    <span class="label-text">
                      <i class="icon icon-down-arrow"></i>
                    </span>
                    <span class="tilder"></span>
                    </span>
                    <ul class="dropdown-menu pull-right" role="menu"  id="course_action_<?php echo $course['id']; ?>">
                    <?php
                    if (!empty($this->course_content_privilege)) {
                        if (in_array($this->privilege['edit'], $this->course_content_privilege)) {
                    ?>
                        <li id="status_btn_<?php echo $course['id'] ?>">
                            <?php $upcomming_status = ($course['cb_status'] == 1) ? '0' : '1';?>
                            <?php $on_click         = 'onclick="changeCourseStatus(\'' . $course['id'] . '\', \'' . $upcomming_status . '\')"';?>
                            <a href="javascript:void(0);" onclick="changeCourseStatus('<?php echo $course['id'] ?>', '<?php echo $upcomming_status ?>')"><?php echo lang($cb_label_right) ?></a>
                        </li>
                    <?php
                        }
                    }
                    ?>
                    <?php
                    if (!empty($this->course_content_privilege)) {
                        if (in_array($this->privilege['edit'], $this->course_content_privilege)) {
                    ?>
                        <li>
                            <a href="<?php echo admin_url('course_settings/basics/' . $course['id']) ?>"><?php echo ucfirst(lang('settings')) ?></a>
                        </li>
                    <?php
                        }
                    }
                    ?>
                    </ul>
                </div>
                <?php
                    }
                }
                ?>
                <!-- !.Drop down -->
            </div>
            <!-- course-section-info ends -->

            <!-- !.Header left items -->
            <div class="pull-right rite-side">
                <!-- Header right side items with buttons -->
                <ul class="top-rite-materals">
                <?php
                if (in_array($this->privilege['add'], $this->course_content_privilege)) {
                ?>
                    <li class="btn-add-section ui-dragable-btn"><a class="btn btn-blue" data-toggle="modal" data-target="#addsection" onclick="addSection()"><?php echo strtoupper(lang('add_section')) ?></a></li>
                <?php
                }
                ?>
                <?php
                if (!empty($this->course_content_privilege)) {
                    if (in_array($this->privilege['edit'], $this->course_content_privilege)) {
                ?>
                    <li data-coursename="<?php echo $course['cb_title']; ?>" id="action_status_<?php echo $course['id']; ?>">
                        <?php if ($on_click): ?>
                            <a <?php echo $on_click ?> class="btn btn-<?php echo $cb_class_right ?>" id="status_right_button_<?php echo $course['id'] ?>"><?php echo strtoupper(lang($cb_label_right)) ?></a>
                        <?php endif;?>
                    </li>
                <?php
                    }
                }
                ?>
                    <li><a href="<?php echo admin_url('course/basic/' . $course['id']) ?>"><button class="btn btn-red"><i class="icon icon-left"></i><?php echo lang('exit') ?></button></a></li>
                </ul>
            </div>
        </div>
        <!-- curriculum block start -->
        <div class="box-">
            <!-- sortable curriculum start -->
            <ul id="sortable" class="curriculum">
            <?php if (!empty($sections)): ?>
                <?php foreach ($sections as $section): ?>
                    <!-- Section start -->
                    <li class="section" id="section_wrapper_<?php echo $section['id'] ?>" data-section-name="<?php echo ($section['s_name']) ?>">
                        <!-- section-title-holder start -->
                        <div class="section-title-holder d-flex justify-between">
                            <div class="section-title">
                                <!-- section drager start -->
                                <div class="drager">
                                    <img src="<?php echo assets_url() ?>images/drager.png">
                                </div>
                                <!-- section drager end -->

                                <!-- section-counter start -->
                                <div class="section-counter"></div>
                                <!-- section-counter end -->
                                
                                <!-- section-name start -->
                                <span class="section-name" id="section_name_<?php echo $section['id'] ?>"> <?php echo $section['s_name'] ?> </span>
                                <!-- section-name end -->
                            </div>

                            <div class="lecture-action-holder d-flex align-center">
                                <!-- section-control start -->
                                <?php
                                $status_class = 'Inactive';
                                $status_label = 'inactive';
                                if ($section['s_status'] == 1) {
                                    $status_class = 'active';
                                    $status_label = 'active';
                                }
                                ?>
                                <div id="section_status_wraper_<?php echo $section['id'] ?>" class="<?php echo $status_class ?>-section"><i class="icon icon-ok-circled"></i><span class="ap_cont section-main-<?php echo $section['id'] ?>" id="section_status_text_<?php echo $section['id'] ?>"><?php echo lang($status_label) ?></span></div>
                                <?php
                                if (in_array($this->privilege['edit'], $this->course_content_privilege) || in_array($this->privilege['delete'], $this->course_content_privilege)) {
                                ?>
                                <div class="btn-group section-control sectiontitle-dropalign">
                                    <span class="dropdown-tigger" data-toggle="dropdown">
                                        <span class='label-text'>
                                            <i class="icon icon-down-arrow"></i>
                                        </span>
                                    <span class="tilder"></span>
                                    </span>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                    <?php
                                    if (in_array($this->privilege['edit'], $this->course_content_privilege)) {
                                    ?>
                                        <li>
                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#edit_section" onclick="renameSection('<?php echo $section['id'] ?>')"><?php echo lang('edit') ?></a>
                                        </li>
                                        <li id="section_action_status_<?php echo $section['id'] ?>">
                                            <a href="javascript:void(0)"  onclick="changeSectionStatus('0', '<?php echo base64_encode($section['s_name']) ?>', '<?php echo $section['id'] ?>')"><?php echo lang('activate_all') ?></a>
                                        </li>
                                        <li id="section_action_status_<?php echo $section['id'] ?>">
                                            <a href="javascript:void(0)"  onclick="changeSectionStatus('1', '<?php echo base64_encode($section['s_name']) ?>', '<?php echo $section['id'] ?>')"><?php echo lang('deactivate_all') ?></a>
                                        </li>
                                    <?php
                                    }
                                    if (in_array($this->privilege['delete'], $this->course_content_privilege)) {
                                    ?>
                                        <li>
                                            <a href="javascript:void(0)" onclick="deleteSection('<?php echo base64_encode($section['s_name']) ?>', '<?php echo $section['id'] ?>')"><?php echo lang('delete') ?></a>
                                        </li>
                                    <?php
                                    }
                                    ?>  
                                    </ul>
                                </div>
                                <?php
                                }
                                ?>
                                <!-- section-control end -->
                                </div>
                            </div>
                        <!-- section-title-holder end -->
                        <!-- lecture-wrapper start -->
                        <ul class="lecture-wrapper" id="section_lecture_<?php echo $section['id'] ?>">
                            <?php if (!empty($section['lecture'])): ?>
                                <?php foreach ($section['lecture'] as $lecture): ?>
                                    <?php
                                    $status_class = 'Inactive';
                                    $status_label = 'inactive';
                                    if ($lecture['cl_status'] == 1) {
                                    $status_class = 'active';
                                    $status_label = 'active';
                                    }
                                    $lecture_type = ((isset($lecture['cl_lecture_type']) && $lecture['cl_lecture_type'] > 0) ? $lecture['cl_lecture_type'] : 1);
                                    // if (!(($lecture_type == 3) && (!in_array('1', $this->__quiz_permission)))) {
                                    ?>
                                     <li id="lecture_id_<?php echo $lecture['id'] ?>" class="<?php echo $status_class ?>-lecture section-lecture">
                                        <!-- lecture-hold start -->
                                        <div class="lecture-hold">
                                            <!-- lecture drager start -->
                                            <div class="drager"><img src="<?php echo assets_url() ?>images/drager.png"></div>
                                            <!-- lecture drager end -->

                                            <!-- lecture-counter start -->
                                            <div class="lecture-counter"></div>
                                            <!-- lecture-counter end -->

                                            <div class="d-flex justify-between" style="width:100%;"> 
                                            <?php 
                                                 $import_hide_style     = '';
                                                 $import_status_label   = lang($status_label);
                                                 $lecture_url           = admin_url('coursebuilder/lecture/' . $lecture['id']);
                                                 $tick_icon             = '<i class="icon icon-ok-circled"></i>';
                                                 $file_copy_failed      = false;
                                                 $label_style           = '';

                                                 // file_copy_failed - this is for importing lecture or restore lecture  copy assest  status 

                                                if( $lecture['cl_conversion_status'] == '6' ) 
                                                {
                                                    $import_hide_style      = 'style="display:none;"';
                                                    $label_style            = 'style="color: #51b957;font-style: normal;"';
                                                    $import_status_label    = 'File Copy On Progress.......';
                                                    $lecture_url            = 'javascript:void(0)';
                                                    $tick_icon              = '';
                                                }elseif($lecture['cl_conversion_status'] == '7' ) 
                                                {
                                                    $import_hide_style      = '';
                                                    $label_style            = 'style="color: #e45a57;font-style: normal;"';
                                                    $import_status_label    = 'File Copy Failed.......';
                                                    $lecture_url            = 'javascript:void(0)';
                                                    $tick_icon              = '';
                                                    $file_copy_failed       = true;
                                                }
                                                
                                                ?>
                                                <!-- lecture - click to get access innerpage -->
                                                <a href="<?php echo $lecture_url  ?>" class="lecture-innerclick" style="width: 450px;">
                                                    <!-- lecture type icon start -->
                                                    <span class="lecture-icon <?php echo $lecture_icons[$lecture_type]['parent'] ?>">
                                                        <span class="<?php echo $lecture_icons[$lecture_type]['child'] ?>"></span>
                                                    <?php /* <i class="icon <?php echo $lecture_icons[$lecture_type]['child'] ?>"></i>  */ ?>
                                                    </span>
                                                    <!-- lecture type icon end -->
                                                    <!-- lecture-name start -->
                                                    <span class="lecture-name"><?php echo $lecture['cl_lecture_name']; ?></span>
                                                    <!-- lecture-name end -->
                                                </a>
                                                <div class="d-flex align-center">
                                               
                                                    <div id="lecture_status_wraper_<?php echo $lecture['id'] ?>" class="<?php echo $status_class ?>-section"><?php echo $tick_icon ?><span class="ap_cont lecture-group" id="lecture_status_text_<?php echo $lecture['id'] ?>"  <?php echo $label_style;?>><?php echo $import_status_label ?></span></div>
                                                        <!-- lecture-control start -->
                                                        <?php
                                                        if (in_array($this->privilege['edit'], $this->course_content_privilege)||in_array($this->privilege['delete'], $this->course_content_privilege)) {
                                                        ?>
                                                        <div id="toggleOptions_<?php echo $lecture['id'] ?>" class="btn-group lecture-control" <?php echo $import_hide_style?>>
                                                            <span class="dropdown-tigger" data-toggle="dropdown">
                                                                <span class='label-text'>
                                                                    <i class="icon icon-down-arrow"></i>
                                                                </span>
                                                                <span class="tilder"></span>
                                                            </span>
                                                            <ul class="dropdown-menu pull-right" role="menu">
                                                                <?php
                                                                    $status_label_btn = ($lecture['cl_status'] == 1) ? 'deactivate' : 'activate';
                                                                ?>
                                                                <?php
                                                                if (in_array($this->privilege['edit'], $this->course_content_privilege) && !$file_copy_failed) {
                                                                ?>
                                                                    <li id="lecture_action_status_<?php echo $lecture['id'] ?>">
                                                                        <a href="javascript:void(0)" onclick="changeLectureStatus('<?php echo $lecture['cl_status'] ?>', '<?php echo base64_encode($lecture['cl_lecture_name']) ?>', '<?php echo $lecture['id'] ?>','<?php echo $lecture['cl_sent_mail_on_lecture_creation'] ?>','<?php echo $lecture['cl_section_id'] ?>')"><?php echo lang($status_label_btn) ?></a>
                                                                    </li>
                                                                <?php
                                                                }
                                                                ?>

                                                                <?php
                                                                if (in_array($this->privilege['view'], $this->report_privilege) && !$file_copy_failed) {
                                                                ?>
                                                                <?php if ($lecture['cl_lecture_type'] == 3 || $lecture['cl_lecture_type'] == 8): ?>
                                                                <li>
                                                                    <?php
                                                                    if($lecture['cl_lecture_type'] == 3){
                                                                    ?>
                                                                        <a href="<?php echo admin_url().'report/assessments_report?&course='.$lecture['cl_course_id'].'&assessment=' . $lecture['id'] ?>"><?php echo lang('report') ?></a>
                                                                    <?php
                                                                    }
                                                                    if($lecture['cl_lecture_type'] == 8){
                                                                    ?>
                                                                        <a href="<?php echo admin_url().'report/assignment_report?&course='.$lecture['cl_course_id'].'&assignment=' . $lecture['id'] ?>"><?php echo lang('report') ?></a>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </li>
                                                                <?php endif;?>
                                                                <?php
                                                                }
                                                                ?>

                                                                <?php
                                                                if (in_array($this->privilege['edit'], $this->course_content_privilege) ) {
                                                                ?>
                                                                
                                                                    <?php
                                                                    if ($file_copy_failed) {
                                                                    ?>
                                                                        <li>
                                                                            <a onclick="reInitializeCopy({'lectureId':'<?php echo $lecture['id']?>', 'copy_queue_id':'<?php echo $lecture['cl_copy_queue_id'];?>'})" href="javascript:void(0)">Re-initialize</a>
                                                                        </li>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                    <?php
                                                                    if (!(($lecture_type == 3) && (!in_array('3', $this->__quiz_permission)))) {
                                                                    ?>
                                                                        <li>
                                                                            <a href="<?php echo admin_url('coursebuilder/lecture/' . $lecture['id']) ?>"><?php echo lang('settings') ?></a>
                                                                        </li>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                <?php
                                                                }
                                                                ?>

                                                                <?php
                                                                if (in_array($this->privilege['delete'], $this->course_content_privilege)) {
                                                                ?>
                                                                    <?php
                                                                        if (!(($lecture_type == 3) && (!in_array('4', $this->__quiz_permission)))) {
                                                                    ?>
                                                                        <li>
                                                                            <a href="javascript:void(0)" onclick="deleteLecture('<?php echo base64_encode($lecture['cl_lecture_name']) ?>', '<?php echo $lecture['id'] ?>')"><?php echo lang('delete') ?></a>
                                                                        </li>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                <?php
                                                                }
                                                                ?>
                                                            </ul>
                                                        </div> 
                                                        <?php
                                                        }
                                                        ?>
                                                    <!-- lecture-control end -->
                                                </div>
                                            </div>

                                        </div>
                                        <!-- lecture-hold end -->
                                    </li>
                                    
                                <?php endforeach;?>
                            <?php endif;?>
                        </ul>
                        <!-- lecture-wrapper end -->
                    </li>
                    <!-- Section end -->
                <?php endforeach;?>
            <?php endif;?>
            </ul>
            <!-- sortable curriculum end -->
        </div>
        <!-- curriculum block end -->
        <!-- Right block start -->
        <!-- __lecture_type_array Array
            (
                [1] => video
                [2] => document
                [9] => recorded_videos
                [10] => scorm
                [12] => audio
            )-->
        <div class="right_block training-content-type-holder" <?php echo $wrapper_display ?> id="side_nav_content"  >
            <?php 
            if (!empty($this->access_content_types)) 
            {
                if(in_array($this->__lecture_type_array['1'],  $this->access_content_types)||
                    in_array($this->__lecture_type_array['2'], $this->access_content_types)||
                    in_array($this->__lecture_type_array['9'], $this->access_content_types)||
                    in_array($this->__lecture_type_array['10'],$this->access_content_types)||
                    in_array($this->__lecture_type_array['12'],$this->access_content_types))
                {
            ?>
            <div class="drop-area-section border-bottom-white" id="drop-to-pop">
                <DIV id="status"></DIV>
                <div id="list"></div>
                    <!--  Drop Down area and dropbox integration -->
                    <i class="icon icon-upload-cloud-1"></i>
                    <h3 class="noselect"><?php echo lang('drop_files_here') ?></h3>
                    
                    <p class="noselect" style="font-size: 13px;">Recommended File Formats: <?php echo '.'.implode(', .', $recommended_files).', scorm package(.zip)';//print_r($this->access_content_types); ?></p>
                    <div class="form-group">
                        <label class="fileContainer btn btn-black m0 width100">
                            <i class="icon icon-desktop"></i>
                            <?php echo strtoupper(lang('choose_file')) ?>
                            <input type="file" class="" id="lecture_file_upload_manual">
                        </label> 
                    </div>
                    <?php if( $dropbox_js ): ?>
                    <div class="form-group"><a class="btn btn-black width100 m0" onclick="dropBoxFileUpload()">
                        <i class="icon  icon-dropbox"></i><?php echo lang('dropbox') ?></a></div>
                    <?php endif; ?>
            </div>
            <?php
                    }
                }
            ?>
            <!--  !.Content topics area and dropbox integration -->
            <div class="drop-area-section">

                <!--  Content topics area with all other topics as buttons you can drag and also access of popup -->
                <ul class="each-topics">
                    <?php
                    if (!empty($this->access_content_types)) {
                        if(in_array($this->__lecture_type_array['3'],$this->access_content_types)){
                    ?>
                    <li class="form-group">
                        <a class="btn btn-black" data-target="#assesment" data-toggle="modal" onclick="createAssesment()">
                            <span class="course-icon quiz-icon-white"></span>
                            <span class="lecture-type-holder"><?php echo lang('assesment') ?></span></a>
                    </li>
                    <?php
                        }
                    }
                    ?>
                    <?php
                    if (!empty($this->access_content_types)) {
                        if(in_array($this->__lecture_type_array['8'],$this->access_content_types)){
                    ?>
                    <li class="form-group">
                        <a class="btn btn-black" data-target="#descriptive" data-toggle="modal" onclick="createDescriptive()">
                            <span class="course-icon assignments-icon-white"></span>
                            <span class="lecture-type-holder"><?php echo lang('descriptive_test') ?></span>
                        </a>
                    </li>
                    <?php
                        }
                    }
                    ?>
                    <?php
                    /*if (!empty($this->access_content_types)) {
                        if(in_array($this->__lecture_type_array['7'],$this->access_content_types)){
                    ?>
                    <li class="form-group">
                        <a class="btn btn-black" data-target="#livelecture" data-toggle="modal" onclick="scheduleLiveLecture()">
                            <span class="course-icon live-icon-white"></span>
                            <span class="lecture-type-holder">live Lecture</span>
                        </a>
                    </li>
                    <?php
                        }
                    }*/
                    ?>
                    <?php
                    if (!empty($this->access_content_types)) {
                        if(in_array($this->__lecture_type_array['5'],$this->access_content_types)){
                    ?>
                    <li class="form-group">
                        <a class="btn btn-black" data-target="#htmlcode" data-toggle="modal" onclick="createHtml()">
                            <span class="course-icon html-icon-white"></span>
                            <span class="lecture-type-holder"><?php echo lang('html') ?></span>
                        </a>
                    </li>
                    <?php
                        }
                    }
                    ?>
                    <?php
                    if (!empty($this->access_content_types)) {
                        if(in_array($this->__lecture_type_array['4'],$this->access_content_types)){

                    ?>
                    <li class="form-group">
                        <a class="btn btn-black" data-target="#youtube" onclick="createYoutubeLecture()" data-toggle="modal">
                            <span class="course-icon video-icon-white"></span>
                            <span class="lecture-type-holder"><?php echo lang('youtube')." / ".lang('vimeo') ?></span>
                        </a>
                    </li>
                    <?php
                        }
                    }
                    ?>
                    <!-- <li class="form-group"> -->
                        <!-- <a class="btn btn-black" data-target="#importContent" data-toggle="modal" onclick="importContent()">
                            <i class="icon icon-dropbox"></i>
                            <?php //echo lang('import_content') ?>
                        </a> -->
                    <!-- </li> -->
                    <!-- < ?php
                    if (!empty($this->access_content_types)) {
                        if(in_array($this->__lecture_type_array['10'],$this->access_content_types)){

                    ?>
                    <li class="form-group">
                        <a class="btn btn-black" data-target="#scorm_modal" data-toggle="modal" onclick="createScorm()">
                            <span class="course-icon scorm-icon-white"></span>
                            <span class="lecture-type-holder">Scorm Upload</span>
                        </a>
                    </li>
                    < ?php
                        }
                    }
                    ?> -->
                    <?php
                    if (!empty($this->access_content_types)) {
                        if(in_array($this->__lecture_type_array['11'],$this->access_content_types)){

                    ?>
                    <?php /*
                    <li class="form-group">
                        <a class="btn btn-black" data-target="#recordedVideos" data-toggle="modal" onclick="recordedVideos()">
                            <span class="course-icon recorded-icon-white"></span>
                            <span class="lecture-type-holder">Recorded Videos</span>
                        </a>
                    </li>
                    */ ?>
                    <?php
                        }
                    }
                    ?>
                    <?php
                    if (!empty($this->access_content_types)) {
                        if(in_array($this->__lecture_type_array['13'],$this->access_content_types)){

                    ?>
                    <li class="form-group">
                        <a class="btn btn-black" data-target="#survey" data-toggle="modal" onclick="createSurvey()">
                            <span class="course-icon survey-icon-white"></span>
                            <span class="lecture-type-holder">Survey</span>
                        </a>
                    </li>
                    <?php
                        }
                    }
                    ?>
                    <?php
                    if ($course['cb_has_certificate'] == "1") {
                        if (!empty($this->access_content_types)) {
                            if(in_array($this->__lecture_type_array['14'],$this->access_content_types)){

                    ?>
                        <li class="form-group">
                            <a class="btn btn-black" data-target="#addcertificate" data-toggle="modal" onclick="generateCertificate()">
                                <span class="course-icon certificate-icon-white"></span>
                                <span class="lecture-type-holder">Certificate</span>
                            </a>
                        </li>
                    <?php
                            }
                        }
                    }
                    ?>

                    <li class="form-group">
                        <a class="btn btn-black" data-target="#importContent" data-toggle="modal" onclick="importContent()"><i class="icon icon-dropbox"></i>Import Content<ripples></ripples></a>
                    </li>


                </ul>
                <!--  !.Content topics area with all other topics as buttons you can drag and also access of popup -->
            </div>

        </div>
    </div>
    <!-- Manin Iner container end -->
    <?php include_once __DIR__ . '/../common_modals.php'?>
</body>
<!-- <input type="hidden" value="" class="hidden_edit_section_id"> -->
<!-- body end-->
</html>
<script>

    var __controller         = '<?php echo $this->router->fetch_class() ?>';
    var __youtubeId          = '<?php echo config_item('youtube_id') ?>';
    var __isFirstView        = '<?php echo $is_first_view ?>';
    var __allowed_files      = $.parseJSON('<?php echo json_encode($accepted_files) ?>');
     var __start_date        = '';
</script>

<script type="text/javascript" src="<?php echo assets_url() ?>js/language.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/course_builder.js"></script>


<?php echo $upload_js; ?>
<?php echo $dropbox_js; ?>

<!-- Jquery ui library -->
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery-ui.min.js"></script>
<!-- bootstrap library -->
<script type="text/javascript" src="<?php echo assets_url() ?>js/bootstrap.min.js"></script>
<!-- curriculum.js handles section and lecture sorting and dragable effects -->
<script type="text/javascript" src="<?php echo assets_url() ?>js/curriculum.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.timepicker.min.js"></script>
<?php 
    if(isset($s3_section_image))
    {
        $section_image_url = $s3_section_image;
    }
    else
    {
        $section_image_url = base_url().config_item('upload_folder').'/'.config_item('acct_domain').'/course/'.$id.'/course/section/'; 
    }     
?>
<script type="text/javascript">
const getSectionDetails = '<?php echo site_url("admin/coursebuilder/get_section_details"); ?>';
const check_file = '<?php echo site_url("admin/coursebuilder/check_file_exist"); ?>';
var section_url = '<?php echo $section_image_url; ?>';
var _section_url =   '<?php echo base_url().config_item('upload_folder'). '/' . config_item('acct_domain'). '/course/' ?>'+__course_id+'<?php echo '/course/section/' ?>';
console.log(section_url);
var _course_url =   '<?php echo base_url().config_item('upload_folder'). '/' . config_item('acct_domain'). '/course/' ?>';
var __course_name = '<?php echo addslashes($course['cb_title']) ?>';
    $(function(){
        var today = new Date();
        $("#descriptive_submission_date").datepicker({
            language: 'en',
            minDate: new Date(),
            minDate: 'today',
            dateFormat: 'dd/mm/yy',
            autoClose: true
        });
    });
    
    $(document).ready(function() {
        $('input[name="submission_method"]').click(function(){
            var value   = $(this).attr("value");
            if(value==1){
                $("#submission_date").show();
                $("#submission_days").hide();
            } else if(value==2){
                $("#submission_days").show();
                $("#submission_date").hide();
            }
        });
        $("#schedule_date").datepicker({
            language: 'en',
            minDate: new Date(),
            
            onSelect: function(dateText, inst) {

                var sel_date            = new Date(dateText);
                var today_date          = new Date();
                var today_date_second   = new Date(today_date.getFullYear(),today_date.getMonth(),today_date.getDate(),0,0,0,0);

                if(sel_date.getDate() == today_date_second.getDate()){
                    var current_time = today_date.getHours();
                    $('#live_lecture_start_time').remove();
                    $('#divtime').prepend('<input type="text" class="form-control" id="live_lecture_start_time" placeholder="10.45" onkeydown="event.preventDefault()" >');
                    $('#live_lecture_start_time').timepicker({
                        timeFormat: 'h:i A',
                        interval: 60,
                        minTime: (current_time+1).toString(),
                        maxTime: '11:30pm',
                        dynamic: false,
                        dropdown: true,
                        
                    });
                }else{

                    $('#live_lecture_start_time').remove();
                    $('#divtime').prepend('<input type="text" class="form-control" id="live_lecture_start_time" placeholder="10.45" onkeydown="event.preventDefault()" >');
                    $('#live_lecture_start_time').timepicker({
                        timeFormat: 'h:i A',
                        interval: 60,
                        maxTime: '11:30pm',
                        dynamic: false,
                        dropdown: true,
                    });
                }

            }
        });

    });
</script>
<?php if ($is_first_view == 1): ?>
    <!-- //$('#help-intro').modal({backdrop: 'static', keyboard: false}) -->
<?php endif;?>
<script>
var __create_section_as_new = false;
$(document).ready(function(){
    $('#create_new_section_cancel_recorded_video, #section_name_recorded_video').hide();
});
$(document).on('click', '#create_new_section_recorded_video', function(){
    __create_section_as_new = true;
    $('#section_id_recorded_video, #create_new_section_recorded_video').hide();
    $('#create_new_section_cancel_recorded_video, #section_name_recorded_video').show();
});
$(document).on('click', '#create_new_section_cancel_recorded_video', function(){
    __create_section_as_new = false;
    $('#section_id_recorded_video, #create_new_section_recorded_video').show();
    $('#create_new_section_cancel_recorded_video, #section_name_recorded_video').hide();
});

$(document).on('click', '#lecture_file_upload_manual', function(){
    $("#popUpMessage").remove();
    $('#lecture_name').val('');
    $('#section_id').val('');
    $('#lecture_description').val('');
    $('#section_name').val('');
});

function recordedVideos()
{
   $('#section_id_recorded_video').html(getSectionsOptionHtml());
   $('#recorder_video_list').html(getRecordedListHtml());
}

function getRecordedListHtml()
{
    var recordListHtml   = '';
    $.ajax({
        url: admin_url+'coursebuilder/recorded_json',
        type: "POST",
        async:false,
        data:{ "is_ajax":true, "date":$('#cisco_recorded_date').val()},
        success: function(response) {
            var data            = $.parseJSON(response);
            var date            = $('#cisco_recorded_date').val();
                if(date != '' ) {
                    recordListHtml  = '<option value="">Recordings on '+date+'</option>';
                } else {
                    recordListHtml  = '<option value="">Recent Recordings</option>';
                    var recordedDate = '<option value="">Filter Recording by Date</option>';
                    if(Object.keys(data['recorded_dates']).length > 0 ){
                        $.each(data['recorded_dates'], function(dateKey, dateObject )
                        {
                            recordedDate += '<option value="'+dateObject['cr_date']+'">'+dateObject['cr_date']+'</option>';
                        });
                    }
                    $("#cisco_recorded_date").html(recordedDate);
                }
            if(Object.keys(data['recordings']).length > 0 )
            {
                $.each(data['recordings'], function(videoKey, recordedVideo )
                {
                    recordListHtml += '<option value="'+recordedVideo['cr_filename']+'">'+recordedVideo['cr_filename']+'</option>';
                });
            }
        }
    });
    return recordListHtml;
}
$(document).on('change', '#cisco_recorded_date', function(){
    $('#recorder_video_list').html(getRecordedListHtml());
});

var __ciscoRecordedLectureCreationInProgress = false;
function recordedVideosConfirm()
{
    if(__ciscoRecordedLectureCreationInProgress == true) {
        return false;
    }
    var record_name         = $('#record_name').val();
    var cospace_id          = $('#cospace_id').val();
    var recorded_video_id   = $('#recorder_video_list').val();
    var section_id          = $('#section_id_recorded_video').val();
    var section_name        = $('#section_name_recorded_video').val();
    var lecture_description = record_name;
    var errorCount          = 0;
    var errorMessage        = '';

    //validation process
    if(record_name == '')
    {
        errorCount++;
        errorMessage += 'Record title required <br />';
    }

    if(recorded_video_id == '')
    {
        errorCount++;
        errorMessage += 'Assign recording video to lecture <br />';
    }

    if(__create_section_as_new == true && section_name == '')
    {
        errorCount++;
        errorMessage += 'Section required <br />';
    }

    if(__create_section_as_new == false && section_id == '')
    {
        errorCount++;
        errorMessage += 'Please choose section <br />';
    }
    cleanPopUpMessage();
    if(errorCount > 0 )
    {
        $('#recordedVideos .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        return false;
    }

    if(__create_section_as_new==true)
    {
        section_name   = section_name;
        section_id     = '';
    }
    else
    {
        section_name   = '';
        section_id     = section_id;
    }
    __ciscoRecordedLectureCreationInProgress = true;
    $('#recorded_video_trigger_btn').html('CREATING..<ripples></ripples>');
    $.ajax({
        url: admin_url+'coursebuilder/create_recorded_video',
        type: "POST",
        data:{ "is_ajax":true, "course_id":__course_id, 'sent_mail_on_import_creation':1, 'cospace_id':cospace_id, 'section_id':section_id, 'section_name':section_name, 'recorded_video_id':recorded_video_id, 'record_name':record_name, 'lecture_description':lecture_description},
        success: function(response) {
            var data            = $.parseJSON(response);
            var lecture_icons   = data['lecture_icons'];
            if(data['error'] == "false")
            {
                location.href = admin_url+'coursebuilder/lecture/'+data['id'];
            }
            else
            {
                cleanPopUpMessage();
                $('#recordedVideos .modal-body').prepend(renderPopUpMessage('error', data['message']));
            }
        }
    });
}

function uploadAudioFile()
{
    $('#audio_file_name, #section_id_audio_file, #section_name_audio_file, #audio_file_description, #audio_file_file, #upload_audio_file').val('');
    $('#section_id_audio_file, #create_new_audio_file').show();
    $('#section_name_audio_file, #create_new_audio_file_cancel, #attached_file_audio_file, #popUpMessage, #audio_file_percentage_bar').hide();
    $('#section_id_audio_file').html(getSectionsOptionHtml());
    $('#audio_file_description_char_left').html('1000 Characters left');
}

</script>
<script src="https://www.youtube.com/player_api"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/help_video.js"></script>

<style type="text/css" media="screen">
    #drop {
  min-height: 150px;
  width: 250px;
  border: 1px solid blue;
  margin: 10px;
  padding: 10px;
}
#drop-to-pop.hover{
    background: rgba(255, 255, 255, 0.2);
}
#drop-to-pop.hover .icon-upload-cloud-1{
    transform: scale(1.3);
    transition: all cubic-bezier(0.01, 0.95, 0.49, 1.51) 0.2s;
    -webkit-transition: all cubic-bezier(0.01, 0.95, 0.49, 1.51) 0.2s;

}
.top-rite-materals li{
    display: inline-block;
}
.file-descriptive{
    display:none;
}
</style>

<script>
    var _default_course_url = '<?php echo  default_course_path() ?>';
    var courseContentPrivilege = '<?php echo json_encode($this->course_content_privilege) ?>';
    var __content_privilege = new User(courseContentPrivilege); 

    var accessContentTypes = '<?php echo json_encode($this->access_content_types) ?>';
    $("#site_logo_btn").change(function() {
        readImageData(this,'add'); //Call image read and render function
    });
    $("#section_logo_btn_edit").change(function() {
        readImageData(this,'edit'); //Call image read and render function
    });
    function readImageData(imgData,action) {
        // console.log($(imgData).attr('section-id'));
        if (imgData.files && imgData.files[0]) {
            var readerObj = new FileReader();
            
            readerObj.onload = function(element) {
                var image_files  = [];
                image_files_type = "";
                if(action == 'add'){
                    $('#section_logo').attr('src', element.target.result);
                    image_files_type = $('#site_logo_btn')[0].files[0].type;
                }else{
                    $('#section_logo_Edit').attr('src', element.target.result);
                    image_files_type = $('#section_logo_btn_edit')[0].files[0].type;
                }
                
                var img = new Image;

                var image_alowed_types = ['image/png', 'image/jpg', 'image/gif', 'image/jpeg', 'image/png'];
                if(jQuery.inArray(image_files_type, image_alowed_types) < 0){
                    if(action == 'add'){
                            $('#site_logo_btn').val("");
                            $('#section_logo').attr('src', _course_url+'default-section.jpg');
                        }else{
                            var section_id = $(imgData).attr('section-id');
                            var url_image  = _section_url+section_id+'.jpg';
                            isUrlExists(url_image, function(status){
                                if(status === 200){
                                    $('#section_logo_btn_edit').val("");
                                    $('#section_logo_Edit').attr('src',url_image);
                                }
                                else if(status === 404){
                                    // alert(default_course_path()+'default-section.jpg');
                                    $('#section_logo_btn_edit').val("");
                                    $('#section_logo_Edit').attr('src', _default_course_url+'default-section.jpg');
                                }
                            });
                        }
                    lauch_common_message('Image type', 'The file you have chosen is not allowed.');
                    return false;
                }
                img.onload = function() {
                    if(img.width < 500 || img.height < 354){
                        lauch_common_message('Image Size', 'The image you have choosen is too small and cannot be uploaded.');
                        // $('#site_logo').attr('src',__course_loaded_img);
                        if(action == 'add'){
                            $('#site_logo_btn').val("");
                            $('#section_logo').attr('src', _course_url+'default-section.jpg');
                        }else{
                            var section_id = $(imgData).attr('section-id');
                            var url_image  = _section_url+section_id+'.jpg';
                            isUrlExists(url_image, function(status){
                                if(status === 200){
                                    $('#section_logo_btn_edit').val("");
                                    $('#section_logo_Edit').attr('src',url_image);
                                }
                                else if(status === 404){
                                    // alert(default_course_path()+'default-section.jpg');
                                    $('#section_logo_btn_edit').val("");
                                    $('#section_logo_Edit').attr('src', _default_course_url+'default-section.jpg');
                                }
                            });
                        }
                        return false;
                    }
                };

                img.src = element.target.result;
            }
            readerObj.readAsDataURL(imgData.files[0]);
        }
    }
    $(document).on('click', '.close, .btn-red', function(){
        __create_section_as_new = false;
    });

    function reInitializeCopy(data){
        var lectureId       = data.lectureId;
        var copy_queue_id   = data.copy_queue_id;
        //console.log(lectureId, copy_queue_id);
            if(lectureId && copy_queue_id){
                $.ajax({
                url: admin_url+'backup/reInitializeCopy',
                type: "POST",
                data:{ "lectureId" : lectureId, "copy_queue_id" : copy_queue_id, "is_ajax" : true },
                success: function(response){
                    if(response.error === false){
                        $('#lecture_status_text_'+lectureId).attr("style","color: #51b957;font-style: normal;");
                        $('#toggleOptions_'+lectureId).hide();
                        //document.getElementById("lecture_status_wraper_"+lectureId).firstElementChild.remove();
                        //$('#lecture_status_text_'+lectureId).
                        $('#lecture_status_text_'+lectureId).text('File Copy On Progress.......');
                        lauch_common_success_message('success', response.message);
                        setInterval(() => {
                            $.get(admin_url+'backup/process_copy_queue/'+copy_queue_id);
                        }, 30000);
                    }
                    else 
                    {
                        $('#lecture_status_text_'+lectureId).text('Re Initalize failed.......');
                        lauch_common_message('failed', response.message);
                        $('#advanced_confirm_box_ok').click(function(){
                            location.replace(admin_url+'coursebuilder/lecture/'+lectureId);
                        });
                    }
                }
            });
        }
    }

</script>
