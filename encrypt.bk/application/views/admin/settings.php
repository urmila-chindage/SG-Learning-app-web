<?php //echo '<pre>'; print_r($question_categories);die; ?>
<script>
    var __controller = '<?php echo $this->router->fetch_class() ?>';
    var assets_url = '<?php echo assets_url() ?>';
</script>

<?php include_once 'header.php'; ?> 

<style>
    .d-flex-center{display: flex;align-items: center;padding: 10px 0;}
    .btn-half{padding:0px;width:50%;}
    .ui-state-highlight
    {
        width: 130px;
        height: 140px;
        border:dashed 3px #ccc;
    }
</style>

<script type="text/javascript" src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>

<section class="content-wrap create-group-wrap settings-top">
    <!-- LEFT CONTENT --> <!-- STARTS -->
    <!-- ===========================  -->
    <div class="col-sm-12 pad0 settings-left-wrap">
        <!-- Group content section  -->
        <!-- ====================== -->
        <div class="col-sm-12 group-content course-cont-wrap"> 
            <div class="table course-cont rTable" style="">
                <div class="rTableRow settings-table">
                    <div class="rTableCell"> 
                        <span> 
                            <a href="javascript:void(0)" data-ID="#basicSettings" class="normal-base-color grp-click-fn settings-link activeDiv">
                                <span class="font-bold center-block">Basic Settings</span></a>
                            <span class="settings-text">Change Logo And Basic settings</span>
                        </span>
                    </div>
                    <div class="rTableCell pos-rel active-table">
                        <span class="active-arrow"></span>
                    </div>
                </div>
                <div class="rTableRow ">
                    <div class="rTableCell"> 
                        <span> 
                            <a href="javascript:void(0)" data-ID="#homeSettings" class="normal-base-color grp-click-fn settings-link">
                                <span class="font-bold center-block">Home Page Settings</span></a>
                            <span class="settings-text"><a href="javascript:void(0)" data-ID="#homeSettings" class="grp-click-fn settings-link">Banner Settings</a></span> | 
                            <span class="settings-text"><a href="javascript:void(0)" data-ID="#testimonialSettings" class="grp-click-fn settings-link">Testimonials</a></span> |
                            <span class="settings-text"><a href="javascript:void(0)" data-ID="#mobileBannerSettings" class="grp-click-fn settings-link">Mobile Banner Settings</a></span>
                        </span>
                    </div>
                    <div class="rTableCell pos-rel">
                        <span class="active-arrow"></span>
                    </div>
                </div>
                
                <div class="rTableRow ">
                    <div class="rTableCell"> 
                        <span> 
                            <a href="javascript:void(0)" data-ID="#contactSettings" class="normal-base-color grp-click-fn settings-link">
                                <span class="font-bold center-block">Contacts Settings</span></a>
                            <span class="settings-text">Contact Phone, Email And Address Settings</span>
                        </span>
                    </div>
                    <div class="rTableCell pos-rel">
                        <span class="active-arrow"></span>
                    </div>
                </div>
                
                <div class="rTableRow ">
                    <div class="rTableCell"> 
                        <span> 
                            <a href="javascript:void(0)" data-ID="#socialSettings" class="normal-base-color grp-click-fn settings-link">
                                <span class="font-bold center-block">Social Media Settings</span></a>
                            <span class="settings-text">Social Media Registration and follow settings</span>
                        </span>
                    </div>
                    <div class="rTableCell pos-rel">
                        <span class="active-arrow"></span>
                    </div>
                </div>
    
                <?php /* ?><div class="rTableRow ">
                    <div class="rTableCell"> 
                        <span> 
                            <a href="javascript:void(0)" data-ID="#dropboxSettings" class="normal-base-color grp-click-fn settings-link">
                                <span class="font-bold center-block">Dropbox Settings</span></a>
                            <span class="settings-text">Enable / Disable Dropbox</span> 
                        </span>
                    </div>
                    <div class="rTableCell pos-rel">
                        <span class="active-arrow"></span>
                    </div>
                </div>    <?php  */?>                    
                <div class="rTableRow">
                    <div class="rTableCell"> 
                        <span> 
                            <a href="javascript:void(0)" data-ID="#filestorageSettings" class="normal-base-color grp-click-fn settings-link">
                                <span class="font-bold center-block">File Storage &amp; CDN Settings</span></a>
                            <span class="settings-text">Enable / Disable Cloud file storage</span>
                        </span>
                    </div>
                    <div class="rTableCell pos-rel">
                        <span class="active-arrow"></span>
                    </div>
                </div>
                <div class="rTableRow ">
                    <div class="rTableCell"> 
                        <span> 
                            <a href="javascript:void(0)" data-ID="#emailserviceSettings" class="normal-base-color grp-click-fn settings-link">
                                <span class="font-bold center-block">Email Service Settings</span></a>
                            <span class="settings-text">Email Service (SMTP, AWS SES..) Settings</span>
                        </span>
                    </div>
                    <div class="rTableCell pos-rel">
                        <span class="active-arrow"></span>
                    </div>
                </div>                        
                <div class="rTableRow ">
                    <div class="rTableCell"> 
                        <span> 
                            <a href="javascript:void(0)" data-ID="#supportSettings" class="normal-base-color grp-click-fn settings-link">
                                <span class="font-bold center-block">Support Settings</span></a>
                            <span class="settings-text">Support chat system settings</span>
                        </span>
                    </div>
                    <div class="rTableCell pos-rel">
                        <span class="active-arrow"></span>
                    </div>
                </div>
                <!-- <div class="rTableRow ">
                    <div class="rTableCell"> 
                        <span> 
                            <a href="javascript:void(0)" data-ID="#securitySettings" class="normal-base-color grp-click-fn settings-link">
                                <span class="font-bold center-block">Content Security Settings</span></a>
                            <span class="settings-text">Content security settings</span>
                        </span>
                    </div>
                    <div class="rTableCell pos-rel">
                        <span class="active-arrow"></span>
                    </div>
                </div> -->

                <div class="rTableRow ">
                    <div class="rTableCell"> 
                        <span> 
                            <a href="javascript:void(0)" data-ID="#analyticsSettings" class="normal-base-color grp-click-fn settings-link">
                                <span class="font-bold center-block">Analytics</span></a>
                            <span class="settings-text">Analytics settings</span>
                        </span>
                    </div>
                    <div class="rTableCell pos-rel">
                        <span class="active-arrow"></span>
                    </div>
                </div>
                <div class="rTableRow ">
                    <div class="rTableCell"> 
                        <span> 
                            <a href="javascript:void(0)" data-ID="#profileFieldsSettings" class="normal-base-color grp-click-fn settings-link">
                                <span class="font-bold center-block">Profile Fields Settings</span></a>
                            <span class="settings-text">Create dynamic profile fields</span>
                        </span>
                    </div>
                    <div class="rTableCell pos-rel">
                        <span class="active-arrow"></span>
                    </div>
                </div>
                <?php /* ?>
                <div class="rTableRow ">
                    <div class="rTableCell"> 
                        <span> 
                            <a href="javascript:void(0)" data-ID="#newsletterFieldsSettings" class="normal-base-color grp-click-fn settings-link">
                                <span class="font-bold center-block">Newsletter Settings</span></a>
                            <span class="settings-text">Newsletter Provider & Fields</span>
                        </span>
                    </div>
                    <div class="rTableCell pos-rel">
                        <span class="active-arrow"></span>
                    </div>
                </div>
                <?php */ ?>
                <div class="rTableRow ">
                    <div class="rTableCell"> 
                        <span> 
                            <a href="javascript:void(0)" data-ID="#taxSettings" id="taxBtn" class="normal-base-color grp-click-fn settings-link">
                                <span class="font-bold center-block">TAX Settings</span></a>
                            <span class="settings-text">Manage TAX percentage</span>
                        </span>
                    </div>
                    <div class="rTableCell pos-rel">
                        <span class="active-arrow"></span>
                    </div>
                </div> 
                <div class="rTableRow ">
                    <div class="rTableCell"> 
                        <span> 
                            <a href="javascript:void(0)" data-ID="#paymentMode" id="taxBtn" class="normal-base-color grp-click-fn settings-link">
                                <span class="font-bold center-block">Payment Gateway Settings</span></a>
                            <span class="settings-text">Manage Payment Gateway</span>
                        </span>
                    </div>
                    <div class="rTableCell pos-rel">
                        <span class="active-arrow"></span>
                    </div>
                </div>
                <div class="rTableRow ">
                    <div class="rTableCell"> 
                        <span> 
                            <a href="javascript:void(0)" data-ID="#certificateSettings" id="certificateBtn" class="normal-base-color grp-click-fn settings-link">
                                <span class="font-bold center-block">Certificate Settings</span></a>
                            <span class="settings-text">Manage certificate template</span>
                        </span>
                    </div>
                    <div class="rTableCell pos-rel">
                        <span class="active-arrow"></span>
                    </div>
                </div>  

                <div class="rTableRow ">
                    <div class="rTableCell" onclick="restoreFromAnotherCourse()"> 
                        <span> 
                            <a href="javascript:void(0)" data-ID="#backup_restore_settings" id="backup_restore_settings_btn" class="normal-base-color grp-click-fn settings-link">
                            <span class="font-bold center-block">Backups</span></a>
                            <span class="settings-text">Listing course backups</span>
                        </span>
                    </div>
                    <div class="rTableCell pos-rel">
                        <span class="active-arrow"></span>
                    </div>
                </div>  

                <div class="rTableRow ">
                    <div class="rTableCell"> 
                        <span> 
                            <a href="javascript:void(0)" data-ID="#RestrictUserLogin" class="normal-base-color grp-click-fn settings-link">
                                <span class="font-bold center-block">Restricted Login Settings</span>
                                <span class="settings-text">Allow user login from only one device at a time</span>
                            </a>
                        </span>
                    </div>
                    <div class="rTableCell pos-rel">
                        <span class="active-arrow"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ====================== -->
        <!-- Group content section  -->



    </div>


    <!-- ==========================  -->
    <!--  LEFT CONTENT--> <!-- ENDS -->


