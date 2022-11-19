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
                <form class="form-horizontal" method="POST" id="faculty_form" action="<?php echo admin_url('user/edit/'.$faculty['id']); ?>">
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
                        <div class="col-sm-12">
                            <?php echo lang('sf_name'); ?> * : 
                            <input type="text" onkeypress="return preventSpecialCharector(event)" maxlength="50" class="form-control" id="us_name" name="us_name" value="<?php echo $faculty['us_name'] ?>" placeholder="eg: Ankit Verma">
                        </div>
                    </div>

                    <div class="form-group">
                        <!-- <div class="col-sm-6">
                            <?php echo lang('sf_email'); ?> * : 
                            <input type="text" class="form-control" id="us_email" maxlength="50" name="us_email" value="<?php echo $faculty['us_email'] ?>" placeholder="eg: youremail@domain.com">
                        </div> -->

                        <div class="col-sm-6">
                            <?php echo lang('sf_contact_number'); ?> * : 
                            <input type="text" class="form-control" id="us_phone" onkeypress="return preventAlphabets(event)" maxlength="13" name="us_phone" value="<?php echo ($faculty['us_phone']>0)?$faculty['us_phone']:'' ?>" placeholder="eg: 1234567890">
                        </div>

                    </div>
                    
                    <!-- Text Box Addons  -->
                    <div class="form-group">
                        <div class="col-sm-12">
                            <?php echo lang('sf_about_me'); ?> * : 
                            <textarea class="form-control" id="us_about" name="us_about" onkeyup="validateMaxLength(this.id)" maxlength="1000" rows="3"><?php echo $faculty['us_about'] ?></textarea>
                            <span class="pull-right my-italic" id="us_about_char_left">  <?php echo intval(1000 - strlen($faculty['us_about'])) ?> Characters left</span>
                        </div>
                    </div>

                    <input type="hidden" id="us_role_id" value="<?php echo $faculty['us_role_id'] ?>">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <input type="button" class="pull-right btn btn-green marg10" onclick="saveFaculty()" value="SAVE"></input>
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
                                        <?php $user_img     = (($faculty['us_image'] == 'default.jpg')?default_user_path():user_path()); ?>
                                        <img id="faculty_image" src="<?php echo $user_img.$faculty['us_image']?>">
                                    </span>
                                </div>
                                <input name="file" class="faculty-image-upload-btn" id="us_image" accept="image/*" type="file">
                                <button class="btn btn-green pos-abs">CHANGE IMAGE<ripples></ripples></button>
                            </div>
                        </div>

                    </div> 
                    <div class="faculty-info pull-left">   
                        <span class="center-block faculty-name wrap"><h1><?php echo $faculty['us_name'] ?></h1></span>
                        <span class="center-block faculty-qualification wrap"><?php echo ($faculty['us_degree'])?$faculty['us_degree']:$faculty['rl_name']; ?></span>
                    </div>
                </div>
                <div class="row line"></div>
                <?php if($faculty['us_about'] != '' ):?>
                    <div class="faculty-intro" style="max-height:150px;overflow-y:auto;">
                        <h4 class="text-uppercase small-head"><?php echo lang('sf_about_me') ?></h4>
                        <p class="wrap"><?php echo $faculty['us_about'] ?></p>
                    </div>
                <?php endif; ?>
                <!--faculty profile div ends here-->

            </div>

        </div>
    </div>
</div>

<script>
    var __facultyId         = '<?php echo $faculty['id']; ?>';
</script>
<script src="<?php echo assets_url() ?>js/user_form.js"></script>
<script src="<?php echo assets_url() ?>js/jquery.rateyo.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.slimscroll.min.js"></script>
<?php include_once 'footer.php'; ?>