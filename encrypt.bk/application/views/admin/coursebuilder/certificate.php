
<?php include_once 'lecture_header.php';?>
<?php

$checked = 'checked="checked"';

$status_button = 'btn-green';
$status_label  = 'activate';
$status_mark   = 'Inactive';
$status_lang   = 'inactive';
if ($lecture['cl_status'] == 1) {
 $status_button = 'btn-orange';
 $status_label  = 'deactivate';
 $status_mark   = 'active';
 $status_lang   = 'active';
}

?>
<style>
    .logo-image-upload-btn {
        cursor: pointer;
        height: 61%;
        left: 72px;
        opacity: 0;
        position: absolute;
        top: 33px;
        width: 48%;
        z-index: 2;
    }
    .certificate-preview{
        width: 100%;
        margin: 0 auto;
        padding: 25px 0 0 0;
    }
    .certificate-upload-infotable{margin-bottom:30px;}
    .certificate-upload-infotable th{padding: 10px 20px;}
    .certificate-upload-infotable td{padding: 10px 20px;}
    .notes-msg{
        font-size: 11px;
        color: green;
    }
</style>

<!-- Manin Iner container start -->
<div class='course-bulder-content-inner'>
        <!-- top Header -->
        <div class="buldr-header inner-buldr-header clearfix">
            <div class="pull-left">
                <!-- Header left items -->
                <div class="lecture-icon-big <?php echo $lecture_icons[$lecture['cl_lecture_type']]['parent'] ?>"><i class="icon <?php echo $lecture_icons[$lecture['cl_lecture_type']]['child'] ?>"></i></div>
                <h3><?php echo $lecture['cl_lecture_name'] ?></h3>
            </div>
            <!-- !.Header left items -->
            <div class="pull-right rite-side">
                <!-- Header right side items with buttons -->

                <?php
                    if (!empty($this->course_content_privilege)) {
                        if (in_array($this->privilege['edit'], $this->course_content_privilege)) {

                ?>
                <a id="lecture_action_status_<?php echo $lecture['id'] ?>" class="btn <?php echo $status_button ?>" onclick="changeLectureStatus('<?php echo $lecture['cl_status'] ?>', '<?php echo base64_encode(htmlentities($lecture['cl_lecture_name'])); ?>', '<?php echo $lecture['id'] ?>')"><?php echo strtoupper(lang($status_label)) ?></a>
                <?php
                        }
                    }
                ?>
                <a href="<?php echo admin_url('coursebuilder/home/' . $lecture['cl_course_id']) ?>"><button class="btn btn-red" id="back_button"><i class="icon icon-left"></i><?php echo lang('back') ?></button></a>
            </div>
        </div>
        <!-- !.top Header -->
        <div class="col-sm-6 builder-left-inner">
            <!-- !.Left side bar section -->
            <!-- accordion starts here -->
            <div class="panel-group" id="accordion">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                          <h4 class="coursebuilder-settingstab-title">Basic Settings</h4>
                        </a>
                        </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <!-- Form elements Left side :: lecture Documents-->
                            <div class="builder-inner-from" id="video_form">
                                <div class="form-group clearfix">
                                    <label>Certificate Title *:</label>
                                    <!-- <div id="lecture_status_wraper_6" class="active-section"></div> -->
                                    <input type="text" maxlength="80" placeholder="eg: Mathematical Calculations" id="certificate_title" name="certificate_title" value="<?php echo isset($lecture['cl_lecture_name']) ? htmlentities($lecture['cl_lecture_name']) : ''; ?>" class="form-control">
                                </div>
                                <!-- Description area -->
                                <div class="form-group clearfix">
                                    <label>Description :</label>
                                    <textarea class="form-control" id="certificate_description" onkeyup="validateMaxLength(this.id)" maxlength="1000" name="lecture_description"><?php echo isset($lecture['cl_lecture_description']) ? $lecture['cl_lecture_description'] : ''; ?></textarea>
                                    <label class="pull-right" id="lecture_description_char_left"> 968 Characters left</label>
                                </div>
                                <!-- !.Description area -->
                                <div class="text-right">
                                    <input type="hidden" id="lecture_id" value="<?php echo $lecture['id'] ?>">
                                    <input type="hidden" id="section_id" value="<?php echo $lecture['cl_section_id'] ?>">
                                    <input type="hidden" id="course_id" value="<?php echo $lecture['cl_course_id'] ?>">
                                    <?php
                                        if (!empty($this->course_content_privilege)) {
                                            if (in_array($this->privilege['edit'], $this->course_content_privilege)) {
    
                                    ?>
                                    <button type="button" id="save_certificate_btn" onclick="generateCertificateConfirmed()" class="btn btn-green">SAVE</button>
                                    <?php
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <!-- !.Form elements Left side :: lecture Documents-->
                        </div>
                    </div>
                    <?php include_once "access_restriction.php"; ?>
                    <?php include_once "support_files.php" ?>
                </div>
            </div>
            <!-- accordion starts here -->
        </div>
        <!-- !.Left side bar section -->
        <div class="col-sm-6 builder-right-inner">
            <div class="col-sm-12 course-cont-wrap image-uploader innercontent" id="certificateSettings" style="display: block;">
                
                <div id="certificate_message"></div>
                <div class="col-sm-12">
                    <div class="certificate-preview">
                        <img class="img-responsive" id="previewImg" src="" width="100%">
                    </div>
                    <!-- certificate thumbs starts -->
                    <div class="text-center">
                        <ul class="banner-list" id="certificates_list" style="display: inline-block;">


                        </ul>
                    </div>
                    <!-- certificate thumbs ends -->
                </div>
                <?php
                    if (!empty($this->course_content_privilege)) {
                        if (in_array($this->privilege['add'], $this->course_content_privilege)) {

                ?>
                <div class="col-sm-12">
                    <div class="banner-setting">
                        <div class="text-center">
                            <button class="btn btn-green pos-abs" data-toggle="modal" onclick='beforeUploadClear();' data-target="#addCertificate">UPLOAD CERTIFICATE
                            </button>
                        </div>
                    </div>
                    <div class="banner-upload upload-info">
                        Supported File Format : DOCX <br />
                        For course name use <b>{Course_name}</b><br />
                        For student name use <b>{Name}</b><br />
                        For date use <b>{dd-mm-yyyy}</b>
                    </div>
                </div>
                <?php
                        }
                    }
                ?>
            </div>
        </div>
    </div>
    </div>

    
    <?php include_once 'lecture_footer.php';?>
    <script type="text/javascript" src="<?php echo assets_url() ?>js/youtube.js"></script>
    <script>

        var __certificatePath   = '<?php echo certificate_path() ?>';
        var __certificates      = atob('<?php echo base64_encode(json_encode($certificates)); ?>');
            __certificates      = $.parseJSON(__certificates);
        var __activeCertificate = $.parseJSON(atob('<?php echo base64_encode(json_encode($lecture)); ?>'));
        var __uploading_file_certificate = new Array();
        var __uploaded_files    = null;
        $(document).ready(function(){
            $('#certificates_list').html(renderCertificatesHtml(__certificates));
            $("#addCertificate").on("hidden.bs.modal", function () {
                setTimeout(() => {
                    $('#certificateSettings')[0].scrollIntoView(true);
                }, 200);
            });
        });
        

    </script>
    <script type="text/javascript" src="<?php echo assets_url() ?>js/certificate.js"></script>

<!-- upload certificate modal starts -->
    <div class="modal fade" id="addCertificate" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel">IMPORT CERTIFICATE TEMPLATE</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group mb10">
                        <p><b>Step 1:</b> Download <a href="<?php echo base_url().'uploads/default/certificate/default.docx' ?>" class="link-style"><em>default.docx</em></a> if you dont have the one.</p>
                    </div>
                     <div class="form-group mb10">
                        <p><b>Step 2:</b> Use variables specified in the table as per your requirement.</p>
                    </div>
                    <!-- certificate variable preview starts -->
                    <div class="form-group">
                        <table class="table table-bordered certificate-upload-infotable">
                            <thead>
                                <tr>
                                    <th>Variable Name</th>
                                    <th>Purpose</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{Name}</td>
                                    <td>To show student name in certificate</td>
                                </tr>
                                <tr>
                                    <td>{Course_name}</td>
                                    <td>To show course name in certificate</td>
                                </tr>
                                <tr>
                                    <td>{dd-mm-yyyy}</td>
                                    <td>To show the date in which the certificate is issued.</td>
                                </tr>
                                <tr>
                                    <td>{Percentage}</td>
                                    <td>To show the percentage achieved by the student in this course.</td>
                                </tr>
                                <tr>
                                    <td>{Grade}</td>
                                    <td>To show the grade achieved by the student in this course.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group mb10">
                        <p><b>Step 3:</b> Upload once your template is ready.</p>
                    </div>
                    <!-- certificate variable preview ends -->
                    <div class="form-group clearfix">
                      <div class="fle-upload">
                        <label class="fle-lbl">BROWSE</label>
                        <input type="file" class="form-control upload"  id="import_certificate">
                        <input value="" readonly="" class="form-control upload-file-name" id="upload_user_file" type="text">
                      </div>
                    </div>
                    <div class="clearfix progress-custom" id="certificate_progress_div" style="display:none">
                        <div class="progress width100">
                            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                <span class="sr-only">0% Complete</span>
                            </div>
                        </div>
                        <span id="certification_status_wrapper"><b id="certificate_percentage_count">Uploading...</b><b class="percentage-text"></b></span>
                    </div>
                    <div class="form-group mb10">
                        <p class="notes-msg">If you want a new line before/after the above system defined variable, instead of pressing <b>"Enter"</b> key, press <b>"Shift+Enter"</b>.</p>
                    </div>
                </div>
                <div class="modal-footer" id="certificate_action_button_wrapper">
                    <button type="button" id="upload_certificate_button_cancel"  class="btn btn-red" data-dismiss="modal">CANCEL<ripples></ripples></button>
                    <button type="button" id="upload_certificate_button"  class="btn btn-green" onclick="uploadCertificate()">UPLOAD<ripples></ripples></button>
                </div>
            </div>
        </div>
    </div>