</section>



<div class="col-sm-6 pad0 right-content">
    <div class="container-fluid right-box">

        <div class="row overflow100">

            <!--basic settings div content starts here -->
            <div class="col-sm-12 course-cont-wrap image-uploader innercontent" id="basicSettings"> 
                <h3 class="text-center">Basic Settings</h3>
                <div id="message_basic_div"></div>
                <div class="upload-prieview"> 
                    <div class="img-chng">
                        <div class="settings-logo">
                            <!-- <img id="loading" src="<?php //echo assets_url()    ?>images/loading.gif" style="display:none"> -->
                            <?php
                            $web_objects = $s3_setting_web['as_setting_value']['setting_value'];
                            $default_image = uploads_url().'uploads/default/logo/'.$web_objects->site_logo;
                            ?>
                            <?php $site_image = ((isset($web_objects->site_logo) && $web_objects->site_logo != 'default.png') ? (logo_path() . $web_objects->site_logo) : uploads_url().$default_image); ?>
                            <img id="site_logo" src="<?php echo $site_image.'?v='.rand(100, 999) ?>">
                        </div>
                        <input name="file" class="logo-image-upload-btn" id="site_logo_btn" accept="image/*" type="file">
                        <button class="btn btn-green pos-abs">CHANGE IMAGE<ripples></ripples></button>
                    </div>
                </div>
                <div class="upload-info">Supported File Format : Png &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; File Size :  275px x 105px<br>
                    For better view use logo without background
                </div>
                <div class="custom-form">
                    <input type="hidden" id="hidden_basic_id" value="<?php if (isset($s3_setting_web['as_key_id']) && $s3_setting_web['as_key_id'] != '') {
                                echo $s3_setting_web['as_key_id'];
                            } ?>">

                    <div class="form-group">
                        <div class="col-sm-12">
                            <span class="settings-text">Site Name / Title * :</span>                                                                
                            <input type="text" maxlength="50" class="form-control"   placeholder="eg: James Academy" id="title_text" value="<?php if (isset($s3_setting_web['as_setting_value']['setting_value']->site_name) && $s3_setting_web['as_setting_value']['setting_value']->site_name != '') {
                                echo $s3_setting_web['as_setting_value']['setting_value']->site_name;
                            } ?>">
                        </div>
                    </div>          
                    <div class="form-group">
                        <div class="col-sm-12">
                            <span class="settings-text">Banner Caption / Title * :</span>                                                                       
                            <input type="text" maxlength="50" class="form-control"   placeholder="eg: Learning At Your Finger Tips" id="banner_text" value="<?php if (isset($s3_setting_web['as_setting_value']['setting_value']->banner_text) && $s3_setting_web['as_setting_value']['setting_value']->banner_text != '') {
                                echo $s3_setting_web['as_setting_value']['setting_value']->banner_text;
                            } ?>">
                        </div>
                    </div>                  
                    <div class="form-group">
                        <div class="col-sm-12">
                            <span class="settings-text">Site Meta Description / About Us:</span>
                            <textarea class="form-control" id="meta_description" onkeyup="validateMaxLength(this.id)" maxlength="200"><?php if (isset($s3_setting_web['as_setting_value']['setting_value']->meta_description) && $s3_setting_web['as_setting_value']['setting_value']->meta_description != '') {
                                echo $s3_setting_web['as_setting_value']['setting_value']->meta_description;
                            } ?></textarea>
                        </div>
                        <div class="info-text text-right" id="meta_description_char_left">200 Characters Left</div>
                    </div> 

                    <div class="upload-info" style="padding-bottom: 0px !important">Set Favicon.ico Supported File Format : .png &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                    <div class="upload-prieview" style="padding-top:0px !important"> 
                    <div class="img-chng">
                        <div class="settings-logo">
                            <?php
                                $web_objects    = '';
                                if(file_exists(favicon_upload_path().'/favicon.png'))
                                {
                                    $favicon = base_url(favicon_upload_path().'favicon.png?v='.rand());
                                }
                                else
                                {
                                    $favicon = base_url('favicon.png').'?v='.rand();
                                }
                            ?>
                            
                            <img id="site_favicon" src="<?php echo $favicon; ?>">                        </div>
                        <input name="favicon" class="logo-image-upload-btn" id="site_favicon_btn" accept="image/*" type="file">
                        <button class="btn btn-green pos-abs">CHANGE FAVICON<ripples></ripples></button>
                    </div>
                </div>
                    
                    <div class="col-sm-12 save-btn"><input type="button" class="pull-right btn btn-green selected" value="SAVE" id="save_basic_button"></input></div>
                </div> 
            </div>
            <!--basic settings div ends here-->

            <!--homepage banner settings div starts here-->
            <div class="col-sm-12 course-cont-wrap image-uploader innercontent" id="homeSettings"> 
                <h3 class="text-center social-heading">Banner Settings</h3>
                <div id="banner_message"></div>
                <div class="col-sm-12">
                    <ul class="banner-list" id="banner_list">

                        <?php if(!empty($banners)): ?>
                        <?php foreach ($banners as $banner): ?>
                            <li class="col-sm-3">
                                <a href="javascript:void(0)" class="banner-thumb banner-item <?php echo (($banner['banner_active'] == '1')?'active-banner':'') ?>"  id="<?php echo $banner['id']; ?>">
                                    <img src="<?php echo banner_crop_path() . $banner['banner_name'] ?>" width="100%">
                                    <span class="triangle"><i class="icon icon-ok-circled"></i></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                        <?php /* ?><?php else: ?>
                            <li classass="col-sm-3">
                                <a href="javascript:void(0)" class="banner-thumb banner-item active-banner" id="0">
                                    <img src="<?php echo uploads_url('site/banner/default.jpg'); ?>" width="100%">
                                    <span class="triangle"><i class="icon icon-ok-circled"></i></span>
                                </a>
                            </li><?php */ ?>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-sm-12">
                    <div class="banner-setting"> 
                        <div class="text-center">
                            <input name="file" class="logo-image-upload-btn" id="site_banner_btn" accept="image/*" type="file">
                            <button class="btn btn-green pos-abs">UPLOAD BANNER</button>
                        </div>
                    </div>
                    <div class="banner-upload upload-info">Supported File Format : JPG &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; File Size :  1647px x 526px</div>
                    <div class="clearfix progress-custom" id="progress_div" style="display:none">
                        <div class="progress width100">
                            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                <span class="sr-only">0% Complete</span>
                            </div>
                        </div>
                        <span><b>Uploading...</b><b class="percentage-text"></b></span>
                    </div>        
                </div>                    
            </div>
            <!--homepage banner settings div ends here-->
            
            <!--homepage testimonial settings div starts here-->
            <div class="col-sm-12 course-cont-wrap image-uploader innercontent" id="testimonialSettings"> 
                <h3 class="text-center social-heading">Testimonials</h3>
                <div id="testimonial_message"></div>
                
                <div class="col-sm-12 text-center">
                    <input type="button" class="btn btn-green selected cancel-all" value="ADD TESTIMONIAL" id="add_testimonial"></input>
                </div>
                <div class="clearfix"></div>
                <div class="testimonial-create" id="testimonial_create"></div>
