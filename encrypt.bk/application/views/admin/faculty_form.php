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
                <form class="form-horizontal" method="POST" id="faculty_form" action="<?php echo admin_url('faculties/faculty/'.$faculty['id']); ?>">
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
                            <?php echo lang('name'); ?> * : 
                            <input type="text" /*onkeypress="return preventSpecialCharector(event)"*/ maxlength="50" class="form-control" id="us_name" name="us_name" value="<?php echo $faculty['us_name'] ?>" placeholder="eg: Ankit Verma">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-6">
                            <?php echo lang('email'); ?> * : 
                            <input type="text" class="form-control" id="us_email" maxlength="50" name="us_email" value="<?php echo $faculty['us_email'] ?>" placeholder="eg: youremail@domain.com">
                        </div>

                        <div class="col-sm-6">
                            <?php echo lang('contact_number'); ?> * : 
                            <input type="text" class="form-control" id="us_phone" onkeypress="return preventAlphabets(event)" maxlength="11" name="us_phone" value="<?php echo ($faculty['us_phone']!='')?$faculty['us_phone']:'' ?>" placeholder="eg: 1234567890">
                        </div>

                    </div>
                    
                    <?php //if($faculty['us_role_id'] == 3): ?>
                    <div class="form-group">
                        <div class="col-sm-8">
                            <?php echo lang('qualification'); ?> <?php echo (($faculty['us_role_id'] == 3)?'*':''); ?> : 
                            <input type="text" class="form-control" id="us_degree" name="us_degree" value="<?php echo $faculty['us_degree'] ?>" placeholder="eg: MS.(BIOLOGY), IISER MOHALI" maxlength="75">
                        </div>
                        <div class="col-sm-4">
                            <?php echo lang('experiance'); ?> (Years)<?php echo (($faculty['us_role_id'] == 3)?'*':''); ?> : 
                            <input type="text" class="form-control" id="us_experiance" maxlength="5" name="us_experiance" value="<?php echo ($faculty['us_experiance']>0)?floor($faculty['us_experiance']/12).'.'.($faculty['us_experiance']%12):'' ?>" placeholder="eg: 2">
                        </div>
                    </div>
                    <?php //endif; ?>
                    
                    <!-- Text Box Addons  -->
                    <div class="form-group">
                        <div class="col-sm-12">
                            <?php echo lang('about_me'); ?> * : 
                            <textarea class="form-control" id="us_about" name="us_about" onkeyup="validateMaxLength(this.id)" maxlength="1000" rows="3"><?php echo $faculty['us_about'] ?></textarea>
                            <span class="pull-right my-italic" id="us_about_char_left">  <?php echo intval(1000 - strlen($faculty['us_about'])) ?> Characters left</span>
                        </div>
                    </div>
                    <?php
                    if($faculty['us_role_id'] == 3):
                        ?>
                        <div class="form-group user-taginput-type" id="expertise_id">
                            <div class="col-sm-12">
                                Expertise *: 
                                <input type="text" id="us_expertise" data-role="tagsinput" name="us_expertise" value="<?php echo $faculty['us_expertise'] ?>"  class="form-control" autocomplete="off" placeholder="eg: CAD, Computer Science" >
                                <ul class="auto-search-lister-expertise" id="listing_expertise" style="display: none;"></ul>
                            </div>
                        </div> 
                        <?php
                    endif;
                    ?>                    
                    <!-- Text Box  -->
                    <div class="form-group">
                        <div class="col-sm-6">
                            <?php echo 'State'; echo ($faculty['us_role_id'] == 3)? '*':''?> : 
                            <select id="faculty_state" class="form-control">
                                <option value="">Choose State</option>
                                <?php if(!empty($states)): ?>
                                <?php foreach ($states as $state): ?>
                                    <option <?php echo ((isset($faculty_city['state_id']) && $faculty_city['state_id'] == $state['id'])?'selected="selected"':'') ?> value="<?php echo $state['id'] ?>"><?php echo $state['state_name'] ?></option>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <?php echo 'District'; echo ($faculty['us_role_id'] == 3)? '*':''?> : 
                            <select id="us_native" name="us_native" class="form-control">
                                <option value="">Choose District</option>
                                <?php if(!empty($cities)): ?>
                                <?php foreach ($cities as $city): ?>
                                    <option <?php echo ((isset($faculty_city['state_id']) && $faculty_city['id'] == $city['id'])?'selected="selected"':'') ?> value="<?php echo $city['id'] ?>"><?php echo $city['city_name'] ?></option>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>                                   

                    <!-- Text Box  -->                   
                    <div class="form-group user-taginput-type" id="language_speaks">
                        <div class="col-sm-12">
                            <?php echo lang('language_speaks'); echo ($faculty['us_role_id'] == 3)? '*':'' ?> : 
                            <input type="text" id="us_language_speaks" onkeypress="return preventNumbers(event)" data-role="tagsinput" name="us_language_speaks" value="<?php echo $faculty['us_language_speaks'] ?>"  class="form-control" autocomplete="off" placeholder="eg: English, Hindi" >
                            <ul class="auto-search-lister-lang" id="listing_language" style="display: none;"></ul>
                        </div>
                    </div>
                    <?php /* ?><input type="hidden" name="us_degree" value="">
                    <input type="hidden" name="us_experiance" value="">
                    <input type="hidden" name="us_native" value="">
                    <input type="hidden" name="us_language_speaks" value=""><?php */ ?>
                    <input type="hidden" name="us_badge" value="0"> 
                    
                    <?php
                    if($faculty['us_role_id'] == 3):
                        ?>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <?php echo lang('youtube_url'); ?> 1: 
                                <input type="text" class="form-control us_youtube_url" id="us_youtube_url" name="us_youtube_url[]" value="<?php echo $faculty['us_youtube_url'][0]; ?>" placeholder="eg: http://youtube.com/example" maxlength="75">
                            </div>
                            <div class="col-sm-12">
                                <?php echo lang('youtube_url'); ?> 2: 
                                <input type="text" class="form-control us_youtube_url" id="us_youtube_url" name="us_youtube_url[]" value="<?php echo $faculty['us_youtube_url'][1]; ?>" placeholder="eg: http://youtube.com/example" maxlength="75">
                            </div>
                            <div class="col-sm-12">
                                <?php echo lang('youtube_url'); ?> 3: 
                                <input type="text" class="form-control us_youtube_url" id="us_youtube_url" name="us_youtube_url[]" value="<?php echo $faculty['us_youtube_url'][2]; ?>" placeholder="eg: http://youtube.com/example" maxlength="75">
                            </div>
                        </div> <br />
                        <?php
                    endif;
                    ?>
                    
                    
                    <input type="hidden" id="us_role_id" value="<?php echo $faculty['us_role_id'] ?>">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <input type="button" class="pull-right btn btn-green marg10" onclick="saveFaculty()" value="SAVE"></input>
                            <input type="button" class="pull-right btn btn-danger marg10" onclick="location.href='<?php echo admin_url('faculties') ?>'" value="Back"></input>
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
                        
                        <?php if($faculty['us_experiance'] != '' && $faculty['us_experiance'] > 0 ):?>
                        <?php 
                            $experianceHtml  = '';
                            $years           = floor($faculty['us_experiance'] / 12); // 1
                            $remainingMonths = floor($faculty['us_experiance'] % 12); // 6
                            if($years > 0 )
                            {
                                $experianceHtml .= $years+'';
                            }
                            if($remainingMonths > 0 )
                            {
                                $experianceHtml .= '.'.$remainingMonths;
                            }
                        ?>
                        <span class="center-block line"></span>
                        <span class="teach-exp pull-right">Experience: <span class="font-bold"><?php echo $experianceHtml; ?> yrs</span></span>
                        <?php endif;?> 
                        <span><?php echo $faculty['us_email'] ?></span>                              

                        </span>
                    </div>
                </div>
                <div class="row line"></div>
                <?php if($faculty['us_about'] != '' ):?>
                    <div class="faculty-intro" style="max-height:150px;overflow-y:auto;">
                        <h4 class="text-uppercase small-head"><?php echo lang('about_me') ?></h4>
                        <p class="wrap"><?php echo $faculty['us_about'] ?></p>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-sm-12">
                        <ul class="teacher-specs">
                            <?php 
                            $faculty_native = '';
                            if(isset($faculty_city['city_name'])&& $faculty_city['city_name'] != '')
                            {
                                $faculty_native = $faculty_city['city_name'];
                                if(isset($states[$faculty_city['state_id']])&& !empty($states[$faculty_city['state_id']]))
                                {
                                    if($states[$faculty_city['state_id']]['state_name'] != $faculty_city['city_name'])
                                    {                                    
                                        $faculty_native .= ', '. $states[$faculty_city['state_id']]['state_name'];
                                    }
                                }
                            }
                            ?>
                            <?php if($faculty['us_phone'] != '' && $faculty['us_phone'] != '0'):?>
                                <li><i class="icon"></i><b>Contact Number</b> : <?php echo $faculty['us_phone']; ?></li>
                            <?php endif; ?>
                            <?php if($faculty_native != '' ):?>
                                <li><i class="icon icon-location"></i><b>From</b> : <?php echo $faculty_native; ?></li>
                            <?php endif; ?>
                            <?php if($faculty['us_language_speaks'] != '' ):?>
                                <li><i class="icon icon-volume"></i><b>Speaks</b> : <?php echo $faculty['us_language_speaks'] ?></li>
                            <?php endif; ?>
                        </ul>    
                    </div>
                </div> 
                <?php 
                if($faculty['us_role_id'] != '8')
                {
                    $permission     = $this->accesspermission->get_permission(
                        array(
                            'role_id' => $faculty['us_role_id'],
                            'module' => 'course'
                            ));
                    if(in_array('1', $permission) && $faculty['rl_full_course'] == 0)
                    {
                        ?>
                        <div class="col-sm-12 bottom-line">
                            <div class="row">
                                <div class="col-sm-8 no-padding">
                                    <h4 class="text-uppercase custom-head">Courses Handling</h4>
                                </div>
                                <div class="col-sm-4 no-padding">
                                    <input type="button" class="pull-right btn btn-green marg10 selected" onclick="addFacultyToCourse('<?php echo $faculty['id'] ?>')" value="ADD TO COURSE"></input>
                                </div>
                            </div>
                        </div>
                        <div class="row"> 
                            <div class="course-cont-wrap wrap-fix-course no-padding"> 
                                <div class="col-sm-12 table course-cont only-course rTable" style="" id="assigned_course_wrapper">
                                    <?php if(isset($faculty['courses']) && !empty($faculty['courses'])): ?>
                                        <?php foreach ($faculty['courses'] as $course): ?>
                                            <div class="rTableRow" id="course_row_<?php echo $course['ct_course_id']  ?>">
                                                <div class="rTableCell cours-fix ellipsis-hidden no-border no-padding"> 
                                                    <div class="ellipsis-style">  
                                                        <span class="icon-rounder"><i class="icon icon-graduation-cap"></i></span>
                                                        <a href="javascript:void(0)" class="cust-sm-6 padd0" id="course_title_<?php echo $course['ct_course_id'] ?>"> <?php echo $course['cb_title'] ?></a>
                                                    </div>
                                                </div>
                                                <div class="rTableCell pad0 cours-fix no-border"> 
                                                    <div class="col-sm-12 pad0"><span class="delete-cover pull-right"><a href="javascript:void(0)" onclick="unassignCourse('<?php echo $course['ct_course_id'] ?>');"><i class="icon icon-cancel-1 delte"></i></a></span></div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>   
                        </div>
                        <?php
                    }
                }
                ?>
                <!--faculty profile div ends here-->

            </div>

        </div>
    </div>
</div>

<script>
    var __facultySelected   = new Array();
    var __courseSelected    = new Array();
    var __facultyId         = '<?php echo $faculty['id']; ?>';
</script>
<script src="<?php echo assets_url() ?>js/faculty_form.js"></script>
<script src="<?php echo assets_url() ?>js/jquery.rateyo.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.slimscroll.min.js"></script>
<?php include_once 'footer.php'; ?>