<?php include_once 'header.php'; ?>
<style>
.faculty-image-upload-btn {
    cursor: pointer;
    height: 35%;
    left: 9px;
    opacity: 0;
    position: absolute;
    top: 35px;
    width: 87%;
    z-index: 2;
}
#us_about{ resize: vertical;}
.wrap { 
word-wrap: break-word; 
   white-space: pre-wrap;       
   white-space: -moz-pre-wrap;
   white-space: -pre-wrap;     
   white-space: -o-pre-wrap;   
       
}
</style>
<link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/jquery.rateyo.min.css">

<link href="<?php echo assets_url() ?>css/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
<script src="<?php echo assets_url() ?>js/bootstrap-tagsinput.js" type="text/javascript"></script>

<section class="content-wrap create-group-wrap settings-top">
    <!-- LEFT CONTENT --> <!-- STARTS -->
    <!-- ===========================  -->
    <div class="col-sm-12 faculty-left-wrap">
        <div class="col-sm-12">
            <div class="form-horizontal">
                <form class="form-horizontal" method="POST" id="institute_form" action="<?php echo admin_url('institutes/institute/'.$institute['id']); ?>">
                    <?php if(isset($error) && $error!=''): ?>
                        <div id="popUpMessage" class="alert alert-danger">    
                            <a data-dismiss="alert" class="close">×</a>
                            <?php echo $error ?>
                        </div>
                    <?php endif; ?>
                    <?php
                        if($this->session->flashdata('success'))
                        {
                            $success = $this->session->flashdata('success');
                        }
                    ?>
                    <?php if(isset($success) && $success!=''): ?>
                        <div id="popUpMessage" class="alert alert-success">    
                            <a data-dismiss="alert" class="close">×</a>
                            <?php echo $success ?>
                        </div>
                    <?php endif; ?>
                    <?php
                        if($this->session->flashdata('error'))
                        {
                            $flash_error = $this->session->flashdata('error');
                        }
                    ?>
                    <?php if(isset($flash_error) && $flash_error!=''): ?>
                        <div id="popUpMessage" class="alert alert-danger">    
                            <a data-dismiss="alert" class="close">×</a>
                            <?php echo $flash_error ?>
                        </div>
                    <?php endif; ?>
                    <!-- Text Box  -->
                    <div class="form-group">
                        <div class="col-sm-8">
                            <?php echo 'Institute Name'; ?> * : 
                            <input type="text" onkeypress="return preventSpecialCharector(event)" maxlength="50" class="form-control" id="us_name" name="ib_name" value="<?php echo $institute['ib_name'] ?>" placeholder="eg: Technical Institute">
                        </div>
                        <div class="col-sm-4">
                            <?php echo lang('institute_code'); ?> * : 
                            <input type="text" maxlength="6" class="form-control" id="us_institute_code" name="ib_institute_code" value="<?php echo $institute['ib_institute_code'] ?>" placeholder="eg: KTU01">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12">
                            <?php echo lang('institute_address'); ?> * : 
                            <textarea class="form-control" id="ib_address" name="ib_address" onkeyup="validateMaxLength(this.id)" maxlength="1000" rows="3"><?php echo $institute['ib_address'] ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        
                        <div class="col-sm-6">
                            <?php echo lang('contact_number'); ?> * : 
                            <input type="text" class="form-control" id="us_phone" onkeypress="return preventAlphabets(event)" maxlength="11" name="ib_phone" value="<?php echo ($institute['ib_phone']>0)?$institute['ib_phone']:'' ?>" placeholder="eg: 1234567890">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-6">
                            <?php echo 'State'; ?> *: 
                            <select id="institute_state" class="form-control">
                                <option value="">Choose State</option>
                                <?php if(!empty($states)): ?>
                                <?php foreach ($states as $state): ?>
                                    <option <?php echo ((isset($institute_city['state_id']) && $institute_city['state_id'] == $state['id'])?'selected="selected"':'') ?> value="<?php echo $state['id'] ?>"><?php echo $state['state_name'] ?></option>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <?php echo 'District'; ?> *: 
                            <select id="us_native" name="ib_native" class="form-control">
                                <option value="">Choose District</option>
                                <?php if(!empty($cities)): ?>
                                <?php foreach ($cities as $city): ?>
                                    <option <?php echo ((isset($institute_city['state_id']) && $institute_city['id'] == $city['id'])?'selected="selected"':'') ?> value="<?php echo $city['id'] ?>"><?php echo $city['city_name'] ?></option>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-sm-12">
                            <?php echo lang('institute_head_name'); ?> * : 
                            <input type="text" onkeypress="return preventSpecialCharector(event)" maxlength="50" class="form-control" id="ib_head_name" name="ib_head_name" value="<?php echo $institute['ib_head_name'] ?>" placeholder="eg: Ankit Verma">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-6">
                            <?php echo lang('institute_head_email'); ?> * : 
                            <input type="text" class="form-control" id="ib_head_email" maxlength="50" name="ib_head_email" value="<?php echo $institute['ib_head_email'] ?>" placeholder="eg: youremail@domain.com">
                        </div>
                        <div class="col-sm-6">
                            <?php echo lang('institute_head_phone'); ?> * : 
                            <input type="text" onkeypress="return preventAlphabets(event)" maxlength="11" class="form-control" id="ib_head_phone" name="ib_head_phone" value="<?php echo $institute['ib_head_phone'] ?>" placeholder="eg: 34563456">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-sm-12">
                            <?php echo lang('nodal_officer_name'); ?> : 
                            <input type="text" onkeypress="return preventSpecialCharector(event)" maxlength="50" class="form-control" id="ib_officer_name" name="ib_officer_name" value="<?php echo $institute['ib_officer_name'] ?>" placeholder="eg: David John">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-6">
                            <?php echo lang('nodal_officer_email'); ?> : 
                            <input type="text" class="form-control" id="ib_officer_email" maxlength="50" name="ib_officer_email" value="<?php echo $institute['ib_officer_email'] ?>" placeholder="eg: youremail@domain.com">
                        </div>
                        <div class="col-sm-6">
                            <?php echo lang('nodal_officer_phone'); ?> : 
                            <input type="text" onkeypress="return preventAlphabets(event)" maxlength="11" class="form-control" id="ib_officer_phone" name="ib_officer_phone" value="<?php echo $institute['ib_officer_phone'] ?>" placeholder="eg: 3456345634">
                        </div>
                    </div>
                    
                    <!-- Text Box Addons  -->
                    <div class="form-group">
                        <div class="col-sm-12">
                            <?php echo lang('about_institution'); ?> * : 
                            <textarea class="form-control" id="us_about" name="ib_about" onkeyup="validateMaxLength(this.id)" maxlength="1000" rows="3"><?php echo $institute['ib_about'] ?></textarea>
                            <span class="pull-right my-italic" id="us_about_char_left">  <?php echo intval(1000 - strlen($institute['ib_about'])) ?> Characters left</span>
                        </div>
                    </div>

                    <!-- Text Box  -->
                    
                    <div class="form-group">
                        <div class="col-sm-6">
                            <?php echo lang('classroom_code'); ?> (Dial-in number)* : 
                            <input type="text" maxlength="10" class="form-control" id="ib_class_code" name="ib_class_code" value="<?php echo $institute['ib_class_code'] ?>" placeholder="eg: KKS2345SB">
                        </div>
                        <div class="col-sm-6">
                            Class Room Strength : 
                            <input type="text" maxlength="5" onkeypress="return preventAlphabets(event)" class="form-control" id="ib_class_strength" name="ib_class_strength" value="<?php echo $institute['ib_class_strength'] ?>" placeholder="eg: 200">
                        </div>
                    </div>

                    <!-- Text Box  -->

                    <input type="hidden" id="ib_id" name="id" value="<?php echo $institute['id'] ?>">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <input type="button" class="pull-right btn btn-green marg10" onclick="saveInstitute()" value="SAVE"></input>
                            <input type="button" class="pull-right btn btn-danger marg10" onclick="location.href='<?php echo admin_url('institutes') ?>'" value="Cancel"></input>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
    <!-- ==========================  -->
    <!--  LEFT CONTENT--> <!-- ENDS -->