<!--                <div class="col-sm-12 text-center"><span class="settings-text">Support up to 5 Testimonial</span></div>-->
                <!-- <div class="clearfix progress-custom" id="progress_div_testimonial" style="display: none;">
                    <div class="progress width100">
                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                            <span class="sr-only">0% Complete</span>
                        </div>
                    </div>
                    <span><b>Uploading...</b><b class="percentage-text"></b></span>
                </div> -->

                <div class="testimonials_list">
                <?php if (!empty($testimonials)) 
                    {
                        foreach ($testimonials as $testimonial) 
                        { $checked = ($testimonial['t_featured'] == '1') ? 'checked="checked"' : '';
                            ?>
                            <div class="testimonial-manager" id="list_<?php echo $testimonial['id'] ?>">
                                <div class="testimonial-column preview">
                                    <div class="testimonial-user-info">
                                        <div class="user-info-edit">
                                            <label class="file-uploader">
                                                <!-- <img class="img-upload-icon" src="< ?php echo testimonial_path() . $testimonial['t_image'] ?>" /> -->
                                                <img class="img-upload-icon" id="testimonial_upload_image_preview_<?php echo $testimonial['id'] ?>" src="<?php echo testimonial_path() . $testimonial['t_image'] ?>" />
                                                <input type="file" class="testimonial-image" id="testimonial_image_<?php echo $testimonial['id'] ?>" value="<?php echo $testimonial['t_image'] ?>" data-img = "<?php echo $testimonial['t_image'] ?>" data-id="<?php echo $testimonial['id'] ?>"/>
                                            </label>
                                            <div class="user-details">
                                                <div class="form-group"><input type="text" maxlength="50" onkeypress="return preventNumbers(event)" value="<?php echo $testimonial['t_name']; ?>" id="testimonial_name_<?php echo $testimonial['id'] ?>" class="form-control"  placeholder="Name"></div>
                                                <div class=""><input type="text" maxlength="100" onkeypress="return preventNumbers(event)" class="form-control" id="testimonial_other_detail_<?php echo $testimonial['id'] ?>"  placeholder="Designation / Company / Place" value="<?php echo $testimonial['t_other_detail']; ?>"></div>
                                            </div>
                                        </div>
                                        <div class="user-info-preview">
                                            <div class="info-left">
                                                <div class="testimonial-avatar-preview"><img id="testimonial_image_preview_<?php echo $testimonial['id'] ?>" class="avatar" src="<?php echo testimonial_path() . $testimonial['t_image'] ?>" /></div>
                                                <div class="testimonial-username-designation-preview">
                                                    <div class="testimonial-username" id="testimonial_name_preview_<?php echo $testimonial['id'] ?>"><?php echo $testimonial['t_name']; ?></div>
                                                    <div class="testimonial-designation" id="testimonial_other_detail_preview_<?php echo $testimonial['id'] ?>"><?php echo $testimonial['t_other_detail']; ?></div>
                                                </div>
                                            </div>
                                            <div class="info-right">

                                                <label class="check-box-holder">
                                                    <span class="showin-home-text">Show in Home Page</span>
                                                    <label class="custom-checkbox ">
                                                        <input data-id="<?php echo $testimonial['id'] ?>" class="list-checkbox-featured" type="checkbox" <?php echo $checked ?>>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </label>
                                                
                                                <div class="dropdown testimonial-settings">
                                                    <div class="dropdown-toggle" data-toggle="dropdown"><span class="dot-icon">...</span></div>
                                                    <ul class="dropdown-menu">
                                                        <li><a href="#" data-id="<?php echo $testimonial['id'] ?>" class="edit-testimonial">Edit</a></li>
                                                        <li><a href="#" class="remove-testimonial" data-title="<?php echo $testimonial['t_name']; ?>" id="<?php echo $testimonial['id'] ?>">Delete</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="testimonial-content">
                                        <textarea rows="4" class="form-control" maxlength="200" onkeyup="validateMaxLength(this.id)" id="testimonial_content_<?php echo $testimonial['id'] ?>" placeholder="Testimonial"><?php echo $testimonial['t_text']; ?></textarea>
                                        <label class="pull-right testimonial-content-remain" id="testimonial_content_<?php echo $testimonial['id'] ?>_char_left">200 characters left</label>
                                        <div class="review-text"><p id="testimonial_content_preview_<?php echo $testimonial['id'] ?>"><?php echo $testimonial['t_text']; ?></p></div>
                                    </div>
                                    <div class="clearfix"></div><div class="message-testimonial" id="message_testimonial_<?php echo $testimonial['id'] ?>"></div>
                                    <div class="text-right testimonial-action">
                                            <label class="check-box-holder">
                                                <span class="showin-home-text">Show in Home Page</span>
                                                <label class="custom-checkbox ">
                                                    <input data-id="<?php echo $testimonial['id'] ?>" class="edit-checkbox-featured" type="checkbox" id="featured_testimonial_<?php echo $testimonial['id'] ?>" <?php echo $checked ?>>
                                                    <span class="checkmark"></span>
                                                </label>
                                            </label>
                                        <div class="">
                                            <input type="button" id="cancel<?php echo $testimonial['id'];?>" class="btn btn-red" data-id="<?php echo $testimonial['id'] ?>" value="CANCEL">
                                            <input type="button" class="btn btn-green update_testimonial" data-id="<?php echo $testimonial['id'] ?>" value="SAVE">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        <?php }
                    } ?>
                </div>
                
                <!-- <div class="col-sm-12 testimonials-list">
                    <ul id="testimonial_list">
