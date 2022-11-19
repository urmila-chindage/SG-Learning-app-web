<?php include_once 'header.php'; ?>
<style>
section.base-cont-top-heading.courses-tab { height: 47px; }
.courses-tab ol.nav li { border-bottom: unset !important; }
.page-content-editor .redactor-in{min-height: 200px}
</style>
    <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/redactor.css" />
    <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/alignment.css" />
<?php 
$GLOBALS['id']        = $id;
$GLOBALS['selected']  = 'selected="selected"';
$GLOBALS['checked']   = 'checked="checked"';
?>
<section class="courses-tab base-cont-top-heading">
    <ol class="nav nav-tabs offa-tab">
        <li class="active">
            <a href="javascript:void(0)"> <?php echo lang('basic') ?></a>
            <span class="active-arrow"></span>
        </li>
    </ol>
</section>
<section class="content-wrap small-width base-cont-top-heading pad0 information-settings">
<script>
function formValidate(){
     
    var error               = false;
    var errorMessage        = '';
    if(!$('#n_title').val().trim()){
        error = true;
        errorMessage        += '<p>Information Bar Title is required.</p>';
    }

    var notification_description    = $('#n_content').val().trim();
        notification_description    = notification_description.replace(/<img[^>]+>/i, 'img');
        notification_description    = notification_description.replace(/<\/?[^>]+(>|$)/g, '');
        notification_description    = notification_description.replace(/&.*;/g, '');
    if(!notification_description){
        errorMessage    += '<p>Description is required.</p> ';
        error = true;
    }
    var regex = /(<([^>]+)>)/ig;
    var removedHtml = $('#n_content').val().replace(regex,'');
    var spaceRemovedHtml = removedHtml.replace(/&nbsp;/g, ' ');

    if(spaceRemovedHtml.length > 300){
        errorMessage    += '<p>Description should be maximum 300 Characters.</p> ';
        error = true;
    }

    var notification_description_tags = ($('#n_content').val().match(/<p>/g) || []).length;
    if(notification_description_tags > 2){
        errorMessage    += '<p>Description should be maximum two lines.</p> ';
        error = true;
    }

    if(!$('#n_expiry_date').val().trim()){
        errorMessage        += '<p>Expiry Date is required.</p>';
        error = true;
    } else {
        
        var now             = new Date();
        var selectedDate    = $('#n_expiry_date').val().split('-');
            selectedDate    = new Date(selectedDate[2]+'-'+selectedDate[1]+'-'+selectedDate[0]);
            selectedDate.setDate(selectedDate.getDate() + 1);

       if (selectedDate < now){
        errorMessage    += '<p>Expiry Date should be future date.</p> ';
        error = true;
       } 
    }
    if(error){
        $('.alert').hide();
        $('#js_error_container').show().html(`
                        <a class="close" data-dismiss="alerts" onClick="$('#js_error_container').hide()">×</a>
                                ${errorMessage}
                        `).addClass('alert-danger');
        $(window).scrollTop(0);
        return false;
    }
return true;
}
</script>
    <div class="left-wrap col-sm-12 pad0">
        <div class="container-fluid course-create-wrap"  id="notification_form">
            <div class="row-fluid course-cont">
                <div class="col-sm-12 pad0 notification-manager">
                    <div class="col-md-6">
                        <div class="form-horizontal">
                            <?php include_once('messages.php') ?>
                            <form onSubmit="return formValidate()" class="form-horizontal" action="<?php echo admin_url('notification/basics/'.$id) ?>" method="POST">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <?php echo lang('notification_title') ?> *  : 
                                        <input type="text" class="form-control" maxlength="50" placeholder="eg: Mathematical Calculations" name="n_title" id="n_title" value="<?php echo htmlentities($n_title) ?>" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-3">
                                        <label class="check-box-holder">   
                                            <label class="custom-radio">
                                                <input class="list-checkbox-featured n_bar" type="radio" name="n_bar_type" value="1" <?=($n_notification_bar_type)?(($n_notification_bar_type == '1')?'checked="true"':''):'checked="true"'?>>
                                                <span class="checkmark"></span>
                                            </label>
                                            <span class="showin-home-text">Pop Up</span>
                                        </label>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="check-box-holder">   
                                            <label class="custom-radio">
                                                <input class="list-checkbox-featured n_bar" type="radio" name="n_bar_type" value ="2" <?=($n_notification_bar_type)?(($n_notification_bar_type == '2')?'checked="true"':''):''?>>
                                                <span class="checkmark"></span>
                                            </label>
                                            <span class="showin-home-text">Top Bar</span>
                                        </label> 
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <?php echo lang('description') ?> <em>(Maximum 300 characters and two lines are allowed)</em> * : 
                                        <textarea class="form-control information-writer" rows="10" name="n_content" id="n_content"><?php echo $n_content ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">    
                                    <div class="col-md-6">
                                        <?php echo lang('expiry_date') ?> * :
                                        <input placeholder="dd-mm-yyyy" type="text" id="n_expiry_date" name="n_expiry_date" class="form-control" readonly="" style="background: #fff;" value="<?php echo date('d-m-Y',strtotime($n_expiry_date)); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo lang('status') ?>:
                                        <select class="form-control" name="n_status" id="n_status"> 
                                            <option value="1" <?php echo ($n_status == 1)?$GLOBALS['selected']:'';  ?>><?php echo lang('active') ?></option>
                                            <option value="0" <?php echo ($n_status == 0)?$GLOBALS['selected']:'';  ?>><?php echo lang('inactive') ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <input type="submit" class="pull-right btn btn-green marg10" value="SAVE">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="col-sm-12">
                                <span>Preview  : </span>
                                <div class="preview-wrapper">
                                    <div class="prev-home-screen">
                                        <!-- Top notitfication on header -->
                                        <div class="top-notification-slider text-center" id="top_notification_slider" style="display: none;">
                                            <div class="slide">
                                                <p><strong>Special Offer</strong> for new courses and bundles.... <strong>Hurry!!!!!</strong></p>            
                                            </div>
                                            <div class="slide showing n_content_html" >
                                                <?php if($n_content): echo $n_content?>
                                                <?php else:?>
                                                <p>Helloo all ,we are having an offer on all&nbsp;course.please contact administrator for details.&nbsp;</p>            
                                                <?php endif;?>
                                                
                                            </div>
                                            <span class="close">×</span>
                                        </div>
                                        <!-- Top notitfication on header ends -->
                                        
                                        <div class="preview-screenshot"><img class="img-responsive" src="<?php echo assets_url() ?>images/screen.png"></div>

                                        <!-- Top notitfication on modal -->
                                        <div class="information-modal modal fade in" id="information-modal" role="dialog" style="display: block;">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-body">
                                                    <div class="">
                                                        <button type="button" class="close" data-dismiss="modal">×</button>
                                                    </div>
                                                    <div class="text-center information-content ">
                                                        <div class="n_content_html">
                                                            <?php if($n_content): echo $n_content?>
                                                            <?php else:?>
                                                                <p>New Student Deal</p>
                                                                <p>up to 90% off Sign up now to get course for as low as Rs.710 each New user only</p>
                                                                <p>Hurry, sale ends on Monday 1st July 2019</p>
                                                            <?php endif;?>
                                                        </div>
                                                        <div class="close-btn-holder">
                                                            <button type="button" class="btn close-btn-orange" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Top notitfication on modal ends -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- JS -->
<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>

<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/limiter.js"></script>
<script src="<?php echo assets_url() ?>js/jquery.form-validator.min.js"></script>
<script src="<?php echo assets_url() ?>js/notification_settings.js"></script>
<?php include_once 'footer.php'; ?>
<link rel="stylesheet" href="<?php echo assets_url() ?>css/jquery-ui.css">
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery-ui.min.js"></script>