</section>
<div class="col-sm-6 pad0 right-content">
    <div class="container-fluid right-box teacher-bg">
        <div class="row overflow100">
            <!--faculty profile div content starts here -->
            <div class="col-sm-12 course-cont-wrap image-uploader faculty innercontent">
                <div class="row pattern-bg">
                    <div class="faculty-img pull-left">

                        <div class="upload-prieview no-padding"> 
                            <div class="img-chng teacher-pic">
                                <div class="settings-logo">
                                    <span class="icon-wrap-round img teacher-wrap">
                                        <?php $user_img     = (($institute_data['ib_image'] == 'default.jpg')?default_institute_path():institute_path()); ?>
                                        <img id="institute_image" src="<?php echo $user_img.$institute_data['ib_image']?>">
                                    </span>
                                </div>
                                <input name="file" class="faculty-image-upload-btn" id="ib_image" accept="image/*" type="file">
                                <button class="btn btn-green pos-abs">CHANGE IMAGE<ripples></ripples></button>
                            </div>
                        </div>

                    </div> 
                    <div class="faculty-info pull-left">   
                        <span class="center-block faculty-name wrap"><h1><?php echo $institute_data['ib_institute_code'] ?> - <?php echo $institute_data['ib_name'] ?></h1></span>                                                
                    </div>
                </div>
                <div class="row line"></div>
                <?php if($institute_data['ib_about'] != '' ):?>
                    <div class="faculty-intro" style="max-height:150px;overflow-y:auto;">
                        <h4 class="text-uppercase small-head"><?php echo lang('about_institution') ?></h4>
                        <p class="wrap"><?php echo $institute_data['ib_about'] ?></p>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-sm-12">
                        <ul class="teacher-specs">
                            <?php 
                            $institute_native = '';
                            if(isset($instituted_city['city_name'])&& $instituted_city['city_name'] != '')
                            {
                                $institute_native = $instituted_city['city_name'];
                                if(isset($states[$instituted_city['state_id']])&& !empty($states[$instituted_city['state_id']]))
                                {
                                    if($states[$instituted_city['state_id']]['state_name'] != $instituted_city['city_name'])
                                    {                                    
                                        $institute_native .= ', '. $states[$instituted_city['state_id']]['state_name'];
                                    }
                                }
                            }
                            ?>

                            <?php if($institute_data['ib_phone'] != '' ):?>
                                <li><i class="icon"></i><b>Contact Number</b> : <?php echo $institute_data['ib_phone']; ?></li>
                            <?php endif; ?>
                            <?php if($institute_native != '' ):?>
                                <li><i class="icon icon-location"></i><b>Location</b> : <?php echo $institute_native; ?></li>
                            <?php endif; ?>

                            <?php if($institute_data['ib_address'] != '' ):?>
                                <li class="d-flex">
                                    <div style="min-width: 93px;"><i class="icon icon-location"></i><b>Address</b> :</div>
                                    <div><?php echo $institute_data['ib_address']; ?></div>
                                </li>
                            <?php endif; ?>
                            <?php if($institute_data['ib_head_name'] != '' ):?>
                                <li><i class="icon"></i><b><?php echo lang('institute_head_name') ?></b> : <?php echo $institute_data['ib_head_name']; ?></li>
                            <?php endif; ?>

                            <?php if($institute_data['ib_head_email'] != '' ):?>
                                <li><i class="icon"></i><b><?php echo lang('institute_head_email') ?></b> : <?php echo $institute_data['ib_head_email']; ?></li>
                            <?php endif; ?>

                            <?php if($institute_data['ib_head_phone'] != '' ):?>
                                <li><i class="icon"></i><b><?php echo lang('institute_head_phone') ?></b> : <?php echo $institute_data['ib_head_phone']; ?></li>
                            <?php endif; ?>

                            <?php if($institute_data['ib_officer_name'] != '' ):?>
                                <li><i class="icon"></i><b><?php echo lang('nodal_officer_name') ?></b> : <?php echo $institute_data['ib_officer_name']; ?></li>
                            <?php endif; ?>

                            <?php if($institute_data['ib_officer_email'] != '' ):?>
                                <li><i class="icon"></i><b><?php echo lang('nodal_officer_email') ?></b> : <?php echo $institute_data['ib_officer_email']; ?></li>
                            <?php endif; ?>

                            <?php if($institute_data['ib_officer_phone'] != '' ):?>
                                <li><i class="icon"></i><b><?php echo lang('nodal_officer_phone') ?></b> : <?php echo $institute_data['ib_officer_phone']; ?></li>
                            <?php endif; ?>

                            <?php if($institute_data['ib_class_code'] != '' ):?>
                                <li><i class="icon"></i><b><?php echo lang('classroom_code') ?></b> : <?php echo $institute_data['ib_class_code']; ?></li>
                            <?php endif; ?>
                            <?php if($institute_data['ib_class_strength'] != '' ):?>
                                <li><i class="icon"></i><b>Class Room Strength</b> : <?php echo $institute_data['ib_class_strength']; ?></li>
                            <?php endif; ?>
                            <?php /* ?><?php if($institute['us_language_speaks'] != '' ):?>
                                <li><i class="icon icon-volume"></i><b>Speaks</b> : <p class="wrap"><?php echo $institute['us_language_speaks'] ?></p></li>
                            <?php endif; ?><?php */ ?>
                        </ul>    
                    </div>
                </div> 
                

            </div>

        </div>
    </div>
</div>

<script>
    var __instituteSelected   = new Array();
    var __courseSelected    = new Array();
    var __instituteId         = '<?php echo $institute['id']; ?>';
</script>
<script src="<?php echo assets_url() ?>js/institute_form.js"></script>
<script src="<?php echo assets_url() ?>js/jquery.rateyo.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.slimscroll.min.js"></script>
<?php include_once 'footer.php'; ?>