< ?php if (!empty($testimonials)) {
    foreach ($testimonials as $testimonial) { ?>
                                <li class="testimonials-single" id="list_< ?php echo $testimonial['id']; ?>">
                                    <span class="closebtn icon icon-cancel-1 pull-right remove-testimonial" data-title="< ?php echo $testimonial['t_name']; ?>"  id="< ?php echo $testimonial['id']; ?>"></span>
                                     <span class="testimonial-thumb"></span> 
                                    <img class="testimonial-thumb" src="< ?php echo testimonial_path() . $testimonial['t_image'] ?>">
                                    <span class="testimonial-content">

                                        <span class="testimonial-author">< ?php echo $testimonial['t_name']; ?></span>

                                        <p class="testimonial-text">< ?php echo $testimonial['t_text']; ?></p>

                                    </span>

                                </li>
    < ?php }
} ?>                          
                    </ul>

                </div>    -->

                
            </div>
            <!--homepage banner settings div ends here-->

            <!--mobile banner settings div starts here-->
            <div class="col-sm-12 course-cont-wrap image-uploader innercontent" id="mobileBannerSettings"> 
                <h3 class="text-center social-heading">Mobile Banner Settings</h3>
                <div id="mobile_banner_message"></div>
                <div class="col-sm-12">
                    <ul class="banner-list" id="mobile_banner_list">
                        <?php if(!empty($mobile_banners)): ?>
                        <?php foreach ($mobile_banners as $mobile_banner): ?>
                            <li class="col-sm-3" id="mobile_banner_<?php echo $mobile_banner['id'] ?>">
                                <span class="remove_banner" id="<?php echo $mobile_banner['id']; ?>">&times;</span>
                                <a href="javascript:void(0)" class="banner-thumb mobile-banner-item <?php echo (($mobile_banner['mb_status'] == '1')?'active-banner':'') ?>"  id="<?php echo $mobile_banner['id']; ?>">
                                    <img src="<?php echo mobile_banner_crop_path() . $mobile_banner['mb_original_title'] ?>" width="100%">
                                    <span class="triangle"><i class="icon icon-ok-circled"></i></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-sm-12">
                    <div class="banner-setting"> 
                        <div class="text-center">
                            <input name="file" class="logo-image-upload-btn" id="mobile_banner_btn" accept="image/*" type="file">
                            <button class="btn btn-green pos-abs">UPLOAD MOBILE BANNER</button>
                        </div>
                    </div>
                    <div class="banner-upload upload-info">Supported File Format : JPG &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; File Size :  Min - 720px x 405px & Max - 1280px x 720px</div>
                    <div class="clearfix progress-custom" id="mobile_progress_div" style="display:none">
                        <div class="progress width100">
                            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                <span class="sr-only">0% Complete</span>
                            </div>
                        </div>
                        <span><b>Uploading...</b><b class="percentage-text"></b></span>
                    </div>        
                </div>                    
            </div>
            <!--mobile banner settings div ends here-->


            <!-- start of contact settings -->    
            <div class="col-sm-12 course-cont-wrap contact-settings innercontent" id="contactSettings"> 
                <h3 class="text-center">Contacts Settings</h3>
                <?php  //echo '<pre>';print_r($s3_setting_web['as_setting_value']['setting_value']); ?>
                <div id="contact_message"></div>
                <div class="custom-form">
                    <form method="post">
                        <input type="hidden" id="hidden_contact_id" value="<?php if (isset($s3_setting_web['as_key_id']) && $s3_setting_web['as_key_id'] != '') {
    echo $s3_setting_web['as_key_id'];
} ?>">
                        <div class="form-group">
                            <div class="col-sm-12">
                                <span class="settings-text">Phone Number (Toll free / Non Toll free)* :</span>
                                <input type="text" class="form-control" /*onkeypress="return preventAlphabets(event)"*/ placeholder="eg :  87879798666, 87879798667, 87879798668" id="contact_phone" value="<?php if(($s3_setting_web['as_setting_value']['setting_value']) && ($s3_setting_web['as_setting_value']['setting_value']->site_phone!='')) {
    echo str_replace('<br/>', ',',$s3_setting_web['as_setting_value']['setting_value']->site_phone);
} ?>">
                            </div>
                        </div>                            
                        <div class="form-group">
                            <div class="col-sm-12">
                                <span class="settings-text">Email Address For Communication* :</span>
                                <input type="text" class="form-control"   placeholder="eg :  info@example1.com, info@example2.com, info@example3.com" id="contact_email" value="<?php if(($s3_setting_web['as_setting_value']['setting_value']) && ($s3_setting_web['as_setting_value']['setting_value']->site_email!='')) {
    echo str_replace('<br/>', ',',$s3_setting_web['as_setting_value']['setting_value']->site_email);
} ?>">
                            </div>
                        </div> 
                        <div class="form-group">
                            <div class="col-sm-12">
                                <span class="settings-text">Whatsapp Number* :</span>
                                <input type="text" class="form-control"   placeholder="eg :  +91-9685741230" id="whatsapp_number" value="<?php if(($s3_setting_web['as_setting_value']['setting_value']) && ($s3_setting_web['as_setting_value']['setting_value']->site_whatsapp_number!='')) {
    echo $s3_setting_web['as_setting_value']['setting_value']->site_whatsapp_number;
} ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <span class="settings-text">Business Address* :</span> 
                                <textarea placeholder="eg :  Address Line 1, for next line Press Enter key!" class="form-control big-area" id="contact_address" maxlength="100"><?php if(($s3_setting_web['as_setting_value']['setting_value']) && ($s3_setting_web['as_setting_value']['setting_value']->site_address!='')) {
    echo $s3_setting_web['as_setting_value']['setting_value']->site_address;
} ?></textarea>
                            </div>
                        </div> 
                        <div class="col-sm-12 save-btn">
                            <input type="button" class="pull-right btn btn-green selected" value="SAVE" id="save_contact_button">
                        </div>
                    </form>
                </div> 
            </div>  
            <!-- end of contact settings --> 

            

            <!-- start of social media settings -->             
            <div class="col-sm-12 course-cont-wrap image-uploader innercontent" id="socialSettings"> 
                <h3 class="text-center social-heading">Social Media Settings</h3>
                <div id="show_fb_message"></div>
                <?php /*
                <div class="col-sm-9">
                    <span class="settings-text">Facebook Login and Registration</span>
                </div>
                <div class="col-sm-3">
                    <section class="model-check pull-right">
                        <div class="cust-checkbox">
                            <input type="checkbox" name="facebook" class="show-checkbox" <?php $checked = "checked"; echo ($has_facebook == true) ? $checked : '';?> id="fb_app_onoff"/>
                            <label></label>
                        </div>
                    </section><!-- !.Custom check box with css styles -->
                </div>
                <?php */?>    
                <form method="post">
                    <div class="show-checkbox-content smallbotpad" id="app_div_content">

                        <input type="hidden" id="fb_app_hidden" value="<?php if (isset($fb_setting['as_key_id']) && $fb_setting['as_key_id'] != '') {
                                       echo $fb_setting['as_key_id'];
                                   } ?>">
                        <div class="form-group">
                            <div class="col-sm-11 pull-right">
                                <span class="settings-text">Facebook App key* :</span>
                                <a href="#" class="social-links pull-right">How to get APP Key ?</a>
                                <input type="text" maxlength="50" class="form-control"   placeholder="eg :  FTYFTFYFINBNO" id="app_text" value="<?php if (isset($fb_setting['as_setting_value']['setting_value']->app_id) && $fb_setting['as_setting_value']['setting_value']->app_id != '') {
                                       echo $fb_setting['as_setting_value']['setting_value']->app_id;
                                   } ?>">
                            </div>
                        </div>                            
                        <!--         <div class="form-group">
                                    <div class="col-sm-11 pull-right">
                                        <span class="settings-text">Secret Key* :</span>
                                        <a href="#" class="social-links pull-right">How to get Secret Key ?</a>
                                        <input type="text" maxlength="50" class="form-control"   placeholder="eg :  BKJGVLH6767HFHJFKTYKFTDYRFJGDKKH" >
                                    </div>
                                </div> -->  
                    </div>
                    <div class="pull-left">

                        <input type="hidden" id="social_link_hidden" value="<?php if (isset($social_links['as_key_id']) && $social_links['as_key_id'] != '') {
                                       echo $social_links['as_key_id'];
                                   } ?>">
                        <div class="form-group">
                            <div class="col-sm-12">
                                <span class="settings-text">Facebook Page Link :</span>
                                <input type="text" maxlength="50" class="form-control"   placeholder="eg :  https://web.facebook.com/OfaBee/" id="fb_link_text" value="<?php if (isset($social_links['as_setting_value']['setting_value']->facebook) && $social_links['as_setting_value']['setting_value']->facebook != '') {
                                       echo $social_links['as_setting_value']['setting_value']->facebook;
                                   } ?>">
                            </div>
                        </div> 
                        <div class="form-group">
                            <div class="col-sm-12">
                                <span class="settings-text">Twitter Page Link :</span>
                                <input type="text" maxlength="50" class="form-control"   placeholder="eg :  https://twitter.com/Enfin?lang=en" id="twitter_link_text" value="<?php if (isset($social_links['as_setting_value']['setting_value']->twitter) && $social_links['as_setting_value']['setting_value']->twitter != '') {
                                       echo $social_links['as_setting_value']['setting_value']->twitter;
                                   } ?>">
                            </div>
                        </div> 
                        <div class="form-group" style="display:none;">
                            <div class="col-sm-12">
                                <span class="settings-text">YouTube Page Link :</span>
                                <input type="text" maxlength="50" class="form-control"   placeholder="eg :  https://www.youtube.com/channel/UC0cdAEx_i_Tgwi3eIvl" id="youtube_link_text" value="<?php if (isset($social_links['as_setting_value']['setting_value']->google_plus) && $social_links['as_setting_value']['setting_value']->google_plus != '') {
                                       echo $social_links['as_setting_value']['setting_value']->google_plus;
                                   } ?>">
                            </div>
                        </div> 

                        <div class="col-sm-12 save-btn"><input type="button" class="pull-right btn btn-green selected" value="SAVE" id="fb_save_button"/></div> 
                    </div> 
                </form>
            </div>
            <!-- end of social media settings -->     

            <!--dropbox settings div starts here-->
            <div class="col-sm-12 course-cont-wrap image-uploader innercontent" id="dropboxSettings"> 
                <h3 class="text-center social-heading">Dropbox Settings</h3>
                <div id="show_dropbox_message"></div>
                <div class="col-sm-9"><span class="settings-text">Enable Dropbox</span></div>
                <div class="col-sm-3">
                    <section class="model-check pull-right">
                        <div class="cust-checkbox">
                            <input type="checkbox" class="show-checkbox" <?php
                                   $checked = "checked";
                                   echo ($has_dropbox == true) ? $checked : '';
?> id="dropbox_checkbox" />
                            <label></label>
                        </div>
                    </section><!-- !.Custom check box with css styles -->
                </div>    

                <div class="show-checkbox-content" id="dropbox_div_content">
                    <form method="post">
                        <!-- <div class="form-group">
                            <div class="col-sm-11 pull-right">
                                <span class="settings-text">Dropbox App key* :</span>
                                <a href="#" class="social-links pull-right">How to get APP Key ?</a>
                                <input type="text" maxlength="50" class="form-control"   placeholder="eg :  FTYFTFYFINBNO" >
                            </div>
                        </div>    -->            

                        <input type="hidden" id="dropbox_hidden" value="<?php if (isset($drop_setting['as_key_id']) && $drop_setting['as_key_id'] != '') {
                                       echo $drop_setting['as_key_id'];
                                   } ?>">             
                        <div class="form-group botpad">
                            <div class="col-sm-11 pull-right">
                                <span class="settings-text">Dropbox Secret Key* :</span>
                                <a href="#" class="social-links pull-right">How to get Secret Key ?</a>
                                <input type="text" maxlength="50" class="form-control"   placeholder="eg :  BKJGVLH6767HFHJFKTYKFTDYRFJGDKKH" id="dropbox_text" value="<?php if (isset($drop_setting['as_setting_value']['setting_value']->secret_key) && $drop_setting['as_setting_value']['setting_value']->secret_key != '') {
                                       echo $drop_setting['as_setting_value']['setting_value']->secret_key;
                                   } ?>">
                            </div>
                        </div>  
                        <div class="col-sm-12 save-btn"><input type="button" class="pull-right btn btn-green selected" value="SAVE" id="dropbox_button"></input></div>  
                    </form>
                </div>
            </div>
            <!--dropbox settings div ends here-->


            <!--file storage and cdn settings div starts here-->
            <div class="col-sm-12 course-cont-wrap image-uploader innercontent" id="filestorageSettings"> 
                <h3 class="text-center social-heading">File Storage & CDN Settings</h3>
                <div id="storage_cdn_div"></div>
                <div class="col-sm-9"><span class="settings-text center-block">Enable AWS S3</span>
                    <span class="info-text">Store Course contents in Amazon cloud</span>
                </div>
                <div class="col-sm-3">
                    <section class="model-check pull-right">
                        <div class="cust-checkbox">
                            <input type="checkbox" name="facebook" class="show-checkbox" <?php $checked = "checked";
                                   echo ($has_s3 == true) ? $checked : '';
?> id="storage_s3_onoff"/>
                            <label></label>
                        </div>
                    </section><!-- !.Custom check box with css styles -->
                </div>    
                <form method="post">
                    <div class="show-checkbox-content" id="div_aws_storage">

                        <input type="hidden" id="storage_hidden_key" value="<?php if (isset($s3_account['as_key_id']) && $s3_account['as_key_id'] != '') {
                                       echo $s3_account['as_key_id'];
                                   } ?>">
                        <input type="hidden" id="cdn_hidden_key" value="<?php if (isset($cdn_url['as_key_id']) && $cdn_url['as_key_id'] != '') {
                                       echo $cdn_url['as_key_id'];
                                   } ?>">
                        <div class="form-group">
                            <div class="col-sm-11 pull-right">
                                <span class="settings-text">AWS App key* :</span>
                                <a href="#" class="social-links pull-right">How to get APP Key ?</a>
                                <input type="text" maxlength="50" class="form-control"   placeholder="eg :  FTYFTFYFINBNO" value="<?php if (isset($s3_account['as_setting_value']['setting_value']->s3_access) && $s3_account['as_setting_value']['setting_value']->s3_access != '') {
                                       echo $s3_account['as_setting_value']['setting_value']->s3_access;
                                   } ?>" id="storage_app_key_text">
                            </div>
                        </div>                            
                        <div class="form-group">
                            <div class="col-sm-11 pull-right">
                                <span class="settings-text">Secret Key* :</span>
                                <a href="#" class="social-links pull-right">How to get Secret Key ?</a>
                                <input type="text" maxlength="50" class="form-control"   placeholder="eg :  BKJGVLH6767HFHJFKTYKFTDYRFJGDKKH" value="<?php if (isset($s3_account['as_setting_value']['setting_value']->s3_secret) && $s3_account['as_setting_value']['setting_value']->s3_secret != '') {
                                       echo $s3_account['as_setting_value']['setting_value']->s3_secret;
                                   } ?>" id="storage_secret_key_text">
                            </div>
                        </div> 
                        <div class="form-group">
                            <div class="col-sm-11 pull-right">
                                <span class="settings-text">Bucket Name* :</span>
                                <a href="#" class="social-links pull-right">How to configure s3 Bucket ?</a>
                                <input type="text" maxlength="50" class="form-control"   placeholder="eg :  Ofabee" value="<?php if (isset($s3_account['as_setting_value']['setting_value']->s3_bucket) && $s3_account['as_setting_value']['setting_value']->s3_bucket != '') {
                                       echo $s3_account['as_setting_value']['setting_value']->s3_bucket;
                                   } ?>" id="storage_bucket_text">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-11 pull-right">
                                <span class="settings-text">Region* :</span>
                                <input type="text" maxlength="50" class="form-control"   placeholder="eg : ap-south-1" value="<?php if (isset($s3_account['as_setting_value']['setting_value']->s3_region) && $s3_account['as_setting_value']['setting_value']->s3_region != '') {
                                       echo $s3_account['as_setting_value']['setting_value']->s3_region;
                                   } ?>" id="storage_region_text">
                            </div>
                        </div>  

                        <div class="form-group">
                            <div class="col-sm-11 pull-right">
                                <span class="settings-text">CDN URL(<small>Enter if you have the CDN url. Not mandatory</small>) :</span>
                                <input type="text" maxlength="50" class="form-control"   placeholder="eg : xyz.cloudfront.net" value="<?php if (isset($s3_account['as_setting_value']['setting_value']->cdn) && $s3_account['as_setting_value']['setting_value']->cdn != '') {
                                       echo $s3_account['as_setting_value']['setting_value']->cdn;
                                   } ?>" id="storage_cdn_text">
                            </div>
                        </div>        
                    </div>

                    <?php /* ?><div class="cdn-head" id="head_cdn">
                        <div class="col-sm-9">
                            <span class="settings-text center-block">Enable CDN</span>
                            <span class="info-text">CDN makes your content delivery flawless.</span>
                        </div>
                        <div class="col-sm-3">
                            <section class="model-check pull-right">
                                <div class="cust-checkbox">
                                    <input type="checkbox" class="show-cdn" <?php $checked = "checked";
                                   echo ($has_cdn == true) ? $checked : '';
?> id="storage_cdn_onoff"/>
                                    <label></label>
                                </div>
                            </section><!-- !.Custom check box with css styles -->
                        </div>
                    </div>          
                    <div class="show-cdn-content" id="div_cdn_storage">
                        <div class="form-group">
                            <div class="col-sm-11 pull-right">
                                <span class="settings-text">CDN Url* :</span>
                                <a href="#" class="social-links pull-right">CDN?</a>
                                <input type="text" maxlength="50" class="form-control"   placeholder="eg :  https:/yourcdn.com/url" value="<?php if (isset($cdn_url['as_setting_value']['setting_value']->cdn) && $cdn_url['as_setting_value']['setting_value']->cdn != '') {
                                       echo $cdn_url['as_setting_value']['setting_value']->cdn;
                                   } ?>" id="storage_cdn_text">
                            </div>
                        </div>                            
                    </div><?php */ ?>

                    <div class="col-sm-12 save-btn"><input type="button" class="pull-right btn btn-green selected" value="SAVE" id="save_storage_button"></input></div>  
                </form>
            </div>
            <!--file storage and cdn settings div ends here-->

            <!-- start of Email Service settings -->    
            <div class="col-sm-12 course-cont-wrap image-uploader innercontent" id="emailserviceSettings"> 
                <h3 class="text-center social-heading">Email Service Settings</h3>
                <div id="email_show_message"></div>
                <div class="col-sm-9"><span class="settings-text">Enable AWS SES</span></div>
                <div class="col-sm-3">
                    <section class="model-check pull-right">
                        <div class="cust-checkbox">
                            <input type="checkbox" class="show-checkbox"  <?php $checked = "checked";
                                   echo ($has_s3_mail == true) ? $checked : ''; ?> id="s3_onoff"/>
                            <label></label>
                        </div>
                    </section><!-- !.Custom check box with css styles -->
                </div>    
                <form method="post">

                    <input type="hidden" id="s3_hidden_key" value="<?php if (isset($s3_mail_account['as_key_id']) && $s3_mail_account['as_key_id'] != '') {
                                       echo $s3_mail_account['as_key_id'];
                                   } ?>">
                    <div class="show-checkbox-content smallbotpad" id="s3_show_div">
                        <div class="form-group">
                            <div class="col-sm-11 pull-right">
                                <span class="settings-text">AWS mail* :</span>
                                <input type="text" maxlength="50" class="form-control"   placeholder="eg : info@email.com" id="s3_email" value="<?php if (isset($s3_mail_account['as_setting_value']['setting_value']->mail_email) && $s3_mail_account['as_setting_value']['setting_value']->mail_email != '') {
                                       echo $s3_mail_account['as_setting_value']['setting_value']->mail_email;
                                   } ?>">
                            </div>
                        </div> 
                        <div class="form-group">
                            <div class="col-sm-11 pull-right">
                                <span class="settings-text">AWS App key* :</span>
                                <a href="#" class="social-links pull-right">How to get APP Key ?</a>
                                <input type="text" maxlength="50" class="form-control"   placeholder="eg :  FTYFTFYFINBNO" id="s3_app_text" value="<?php if (isset($s3_mail_account['as_setting_value']['setting_value']->mail_key) && $s3_mail_account['as_setting_value']['setting_value']->mail_key != '') {
                                       echo $s3_mail_account['as_setting_value']['setting_value']->mail_key;
                                   } ?>">
                            </div>
                        </div>                            
                        <div class="form-group">
                            <div class="col-sm-11 pull-right">
                                <span class="settings-text">Secret Key* :</span>
                                <a href="#" class="social-links pull-right">How to get Secret Key ?</a>
                                <input type="text" maxlength="50" class="form-control"   placeholder="eg :  BKJGVLH6767HFHJFKTYKFTDYRFJGDKKH" id="s3_secret_text" value="<?php if (isset($s3_mail_account['as_setting_value']['setting_value']->mail_secret) && $s3_mail_account['as_setting_value']['setting_value']->mail_secret != '') {
                                       echo $s3_mail_account['as_setting_value']['setting_value']->mail_secret;
                                   } ?>">
                            </div>
                        </div> 
                    </div>

                    <div class="cdn-head" style="display: none;">
                        <div class="col-sm-9"><span class="settings-text">Enable SMTP</span></div>
                        <div class="col-sm-3">
                            <section class="model-check pull-right">
                                <div class="cust-checkbox">
                                    <input type="checkbox" class="show-cdn" <?php
                                   $checked = "checked";
                                   echo ($has_smtp == true) ? $checked : '';
?> id="smtp_onoff"/>
                                    <label></label>
                                </div>
                            </section><!-- !.Custom check box with css styles -->
                        </div>
                    </div>          
                    <div class="show-cdn-content" id="smtp_show_div">

                        <input type="hidden" id="smtp_hidden_key" value="<?php if (isset($smtp_account['as_key_id']) && $smtp_account['as_key_id'] != '') {
                                       echo $smtp_account['as_key_id'];
                                   } ?>">
                        <div class="form-group">
                            <div class="col-sm-11 pull-right">
                                <span class="settings-text">SMTP Host* :</span>
                                <input type="text" maxlength="50" class="form-control"   placeholder="eg :  abcd.domain.com" id="smtp_user_host" value="<?php if (isset($smtp_account['as_setting_value']['setting_value']->host) && $smtp_account['as_setting_value']['setting_value']->host != '') {
                                       echo $smtp_account['as_setting_value']['setting_value']->host;
                                   } ?>">
                            </div>
                        </div> 
                        <div class="form-group">
                            <div class="col-sm-11 pull-right">
                                <span class="settings-text">SMTP Port* :</span>
                                <input type="text" maxlength="10" class="form-control"   placeholder="eg :  25" id="smtp_user_port" value="<?php if (isset($smtp_account['as_setting_value']['setting_value']->port) && $smtp_account['as_setting_value']['setting_value']->port != '') {
                                       echo $smtp_account['as_setting_value']['setting_value']->port;
                                   } ?>">
                            </div>
                        </div> 
                        <div class="form-group">
                            <div class="col-sm-11 pull-right">
                                <span class="settings-text">SMTP Username* :</span>
                                <input type="text" maxlength="50" class="form-control"   placeholder="eg :  FTYFTFYFINBNO" id="smtp_user_text" value="<?php if (isset($smtp_account['as_setting_value']['setting_value']->user_name) && $smtp_account['as_setting_value']['setting_value']->user_name != '') {
                                       echo $smtp_account['as_setting_value']['setting_value']->user_name;
                                   } ?>">
                            </div>
                        </div> 
                        <div class="form-group">
                            <div class="col-sm-11 pull-right">
                                <span class="settings-text">SMTP Password* :</span>
                                <input type="text" maxlength="50" class="form-control"   placeholder="eg :  BKJGVLH6767HFHJFKTYKFTDYRFJGDKKH" id="smtp_pass_text" value="<?php if (isset($smtp_account['as_setting_value']['setting_value']->password) && $smtp_account['as_setting_value']['setting_value']->password != '') {
                                       echo $smtp_account['as_setting_value']['setting_value']->password;
                                   } ?>">
                            </div>
                        </div>                                       
                    </div>

                    <div class="col-sm-12 save-btn"><input type="button" class="pull-right btn btn-green selected" value="SAVE" id="email_save_button"></input></div>  
                </form>
            </div>    
            <!-- start of Email Service settings -->   
            
            <!-- start of support settings -->
            <div class="col-sm-12 course-cont-wrap image-uploader innercontent" id="supportSettings"> 
                <h3 class="text-center social-heading">Support Chat Settings</h3>
                <div id="support_message"></div>
                <div class="col-sm-9"><span class="settings-text">Enable Support Chat</span></div>
                <div class="col-sm-3">
                    <section class="model-check pull-right">
                        <div class="cust-checkbox">
                            <input type="checkbox" class="show-checkbox" value="1" <?php
                                   $checked = "checked";
                                   echo ($support['support_chat_status'] == 1) ? $checked : '';
?> id="enable_chat"/>
                            <label></label>
                        </div>
                    </section><!-- !.Custom check box with css styles -->
                </div>    
                <div class="show-checkbox-content" id="support_chat_content">
                    <div class="form-group botpad">
                        <div class="col-sm-12">
                            <span class="settings-text center-block text-center smallpad">System will support Fresh Desk, Zopiom Chat etc..</span></div>
                        <div class="col-sm-11 pull-right">
                            <span class="settings-text">Support Chat Script* :</span>
                            <a href="#" class="social-links pull-right">How to get APP Key ?</a>
                            <textarea class="form-control big-area" id="text_chat_script"><?php if ($support['support_chat_script'] && $support['support_chat_script'] != "") {
                                       echo base64_decode($support['support_chat_script']);
                                   } ?></textarea>
                        </div>
                    </div>  
                    <div class="col-sm-12 save-btn"><input type="button" class="pull-right btn btn-green selected" value="SAVE" id="button_support_chat"></input></div>                           
                </div>

            </div>
            <!-- end of support settings -->   

            <!-- start of content security settings -->  

             <div class="col-sm-12 course-cont-wrap image-uploader innercontent" id="securitySettings"> 
                <h3 class="text-center social-heading">Content Security Settings</h3>
                <div id="content_security_message"></div>
                <div class="col-sm-9"><span class="settings-text">Enable content security</span></div>
                <div class="col-sm-3">
                        <section class="model-check pull-right">
                        <div class="cust-checkbox">
                                   
                                <input type="checkbox" class="show-checkbox" value="1" <?php 
                                   $checked = "checked";
                                   echo ($content_security['as_setting_value']['setting_value']->content_security_status == '1') ? $checked : ''; 
?> id="enable_content_security"/>
                            <label></label>
                            <input type="hidden" name="content_security_setting_key" id="content_security_setting_key" value="<?php echo $content_security['as_key_id']; ?>" >
                        </div>
                    </section><!-- !.Custom check box with css styles -->
                </div>    
                <div class="show-checkbox-content" id="content_security_settings">
                    <div class="col-sm-12 save-btn">
                        <input type="button" class="pull-right btn btn-green selected" value="SAVE" id="button_content_security">
                    </div>                           
                </div>

            </div>
            <!-- end of content security settings -->  


            <!-- starts restricted user login settings -->  

            <div class="col-sm-12 course-cont-wrap image-uploader innercontent" id="RestrictUserLogin">
                <h3 class="text-center social-heading">Restricted Login Settings</h3>
                <div id="restrict-login_security_message"></div>
                <div class="col-sm-9"><span class="settings-text">Allow user login from only one device at a time</span></div>
                <div class="col-sm-3">
                    <section class="model-check pull-right">
                        <div class="cust-checkbox">
                            <input type="checkbox" class="show-checkbox" value="1" <?php 
                                   $checked = "checked";
                                   echo ($restrictUser['as_setting_value']['setting_value']->login_restricted == '1') ? $checked : ''; ?> id="login_restricted"/>
                            <label></label>
                            <input type="hidden" name="login_restricted_setting_key" id="login_restricted_setting_key" value="<?php echo $restrictUser['as_key_id']; ?>" >
                        </div>
                    </section><!-- !.Custom check box with css styles -->
                </div> 

            </div>
            <!-- ends restricted user login settings-->  


            <!-- start of Analytics settings -->
            <div class="col-sm-12 course-cont-wrap image-uploader innercontent" id="analyticsSettings"> 
                <h3 class="text-center social-heading">Analytics Settings</h3>
                <div id="analytics_message"></div>
                <input type="hidden" name="analytics_hidden" id="analytics_hidden" value="<?php echo base64_encode($google_analytics['id']); ?>">
                <div class="col-sm-9"><span class="settings-text">Enable Analytics</span></div>
                <div class="col-sm-3">
                    <section class="model-check pull-right">
                        <div class="cust-checkbox">
                            <input type="checkbox" class="show-checkbox" value="1" <?php
                                   $checked = "checked";
                                   echo ($google_analytics['as_siteadmin_value'] == true) ? $checked : '';
?> id="enable_analytics"/>
                            <label></label>
                        </div>
                    </section><!-- !.Custom check box with css styles -->
                </div>    
                <div class="show-checkbox-content" id="analytics_content">
                    <div class="form-group botpad">
                        <div class="col-sm-12">
                            <span class="settings-text center-block text-center smallpad">Support Google Analytics.</span>
                        </div>
                        <div class="col-sm-11 pull-right">
                            <span class="settings-text">Analytics Script* :</span>
                            <a href="javascript:void(0)" class="social-links pull-right">You need to verfiy your identity manually.</a>
                                <?php $a_script = $google_analytics['as_setting_value']['setting_value']->script;?>
                            <textarea class="form-control big-area" id="text_analytics_script">
                                <?php if ($a_script && $a_script != "") {
                                    echo base64_decode($a_script);
                                } ?>
                            </textarea>
                        </div>
                        <div class="col-sm-11 pull-right" style="margin-top:20px">
                            <span class="settings-text">Analytics Access Url* :</span>
                            <?php $access_url = $google_analytics['as_setting_value']['setting_value']->access_url;?>
                            <input type="text" id="analytic_url" class="form-control" value=" <?php if ($access_url && $access_url != "") {echo base64_decode($access_url);} ?>"/>
                        </div>
                    </div>  
                    <div class="col-sm-12 save-btn"><button type="button" class="pull-right btn btn-green selected" id="button_analytics">SAVE</button></div>                           
                </div>

            </div>
            <!-- end of Analytics settings -->  

            <!-- start of profile fields setting -->
<?php include_once "profile_field_form.php"; ?>
            <!-- End of profile fields setting -->


            <!-- start of Email Service settings -->    
            <div class="col-sm-12 course-cont-wrap image-uploader innercontent" id="newsletterFieldsSettings"> 
                <h3 class="text-center social-heading">NewsLetter Settings</h3>
                <div id="newsletter_show_message"></div>
                <input type="hidden" id="newsletter_hidden_key" value="<?php if (isset($mail_subscription['as_key_id']) && $mail_subscription['as_key_id'] != '') {
    echo $mail_subscription['as_key_id'];
} ?>">
                <div class="col-sm-9"><span class="settings-text">Enable/Disable NewsLetter</span></div>
                <div class="col-sm-3">
                    <section class="model-check pull-right">
                        <div class="cust-checkbox">
                            <input type="checkbox" class="show-checkbox"  <?php $checked = "checked";
echo ($mail_subscription['as_siteadmin_value'] == true) ? $checked : ''; ?> id="newsletter_onoff"/>
                            <label></label>
                        </div>
                    </section><!-- !.Custom check box with css styles -->
                </div>    
                <div id="newsletter_show_message"></div>
                <div id="zoho_label" class="col-sm-9"><span class="settings-text">Enable ZohoCampaigns</span></div>
                <div id="zoho_onoff_input" class="col-sm-3">
                    <section class="model-check pull-right">
                        <div class="cust-checkbox">
                            <input type="checkbox" class="show-checkbox"  <?php $checked = "checked";
echo ($zoho['as_siteadmin_value'] == true) ? $checked : ''; ?> id="zoho_onoff"/>
                            <label></label>
                        </div>
                    </section><!-- !.Custom check box with css styles -->
                </div>    
                <form method="post">

                    <input type="hidden" id="zoho_hidden_key" value="<?php if (isset($zoho['as_key_id']) && $zoho['as_key_id'] != '') {
    echo $zoho['as_key_id'];
} ?>">
                    <div class="show-checkbox-content smallbotpad" id="zoho_show_div">
                        <div class="form-group">
                            <div class="col-sm-11 pull-right">
                                <span class="settings-text">Zoho campaign Api key * :</span>
                                <input type="text" maxlength="50" class="form-control"   placeholder="eg :  ajhfkjhfsjdfygwe" id="zoho_api_key" value="<?php if (isset($zoho['as_setting_value']['setting_value']->api_key) && $zoho['as_setting_value']['setting_value']->api_key != '') {
    echo $zoho['as_setting_value']['setting_value']->api_key;
} ?>">
                            </div>
                        </div>                            
                    </div>

                    <div class="cdn-head">
                        <div id="mailchimp_label" class="col-sm-9"><span class="settings-text">Enable MailChimp</span></div>
                        <div id="mailchimp_onoff_input" class="col-sm-3">
                            <section class="model-check pull-right">
                                <div class="cust-checkbox">
                                    <input type="checkbox" class="show-cdn" <?php $checked = "checked";
echo ($mailchimp['as_siteadmin_value'] == true) ? $checked : ''; ?> id="mailchimp_onoff"/>
                                    <label></label>
                                </div>
                            </section><!-- !.Custom check box with css styles -->
                        </div>
                    </div>          
                    <div class="show-cdn-content" id="mailchimp_show_div">

                        <input type="hidden" id="mailchimp_hidden_key" value="<?php if (isset($mailchimp['as_key_id']) && $mailchimp['as_key_id'] != '') {
    echo $mailchimp['as_key_id'];
} ?>" >
                        <div class="form-group">
                            <div class="col-sm-11 pull-right">
                                <span class="settings-text">MailChimp Api key * :</span>
                                <input type="text" maxlength="50" class="form-control"   placeholder="eg :  asdjsjfgsytwyyw7ewew" id="mailchimp_api_key" value="<?php if (isset($mailchimp['as_setting_value']['setting_value']->api_key) && $mailchimp['as_setting_value']['setting_value']->api_key != '') {
    echo $mailchimp['as_setting_value']['setting_value']->api_key;
} ?>">
                            </div>
                        </div> 

                    </div>

                    <div class="col-sm-12 save-btn"><input type="button" class="pull-right btn btn-green selected" value="SAVE" id="newsletter_save_button"></input></div>  
                </form>
            </div>    
            <!-- End of Newsletter settings --> 

             <!-- start of gst settings -->    
             <div class="col-sm-12 course-cont-wrap contact-settings innercontent" id="taxSettings"> 
                <h3 class="text-center">TAX Settings</h3>
                <div id="gst_message"></div>
                <div class="custom-form">
                    <form method="post">
                        <input type="hidden" id="hidden_gst_id" value="<?php if (isset($gst_setting['as_key_id']) && $gst_setting['as_key_id'] != '') {
    echo $gst_setting['as_key_id'];
} ?>">
                        <div class="form-group">
                            <div class="col-sm-12">
                                <span class="settings-text">CGST :</span>
                                <input type="text" maxlength="6" class="form-control" onkeypress="return isNumber(event)"   placeholder="eg :  10" id="cgst" value="<?php if ($gst_setting['as_setting_value']['setting_value']->cgst && $gst_setting['as_setting_value']['setting_value']->cgst != '') {
    echo $gst_setting['as_setting_value']['setting_value']->cgst;
} ?>">
                            </div>
                        </div>                            
                        
                        <div class="form-group">
                            <div class="col-sm-12">
                                <span class="settings-text">SGST :</span>
                                <input type="text" maxlength="6" class="form-control" onkeypress="return isNumber(event)"   placeholder="eg :  10" id="sgst" value="<?php if ($gst_setting['as_setting_value']['setting_value']->sgst && $gst_setting['as_setting_value']['setting_value']->sgst != '') {
    echo $gst_setting['as_setting_value']['setting_value']->sgst;
} ?>">
                            </div>
                        </div>  
                        <div class="col-sm-12 save-btn">
                            <input type="button" class="pull-right btn btn-green selected" value="SAVE" id="save_gst_button"></input>
                        </div>
                    </form>
                </div> 
            </div>  
            <!-- end of gst settings --> 

            <!-- start of gateway settings -->    
            <div class="col-sm-12 course-cont-wrap contact-settings innercontent" id="paymentMode"> 
               <?php include_once 'payment.php'; ?>
            </div>  
            <!-- end of gst settings -->
            
            <!--certificate settings div content starts here -->
            <div class="col-sm-12 course-cont-wrap image-uploader innercontent" id="certificateSettings"> 
                <?php /* ?><h3 class="text-center">Certificate Settings</h3>
                <div id="message_certificate_div"></div>
                <div class="upload-prieview"> 
                    <div class="">
                        <div class="fle-upload">
                            <label class="fle-lbl">BROWSE</label>
                            <input class="form-control upload" id="import_certificate" type="file">
                            <input value="" readonly="" class="form-control upload-file-name" id="upload_certificate_file" type="text">
                        </div>
                        <div class="clearfix progress-custom" id="percentage_bar" style="display: none">
                            <div class="progress width100">
                                <div style="width: 60%;" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class="progress-bar progress-bar-success">
                                    <span class="sr-only">60% Complete</span>
                                </div>
                            </div>
                            <span class="">Uploading...<b class="percentage-text">60%</b></span>
                        </div>
                        <div class="custom-form">
                            <div class="col-sm-12 save-btn"><input type="button" class="pull-right btn btn-green selected" onclick="uploadCertificate()" value="Upload" id="save_certificate_button"></input></div>
                        </div> 
                        <div class="settings-certificate-template">
                            <div class="preivew-area  text-center">
                                <?php if(!empty($certificates)){
                                    $converted_path = template_upload_path().$certificates['cm_filename'];
                                    //echo template_path();
                                    $converted_file = scandir($converted_path);
                                    //echo "<pre>";print_r($converted_file);
                                    unset($converted_file[0]);
                                    unset($converted_file[1]);
                                ?>
                                    <?php foreach ($converted_file as $file): ?>
                                        <div class="preivew-area overflw-Y-scroll text-center">
                                            <img style="width:100%;" src="<?php echo template_path().$certificates['cm_filename'].'/'.$file ?>" alt="<?php echo $file ?>" >
                                        </div>
                                    <?php endforeach; ?>
                                <?php } ?>
                            </div> 
                        </div>
                    </div>
                </div><?php */ ?>
                <?php include_once 'certificate_settings.php'; ?>
            </div>
            <div class="col-sm-12 course-cont-wrap image-uploader innercontent" id="backup_restore_settings"> 
                <?php include_once 'backup_listing.php'; ?>
            </div>
            <!--certificate settings div ends here-->

        </div>

    </div>
</div>

</div>
<!-- Manin Iner container end -->

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
                <button type="button" class="btn btn-red" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
<!-- !.Modal pop up contents :: Delete Section popup-->
<!-- Modal pop up contents:: Delete Section popup-->
<div class="modal fade alert-modal-new" id="activate_user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-group">
                    <b id="confirm_box_title_delete"></b>
                    <p class="m0" id="confirm_box_content_1"> </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-green" data-dismiss="modal">CANCEL</button>
                <button type="button" class="btn btn-red" id="confirm_box_ok">CONTINUE</button>
            </div>
        </div>
    </div>
</div>
<!-- !.Modal pop up contents :: Delete Section popup-->

<!-- Modal pop up contents :: Create new section popup-->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="addblock" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                <h4 class="modal-title" id="myModalLabel">Create New Block</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Block Title*:</label>
                    <input type="text" id="block_name_create" placeholder="eg: Certifications" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-red" data-dismiss="modal" onclick="$('.close').click()">CANCEL</button>
                <button type="button" class="btn btn-green" id="add_block_save_ok">CREATE</button>
                
            </div>
        </div>
    </div>
</div>
<!-- !.Modal pop up contents :: create new section popup-->

<!-- Modal pop up contents :: Create new section popup-->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="addblockdraganddrop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                <h4 class="modal-title" id="myModalLabel">Create New Block</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Block Title*:</label>
                    <input type="text" id="block_name_create_on_drag_drop" placeholder="eg: Mathematical Calculations" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-red" id="cancel_block_drag_drop" data-dismiss="modal">CANCEL</button>
                <button type="button" class="btn btn-green" id="save_block_drag_drop">CREATE</button>
                
            </div>
        </div>
    </div>
</div>
<!-- !.Modal pop up contents :: create new section popup-->

<!-- Modal pop up contents :: rename section popup-->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="rename_block" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                <h4 class="modal-title" id="myModalLabel">Rename Block</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Block Title*:</label>
                    <input type="text" placeholder="eg: Mathematical Calculations" class="form-control" id="block_name_rename">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-red" data-dismiss="modal" onclick="$('.close').click();">CANCEL</button>
                <button type="button" class="btn btn-green" id="block_save_ok"></button>
            </div>
        </div>
    </div>
</div>
<!-- !.Modal pop up contents :: rename section popup-->

<!-- Modal pop up contents :: Create html -->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="profile_field" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                <h4 class="modal-title" id="myModalLabel">PROFILE FIELD FORM</h4>
            </div>
            <div class="modal-body">
            <div class="form-group">
                    <label>Field Label *:</label>
                    <input type="text" maxlength="80" placeholder="eg: SSLC mark" id="field_label" class="form-control">
                </div>
                <div class="form-group">
                    <label>Field Type *:</label>
                    <select class="form-control" id="field_input_type" onchange="updateFieldAttributes()">
                        <option value="1" selected="selected">Text Input</option>
                        <option value="2">Dropdown</option>
                    </select>
                </div>
                <div class="form-group" id="placeholder_wrapper">
                    <label>Placeholder :</label>
                    <input type="text" maxlength="80" placeholder="eg: Enter SSLC mark" id="field_placeholder" class="form-control">
                </div>
                
                <div class="form-group" id="default_value_wrapper">
                    <label>Default Value *:</label>
                    <input type="text" onkeypress="return preventHtmlTag(event)"  onblur="triggerProcessDefaultValues(this)" placeholder="eg: option 1, option 2" id="field_default_value" class="form-control">
                </div>
                <!-- Click to get add the cateogry to the select box -->
                <div class="form-group add-category clearfix" id="block_field_wrapper">
                    <div class="add-selectn alignment-order">
                        <label>Block *:</label>
                        <select class="form-control" id="block_id_field">
                        </select>
                        <input type="text" aria-describedby="basic-addon2" id="block_name_field" placeholder="eg: Catalog" class="form-control" id="block_name_field">
                    </div>
                    <div class="add-btn alignment-order">
                        <label>Or</label>
                        <a href="javascript:void(0)" class="btn btn-danger" id="create_new_block_cancel_field">CANCEL</a>
                        <a href="javascript:void(0)" class="btn btn-green" id="create_new_block_field">ADD NEW BLOCK</a>
                        
                    </div>
                </div>
                <!-- !.Click to get add the cateogry to the select box -->
                <div class="form-group">
                    <div class="checkbox"><label><input type="checkbox" id="is_field_mandatory" value="1"><span class="ap_cont chk-box">Is this field mandatory</span></label></div>
                </div>
                <div class="form-group" id="enable_autosuggestion_wrapper">
                    <div class="checkbox"><label><input type="checkbox" id="enable_autosuggestion" value="1"><span class="ap_cont chk-box">Enable autosuggestion</span></label></div>
                </div>
            </div>
            <div class="modal-footer">
                
                <button type="button" class="btn btn-red" data-dismiss="modal" onclick="$('.close').click();">CANCEL</button>
                <button type="button" class="btn btn-green" id="save_profile_field_btn" onclick="saveField()" >SAVE</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal pop up contents :: Create html -->


<!-- Modal pop up contents:: Delete Section popup-->
<div class="modal fade alert-modal-new" id="deleteBlock" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
            <div class="modal-body">
                <div class="form-group">
                    <b id="delete_header_text"></b>
                    <p class="m0" id="delete_message"></p>
                    <p>This action cannot be undone</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-green" data-dismiss="modal">CANCEL</button>
                <button type="button" class="btn btn-red" id="delete_block_ok">YES, DELETE !</button>
            </div>
        </div>
    </div>
</div>
<!-- !.Modal pop up contents :: Delete Section popup-->

<!-- Modal pop up contents :: Create html -->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="category_manage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                <h4 class="modal-title" id="myModalLabel">ADD/EDIT CATEGORY</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Category Name *:</label>
                    <input type="text" maxlength="80" placeholder="eg: Aptitude" id="category_name" class="form-control">
                </div>
        
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-green" id="save_category_btn" onclick="saveCategory()" >SAVE</button>
                <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal pop up contents :: Create html -->

<!-- Modal pop up contents :: Create html -->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="category_migrate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                <h4 class="modal-title" id="myModalLabel">MIGRATE CATEGORY</h4>
            </div>
            <div class="modal-body">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>From *:</label>
                        <select class="form-control" id="category_selected_migrate">
                            
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>To *:</label>
                        <select class="form-control" id="category_select_migrate">
                            
                        </select>
                    </div>
                </div>       
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-green" id="save_migrate_category_btn" >MIGRATE</button>
                <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal pop up contents :: Create html -->

<!-- Modal pop up contents :: Create html -->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="questions_migrate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                <h4 class="modal-title" id="qmyModalLabel">MIGRATE QUESTIONS</h4>
            </div>
            <div class="modal-body">
                <p>The content belongs to this topic will be migrated to topic you selected. Choose any topics by choosing category and you can migrate content of this topics to other topics. </p>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Choose Category *:</label>
                        <select class="form-control" id="main_category_from">
                            
                        </select>
                        <input type="hidden" id="main_category_h_from" value="" />

                        <label>Choose Topics *:</label>
                        <select class="form-control" id="question_category_from">
                            
                        </select>
                        <input type="hidden" id="question_category_selected_h_from" value="" />
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Choose Category *:</label>
                        <select class="form-control" id="main_category_select_migrate">
                            
                        </select>
                        <input type="hidden" id="main_category_selected_to_migrate" value="" />

                        <label>Choose Topics *:</label>
                        <select class="form-control" id="question_category_select_migrate">
                            
                        </select>
                        <input type="hidden" id="question_category_selected_to_migrate" value="" />
                    </div>
                </div>
        
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-green" onclick="migrateQueCategoryConfirmed()">MIGRATE</button>
                <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal pop up contents :: Create html -->

<!-- Modal pop up contents:: Delete Section popup-->
<div class="modal fade alert-modal-new" id="deleteCategory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
            <div class="modal-body">
                <div class="form-group">
                    <b id="category_delete_header_text"></b>
                    <p class="m0" id="category_delete_message"></p>
                    <p>This action cannot be undone</p>
                </div>
            </div>
            <div class="modal-footer" id="category_delete_footer">
                <button type="button" class="btn btn-green" data-dismiss="modal">CANCEL</button>
                <button type="button" class="btn btn-red" id="delete_category_ok">YES, DELETE !</button>
            </div>
        </div>
    </div>
</div>
<!-- !.Modal pop up contents :: Delete Section popup-->

<!-- Modal pop up contents:: Delete Section popup-->
<div class="modal fade alert-modal-new" id="deleteQueCategory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
            <div class="modal-body">
                <div class="form-group">
                    <b id="topic_delete_header_text"></b>
                    <p class="m0" id="topic_delete_message"></p>
                    <p>This action cannot be undone</p>
                </div>
            </div>
            <div class="modal-footer" id="category_delete_footer">
                <button type="button" class="btn btn-green" data-dismiss="modal">CANCEL</button>
                <button type="button" class="btn btn-red" id="delete_topic_ok">YES, DELETE !</button>
            </div>
        </div>
    </div>
</div>
<!-- !.Modal pop up contents :: Delete Section popup-->

    <?php include_once "common_modals.php" ?>


<!-- Redactor for contact -->
<script type="text/javascript">
    var __testimonialTotalHomeCount = '<?php echo $testimonial_home_count; ?>';
    $('#contact_address').redactor({
        toolbar: false,
        shortcuts: false,
        minHeight: 250,
        maxHeight: 250,
    });
    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        // if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        //     return false;
        // }
        if ((evt.which != 46 || $(this).val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
            evt.preventDefault();
            return false;
        }
        return true;
    }


    function toggleIcon(e) {
    $(e.target)
        .prev('.panel-heading')
        .find(".more-less")
        .toggleClass('glyphicon-plus glyphicon-minus');
    }
    $('.panel-group').on('hidden.bs.collapse', toggleIcon);
    $('.panel-group').on('shown.bs.collapse', toggleIcon);
</script>
<!-- Redactor end -->
</body>
<!-- body end-->

</html>

<!-- Basic All Javascript -->
<!-- Jquery library -->
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.slimscroll.min.js"></script>
<!-- bootstrap library -->
<script type="text/javascript" src="<?php echo assets_url() ?>js/bootstrap.min.js"></script>
<script type="text/javascript">
    $('[data-toggle="tooltip"]').tooltip();
    var assets_url = '<?php echo assets_url(); ?>';
</script>

<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery-ui.min.js"></script>
<!-- custom layput js handling tooltip and hide show switch -->
<script type="text/javascript" src="<?php echo assets_url() ?>js/layout.min.js"></script>

<!-- Page Level Javascript -->
<script type="text/javascript" src="<?php echo assets_url() ?>js/app.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/system.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/language.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/environment.js"></script>
